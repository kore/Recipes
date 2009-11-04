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
class arbitCoreModuleController extends arbitController
{
    /**
     * Currently selected project, the controller is called for.
     *
     * @var string
     */
    protected $project;

    /**
     * The currents project configuration
     *
     * @var arbitBackendIniProjectConfiguration
     */
    protected $conf;

    /**
     * Array with authentification class short identifiers for nicer URLs and
     * identification.
     *
     * @var array
     */
    protected $authIds = array(
        'arbitCoreModuleUserPasswordAuthentification' => 'password',
        'arbitCoreModuleUserOpenIDAuthentification'   => 'openid',
    );

    /**
     * Create controlelr from project name
     *
     * @param string $project
     * @return void
     */
    public function __construct( $project )
    {
        $this->project = $project;
        $this->conf = arbitBackendIniConfigurationManager::getProjectConfiguration( $project );
    }

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
        foreach( $this->conf->auth as $name => $class )
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
            $this->getMenu(),
            new arbitViewCoreUserRegistrationModel(
                $authMechanisms,
                $selected
            )
        );

        // Check if there is submitted data and process it
        if ( arbitHttpTools::get( 'submit' ) !== null )
        {
            $authClasses = $this->conf->auth;
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
            $this->getMenu(),
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
            $this->getMenu(),
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
            $this->getMenu(),
            new arbitViewCoreUserAccountModel(
                new arbitViewUserModel( $user )
            )
        );

        // Dispatch to auth mechanism if the respective subaction has been called.
        $authClasses = $this->conf->auth;
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
            $this->getMenu(),
            new arbitViewCoreUserLoginModel(
                $authMechanisms,
                $selected
            )
        );

        // Check if there is submitted data and process it
        if ( arbitHttpTools::get( 'submit' ) !== null )
        {
            $authClasses = $this->conf->auth;
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
     * About view
     *
     * @param arbitRequest $request
     * @return arbitViewModuleModel
     */
    public function about( arbitRequest $request )
    {
        $version = '$Revision: 1236 $';
        if ( preg_match( '(\\$Revision:\\s+(?P<revision>\\d+)\\s*\\$)', $version, $match ) )
        {
            $version = '0.0.' . $match['revision'] . '-svn';
        }

        $authors = trim( preg_replace( '(^(?!- ).*)m', '', file_get_contents( ARBIT_BASE . 'AUTHORS' ) ) );
        $license = trim( file_get_contents( ARBIT_BASE . 'LICENSE' ) );

        return new arbitViewModuleModel(
            $request->action,
            $this->getMenu(),
            new arbitViewCoreAboutModel(
                $version,
                $authors,
                $license
            )
        );
    }

    /**
     * Move element down in array
     *
     * MMove the elment specified by its key one step down in the given array
     * and return the resulting array.
     *
     * @param array $array
     * @param mixed $moveKey
     * @return array
     */
    protected function moveDownInArray( $array, $moveKey )
    {
        $newArray = array();
        $toMove = null;
        foreach ( $array as $key => $value )
        {
            if ( $key === $moveKey )
            {
                $toMove = array( $key, $value );
            }
            else
            {
                $newArray[$key] = $value;
                if ( $toMove !== null )
                {
                    list( $key, $value ) = $toMove;
                    $newArray[$key] = $value;
                    $toMove = null;
                }
            }
        }

        // If this was the last version, just append it agaain at the end.
        if ( $toMove !== null )
        {
            list( $key, $value ) = $toMove;
            $newArray[$key] = $value;
        }

        return $newArray;
    }

    /**
     * Find key in array and add new child
     *
     * Find the specified key in the given array and add a new child array with
     * the specified key in $add.
     *
     * @param array $array
     * @param string $key
     * @param string $add
     * @return array
     */
    protected function findAndAdd( array $array, $key, $add )
    {
        foreach ( $array as $k => $v )
        {
            if ( $k === $key )
            {
                // We found our key -> success
                $array[$k][$add] = array();
                return $array;
            }
            else
            {
                // Recurse
                $array[$k] = $this->findAndAdd( $array[$k], $key, $add );
            }
        }

        return $array;
    }

    /**
     * Find key in array and remove subtree
     *
     * Find the specified key in the given array and remove the complete
     * associated subtree.
     *
     * @param array $array
     * @param string $key
     * @return array
     */
    protected function findAndRemove( array $array, $key )
    {
        foreach ( $array as $k => $v )
        {
            if ( $k === $key )
            {
                // We found our key -> success
                unset( $array[$k] );
                return $array;
            }
            else
            {
                // Recurse
                $array[$k] = $this->findAndRemove( $array[$k], $key );
            }
        }

        return $array;
    }

    /**
     * Manage and configure current project
     *
     * Management of the current project, which means, that the configuration
     * options are displayed and the list of available versions may be
     * modified.
     *
     * This action methods is quite lon because it handles lots of different
     * request types, which are operations on the project configuration data.
     * This could be split up in several views.
     *
     * @param arbitRequest $request
     * @return arbitViewModuleModel
     */
    public function project( arbitRequest $request )
    {
        $project = new arbitModelProject();

        $versionUpdate = false;
        $componentUpdate = false;

        if ( ( ( $remove = arbitHttpTools::get( 'delete' ) ) !== null ) &&
               arbitSession::may( 'core_versions_edit' ) )
        {
            // Removing of a existing version has been requested
            $versions = $project->versions;
            unset( $versions[$remove] );
            $project->versions = $versions;
            $project->storeChanges();
            $versionUpdate = true;
        }

        if ( ( ( $move = arbitHttpTools::get( 'up' ) ) !== null ) &&
               arbitSession::may( 'core_versions_edit' ) )
        {
            // Moving up an existing version in the version order has been
            // requested.
            $project->versions = array_reverse(
                $this->moveDownInArray(
                    array_reverse( $project->versions ),
                    $move
                )
            );
            $project->storeChanges();
            $versionUpdate = true;
        }

        if ( ( ( $move = arbitHttpTools::get( 'down' ) ) !== null ) &&
               arbitSession::may( 'core_versions_edit' ) )
        {
            // Moving down an existing version in the version order has been
            // requested.
            $project->versions = $this->moveDownInArray(
                $project->versions,
                $move
            );
            $project->storeChanges();
        }

        if ( ( arbitHttpTools::get( 'create_version' ) !== null ) &&
               arbitSession::may( 'core_versions_edit' ) )
        {
            // Add a new version of its secified precedessor.
            $versions = $project->versions;
            if ( ( $before = arbitHttpTools::get( 'before' ) ) === '-1' )
            {
                // Just append version, if before equals the special string
                // '-1'
                $versions[arbitHttpTools::get( 'version' )] = 1;
            }
            else
            {
                $newVersions = array();
                foreach ( $versions as $version => $state )
                {
                    if ( $before === $version )
                    {
                        $newVersions[arbitHttpTools::get( 'version' )] = 1;
                    }
                    $newVersions[$version] = $state;
                }
                $versions = $newVersions;
            }

            // Store modifications
            $project->versions = $versions;
            $project->storeChanges();
            $versionUpdate = true;
        }

        if ( ( arbitHttpTools::get( 'change_version_state' ) !== null ) &&
               arbitSession::may( 'core_versions_edit' ) )
        {
            // Add a new version of its secified precedessor.
            $versions = $project->versions;

            $states = arbitHttpTools::get( 'state', arbitHttpTools::TYPE_ARRAY );
            foreach ( $states as $version => $state )
            {
                if ( isset( $versions[$version] ) )
                {
                    $versions[$version] = (int) $state;
                }
            }

            // Store modifications
            $project->versions = $versions;
            $project->storeChanges();
            $versionUpdate = true;
        }

        if ( ( arbitHttpTools::get( 'create_component' ) !== null ) &&
               arbitSession::may( 'core_components_edit' ) )
        {
            // Add a new component under the specified root
            $components = $project->components;

            if ( ( $parent = arbitHttpTools::get( 'parent' ) ) === '0' )
            {
                // Add component as a new root component.
                $components[arbitHttpTools::get( 'component' )] = array();
            }
            else
            {
                $components = $this->findAndAdd(
                    $components,
                    $parent,
                    arbitHttpTools::get( 'component' )
                );
            }

            // Store modifications
            $project->components = $components;
            $project->storeChanges();
            $componentUpdate = true;
        }

        if ( ( ( $remove = arbitHttpTools::get( 'delete_component' ) ) !== null ) &&
               arbitSession::may( 'core_components_edit' ) )
        {
            // Removing of a existing component has been requested
            $project->components = $this->findAndRemove(
                $project->components,
                $remove
            );
            $project->storeChanges();
            $componentUpdate = true;
        }

        // Send signals for updated data
        if ( $versionUpdate )
        {
            arbitSignalSlot::emit(
                'coreProjectVersionsUpdate',
                new arbitCoreProjectVersionsUpdateStruct( $this->project, $project->versions )
            );
        }

        if ( $componentUpdate )
        {
            arbitSignalSlot::emit(
                'coreProjectComponentsUpdate',
                new arbitCoreProjectComponentsUpdateStruct( $this->project, $project->components )
            );
        }

        $configuration = arbitBackendIniConfigurationManager::getProjectConfiguration( $request->controller );

        return new arbitViewModuleModel(
            $request->action,
            $this->getMenu(),
            new arbitViewCoreProjectModel(
                new arbitViewProjectConfigurationModel( $configuration ),
                new arbitViewProjectModel( $project ),
                $this->getProjectAdministrators( $configuration->administrators )
            )
        );
    }

    /**
     * Manage users
     *
     * Manage user group associations.
     *
     * @param arbitRequest $request
     * @return arbitViewModuleModel
     */
    public function user( arbitRequest $request )
    {
        if ( ( arbitHttpTools::get( 'store_permissions' ) !== null ) &&
               arbitSession::may( 'core_users_manage' ) )
        {
            $permissions = arbitHttpTools::get( 'permission', arbitHttpTools::TYPE_ARRAY );
            $groups = arbitModelGroup::fetchAll();

            foreach ( $groups as $group )
            {
                // There are no assignements for some groups, because their
                // users are not editable, so we skip groups, for which we do
                // not have any data.
                if ( !isset( $permissions[$group->_id] ) )
                {
                    continue;
                }

                $groupUsers = array();
                foreach ( $permissions[$group->_id] as $user => $v )
                {
                    $groupUsers[] = new arbitModelUser( $user );
                }
                $group->users = $groupUsers;
                $group->storeChanges();
            }
        }

        return new arbitViewModuleModel(
            $request->action,
            $this->getMenu(),
            new arbitViewCoreUserModel(
                arbitModelUser::fetchAll(),
                arbitModelGroup::fetchAll()
            )
        );
    }

    /**
     * Manage users
     *
     * Manage user validation states and user group associations.
     *
     * @param arbitRequest $request
     * @return arbitViewModuleModel
     */
    public function accept( arbitRequest $request )
    {
        if ( ( arbitHttpTools::get( 'store' ) !== null ) &&
               arbitSession::may( 'core_users_accept' ) )
        {
            $user = new arbitModelUser( arbitHttpTools::get( 'user' ) );
            $user->valid = arbitHttpTools::get( 'state' );

            // Set a new random confitrmation state
            if ( $user->valid === '2' )
            {
                $user->valid = md5( microtime() );
            }

            $user->storeChanges();
        }

        return new arbitViewModuleModel(
            $request->action,
            $this->getMenu(),
            new arbitViewCoreUserAcceptModel(
                arbitModelUser::fetchAll()
            )
        );
    }

    /**
     * Manage group permissions
     *
     * Interface for managing group permissions
     *
     * @param arbitRequest $request
     * @return arbitViewModuleModel
     */
    public function permissions( arbitRequest $request )
    {
        if ( ( arbitHttpTools::get( 'create_group' ) !== null ) &&
               arbitSession::may( 'core_groups_create' ) )
        {
            $group = new arbitModelGroup();
            $group->name        = arbitHttpTools::get( 'name' );
            $group->description = arbitHttpTools::get( 'description' );
            $group->permissions = array();
            $group->create();
            $group->storeChanges();

            // Emit signal about new group
            arbitSignalSlot::emit(
                'coreNewGroup',
                new arbitCoreNewGroupStruct( $group )
            );
        }

        if ( ( arbitHttpTools::get( 'store_permissions' ) !== null ) &&
               arbitSession::may( 'core_groups_edit' ) )
        {
            $permissions = arbitHttpTools::get( 'permission', arbitHttpTools::TYPE_ARRAY );
            $groups = arbitModelGroup::fetchAll();

            foreach ( $groups as $group )
            {
                if ( !isset( $permissions[$group->_id] ) )
                {
                    continue;
                }

                $groupPermissions = array();
                foreach ( $permissions[$group->_id] as $permission => $v )
                {
                    // @TODO: Check permission value against whitelist of
                    // existing permissions
                    $groupPermissions[] = $permission;
                }
                $group->permissions = $groupPermissions;
                $group->storeChanges();
            }
        }

        return new arbitViewModuleModel(
            $request->action,
            $this->getMenu(),
            new arbitViewCorePermissionsModel(
                arbitModelGroup::fetchAll(),
                arbitModuleManager::getPermissions()
            )
        );
    }

    /**
     * Dummy action
     *
     * @param arbitRequest $request
     * @return arbitViewModuleModel
     */
    public function index( arbitRequest $request )
    {
        return $this->about( $request );
    }

    /**
     * Return the menu for the core controller
     *
     * @return array
     */
    protected function getMenu()
    {
        return array(
            'Users'                 => 'accept',
            'User Groups'           => 'user',
            'User Permissions'      => 'permissions',
            'Project configuration' => 'project',
            'About'                 => 'about',
        );
    }

    /**
     * Creates a view model with all adminstrator users for the current project.
     *
     * @param array $administrators
     * @return arbitViewCoreUserModel
     */
    protected function getProjectAdministrators( array $administrators )
    {
        // Convert admin IDs to user modelsf
        array_walk( $administrators, function ( &$administrator )
            {
                $administrator = new arbitViewUserModel(
                    new arbitModelUser( $administrator )
                );
            }
        );
        return new arbitViewCoreUserModel( $administrators );
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
             !isset( $this->conf->auth[$authId] ) )
        {
            throw new arbitControllerUnknownActionException( $call );
        }

        // Create model for current call and pass to handler for modification
        $model =  new arbitViewModuleModel(
            'core',
            $this->getMenu(),
            new arbitViewCoreUserRegistrationModel(
                $authMechanisms,
                $call
            )
        );

        // Handle request in auth sub class
        $authClass = $this->conf->auth[$authId];
        $auth = new $authClass( 'handleCustom', $parameters[0] );
        return $auth->handleCustom( $parameters[0], $model );
    }
}

