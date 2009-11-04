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
 * @version $Revision: 1331 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Base class for the OpenID user authentication mechanisms.
 *
 * @package Core
 * @version $Revision: 1331 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitCoreModuleUserOpenIDAuthentification extends arbitCoreModuleUserAuthentification
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
    public function register( arbitRequest $request )
    {
        arbitSession::set( 'openid', $id = arbitHttpTools::get( 'openid' ) );
        arbitSession::set( 'openid_registration', true );

        return $this->startOpenIdAuthentification();
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
    public function login( arbitRequest $request )
    {
        arbitSession::set( 'openid', $id = arbitHttpTools::get( 'openid' ) );
        arbitSession::set( 'openid_registration', false );

        return $this->startOpenIdAuthentification();
    }

    /**
     * Start OpenID authentication process
     *
     * Initializes the OpenID authentication process wither with an error, or
     * by forwarding to the OpenID provider. The response is handled inside the
     * handleCustom() method.
     *
     * @return mixed
     */
    protected function startOpenIdAuthentification()
    {
        $credentials = new ezcAuthenticationIdCredentials( arbitSession::get( 'openid' ) );

        // Build request to factory rediraction target URL from
        $currentRequest = arbitSession::getCurrentRequest();
        $request = new arbitHttpRequest();
        $request->controller = $currentRequest->controller;
        $request->action     = 'core';
        $request->subaction  = 'openid';

        $options = new ezcAuthenticationOpenidOptions();
        $options->returnUrl     = $request->serialize( true );
        $options->openidVersion = ezcAuthenticationOpenidFilter::VERSION_2_0;

        $filter = new arbitCoreModuleUserOpenIDAuthentificationFilter( $options );
        if ( arbitSession::get( 'openid_registration' ) == true )
        {
            // only fetch extended data during registration process.
            $filter->registerFetchData( array(
                'fullname',
            ) );
            $filter->registerMandatoryFetchData( array(
                'nickname',
                'email',
            ) );
        }

        $auth = new ezcAuthentication( $credentials );
        $auth->addFilter( $filter );

        try
        {
            // Tries to send a location header to redirect the browser to the
            // repective OpenID provider and always throws an exception
            // afterwards. We catch the exception and just exit.
            $auth->run();
        }
        catch ( ezcAuthenticationOpenidRedirectException $e )
        {
            // A redirect has been performed.
            exit( 0 );
        }
        catch ( ezcAuthenticationOpenidException $e )
        {
            // Create error model from exception
            return array(
                new arbitViewUserMessageModel(
                    'Invalid OpenID provided, reason: %error.',
                    array(
                        'error' => $e->getMessage()
                    )
                )
            );
        }

        return array(
            new arbitViewUserMessageModel( 'Invalid OpenID provided.' ),
        );
    }

    /**
     * Handle custom dispatched request
     *
     * Handle a custom request to the specific auth controller, which is
     * completely interpreted by this implementation.
     *
     * In case of OpenID this means that we can evaluate the challenge response
     * here.
     *
     * @param arbitHttpRequest $request
     * @param arbitViewModuleModel $model
     * @return arbitViewModuleModel
     */
    public function handleCustom( arbitHttpRequest $request, arbitViewModuleModel $model )
    {
        $registration = arbitSession::get( 'openid_registration' );
        arbitSession::remove( 'openid_registration' );

        $credentials = new ezcAuthenticationIdCredentials( arbitSession::get( 'openid' ) );

        $options = new ezcAuthenticationOpenidOptions();
        $options->requestSource = $request->variables;

        $filter = new arbitCoreModuleUserOpenIDAuthentificationFilter( $options );

        // We only want to fetch data on registration
        if ( $registration )
        {
            $filter->registerFetchData( array(
                'fullname',
            ) );
            $filter->registerMandatoryFetchData( array(
                'nickname',
                'email',
            ) );
        }

        $auth = new ezcAuthentication( $credentials );
        $auth->addFilter( $filter );

        if ( $auth->run() !== true )
        {
            // Some error occured
            $result = $auth->getStatus();

            $authErrorMessages = array(
                ezcAuthenticationOpenidFilter::STATUS_SIGNATURE_INCORRECT =>
                    'OpenID provider said the provided identifier was incorrect.',
                ezcAuthenticationOpenidFilter::STATUS_CANCELLED =>
                    'The OpenID authentication was cancelled.',
                ezcAuthenticationOpenidFilter::STATUS_URL_INCORRECT =>
                    'The identifier you provided is invalid.',
            );

            $errors = array();
            foreach ( $auth->getStatus() as $line )
            {
                $errorType = reset( $line );
                $errors[] = new arbitViewUserMessageModel( $authErrorMessages[$errorType] );
            }

            $model->content->errors = $errors;

            return $model;
        }

        // Depending on the session value try to login or register the user
        // after successfull authentification through OpenID
        if ( $registration )
        {
            return $this->registerUser( $request, $model, $filter->fetchData() );
        }
        else
        {
            return $this->loginUser( $request, $model );
        }
    }

    /**
     * Perform user registration
     *
     * @param arbitHttpRequest $request
     * @param arbitViewModuleModel $model
     * @param array $data
     * @return arbitViewModuleModel
     */
    protected function registerUser( arbitHttpRequest $request, arbitViewModuleModel $model, array $data )
    {
        // Ensure a email address has been provided
        if ( empty( $data['email'] ) )
        {
            $model->content->errors = array( new arbitViewUserMessageModel(
                'A valid email address is required for registration.'
            ) );
            return $model;
        }

        // Ensure we got some kind of nickname
        if ( empty( $data['nickname'] ) && empty( $data['fullname'] ) )
        {
            $model->content->errors = array( new arbitViewUserMessageModel(
                'You need to provide either a nickname or a realname.'
            ) );
            return $model;
        }

        // Create and store user model
        $user = new arbitModelUser();
        $user->login = arbitSession::get( 'openid' );
        $user->create();

        // Set additional provided data on user model
        $user->email     = reset( $data['email'] );
        $user->name      = ( empty( $data['fullname'] ) ? reset( $data['nickname'] ) : reset( $data['fullname'] ) );
        $user->auth_type = 'openid';
        return $this->finishRegistration( $request, $user );
    }

    /**
     * Perform user login
     *
     * @param arbitHttpRequest $request
     * @param arbitViewModuleModel $model
     * @return arbitViewModuleModel
     */
    protected function loginUser( arbitHttpRequest $request, arbitViewModuleModel $model )
    {
        // Ensure user already exists in the database
        try
        {
            $user = arbitModelUser::findByLogin( arbitSession::get( 'openid' ) );
        }
        catch ( arbitFacadeNotFoundException $e )
        {
            $model->content->errors = array( new arbitViewUserMessageModel(
                'You need to register before login.'
            ) );
            return $model;
        }

        // Now we can login the user.
        if ( arbitSession::login( $user ) === false )
        {
            $model->content->errors = array( new arbitViewUserMessageModel(
                'Login failed.'
            ) );
            return $model;
        }

        arbitSession::remove( 'openid' );
        return $this->loginRedirect( $request );
    }
}
