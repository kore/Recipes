<?php
/**
 * arbit core controller
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
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Main controller for the arbit project tracker, implementing all user and
 * group related functionality.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitUserController extends arbitController
{
    /**
     * Array with authentification class short identifiers for nicer URLs and
     * identification.
     *
     * @var array
     */
    protected $authIds = array(
        'arbitCoreModuleUserPasswordAuthentification' => 'password',
    );
    
    /**
     * Available auth mechanisms
     * 
     * @var array
     */
    protected $authMechanisms = array(
        'password',
    );

    /**
     * Get list of auth mechanisms
     *
     * Return the list of authentification mechanisms to use, with a shorter
     * indetifier for the internal authentification handlers, so we get easier
     * URLs.
     *
     * @return array
     */
    protected function getAuthMechanisms()
    {
        $mechanisms = array();
        foreach( $this->authMechanisms as $name => $class )
        {
            if ( isset( $this->authIds[$class] ) )
            {
                $mechanisms[$name] = $this->authIds[$class];
            }
            else
            {
                $mechanisms[$name] = $class;
            }
        }

        return $mechanisms;
    }

    /**
     * User registration
     *
     * Display a registration form with the available registration mechanisms
     * and dispatch to the authentification mechanisms implementation on data
     * retrieval for the actual user registration.
     *
     * @param arbitRequest $request
     * @return arbitViewCoreUserRegistrationModel
     */
    public function register( arbitRequest $request )
    {
        // Fetch auth mechanisms to use from project configuration
        $authMechanisms = $this->getAuthMechanisms();

        // Show form for selected auth mechanism in view, after matching
        // against the whitelist of existing auth mechanisms
        if ( isset( $request->path ) &&
             preg_match( '(^/(?P<id>' . implode( '|', array_map( 'preg_quote', $authMechanisms ) ) . '))', $request->path, $match ) )
        {
            $selected = $match['id'];
        }
        else
        {
            $selected = reset( $authMechanisms );
        }

        $model = new arbitViewModuleModel(
            $request->action,
            array(),
            new arbitViewCoreUserRegistrationModel(
                $authMechanisms,
                $selected
            )
        );

        // Check if there is submitted data and process it
        if ( arbitHttpTools::get( 'submit' ) !== null )
        {
            $authClasses = $this->authMechanisms;
            $authClass = $authClasses[array_search( $selected, $authMechanisms )];

            // Let the current authentification class handle the registration,
            // as it is the only one which knows about that stuff.
            $auth = new $authClass( 'register', $request );
            $return = $auth->register( $request );

            // Let redirects directly bubble up
            if ( $return instanceof ezcMvcInternalRedirect )
            {
                return $return;
            }

            if ( is_array( $return ) )
            {
                $model->content->errors = $return;
            }
        }

        return $model;
    }

    /**
     * Successfully registered user.
     *
     * If a user sucessfully registerd in the system this page tells him about
     * his current state and sends an email with its validation code, if
     * necessary.
     *
     * @param arbitRequest $request
     * @return arbitViewModuleModel
     */
    public function registered( arbitRequest $request )
    {
        $userId = substr( $request->path, 1 );
        $user   = new arbitModelUser( $userId );
        $model  = new arbitViewCoreUserRegisteredModel(
            new arbitViewUserModel( $user )
        );

        if ( ( $user->valid !== '0' ) &&
             ( $user->valid !== '1' ) )
        {
            arbitMessenger::send( $model, $user->email );
        }

        // Display something
        return new arbitViewModuleModel(
            $request->action,
            array(),
            $model
        );
    }

    /**
     * Confirm user registration
     *
     * During the registration process may receive a link with a confirmation
     * request. This action processes this confirmation requests.
     *
     * @param arbitRequest $request
     * @return arbitViewModuleModel
     */
    public function confirm( arbitRequest $request )
    {
        list( $unused, $userId, $hash ) = explode( '/', $request->path );
        $user = new arbitModelUser( $userId );

        // Check if the hash matches and update account then.
        if ( ( $hash !== '0' ) &&
             ( $user->valid === $hash ) )
        {
            $user->valid = '1';
            $user->storeChanges();

            // Emit signal about confirmed account
            arbitSignalSlot::emit(
                'coreConfirmedUser',
                new arbitCoreConfirmedUserStruct( $user )
            );
        }

        // Display something
        return new arbitViewModuleModel(
            $request->action,
            array(),
            new arbitViewCoreUserRegisteredModel(
                new arbitViewUserModel( $user )
            )
        );
    }

    /**
     * Personal user account management
     *
     * Manage user account details, change account data and authentification
     * data, if modifyable.
     *
     * @param arbitRequest $request
     * @return arbitViewModuleModel
     */
    public function account( arbitRequest $request )
    {
        // Direcly forward users, which are not logged in to the registration form
        if ( arbitSession::get( 'login' ) === false )
        {
            $redirect = clone $request;
            $redirect->action    = 'core';
            $redirect->subaction = 'register';
            return new ezcMvcInternalRedirect( $redirect );
        }

        $user = new arbitModelUser( arbitSession::get( 'login' ) );
        $model = new arbitViewModuleModel(
            $request->action,
            array(),
            new arbitViewCoreUserAccountModel(
                new arbitViewUserModel( $user )
            )
        );

        // Dispatch to auth mechanism if the respective subaction has been called.
        $authClasses = $this->authMechanisms;
        $authMechanisms = $this->getAuthMechanisms();
        if ( !empty( $request->path ) &&
             ( $authClass = $authClasses[array_search( substr( $request->path, 1 ), $authMechanisms )] ) )
        {
            $auth = new $authClass( 'account', $request );
            return $auth->account( $model, $request );
        }

        if ( ( arbitHttpTools::get( 'account_change' ) !== null ) &&
               // We allow chaning of data only for logged in users
               arbitSession::get( 'login' ) )
        {
            // @TODO: For now we only allow changing the full name, ignore
            // everything else. At least allow changing the mail address some
            // time in the future.
            $user->name = arbitHttpTools::get( 'fullname' );
            $user->storeChanges();

            // Update user displayed in the view model
            $model->content->user = new arbitViewUserModel( $user );
        }

        if ( ( arbitHttpTools::get( 'settings_change' ) !== null ) &&
               // We allow chaning of data only for logged in users
               arbitSession::get( 'login' ) )
        {
            $user->settings = array(
                'date_timezone' => arbitHttpTools::get( 'date_timezone' ),
                'date_format'   => arbitHttpTools::get( 'date_format' ),
            );
            arbitSession::set( 'settings', $user->settings );
            $user->storeChanges();

            // Update user displayed in the view model
            $model->content->user = new arbitViewUserModel( $user );
        }

        return $model;
    }

    /**
     * Log in user
     *
     * Action to perform user login. This is done on the base of the stored
     * user authenfication mechanism, which need to check if the provided login
     * data is valid.
     *
     * @param arbitRequest $request
     * @return arbitViewModuleModel
     */
    public function login( arbitRequest $request )
    {
        // Fetch auth mechanisms to use from project configuration
        $authMechanisms = $this->getAuthMechanisms();

        // Check if we got a referrer, which is not the login form itself, so
        // set it to forward the user back after the login.
        if ( preg_match(
                '(^https?://' . arbitHttpTools::serverVariable( 'HTTP_HOST' ) . '/.+$)',
                $referrer = arbitHttpTools::serverVariable( 'HTTP_REFERER' ) ) &&
             ( strpos( $referrer, 'core/' ) === false ) )
        {
            arbitSession::set( 'login_redirect', $referrer );
        }

        // Show form for selected auth mechanism in view, after matching
        // against the whitelist of existing auth mechanisms
        if ( isset( $request->path ) &&
             preg_match( '(^/(?P<id>' . implode( '|', array_map( 'preg_quote', $authMechanisms ) ) . '))', $request->path, $match ) )
        {
            $selected = $match['id'];
        }
        else
        {
            $selected = reset( $authMechanisms );
        }

        $model = new arbitViewModuleModel(
            $request->action,
            array(),
            new arbitViewCoreUserLoginModel(
                $authMechanisms,
                $selected
            )
        );

        // Check if there is submitted data and process it
        if ( arbitHttpTools::get( 'submit' ) !== null )
        {
            $authClasses = $this->authMechanisms;
            $authClass   = $authClasses[array_search( $selected, $authMechanisms )];

            // Let the current authentification class handle the registration,
            // as it is the only one which knows about that stuff.
            $auth   = new $authClass( 'login', $request );
            $return = $auth->login( $request );

            // Let redirects directly bubble up
            if ( $return instanceof ezcMvcInternalRedirect )
            {
                return $return;
            }

            if ( is_array( $return ) )
            {
                $model->content->errors = $return;
            }
        }

        return $model;
    }

    /**
     * Log out user
     *
     * @param arbitRequest $request
     * @return arbitViewModuleModel
     */
    public function logout( arbitRequest $request )
    {
        // Rmeove persistent login token on explicit logout
        arbitHttpTools::setCookie(
            'arbit_persistent_login', '',
            $request->root . '/' . $request->controller . '/',
            -60
        );

        arbitSession::logout();

        $redirect = clone $request;
        $redirect->controller = 'core';
        $redirect->action     = 'dashboard';
        $redirect->subaction  = 'index';
        return new ezcMvcInternalRedirect( $redirect );
    }

    /**
     * Check for persitent login
     *
     * Check if the user provides a persistent login token. In this case log
     * the user in directly and update the used token.
     *
     * Meant to be run as a request filter.
     *
     * @param arbitRequest $request
     * @return void
     */
    public static function checkPersitentLogin( arbitRequest $request )
    {
        if ( arbitSession::get( 'login' ) )
        {
            // Only perform filter for users who are not already logged in.
            return false;
        }

        if ( !isset( $request->cookies['arbit_persistent_login'] ) )
        {
            ezcLog::getInstance()->log( 'No persistence token available.', ezcLog::INFO );
            return false;
        }

        // Check if token matches required formatting constraints
        if ( !preg_match( '(^(?P<user>[^:]+):(?P<hash>[a-f0-9]{32})$)', $request->cookies['arbit_persistent_login'], $match ) )
        {
            ezcLog::getInstance()->log( 'Persistence token does not match the expected format.', ezcLog::WARNING );
            GOTO cleanupCookie;
        }

        try
        {
            $user = new arbitModelUser( $match['user'] );
            if ( $user->persitenceToken !== $match['hash'] )
            {
                ezcLog::getInstance()->log( 'Persistence token does not match stored user token.', ezcLog::WARNING );
                GOTO cleanupCookie;
            }
        }
        catch ( Exception $e )
        {
            // Exception thrown if referenced user does not exist
            ezcLog::getInstance()->log( 'Persistence token user does not exist.', ezcLog::WARNING );
            GOTO cleanupCookie;
        }

        // Log user in and create new persistence token
        arbitSession::login( $user );
        ezcLog::getInstance()->log( 'Update persistence token.', ezcLog::INFO );
        $token = $user->getPersistenceToken();
        arbitHttpTools::setCookie(
            'arbit_persistent_login',
            $user->_id . ':' . $token,
            $request->root . '/' . $request->controller . '/',
            31 * 24 * 60 * 60
        );
        return true;

        // In case of an errneous persistence token, remove it.
        cleanupCookie:
        ezcLog::getInstance()->log( 'Remove persistence token.', ezcLog::INFO );
        arbitHttpTools::setCookie(
            'arbit_persistent_login', '',
            $request->root . '/' . $request->controller . '/',
            -60
        );
        return false;
    }

    /**
     * Dispatch calls to authentification mechanisms
     *
     * Dispatches all calls where the name of the action equals one of the
     * authentification mechanisms to the respective implementation to make
     * specific implementation possible, like a "Forgot Password" feature which
     * is not required for OpenId.
     *
     * @param string $call
     * @param array $parameters
     * @return arbitViewModuleModel
     */
    public function __call( $call, array $parameters )
    {
        $authMechanisms = $this->getAuthMechanisms();

        // If dynamic call is not a known auth mechanisms just return the
        // common exception.
        if ( !( $authId = array_search( $call, $authMechanisms ) ) ||
             !isset( $this->authMechanisms[$authId] ) )
        {
            throw new arbitControllerUnknownActionException( $call );
        }

        // Create model for current call and pass to handler for modification
        $model =  new arbitViewModuleModel(
            'core',
            array(),
            new arbitViewCoreUserRegistrationModel(
                $authMechanisms,
                $call
            )
        );

        // Handle request in auth sub class
        $authClass = $this->authMechanisms[$authId];
        $auth = new $authClass( 'handleCustom', $parameters[0] );
        return $auth->handleCustom( $parameters[0], $model );
    }
}

