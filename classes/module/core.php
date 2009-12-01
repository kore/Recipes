<?php
/**
 * arbit base module definition
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
 * @version $Revision: 1352 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Arbit core controller definition
 *
 * @package Core
 * @version $Revision: 1352 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitCoreDefinition extends arbitModuleDefintion
{
    /**
     * Array containing the module structures properties.
     *
     * Take a look at the property descriptions in the class level
     * documentation for a more detailed description for each of the
     * properties.
     *
     * Do not add any properties to this array, which are not defined in this
     * base class, because those might be used later on by the core.
     *
     * @var array
     */
    protected $properties = array(
        'autoload'          => array(
            // No explicit autoload definition required for core module, all
            // classes are available through the default autoload mechanism.
        ),
        'permissions'       => array(
        ),
        'slots'             => array(
        ),
        'signals'           => array(
        ),

        'templateDirectory' => 'templates/',
        'controller'        => 'arbitCoreModuleController',
        'path'              => null,
    );

    /**
     * List of command definitions of the module
     *
     * Array containing command names and their assiciated classes in the module 
     * definition.
     *
     * <code>
     *  array(
     *      'mymodule.mycommand' => 'myModuleMyCommand',
     *      ...
     *  )
     * </code>
     * 
     * @var array
     */
    protected $commands = array(
        'has-module' => 'arbitHasModuleCommand',
    );
}

