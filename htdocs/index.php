<?php
/**
 * arbit main script
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
 * @version $Revision: 1271 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

// Set this to true to see exceptions and their backtraces. Should ALWAYS be
// false in a production environment.
define( 'ARBIT_DEBUG', true );

if ( ARBIT_DEBUG )
{
    // Perform envirnonmental checks if debug mode is active. Ensure we are on
    // a correct PHP version and required settings are made in the PHP
    // configuration
    include './check.php';
}

if ( !defined( 'ARBIT_BASE' ) )
{
    define( 'ARBIT_BASE', dirname( realpath( __DIR__ ) ) . '/' );
}

try
{
    // Set up autoload environment
    require ARBIT_BASE . 'environment.php';

    // Initialize file log writer
    $log     = ezcLog::getInstance();
    $fileLog = new ezcLogUnixFileWriter( ARBIT_LOG_PATH, 'error.log' );
    $filter  = new ezcLogFilter();
    $filter->severity = ezcLog::WARNING | ezcLog::ERROR | ezcLog::FATAL;
    $log->getMapper()->appendRule(
        new ezcLogFilterRule( $filter, $fileLog, true )
    );

    // Add generic arbit logger for all log messages
    $log->getMapper()->appendRule(
        new ezcLogFilterRule( new ezcLogFilter(), new arbitLogger(), true )
    );

    // Register modules
    $conf = arbitBackendIniConfigurationManager::getMainConfiguration();
    arbitModuleManager::registerModule( 'core' );
    arbitModuleManager::activateModule( 'core' );
    foreach ( $conf->modules as $module )
    {
        arbitModuleManager::registerModule( $module );
    }

    // Dispatch request
    $dispatcher = new ezcMvcConfigurableDispatcher(
        new arbitDispatcherConfiguration()
    );
    $dispatcher->run();
}
catch ( Exception $e )
{
    // We should nearly never reach this stage, because all exceptions should
    // already be handled in the controller and routing manager.
    //
    // If you get some exception here, most probably there is something
    // seriously fucked up with your setup. There is no need for a nicer
    // message here, as this really should not happen.
    try
    {
        // Try to log error, if this fails, ignore the error.
        ezcLog::getInstance()->log( $e->getMessage(), ezcLog::FATAL );
    }
    catch ( Exception $e )
    {
        // Ignore, we can't do anything anymore about this.
    }

    // Return a generic HTTP error, so that noone will cache this response
    arbitHttpTools::error( 500 );
    echo "<h1>Internal Server Error</h1>\n";

    if ( ARBIT_DEBUG )
    {
        // Show exception only in debug mode
        echo "<pre>$e</pre>";
        echo "<pre>", var_dump( arbitLogger::getMessages() ), "</pre>";
    }
}

