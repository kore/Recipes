<?php
/**
 * arbit session wrapper class
 *
 * This file is part of arbit.
 *
 * arbit is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * arbit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with arbit; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @version $Revision: 1469 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Arbit session wrapper
 *
 * We use a wrapper around PHPs well working and abstracted sessions functions,
 * because we want to additionally offer the following:
 *
 * - User transparent seperation of project dependend session data, by
 *   maintaining custom arrays for values.
 *
 * - Value checking and readonly values for selected session keys.
 *
 * - Unified session access logging and profiling
 *
 * - Simple helper functions for form token generation and validation inside
 *   the main session
 *
 * @package Core
 * @version $Revision: 1469 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitSession
{
    /**
     * Currently executed request, available for application wide
     * read access of the current request context.
     *
     * @var arbitRequest
     */
    protected static $request;

    /**
     * Currently selected project, defining the currently used session value
     * namespace.
     *
     * @var array
     */
    protected static $project = 'core';

    /**
     * Flag if the session currently is (still) writeable.
     *
     * @var boolean
     */
    protected static $writeable = false;

    /**
     * Currently used session backend
     *
     * @var arbitSessionBackend
     */
    protected static $backend;
    
    /**
     * Get currently selected request
     *
     * Currently executed request, available for application wide
     * read access of the current request context.
     *
     * @return arbitRequest
     */
    public static function getCurrentRequest()
    {
        return self::$request;
    }

    /**
     * Set session backend
     *
     * Configure the session backend to use.
     * 
     * @param arbitSessionBackend $backend 
     * @return void
     */
    public static function setBackend( arbitSessionBackend $backend )
    {
        self::$backend = $backend;
    }

    /**
     * Initialize session from currently selected project
     *
     * This method may throw a arbitSessionTakeOverException, if a session
     * takeover is detected.
     *
     * @param arbitRequest $request
     * @return void
     */
    public static function initialize( arbitRequest $request )
    {
        if ( self::$backend === null )
        {
            throw new arbitRuntimeException( 'No session backend configured.' );
        }

        self::$backend->initialize();
        self::$writeable = true;

        self::$request = $request;
        self::$project = $project = $request->controller;

        // Initialize project array, if required
        if ( !isset( self::$backend[$project] ) )
        {
            self::$backend[$project] = array();
            self::resetSession();
        }

        self::checkClientHash();
        self::generateFormToken();
    }

    /**
     * Close current session
     *
     * Close current session for writing, so that other request are not locked.
     *
     * @return void
     */
    public static function close()
    {
        self::$backend->writeClose();
        self::$writeable = false;
    }

    /**
     * Ensure client hash stays the same
     *
     * This method will throw an exception, if it notices a session takeover.
     * The check for this bases only on the user-agent string, and may be
     * considered too weak.
     *
     * @return void
     */
    protected static function checkClientHash()
    {
        // Build hash on basis of user agent string. This does not prevent
        // completely from session takeover, as long as many users are using
        // the same browser versions, but at least this may work in some cases.
        $hash = md5( 'arbit_' . self::$request->agent );

        if ( !isset( self::$backend['_hash'] ) )
        {
            self::$backend['_hash'] = $hash;
        }
        elseif ( self::$backend['_hash'] !== $hash )
        {
            // Session takeover - provide a new cleaned session
            self::regenerateId();
            self::resetSession();
        }
    }

    /**
     * Reset the session
     *
     * Reset the current session, by assigning only the permissions for
     * anonymous users and resetting the login state.
     *
     * @return void
     */
    protected static function resetSession()
    {
        self::set( 'login',    false );
        self::set( 'tokens',   array() );
        self::set( 'settings', array() );

        // Fetch permissions for anonymous user
        $group = new arbitModelGroup( 'group-anonymous' );
        try
        {
            self::set( 'permissions', $group->permissions );
        }
        catch ( Exception $e )
        {
            ezcLog::getInstance()->log( 'Failed to initialize permissions: ' . $e->getMessage(), ezcLog::NOTICE );
            self::set( 'permissions', array() );
        }
    }

    /**
     * Login a user in the current project
     *
     * @param arbitModelUser $user
     * @return bool
     */
    public static function login( arbitModelUser $user )
    {
        if ( $user->valid !== '1' )
        {
            // Only let users log in, which are marked as valid
            return false;
        }

        // Prevent from session fixation
        self::regenerateId();

        // Emit signal about logged in user
        arbitSignalSlot::emit(
            'coreLoginUser',
            new arbitCoreLoginUserStruct( $user )
        );

        // Store important user data directly in session
        self::set( 'login',       $user->_id );
        self::set( 'permissions', $user->privileges );
        self::set( 'settings',    $user->settings ? $user->settings : array() );

        // Special permission handling for administrators.
        $conf = arbitBackendIniConfigurationManager::getProjectConfiguration( self::$project );
        if ( in_array( $user->_id, $conf->administrators ) )
        {
            $privileges = array();
            foreach ( arbitModuleManager::getPermissions() as $module => $permissions )
            {
                $privileges = array_merge(
                    $privileges,
                    array_keys( $permissions )
                );
            }

            // Assign ALL available permissions to administrators
            self::set( 'permissions', $privileges );
        }

        return true;
    }

    /**
     * Logout a user in the current project
     *
     * @return void
     */
    public static function logout()
    {
        self::resetSession();
        self::regenerateId();
    }

    /**
     * Generate new one time token
     *
     * Generate a new one-time token, which can be used by all forms diuring
     * the current request. Ensures that not too many one-time tokens are
     * stored in the session.
     *
     * This might break submitting forms which are kept open in browser, while
     * surfing the website in another widow / tab.
     *
     * @return void
     */
    protected static function generateFormToken()
    {
        // Store at maximum 100 tokens in session. Otherwise the session file
        // might get too big, especially for crawlers.
        $tokens = self::get( 'tokens' );
        if ( count( $tokens ) > 100 )
        {
            $tokens = array_slice( $tokens, -99 );
        }

        // Append several different seeds for pseudo random numbers, so that
        // the from verification key is hard to predict from outside.
        $tokens[] = $token = md5(
            microtime() .
            uniqid( mt_rand(), true ) .
            implode( '', fstat( fopen( __FILE__, 'r' ) ) )
        );
        self::set( 'tokens', $tokens );
    }

    /**
     * Get one-time form token
     *
     * Generate a one-time token for input forms, so that they cannot be
     * resubmitted. The returned "random" token is stored in the session and
     * can only be used one time to validate form contents against it.
     *
     * @return string
     */
    public static function getFormToken()
    {
        $tokens = self::get( 'tokens' );
        return end( $tokens );
    }

    /**
     * Check one-time token
     *
     * Check if the given one-time token is valid and return the validation
     * state. Valid tokens will be removed from the list, so that they may not
     * be reused, and even a second call to this function will not return true
     * any more.
     *
     * @param string $token
     * @return bool
     */
    public static function checkToken( $token )
    {
        $tokens = self::get( 'tokens' );
        if ( ( $key = array_search( $token, $tokens ) ) === false )
        {
            // The token seems invalid, exit with false.
            return false;
        }

        // The token was valid, remove it from the list, so that it can't be
        // reused and return true.
        unset( $tokens[$key] );
        self::set( 'tokens', $tokens );
        return true;
    }

    /**
     * Method to regenerate session ID
     *
     * Performs additional checks, to work in the test environment and may
     * check for strange circumstances.
     *
     * @return void
     */
    protected static function regenerateId()
    {
        self::$backend->regenerateId();
    }

    /**
     * Checks if user has proper rights for some action
     *
     * Checks if the current user has the permissions for the requested
     * priviledge.
     *
     * @param string $permission
     * @return bool
     */
    public static function may( $permission )
    {
        return in_array( $permission, self::get( 'permissions' ), true );
    }

    /**
     * Get session value.
     *
     * @param string $key
     * @return mixed
     */
    public static function get( $key )
    {
        if ( !isset( self::$backend[self::$project][$key] ) )
        {
            // Exception for the key 'login', we return false if not intialized
            // to prevent from potential irritations.
            if ( $key === 'login' )
            {
                return false;
            }

            throw new arbitPropertyException( $key );
        }

        return self::$backend[self::$project][$key];
    }

    /**
     * Set session value.
     *
     * Set the session value and return the set value to enable fluent
     * interfaces.
     *
     * The method may implement value checks for some values in the future.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function set( $key, $value )
    {
        if ( !self::$writeable )
        {
            throw new arbitRuntimeException( 'Session already closed for writing.' );
        }

        $data = self::$backend[self::$project];
        $data[$key] = $value;
        self::$backend[self::$project] = $data;
        return $value;
    }

    /**
     * Remove a session variable
     *
     * Remove a value completey from the session.
     *
     * @param string $key
     * @return void
     */
    public static function remove( $key )
    {
        if ( !self::$writeable )
        {
            throw new arbitRuntimeException( 'Session already closed for writing.' );
        }

        if ( isset( self::$backend[self::$project][$key] ) )
        {
            unset( self::$backend[self::$project][$key] );
        }
    }

    /**
     * Ret global session key value
     *
     * Set the value of a global session key. These session keys are project
     * independant, and should commonly not be used.
     *
     * Returns the set value.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function setGlobal( $key, $value )
    {
        if ( !self::$writeable )
        {
            throw new arbitRuntimeException( 'Session already closed for writing.' );
        }

        $data = isset( self::$backend['__global'] ) ? self::$backend['__global'] : array();
        $data[$key] = $value;
        self::$backend['__global'] = $data;
        return $value;
    }

    /**
     * Get global session key value
     *
     * Get the value of a global session key. These session keys are project
     * independant, and should commonly not be used.
     *
     * @param string $key
     * @return mixed
     */
    public static function getGlobal( $key )
    {
        if ( !isset( self::$backend['__global'] ) ||
             !isset( self::$backend['__global'][$key] ) )
        {
            throw new arbitPropertyException( $key );
        }

        return self::$backend['__global'][$key];
    }
}

