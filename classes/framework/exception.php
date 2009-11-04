<?php
/**
 * arbit exceptions
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
 * Basic arbit exception
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class arbitException extends Exception implements arbitTranslateable
{
    /**
     * Exception message with optional placeholders
     *
     * @var mixed
     */
    protected $rawMessage;

    /**
     * Array with placeholder replacers.
     *
     * @var array
     */
    protected $properties;

    /**
     * Construct exception message
     *
     * Construct exception message of a string with placeholders and the
     * properties array, where the properties are the values, which will
     * replace the placeholders when the exception is echo'd.
     *
     * This is done to make it possible to echo translated error messages.
     *
     * @param string $message
     * @param array $properties
     * @return void
     */
    public function __construct( $message, array $properties )
    {
        $this->rawMessage = $message;
        $this->properties = $properties;

        parent::__construct( $this->buildMessage( $message, $properties ) );
    }

    /**
     * Build exception message
     *
     * Replace all placeholders in exception message. The exception to do so
     * has been "borrowed" from ezcTranslations, as this will used for the
     * translation, so that we are using the exact same replacement strategy.
     *
     * @param string $message
     * @param array $properties
     * @return string
     */
    protected function buildMessage( $message, array $properties )
    {
        return preg_replace( '(%(([A-Za-z][a-z_]*[a-z])|[1-9]))e', '$properties["\\1"]', $message );
    }

    /**
     * Get message
     *
     * Get raw exception message without replaced placeholders
     *
     * @return string
     */
    public function getText()
    {
        return $this->rawMessage;
    }

    /**
     * Get properties
     *
     * Get text properties containing the values, which should replace the
     * placeholders in the message.
     *
     * @return array
     */
    public function getTextValues()
    {
        return $this->properties;
    }
}

/**
 * Basic runtime exception
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitRuntimeException extends arbitException
{
    /**
     * Create runtime exception from arbitrary message.
     *
     * Normally you should use a specialized exception, as they can be cought
     * and translated properly. Runtime exceptions are only for the really
     * serious stuff.
     *
     * @param string $message
     * @return void
     */
    public function __construct( $message )
    {
        parent::__construct(
            'Runtime exception: %message',
            array(
                'message' => $message,
            )
        );
    }
}

/**
 * Basic property exception
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitPropertyException extends arbitException
{
    /**
     * Construct property exception
     *
     * Construct property exception for the property which was tried to access.
     *
     * @param string $property
     * @return void
     */
    public function __construct( $property )
    {
        parent::__construct(
            "No such property '%property'.",
            array(
                'property' => $property,
            )
        );
    }
}

/**
 * Exception thrown when a passed value to a property was invalid.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitPropertyValidationException extends arbitException
{
    /**
     * Create exception from property name and expected value.
     *
     * @param string $property
     * @param string $expected
     */
    public function __construct( $property, $expected )
    {
        parent::__construct(
            "The given value for the model property '%property' is not of type '%expected'.",
            array(
                'property' => $property,
                'expected' => $expected,
            )
        );
    }
}

/**
 * Exception thrown when a readonly property is tried to access by writing.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitPropertyReadOnlyException extends arbitException
{
    /**
     * Create exception from class name
     *
     * @param string $property
     */
    public function __construct( $property )
    {
        parent::__construct(
            "No write access allowed on read only property '%property'.",
            array(
                'property' => $property,
            )
        );
    }
}

/**
 * Invalid signal exception
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitInvalidSignalException extends arbitException
{
    /**
     * Construct exception
     *
     * Construct exception for the signal which has been emitted.
     *
     * @param string $signal
     * @return void
     */
    public function __construct( $signal )
    {
        parent::__construct(
            "Invalid signal '%signal' emitted.",
            array(
                'signal' => $signal,
            )
        );
    }
}

/**
 * Invalid signal name exception
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitInvalidSignalNameException extends arbitException
{
    /**
     * Construct exception
     *
     * Construct exception for the signal and module.
     *
     * @param string $module
     * @param string $signal
     * @return void
     */
    public function __construct( $module, $signal )
    {
        parent::__construct(
            "Invalid signal name '%signal' in module '%module'.",
            array(
                'signal' => $signal,
                'module' => $module,
            )
        );
    }
}

/**
 * Invalid signal data exception
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitInvalidSignalDataException extends arbitException
{
    /**
     * Construct exception
     *
     * Construct exception from signal name and expected data class
     *
     * @param string $signal
     * @param string $expected
     * @return void
     */
    public function __construct( $signal, $expected )
    {
        parent::__construct(
            "Invalid data struct provided for signal '%signal'. Expected data of class '%expected'.",
            array(
                'signal'   => $signal,
                'expected' => $expected,
            )
        );
    }
}

/**
 * Exception throw, when we tried to send a HTTP header, but there has already
 * been some content set to the client.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitHeadersSentException extends arbitException
{
    /**
     * Construct exception
     *
     * Construct exception from header string.
     *
     * @param string $header
     * @return void
     */
    public function __construct( $header )
    {
        parent::__construct(
            "Could not send header '%header', because there has already been content set to the client.",
            array(
                'header'   => $header,
            )
        );
    }
}

/**
 * Exception throw, when a client tries to use a foreign session
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitSessionTakeOverException extends arbitException
{
    /**
     * Construct exception
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(
            "The current sessions belongs to another user.",
            array(
            )
        );
    }
}

/**
 * Exception throw, when a cache is accessed, which is not configured
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitNoSuchCacheException extends arbitException
{
    /**
     * Construct exception from cache name
     *
     * @param string $cache
     * @return void
     */
    public function __construct( $cache )
    {
        parent::__construct(
            "There is no cache with the name '%cache' configured.",
            array(
                'cache'   => $cache,
            )
        );
    }
}

/**
 * Exception throw, when a object should be cached, which is not cacheable.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitItemNotCacheableException extends arbitException
{
    /**
     * Construct exception from item identifier
     *
     * @param string $id
     * @return void
     */
    public function __construct( $id )
    {
        parent::__construct(
            "The cache item '%id' could not be cached. Only scalar values, arrays and arbitCacheable objects can be cached.",
            array(
                'id'   => $id,
            )
        );
    }
}

/**
 * Exception thrown on file upload errors
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitFileUploadErrorException extends arbitException
{
    /**
     * Mapping of file upload error constants to descriptive texts
     *
     * @var array
     */
    protected $fileUploadErrors = array(
        UPLOAD_ERR_INI_SIZE   =>
            'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
        UPLOAD_ERR_FORM_SIZE  =>
            'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
        UPLOAD_ERR_PARTIAL    =>
            'The uploaded file was only partially uploaded.',
        UPLOAD_ERR_NO_FILE    =>
            'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR =>
            'Missing a temporary folder.',
        UPLOAD_ERR_CANT_WRITE =>
            'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION  =>
            'File upload stopped by extension.',
    );

    /**
     * Construct exception from item identifier
     *
     * @param int $errorConstant
     * @return void
     */
    public function __construct( $errorConstant )
    {
        parent::__construct(
            "Error occured during file upload: %error",
            array(
                'error' => $this->fileUploadErrors[$errorConstant],
            )
        );
    }
}

/**
 * Exception thrown, when a PHP error eccured
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitPhpErrorException extends arbitException
{
    /**
     * Array type to name mapping
     *
     * @var array
     */
    protected static $errorType = array(
        E_STRICT                => 'Strict notice',
        E_NOTICE                => 'Notice',
        E_USER_NOTICE           => 'Notice',
        E_WARNING               => 'Warning',
        E_USER_WARNING          => 'Warning',
        E_RECOVERABLE_ERROR     => 'Recoverable error',
        E_USER_ERROR            => 'Error',
        E_ERROR                 => 'Error',
    );

    /**
     * Construct PHP error
     *
     * Construct PHP error from type, name, file, line and backtrace
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @param array $backtrace
     * @return void
     */
    public function __construct( $errno, $errstr, $errfile, $errline, array $backtrace = array() )
    {
        parent::__construct(
            'A PHP error occured: %message',
            array(
                'message' => self::$errorType[$errno] . ': ' . $errstr,
            )
        );

        $this->file = $errfile;
        $this->line = $errline;
        $this->trace = $backtrace;
    }
}

/**
 * Exception throw on parse errors in commit messages
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitCommitParserException extends arbitException
{
    /**
     * Construct exception from message
     *
     * @param string $message
     * @return void
     */
    public function __construct( $message )
    {
        parent::__construct(
            "Parse error in commit message: %message",
            array(
                'message' => $message,
            )
        );
    }
}

