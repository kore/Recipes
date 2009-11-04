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
// ...

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

