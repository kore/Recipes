<?php
/**
 * arbit base class
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
 * @version $Revision: 1262 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

// These classes are requierd on each request
//
// We require the directly, so that opcode caches may inline the code. Do NOT
// reorder these without knowing what you are doing.
require ARBIT_BASE . 'libraries/ezc/base/base.php';
require ARBIT_BASE . 'libraries/ezc/base/struct.php';
require ARBIT_BASE . 'libraries/ezc/base/options.php';
require ARBIT_BASE . 'libraries/ezc/event_log/interfaces/mapper.php';
require ARBIT_BASE . 'libraries/ezc/event_log/log.php';
require ARBIT_BASE . 'libraries/ezc/event_log/mapper/filterset.php';
require ARBIT_BASE . 'libraries/ezc/event_log/context.php';
require ARBIT_BASE . 'libraries/ezc/base/init.php';
require ARBIT_BASE . 'libraries/ezc/event_log/interfaces/writer.php';
require ARBIT_BASE . 'libraries/ezc/event_log/writers/writer_file.php';
require ARBIT_BASE . 'libraries/ezc/event_log/writers/writer_unix_file.php';
require ARBIT_BASE . 'libraries/ezc/event_log/structs/log_filter.php';
require ARBIT_BASE . 'libraries/ezc/event_log/mapper/filter_rule.php';
require ARBIT_BASE . 'classes/framework/logger.php';
require ARBIT_BASE . 'classes/backend/ini/manager.php';
require ARBIT_BASE . 'libraries/ezc/configuration/configuration_manager.php';
require ARBIT_BASE . 'libraries/ezc/base/features.php';
require ARBIT_BASE . 'libraries/ezc/configuration/interfaces/reader.php';
require ARBIT_BASE . 'libraries/ezc/configuration/file_reader.php';
require ARBIT_BASE . 'libraries/ezc/configuration/ini/ini_reader.php';
require ARBIT_BASE . 'classes/backend/ini/base.php';
require ARBIT_BASE . 'classes/backend/ini/main.php';
require ARBIT_BASE . 'libraries/ezc/configuration/ini/ini_parser.php';
require ARBIT_BASE . 'libraries/ezc/configuration/structs/ini_item.php';
require ARBIT_BASE . 'libraries/ezc/configuration/configuration.php';
require ARBIT_BASE . 'classes/module/manager.php';
require ARBIT_BASE . 'classes/module/locator.php';
require ARBIT_BASE . 'classes/framework/cacheable.php';
require ARBIT_BASE . 'classes/framework/decorateable.php';
require ARBIT_BASE . 'classes/framework/translateable.php';
require ARBIT_BASE . 'classes/framework/struct.php';
require ARBIT_BASE . 'classes/module/definition.php';
require ARBIT_BASE . 'classes/framework/signal_slot.php';
require ARBIT_BASE . 'libraries/ezc/mvc/interfaces/dispatcher.php';
require ARBIT_BASE . 'libraries/ezc/mvc/dispatchers/configurable.php';
require ARBIT_BASE . 'libraries/ezc/mvc/interfaces/dispatcher_configuration.php';
require ARBIT_BASE . 'classes/dispatcher.php';
require ARBIT_BASE . 'libraries/ezc/mvc/interfaces/request_parser.php';
require ARBIT_BASE . 'classes/request/parser.php';
require ARBIT_BASE . 'libraries/ezc/mvc/structs/request.php';
require ARBIT_BASE . 'classes/request/base.php';
require ARBIT_BASE . 'classes/request/http.php';
require ARBIT_BASE . 'libraries/ezc/mvc/structs/request_accept.php';
require ARBIT_BASE . 'classes/framework/http_tools.php';
require ARBIT_BASE . 'libraries/ezc/mvc/router.php';
require ARBIT_BASE . 'classes/router/http.php';
require ARBIT_BASE . 'libraries/ezc/mvc/interfaces/route.php';
require ARBIT_BASE . 'classes/router/arbit_route.php';
require ARBIT_BASE . 'classes/framework/cache.php';
require ARBIT_BASE . 'classes/framework/cache_registry.php';
require ARBIT_BASE . 'classes/framework/cache/filesystem.php';
require ARBIT_BASE . 'libraries/ezc/mvc/structs/routing_information.php';
require ARBIT_BASE . 'classes/framework/session.php';
require ARBIT_BASE . 'libraries/ezc/mvc/structs/result.php';
require ARBIT_BASE . 'classes/controller/result.php';
require ARBIT_BASE . 'libraries/ezc/mvc/interfaces/controller.php';
require ARBIT_BASE . 'classes/controller/base.php';
require ARBIT_BASE . 'classes/controller/core.php';
require ARBIT_BASE . 'classes/controller/main.php';
require ARBIT_BASE . 'classes/view/model.php';
require ARBIT_BASE . 'classes/view/model/context/dashboard.php';
require ARBIT_BASE . 'classes/view/model/dashboard_project.php';
require ARBIT_BASE . 'classes/view/model/error.php';
require ARBIT_BASE . 'libraries/ezc/mvc/view.php';
require ARBIT_BASE . 'classes/view/manager.php';
require ARBIT_BASE . 'classes/view/handler.php';
require ARBIT_BASE . 'classes/view/handler/template.php';
require ARBIT_BASE . 'classes/view/handler/xhtml.php';
require ARBIT_BASE . 'libraries/ezc/template/configuration.php';
require ARBIT_BASE . 'libraries/ezc/template/interfaces/output_context.php';
require ARBIT_BASE . 'libraries/ezc/template/contexts/no_context.php';
require ARBIT_BASE . 'libraries/ezc/template/contexts/xhtml_context.php';
require ARBIT_BASE . 'libraries/ezc/template_translation/configuration.php';
require ARBIT_BASE . 'libraries/ezc/translation/interfaces/backend_interface.php';
require ARBIT_BASE . 'libraries/ezc/translation/interfaces/context_read_interface.php';
require ARBIT_BASE . 'libraries/ezc/translation/interfaces/context_write_interface.php';
require ARBIT_BASE . 'libraries/ezc/translation/backends/ts_backend.php';
require ARBIT_BASE . 'libraries/ezc/translation/options/ts_backend.php';
require ARBIT_BASE . 'libraries/ezc/translation/translation_manager.php';
require ARBIT_BASE . 'classes/framework/translation.php';
require ARBIT_BASE . 'libraries/ezc/template/interfaces/custom_function.php';
require ARBIT_BASE . 'classes/view/handler/functions.php';
require ARBIT_BASE . 'classes/view/handler/xhtml/functions.php';
require ARBIT_BASE . 'classes/view/decorator_manager.php';
require ARBIT_BASE . 'libraries/ezc/template/template.php';
require ARBIT_BASE . 'libraries/ezc/template/variable_collection.php';
require ARBIT_BASE . 'libraries/ezc/base/file.php';
require ARBIT_BASE . 'libraries/ezc/template/compiled_code.php';
require ARBIT_BASE . 'libraries/ezc/template_translation/provider.php';
require ARBIT_BASE . 'libraries/ezc/translation/structs/translation_data.php';
require ARBIT_BASE . 'classes/framework/translation_context.php';
require ARBIT_BASE . 'libraries/ezc/translation/translation.php';
require ARBIT_BASE . 'libraries/ezc/mvc/structs/response.php';
require ARBIT_BASE . 'libraries/ezc/mvc/structs/result_content.php';
require ARBIT_BASE . 'libraries/ezc/mvc/interfaces/response_writer.php';
require ARBIT_BASE . 'libraries/ezc/mvc/response_writers/http.php';

/**
 * Arbit framework base class
 *
 * Implements the autoload handling for arbit and the used libraries.
 *
 * @package Core
 * @version $Revision: 1262 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
final class arbitFrameworkBase
{
    /**
     * Core autoload mapping
     *
     * @var array
     */
    protected static $autoloadMapping = null;

    /**
     * Modules autoload mapping
     *
     * @var array
     */
    protected static $moduleAutoloadMapping = null;

    /**
     * Libraries autoload mapping
     *
     * @var array
     */
    protected static $libraryAutoloadMapping = null;

    /**
     * Logfile pointer for autoload logging
     *
     * @var resource
     */
    protected static $logFile;

    /**
     * Log all autoloads to a file in the arbit root
     * 
     * @param string $file 
     * @return void
     */
    protected static function logAutoloads( $file )
    {
        if ( !isset( self::$logFile ) )
        {
            self::$logFile = fopen( ARBIT_BASE . 'autoload_' . microtime( true ) . '.log', 'w' );
        }

        fwrite( self::$logFile, $file . "\n" );
    }

    /**
     * Tries to load an arbit class
     *
     * Tries to find a arbit class in the core and in the various autoload
     * definitions of the registered modules.
     *
     * @param string $class
     * @return bool
     */
    public static function autoload( $class )
    {
        if ( self::$autoloadMapping === null )
        {
            // Define mapping for all arbit classes
            self::$autoloadMapping = include ARBIT_BASE . 'classes/autoload.php';
        }

        // Check if requested class is a arbit class, and include it.
        if ( isset( self::$autoloadMapping[$class] ) )
        {
            // self::logAutoloads( self::$autoloadMapping[$class] );
            require ARBIT_BASE . self::$autoloadMapping[$class];
            return true;
        }

        // Handle autoload for modules
        if ( self::$moduleAutoloadMapping === null )
        {
            self::$moduleAutoloadMapping = arbitModuleManager::getAutoloads();
        }

        // Check if the requested class is a module class, and then include it
        if ( isset( self::$moduleAutoloadMapping[$class] ) )
        {
            // self::logAutoloads( self::$moduleAutoloadMapping[$class] );
            require ARBIT_BASE . self::$moduleAutoloadMapping[$class];
            return true;
        }

        return false;
    }

    /**
     * Tries to autoload a library class
     *
     * Uses the library autoload array to look for library classes.
     *
     * @param string $class
     * @return bool
     */
    public static function libraryAutoload( $class )
    {
        if ( self::$libraryAutoloadMapping === null )
        {
            // Define mapping for all arbit classes
            self::$libraryAutoloadMapping = include ARBIT_BASE . 'libraries/autoload.php';
        }

        // Check if requested class is a phpillow class, and include it.
        if ( isset( self::$libraryAutoloadMapping[$class] ) )
        {
            // self::logAutoloads( self::$libraryAutoloadMapping[$class] );
            return require ARBIT_BASE . self::$libraryAutoloadMapping[$class];
            return true;
        }

        return false;
    }

    /**
     * Clears autoload cache
     *
     * Especialls module autoload definitions are cached in static properties.
     * When a module is loaded after the cache has been created a first time
     * its autoload definitions would not be found, so that this method allows
     * you to clear this autoload definition cache.
     *
     * @return void
     */
    public static function clearAutoloadCache()
    {
        self::$moduleAutoloadMapping = null;
    }
}

