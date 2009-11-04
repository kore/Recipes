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
 * Module locator, which finds and loads module definitions
 *
 * @package Core
 * @subpackage Module
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitModuleDefinitionLocator
{
    /**
     * Set a module class name, if the class name does not obey the defined
     * naming scheme.
     *
     * @var array
     */
    protected $moduleClass = array(
        'core' =>   'arbitCoreDefinition',
    );

    /**
     * Set a module definition file name, if the file name does not obey the
     * defined naming scheme.
     *
     * @var array
     */
    protected $moduleFile = array(
        'core' =>   'classes/module/core.php',
    );

    /**
     * Set exception for module class name
     *
     * Set the definition class name for a module, which does not obey the
     * defined naming scheme for module definition classes.
     *
     * @param string $moduleName
     * @param string $moduleClassName
     * @return void
     */
    public function setModuleClassName( $moduleName, $moduleClassName )
    {
        $this->moduleClass[$moduleName] = $moduleClassName;
    }

    /**
     * Set exception for module file name
     *
     * Set the definition file name for a module, which does not obey the
     * defined naming scheme for module definition filees.
     *
     * @param string $moduleName
     * @param string $moduleFileName
     * @return void
     */
    public function setModuleFileName( $moduleName, $moduleFileName )
    {
        $this->moduleFile[$moduleName] = $moduleFileName;
    }

    /**
     * Get filename for module definition file
     *
     * Get the file name for a module name either from the exception list, if
     * defined there, or by creating the path matching the convention, which
     * points to modules/<name>/definition.php.
     *
     * @param string $moduleName
     * @return string
     */
    protected function getFileName( $moduleName )
    {
        if ( isset( $this->moduleFile[$moduleName] ) )
        {
            return ARBIT_BASE . $this->moduleFile[$moduleName];
        }

        return ARBIT_BASE . 'modules/' . $moduleName . '/definition.php';
    }

    /**
     * Get classname for module definition class
     *
     * Get the class name for a module name either from the exception list, if
     * defined there, or by creating the name matching the convention, which
     * is arbitModule<Name>Definition
     *
     * @param string $moduleName
     * @return string
     */
    protected function getClassName( $moduleName )
    {
        if ( isset( $this->moduleClass[$moduleName] ) )
        {
            return $this->moduleClass[$moduleName];
        }

        return 'arbitModule' . $moduleName . 'Defintion';
    }

    /**
     * Find the module definition class in the filesystem.
     *
     * Locates the file using either the defined conventions or the exception
     * list, and includes the definitions. The checks if the class has been
     * defined, and returns the module definition class name, if successfully
     * found.
     *
     * @param string $moduleName
     * @return string
     */
    public function findModuleClass( $moduleName )
    {
        // Check if class is already available, then exit immediately with
        // class name.
        if ( class_exists( $moduleClassName = $this->getClassName( $moduleName ), false ) )
        {
            return $moduleClassName;
        }

        // Just try to include class and catch PHP errors. Dooing this we do
        // not need to manually scan the include path for the file.
        try
        {
            include $fileName = $this->getFileName( $moduleName );
        }
        catch ( arbitPhpErrorException $e )
        {
            throw new arbitModuleFileNotFoundException( $fileName );
        }

        // Check if class has really been defined
        if ( !class_exists( $moduleClassName, false ) )
        {
            throw new arbitModuleDefinitionNotFoundException( $moduleClassName );
        }

        // Return class name on success
        return $moduleClassName;
    }
}

