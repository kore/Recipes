<?php
/**
 * arbit ini configuration backend
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
 * @subpackage IniBackend
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Basic ini configuration manager.
 *
 * Implements static access to all relevant configuration parts and ensures
 * lazy loading to spare the maximum amount of ini parsing effort.
 *
 * It does not implement caching for parsed ini files, but this might be added
 * in this class later.
 *
 * @package Core
 * @subpackage IniBackend
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitBackendIniConfigurationManager
{
    /**
     * Main configuration file
     *
     * @var arbitBackendIniMainConfiguration
     */
    protected static $main = null;

    /**
     * Configurations for all registered projects
     *
     * @var array(arbitBackendIniProjectConfiguration)
     */
    protected static $projects = null;

    /**
     * Conatins the configuration for all modules per project. The array
     * structure looks like:
     *
     * <code>
     *  array(
     *      'projectname' => array(
     *          'modulename' => arbitBackendIniModuleConfiguration,
     *          ...
     *      ),
     *      ...
     *  )
     * </code>
     *
     * @var array
     */
    protected static $modules = null;

    /**
     * Configuration manager
     *
     * @var ezcConfigurationManager
     */
    protected static $confManager = null;

    /**
     * Constructor not callable from external
     *
     * @ignore
     * @return void
     */
    protected function __construct()
    {
        // Not callable.
    }

    /**
     * Ensure existance of ezcConfigurationManager existance
     *
     * Ensure a valid instance of the ezcConfigurationManager, which is used as
     * a backend to initialize the ocnfiguration structures.
     *
     * @return void
     */
    protected static function createConfigurationInstance()
    {
        if ( self::$confManager === null )
        {
            self::$confManager = ezcConfigurationManager::getInstance();
            self::$confManager->init(
                'ezcConfigurationIniReader',
                ARBIT_CONFIG
            );
        }
    }

    /**
     * Get arbit main configuration
     *
     * Get the main configuration of the current arbit instance.
     *
     * @return arbitBackendIniMainConfiguration
     */
    public static function getMainConfiguration()
    {
        if ( self::$main === null )
        {
            // Always ensure configuration instance first
            self::createConfigurationInstance();

            // Then create the requested configuration from the passed backend
            // instance.
            self::$main = new arbitBackendIniMainConfiguration(
                self::$confManager
            );

            // Initialize project array
            foreach ( self::$main->projects as $project )
            {
                self::$projects[$project] = null;
                self::$modules[$project] = array();
            }
        }

        return self::$main;
    }

    /**
     * Get configuration of a project
     *
     * Receive the configuration for the given project.
     *
     * @param string $project
     * @return arbitBackendIniProjectConfiguration
     */
    public static function getProjectConfiguration( $project )
    {
        if ( self::$main === null )
        {
            // Initialize main configuration first. This is required to
            // know which projects are existing at all.
            self::getMainConfiguration();
        }

        // Check if project exists in project array. This would have been
        // set, if the project exists in the main.ini.
        if ( !array_key_exists( $project, self::$projects ) )
        {
            throw new arbitBackendNoSuchProjectException( $project );
        }

        // Check if configuration for the requested project has already been
        // loaded
        if ( self::$projects[$project] === null )
        {
            self::$projects[$project] = new arbitBackendIniProjectConfiguration(
                self::$confManager,
                $project
            );

            // Intilize modules array
            foreach ( self::$projects[$project]->modules as $name => $type )
            {
                $identifier = preg_replace( '([^a-z0-9]+)', '_', strtolower( $name ) );
                self::$modules[$project][$identifier] = null;
            }
        }

        // Valid project confiuration ensured.
        return self::$projects[$project];
    }

    /**
     * Get configuration of a projects module
     *
     * Receive the configuration for a module in the given project.
     *
     * Optionally you may specify a class with default values for the settings,
     * like available for the project and main configuration. By default the
     * arbitBackendIniModuleConfiguration class, without any default values,
     * will be used.
     *
     * @param string $project
     * @param string $module
     * @param string $class
     * @return arbitBackendIniModuleConfiguration
     */
    public static function getModuleConfiguration( $project, $module, $class = 'arbitBackendIniModuleConfiguration' )
    {
        if ( ( self::$projects === null ) || ( !isset( self::$projects[$project] ) ) )
        {
            // We enter this section, when the project or main configuration
            // has not been loaded at all, or a project does not exists. (isset
            // returns false for the null value).
            //
            // Is either of the above true, first initilize the project
            // configuration.
            self::getProjectConfiguration( $project );
        }

        // Check if module exists in module array. This would have been set, if
        // the module exists in the respective project.
        if ( !array_key_exists( $module, self::$modules[$project] ) )
        {
            throw new arbitBackendNoSuchModuleException( $project, $module );
        }

        // Check if configuration for the requested project has already been
        // loaded
        if ( self::$modules[$project][$module] === null )
        {
            self::$modules[$project][$module] = new $class(
                self::$confManager,
                $project, $module
            );
        }

        // Valid project confiuration ensured.
        return self::$modules[$project][$module];
    }
}

