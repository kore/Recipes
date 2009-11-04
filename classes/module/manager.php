<?php
/**
 * arbit modules
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
 * @subpackage Module
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Module manager, which needs to take care of proper module registration.
 *
 * @package Core
 * @subpackage Module
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitModuleManager
{
    /**
     * Array with names of all available modules.
     *
     * @var array
     */
    protected static $availableModules = array();

    /**
     * Array with module definitions
     *
     * @var array
     */
    protected static $modules = array();

    /**
     * Module definition class locator
     *
     * @var arbitModuleDefinitionLocator
     */
    protected static $locator;

    /**
     * Registers an available module.
     *
     * Registers a module identifier that is available for the arbit installation.
     *
     * @param string $moduleName
     * @return void
     */
    public static function registerModule( $moduleName )
    {
        self::$availableModules[$moduleName] = true;
    }

    /**
     * Activates a module
     *
     * Activates a module in core, just given by its name.
     *
     * @param name $moduleName
     * @return void
     * @throws arbitUnknownModuleException When an unregistered module is requested.
     */
    public static function activateModule( $moduleName )
    {
        if ( !isset( self::$availableModules[$moduleName] ) )
        {
            throw new arbitUnknownModuleException( $moduleName );
        }

        // Init module
        ezcLog::getInstance()->log( "Activated module $moduleName.", ezcLog::INFO );
        $moduleClassName = self::findModuleClass( $moduleName );
        self::$modules[$moduleName] = new $moduleClassName();

        // Register module signals and slots at the core
        arbitSignalSlot::registerSignals(
            $moduleName,
            self::$modules[$moduleName]->signals
        );

        arbitSignalSlot::registerSlots(
            self::$modules[$moduleName]->slots
        );

        // Initialize module
        self::$modules[$moduleName]->initializeModule();

        // Clear module autoload cache
        arbitFrameworkBase::clearAutoloadCache();
    }

    /**
     * Get all autoload definitions
     *
     * Returns the autoload definition array for all registerd modules. The
     * array is merged from all modules and contains the definition in commonly
     * used structure.
     *
     * @return array
     */
    public static function getAutoloads()
    {
        // Build autoload array from all registered modules
        $autoload = array();
        foreach ( self::$modules as $definition )
        {
            foreach ( $definition->autoload as $class => $file )
            {
                $autoload[$class] = $file;
            }
        }

        return $autoload;
    }

    /**
     * Get template paths
     *
     * Get a plain array of all module template paths.
     *
     * @return array
     */
    public static function getTemplatePaths()
    {
        $paths = array();
        foreach ( self::$modules as $name => $definition )
        {
            if ( $definition->path !== null )
            {
                $paths[] = $definition->path . '/' . $definition->templateDirectory;
            }
        }

        return $paths;
    }

    /**
     * Get all permissions
     *
     * Get a list of all permissions as a multidimensional array, where the
     * module name is the first dimension, wihich contains an array, in which
     * the name of the permission is associated with its description, like:
     *
     * <code>
     *  array(
     *      'module' => array(
     *          'perm' => 'Permission description',
     *          ...
     *      ),
     *      ...
     *  )
     * </code>
     *
     * @return array
     */
    public static function getPermissions()
    {
        $permissions = array();
        foreach ( self::$modules as $name => $definition )
        {
            foreach ( $definition->permissions as $permission => $description )
            {
                $permissions[$name][$permission] = $description;
            }
        }

        return $permissions;
    }

    /**
     * Get all signals
     *
     * Get a list of all signals as a multidimensional array, where the module
     * name is the first dimension, wihich contains an array, in which the name
     * of the signal is associated with its description, like:
     *
     * <code>
     *  array(
     *      'module' => array(
     *          'signal' => 'Signal description',
     *          ...
     *      ),
     *      ...
     *  )
     * </code>
     *
     * @return array
     */
    public static function getSignals()
    {
        return arbitSignalSlot::getSignals();
    }

    /**
     * Set a custom module class locator.
     *
     * This is mainly intented for testing, your module classes really should
     * just obey the defined naming scheme.
     *
     * @param arbitModuleDefinitionLocator $locator
     * @return void
     */
    public static function setLocator( arbitModuleDefinitionLocator $locator )
    {
        self::$locator = $locator;
    }

    /**
     * Get module definition
     *
     * Return the module definition structure for the requested module.
     *
     * @param string $moduleName
     * @return arbitModuleDefinition
     */
    public static function get( $moduleName )
    {
        if ( !isset( self::$modules[$moduleName] ) )
        {
            throw new arbitUnknownModuleException( $moduleName );
        }

        return self::$modules[$moduleName];
    }

    /**
     * Find the module definition class in the filesystem.
     *
     * The module definition struct must be located under
     * modules/<name>/definition.php. This method checks if such a file is
     * available and includes the definition struct.
     *
     * @param string $moduleName
     * @return void
     */
    protected static function findModuleClass( $moduleName )
    {
        // Initialize locator, if not set from external
        if ( self::$locator === null )
        {
            self::$locator = new arbitModuleDefinitionLocator();
        }

        return self::$locator->findModuleClass( $moduleName );
    }
}

