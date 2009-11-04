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
 * Base class for the password user authentication mechanisms.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitCoreModuleUserPasswordAuthentification extends arbitCoreModuleUserAuthentification
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
        $errors = array();

        // Check for the existance of the required parameters
        $required = array(
            'login'      => 'You need to provide a username.',
            'password_0' => 'You need to provide a non empty password.',
            'email'      => 'You need to provide a valid email address.',
        );

        foreach ( $required as $field => $error )
        {
            $data[$field] = arbitHttpTools::get( $field );
            if ( empty( $data[$field] ) )
            {
                $errors[] = new arbitViewUserMessageModel( $error );
            }
        }

        $errors = array_merge(
            $errors,
            $this->checkPassword( $data['password_0'], arbitHttpTools::get( 'password_1' ), $data )
        );

        // If there were any errors, we return telling the user.
        if ( count( $errors ) )
        {
            return $errors;
        }

        // Try to create the user otherwise
        $user = new arbitModelUser();
        $user->login = $data['login'];

        try
        {
            $user->create();
        }
        catch ( arbitFacadeUserExistsException $e )
        {
            return array( $e );
        }

        // User has been created successfully, add the other data.
        $user->email      = $data['email'];
        $user->auth_type  = 'password';
        $user->auth_infos = $this->getPasswordHash( $data['password_0'] );
        return $this->finishRegistration( $request, $user );
    }

    /**
     * Common password checks
     *
     * Performs a list of common checks against the given password, which
     * currently are:
     * - Passwords matche sits repetition
     * - Minimal password length
     * - Password shares not too much text with other provided data
     *
     * More checks could make sense to implement here or in derivating classes
     * like dictonary checks.
     *
     * Returns an aray with validation errors.
     *
     * @param string $password
     * @param string $repetition
     * @param array $data
     * @return array
     */
    protected function checkPassword( $password, $repetition, $data )
    {
        $errors = array();

        // Check that the repeated password matches the original password
        if ( $password !== $repetition )
        {
            $errors[] = new arbitViewUserMessageModel(
                'Your repeated password did not match the new password.'
            );
        }

        // Ensure the password has at minimum 6 characters
        if ( strlen( $password ) < 6 )
        {
            $errors[] = new arbitViewUserMessageModel(
                'Your password is too short.'
            );
        }

        // Ensure the password and user name or email do not share to much text
        if ( ( $this->longestCommonSequence( $password, $data[$match = 'login'] ) > 3 ) ||
             ( $this->longestCommonSequence( $password, $data[$match = 'email'] ) > 3 ) )
        {
            $errors[] = new arbitViewUserMessageModel(
                'Your password has to much text in common with your %field.',
                array(
                    'field' => $match,
                )
            );
        }

        return $errors;
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
        // Ensure user already exists in the database
        try
        {
            $user = arbitModelUser::findByLogin( arbitHttpTools::get( 'login' ) );
        }
        catch ( arbitFacadeNotFoundException $e )
        {
            return array( new arbitViewUserMessageModel(
                'Login failed.'
            ) );
        }

        if ( $this->getPasswordHash( arbitHttpTools::get( 'password' ) ) !== $user->auth_infos )
        {
            return array( new arbitViewUserMessageModel(
                'Login failed.'
            ) );
        }

        // Now we can login the user.
        if ( arbitSession::login( $user ) === false )
        {
            return array( new arbitViewUserMessageModel(
                'Login failed.'
            ) );
        }

        return $this->loginRedirect( $request );
    }

    /**
     * Controller implementation for password changing
     *
     * Performs the requested actions and changes the password.
     *
     * @param arbitViewModuleModel $model
     * @param arbitHttpRequest $request
     * @return arbitViewModel
     */
    public function account( arbitViewModuleModel $model, arbitHttpRequest $request )
    {
        if ( ( arbitHttpTools::get( 'password_change' ) !== null ) &&
             arbitSession::get( 'login' ) )
        {
            $user = new arbitModelUser( arbitSession::get( 'login' ) );

            $errors = $this->checkPassword(
                $password = arbitHttpTools::get( 'password_0' ),
                arbitHttpTools::get( 'password_1' ),
                array(
                    'login' => $user->login,
                    'email' => $user->email,
                )
            );

            if ( $user->auth_infos !== $this->getPasswordHash( arbitHttpTools::get( 'old_password' ) ) )
            {
                $errors[] = new arbitViewUserMessageModel(
                    'The given old password does not match your curent password.'
                );
            }

            if ( count( $errors ) )
            {
                $model->content->errors = $errors;
                return $model;
            }

            $user->auth_infos = $this->getPasswordHash( $password );
            $user->storeChanges();

            $model->content->success = array( new arbitViewUserSuccessModel(
                'Password successfully changed.'
            ) );
        }

        return $model;
    }

    /**
     * Generate a password hash
     *
     * Generates one or more hashes from a password. Must be an injective
     * function.
     *
     * @param string $password
     * @return array
     */
    protected function getPasswordHash( $password )
    {
        return array(
            md5( 'arbit_' . $password ),
            sha1( 'arbit_' . $password ),
        );
    }

    /**
     * longest common sequence implementation
     *
     * Returns the longest common sequence of two strings. may be used to
     * ensure that the password does not have too much in common with other
     * provided data.
     *
     * @param string $password
     * @param string $string
     * @return string
     */
    protected function longestCommonSequence( $password, $string )
    {
        $matrix = array();

        $passwordLength = strlen( $password );
        $stringLength = strlen( $string );

        for ( $i = 0; $i <= $passwordLength; ++$i )
        {
            $matrix[$i][0] = 0;
        }

        for ( $j = 0; $j <= $stringLength; ++$j )
        {
            $matrix[0][$j] = 0;
        }

        $longest = 0;
        for ( $i = 1; $i <= $passwordLength; ++$i )
        {
            for ( $j = 1; $j <= $stringLength; ++$j )
            {
                $matrix[$i][$j] = max(
                    // Differently to the common longestCommonSequence
                    // algorithm we do not reuse the values from the other rows
                    // and columns, because we are only looking for the longest
                    // connected subsequence.
                    0,
                    ( $password[$i - 1] === $string[$j - 1] ? $matrix[$i - 1][$j - 1] + 1 : 0 )
                );

                $longest = max(
                    $longest,
                    $matrix[$i][$j]
                );
            }
        }

        return $longest;
    }
}

