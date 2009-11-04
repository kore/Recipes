<?php
/**
 * arbit http tools class
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
 * Arbit HTTP tool class
 *
 * Provides basic methods to interface with the request handling environment
 * offered by PHP, implementing sanity checking, and a abstraction layer to be
 * more setup independant.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitHttpTools
{
    /**
     * Default values for commonly user server variables, when it makes sense
     * to supply one.
     *
     * This array is provided for the case, that some server variables are not
     * set in some special environment.
     *
     * @var array
     */
    protected static $serverDefaulValues = array(
        'HTTP_HOST'       => 'arbit',
        'HTTP_USER_AGENT' => 'N/A',
        'REMOTE_ADDR'     => '0.0.0.0',
        'REQUEST_METHOD'  => 'GET',
        'REQUEST_URI'     => '/',
        'SERVER_PROTOCOL' => 'HTTP/1.0',
    );

    /**
     * Variable indicating if the one-time token for the submitted form has
     * been OK, or still needs to be checked. Either boolean, indication the
     * check result, or null, if the check did not yet happen.
     *
     * @var mixed
     */
    protected static $formOk = null;

    /**
     * Get raw value, passed in by the browser. Should not be necessary to use
     * normally.
     */
    const RAW          = 'arbitHttpNoConverter';

    /**
     * Get value passed in by the browser as an array. If no value has been
     * provided, an empty array is returned.
     */
    const TYPE_ARRAY   = 'arbitHttpArrayConverter';

    /**
     * Get value passed in by the browser as an UTF-8 string. The charset and
     * encoding conversions will be handled transparently.
     */
    const TYPE_STRING  = 'arbitHttpStringConverter';

    /**
     * Get value passed in by the browser as a float number.
     */
    const TYPE_NUMERIC = 'arbitHttpNumberConverter';

    /**
     * Unquote autoescaped values
     *
     * If magic_quotes_gpc is set, all values are pseudo-escaped with useless
     * slashes. We remove them, if set, or just keep the value. You WANT to
     * disable magic_quotes_gpc in your global php.ini.
     *
     * @param string $value
     * @return string
     * /
    protected static function unquote( $value )
    {
        if ( get_magic_quotes_gpc() )
        {
            return stripslashes( $value );
        }

        return $value;
    } // */

    /**
     * Return server environment variable value
     *
     * Return (unquoted) value from the superglobal $_SERVER array. If the
     * value is not available, return the default value. If there isn't a
     * default value either, null will be returned.
     *
     * @param string $key
     * @return string
     */
    public static function serverVariable( $key )
    {
        // Just return server variable, if set
        if ( isset( $_SERVER ) && isset( $_SERVER[$key] ) )
        {
            return $_SERVER[$key];
        }

        // Return our default value, if set otherwise
        if ( isset( self::$serverDefaulValues[$key] ) )
        {
            return self::$serverDefaulValues[$key];
        }

        // Default to null
        return null;
    }

    /**
     * Set cookie
     *
     * Set a cookie with the given name and value for the currently
     * used host.
     *
     * A path is mandatory to specify the current root of the
     * project.
     *
     * @param string $name
     * @param string $value
     * @param string $path
     * @param int $time
     * @param bool $httpOnly
     * @return void
     */
    public static function setCookie( $name, $value, $path, $time = 0, $httpOnly = true )
    {
        if ( headers_sent() )
        {
            throw new arbitHeadersSentException( "Cookie: $name" );
        }

        setcookie(
            $name, $value,
            time() + $time,
            $path,
            null, // self::serverVariable( 'HTTP_HOST' ),
            false, // HTTPS only
            $httpOnly
        );
    }

    /**
     * Get a request variable.
     *
     * Get POST and COOKIE variables from the current request context. GET
     * variables can already be found in the arbit request struct, so that they
     * cannot be fetched using this method.
     *
     * You may specify the type of the variable you want to receive by the
     * optional second parameter. It defaults to string, which means that no
     * modifications are applied to the request value.
     *
     * If the variable is not available in the current request, or the contents
     * from a form were requested, and the one-time token wasn't valid anymore
     * the method will return null.
     *
     * @param string $name
     * @param int $type
     * @return mixed
     */
    public static function get( $name, $type = self::TYPE_STRING )
    {
        // Default to POST as a source for request variables.
        if ( !isset( $_POST['_arbit_form_token'] ) )
        {
            // Missing form validation token, just also return null, even
            // this may be hard to debug.
            ezcLog::getInstance()->log( 'No form token available', ezcLog::NOTICE );
            return null;
        }

        // Check if the token is valid, if has not been done yet during the
        // request response progress.
        if ( self::$formOk === null )
        {
            self::$formOk = arbitSession::checkToken( $_POST['_arbit_form_token'] );
        }

        // If the token is / was not valid, we do not return any form
        // contents, to mimic a behavious like an unsubmitted form.
        if ( self::$formOk !== true )
        {
            ezcLog::getInstance()->log( 'Invalid form token.', ezcLog::WARNING );
            return null;
        }

        // Get value, and default to null, if not set
        return !isset( $_POST[$name] ) ? $type::defaultValue() : $type::convert( $_POST[$name] );
    }

    /**
     * Get files for given identifier
     *
     * Get all files, which are sent by the client with the given identifier.
     *
     * @param string $name
     * @return array
     */
    public static function getFiles( $name )
    {
        if ( !isset( $_FILES[$name] ) )
        {
            return array();
        }

        $files      = array();
        $properties = array(
            'name',
            'type',
            'tmp_name',
            'error',
        );

        // Restructure files array into a usable state.
        foreach ( $properties as $property )
        {
            if ( is_array( $_FILES[$name][$property] ) )
            {
                foreach ( $_FILES[$name][$property] as $nr => $value )
                {
                    $files[$nr][$property] = $value;
                }
            }
            else
            {
                $files[0][$property] = $_FILES[$name][$property];
            }
        }

        // Check files for upload errors
        foreach ( $files as $fileData )
        {
            if ( $fileData['error'] !==  UPLOAD_ERR_OK )
            {
                throw new arbitFileUploadErrorException( $fileData['error'] );
            }
        }

        // Move uploaded files into temp directory and return list of all
        // files.
        $fileList = array();
        foreach ( $files as $nr => $fileData )
        {
            $name            = tempnam( ARBIT_TMP_PATH, 'uploaded_' );
            $fileList[$name] = array(
                'type'      => $fileData['type'],
                'extension' => preg_replace( '(^.*?((?:\\.[a-zA-Z0-9]+)*)$)', '\\1', $fileData['name'] ),
            );
            move_uploaded_file( $fileData['tmp_name'], $name );
        }

        return $fileList;
    }

    /**
     * Send header
     *
     * Send HTTP header, if this is still possible, and throw a exception
     * otherwise.
     *
     * The exception may be cought in test cases to ensure the correct header
     * has been sent.
     *
     * @param string $string
     * @return void
     */
    public static function header( $string )
    {
        if ( headers_sent() )
        {
            throw new arbitHeadersSentException( $string );
        }

        header( $string );
    }

    /**
     * Is this a HTTP request?
     *
     * Returns true, if this is a HTTP request, and headers may be sent. This
     * is required, since the PHP session functions try to send headers
     * themselves and does not allow us to dispatch the sending of headers into
     * the proper output writers.
     *
     * @return bool
     */
    public static function isHttpRequest()
    {
        return isset( $_SERVER['HTTP_HOST'] );
    }

    /**
     * Send HTTP error
     *
     * Send a HTTP error of the provided code.
     *
     * @param int $code
     * @return void
     */
    public static function error( $code )
    {
        // Error codes used in the application
        $errors = array(
            401 =>'Unauthorized',
            403 =>'Forbidden',
            404 =>'Not Found',
            500 =>'Internal Server Error',
        );

        // Fallback to "Internal Server Error" for unknown error codes.
        if ( !isset( $errors[$code] ) )
        {
            $code = 500;
        }

        // Send header with correct protocol.
        self::header( self::serverVariable( 'SERVER_PROTOCOL' ) . " $code " . $errors[$code] );
    }
}

