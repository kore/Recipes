<?php
/**
 * arbit environment setup
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
 * @version $Revision: 1449 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

// Set constants with apths to the arbit files.
//
// If you want to use a different directory structure you may set these
// constants by yourself in your index.php or similar. These constants will
// only be set to their default values, if not declared elsewhere.
if ( !defined( 'ARBIT_BASE' ) )
{
    define( 'ARBIT_BASE', __DIR__ . '/' );
}

if ( !defined( 'ARBIT_HTDOCS' ) )
{
    define( 'ARBIT_HTDOCS',     ARBIT_BASE . 'htdocs/' );
}

if ( !defined( 'ARBIT_TMP_PATH' ) )
{
    define( 'ARBIT_TMP_PATH',   ARBIT_BASE . 'tmp/' );
}

if ( !defined( 'ARBIT_LOG_PATH' ) )
{
    define( 'ARBIT_LOG_PATH',   ARBIT_BASE . 'var/log/' );
}

if ( !defined( 'ARBIT_CACHE_PATH' ) )
{
    define( 'ARBIT_CACHE_PATH', ARBIT_BASE . 'var/cache/' );
}

if ( !defined( 'ARBIT_STORAGE_PATH' ) )
{
    define( 'ARBIT_STORAGE_PATH', ARBIT_BASE . 'var/lib/' );
}

if ( !defined( 'ARBIT_CONFIG' ) )
{
    define( 'ARBIT_CONFIG',     ARBIT_BASE . 'config/' );
}

// Default to debugging switched off, if not set to something else before.
if ( !defined( 'ARBIT_DEBUG' ) )
{
    define( 'ARBIT_DEBUG',      false );
}

// Always required for autoloading
require ARBIT_BASE . 'classes/framework/base.php';

// Required just for the fucking Zend Framework, because they insist on using
// require_once statements in the class files causing a shitload of additional
// unnecessary stat calls. Also the reason because we need to violate our
// directory naming scheme there.
set_include_path( ARBIT_BASE . 'libraries/' . PATH_SEPARATOR . get_include_path() );

/**
 * arbit autoload mechanism
 *
 * arbit autoload mechanism which tries to find the requested class in its
 * own autoload array, and then may dispatch to autoload mechanism from other
 * libraries.
 *
 * Do not change the order of inclusions and checks. This may break the
 * complete application.
 *
 * @param string $class
 * @return void
 */
function __autoload( $class )
{
    // First check if it is a arbit class, the use our autoloading mechanism
    if ( ( strpos( $class, 'arbit' ) === 0 ) &&
         arbitFrameworkBase::autoload( $class ) )
    {
        return true;
    }

    // Try the library autoload mechanism otherwise.
    return arbitFrameworkBase::libraryAutoload( $class );
}

// Set global error handler which converts all PHP errors to exceptions, so
// that ew get a backtrace for all errors, and they will be properly cought by
// the main execution file.
set_error_handler( 'arbitErrorHandler' );

// Populate debug mode setting to embedded classes
ezcBase::setRunMode( ARBIT_DEBUG ? ezcBase::MODE_DEVELOPMENT : ezcBase::MODE_PRODUCTION );

/**
 * Convert PHP errors to exceptions
 *
 * Convert PHP errors to exceptions
 *
 * @throws arbitPhpErrorException
 *
 * @param int $errno
 * @param string $errstr
 * @param string $errfile
 * @param int $errline
 * @access public
 * @return void
 */
function arbitErrorHandler( $errno, $errstr, $errfile, $errline )
{
    // Get backtrace for error message
    $backtrace = debug_backtrace();

    // Skip intentionally silenced error messages with @.
    //
    // This may give false positives for setups where the error reporting is
    // set to 0 in the php configuration, but people who do that are irrelevant
    // by design.
    if ( error_reporting() === 0 )
    {
        return false;
    }

    // Always log all PHP errors, even they might not be reported to
    // the inteface
    ezcLog::getInstance()->log(
        isset( $backtrace[1]['file'] ) ? "$errstr in {$backtrace[1]['file']} +{$backtrace[1]['line']}." : $errstr,
        ezcLog::ERROR,
        array(
            'source'   => 'PHP',
            'category' => $errno,
        )
    );

    // Display all errors in debug mode, but only errors and warnings in
    // production environments.
    if ( ARBIT_DEBUG ||
         ( $errno & ( E_ERROR | E_WARNING ) ) )
    {
        throw new arbitPhpErrorException(
          $errno,
          $errstr,
          $errfile,
          $errline,
          $backtrace
        );
    }
}

