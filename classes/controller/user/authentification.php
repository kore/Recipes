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
 * Base interface for user authentication mechanisms.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class arbitCoreModuleUserAuthentification extends arbitController
{
    /**
     * Register user
     *
     * Tries to register user with the data provided in the request.
     *
     * If the registration request is valid the user status in the session is
     * chenged and the method will return true.
     *
     * If the registration process fails, an array with errors is returned
     * associated with the respective input field.
     *
     * @param arbitRequest $request
     * @return mixed
     */
    abstract public function register( arbitRequest $request );

    /**
     * Finish registration process for user
     *
     * Finish the registration process for the given user, by assigning the
     * validation key and perform other necssary actions.
     *
     * @param arbitRequest $request
     * @param arbitModelUser $user
     * @return void
     */
    public function finishRegistration( arbitRequest $request, arbitModelUser $user )
    {
        // Set validation key on user object and store it.
        $user->valid     = $this->getValidationKey();
        $user->storeChanges();

        // Emit signal about new user
        arbitSignalSlot::emit(
            'coreNewUser',
            new arbitCoreNewUserStruct( $user )
        );

        // Assign user to default group for all registered users
        try
        {
            $group   = new arbitModelGroup( 'group-users' );
            $users   = $group->users;
            $users[] = $user;
        }
        catch ( arbitFacadeNotFoundException $e )
        {
            // If the group does not exists, recreate it.
            $group = new arbitModelGroup();
            $group->name = 'Users';
            $group->description = 'Default group for all registered users.';
            $group->create();

            // Default to all users in database.
            $users = arbitModelUser::fetchAll();
        }

        // Add newly registered user to default user group
        $group->users = $users;
        $group->storeChanges();

        // Redirect to registration success overview
        $redirect = clone $request;
        $redirect->action    = 'core';
        $redirect->subaction = 'registered';
        $redirect->path      = '/' . $user->_id;
        return new ezcMvcInternalRedirect( $redirect );
    }

    /**
     * Log in user
     *
     * Tries to log in user with the data provided in the request.
     *
     * If the log in request is valid the user status in the session is changed
     * and the method will return true.
     *
     * If the registration process fails, an array with errors is returned
     * associated with the respective input field.
     *
     * @param arbitRequest $request
     * @return mixed
     */
    abstract public function login( arbitRequest $request );

    /**
     * Redirect lgged in user
     *
     * Perform a redirect for user which just logged in. Either back to the
     * site where the log in has been requested or to the project overview
     * page.
     *
     * @param arbitRequest $request
     * @return void
     */
    protected function loginRedirect( arbitRequest $request )
    {
        if ( arbitHttpTools::get( 'keepme' ) )
        {
            // Set persitent cookie for the user. If this cookie is
            // captured, this *is* a security risk, but users should
            // actually already be aware of that.
            $user  = new arbitModelUser( arbitSession::get( 'login' ) );
            $token = $user->getPersistenceToken();

            arbitHttpTools::setCookie(
                'arbit_persistent_login',
                $user->_id . ':' . $token,
                $request->root . '/' . $request->controller . '/',
                31 * 24 * 60 * 60
            );
        }

        // Try to redirect the user to a site, which requested the login.
        try
        {
            $url = arbitSession::get( 'login_redirect' );
            arbitSession::remove( 'login_redirect' );
            arbitHttpTools::header( 'Location: ' . $url );
            exit( 0 );
        }
        catch ( arbitPropertyException $e )
        {
            // otherwise just redirect to some default URL
            $redirect = clone $request;
            $redirect->action    = 'index';
            $redirect->subaction = 'index';
            $redirect->path      = null;
            return new ezcMvcInternalRedirect( $redirect );
        }
    }

    /**
     * Get user validation key
     *
     * The validation key defines the state of the user after registration.
     * Depending on the configuration this should return one of the following
     * values.
     *
     * - '0': User account disabled
     * - '1': User account enabled
     * - 'random string': User validation key
     *
     * If set to a random string the user will receive a mail with the key and
     * can enable the account himself.
     *
     * @return string
     */
    protected function getValidationKey()
    {
        // @TODO: If no mail validation is required, we could set it directly
        // to '1' (activated), and if no user registration is allowed at all,
        // or each registration request should be administrated, we could set
        // it to '0' (deactivated).

        // Append several different seeds for pseudo random numbers, so that
        // the confirmation key is hard to predict from outside.
        return md5(
            microtime() .
            uniqid( mt_rand(), true ) .
            implode( '', fstat( fopen( __FILE__, 'r' ) ) )
        );
    }
}

