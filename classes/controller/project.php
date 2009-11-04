<?php
/**
 * arbit project controller
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
 * @version $Revision: 1413 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Project controller, containing basic project context information and is
 * responsible for dispatching to module controllers.
 *
 * @package Core
 * @version $Revision: 1413 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitProjectController extends arbitController
{
    /**
     * Available modules in prject
     *
     * @var array
     */
    protected $modules = array();

    /**
     * Cuurent project configuration
     *
     * @var arbitBackendIniProjectConfiguration
     */
    protected $config;

    /**
     * Module URL identifiers associate with the corresponding modulem type.
     *
     * @var array
     */
    protected $moduleTypes;

    /**
     * Call module subcontroller to fetch module data
     *
     * @param string $action
     * @param arbitRequest $request
     * @return void
     */
    protected function callModuleController( $action, arbitRequest $request )
    {
        $module = arbitModuleManager::get( $this->moduleTypes[$action] );
        $moduleAction = $this->request->subaction;

        $moduleController = $module->controller;
        $moduleController = new $moduleController( $request->controller, $request );
        return $moduleController->$moduleAction( $this->request );
    }

    /**
     * Normalize module name
     *
     * Returns a normalized version of the module name.
     *
     * @param string $name
     * @return string
     */
    public static function normalizeModuleName( $name )
    {
        return preg_replace( '([^a-z0-9]+)', '_', strtolower( $name ) );
    }

    /**
     * Return modules menu array
     *
     * The array contains all module names, converted to usable keys, as keys
     * and the modules names as value.
     *
     * The conversion to usable keys happens by converting all occurences of
     * (multiple) non alphanumeric characters by underscores.
     *
     * @param array $modules
     * @return array
     */
    protected function getModulesMenu( array $modules )
    {
        $menu = array();
        foreach ( $modules as $name => $type )
        {
            $module = arbitModuleManager::get( $type );

            if ( $module->menu['main'] )
            {
                $identifier                     = self::normalizeModuleName( $name );
                $menu[$identifier]              = $name;
                $this->moduleTypes[$identifier] = $type;
            }
        }

        // Add core module, which is always available, but not part of the
        // configuration
        $this->moduleTypes['core'] = 'core';

        return $menu;
    }

    /**
     * Initialize project
     *
     * Initialize project and project dependent modules.
     *
     * @param ezcMvcRequest $request
     * @return void
     */
    public static function initialize( ezcMvcRequest $request )
    {
        ezcLog::getInstance()->log( "Initialize project {$request->controller}.", ezcLog::INFO );

        // Set selected project in facade manager
        arbitFacadeManager::selectProject( $request->controller );

        $config = arbitBackendIniConfigurationManager::getProjectConfiguration(
            $request->controller
        );

        // Activate all configured modules for this project
        foreach ( $config->modules as $moduleName )
        {
            arbitModuleManager::activateModule( $moduleName );
        }
    }

    /**
     * Runs the controller to process the query and return variables usable
     * to render the view.
     *
     * @throws arbitControllerUnknownActionException if the action method could not be found
     * @return ezcMvcResult|ezcMvcInternalRedirect
     */
    public function createResult()
    {
        $this->config = arbitBackendIniConfigurationManager::getProjectConfiguration(
            $project = $this->request->controller
        );

        // Get list of available modules as menu array
        $menu = $this->getModulesMenu( $this->config->modules );

        // Embed module context in request
        $modules = array();
        foreach ( $this->config->modules as $name => $type )
        {
            $modules[$this->normalizeModuleName( $name )] = $type;
        }
        $this->request->variables['arbit_modules'] = $modules;

        // If the index module has been called replace this by the first module
        // in the list.
        if ( ( $action = $this->request->action ) === 'index' )
        {
            $keys = array_keys( $menu );
            $action = $this->request->action = reset( $keys );
        }

        // Check that a valid module has been called
        if ( !isset( $this->moduleTypes[$action] ) )
        {
            // Leave this case to the parent __call implementation.
            throw new arbitControllerUnknownActionException( $action );
        }

        $moduleResponse = $this->callModuleController(
            $this->request->action,
            $this->request
        );

        if ( $moduleResponse instanceof arbitViewModuleModel )
        {
            // Generate module content
            return new arbitResult( new arbitViewProjectContextModel(
                $project,
                $this->config->name,
                $this->config->description,
                $this->getModulesMenu(
                    $this->config->modules
                ),
                $moduleResponse,
                $this->config->language
            ) );
        }

        // If a internal redirect has been returned, directly pass it up.
        if ( $moduleResponse instanceof ezcMvcInternalRedirect )
        {
            return $moduleResponse;
        }

        // If already a result object, directly pass up
        if ( $moduleResponse instanceof arbitResult )
        {
            return $moduleResponse;
        }

        // Otherwise just return the module response directly, which may for
        // example be a binary data response.
        return new arbitResult( $moduleResponse );
    }
}

