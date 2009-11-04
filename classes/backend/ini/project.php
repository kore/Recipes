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
 * arbit project configuration
 *
 * @package Core
 * @subpackage IniBackend
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitBackendIniProjectConfiguration extends arbitBackendIniConfigurationBase
{
    /**
     * Array with default values for all required configuration directives. The
     * array has the following structure:
     *
     * <code>
     *  array(
     *      'group' => array(
     *          'key' => $value,
     *          ...
     *      ),
     *      ...
     *  )
     * </code>
     *
     * @var array
     */
    protected $defaultValues = array(
        'project' => array(
            'name'          => 'Missing project name',
            'description'   => 'Missing project description.',
            'language'      => 'en',
            'module'        => array(),
        ),
        'user' => array(
            'auth'          => array(
                'Password'  => 'arbitCoreModuleUserPasswordAuthentification'
            ),
            'administrator' => array(),
        ),
    );

    /**
     * Shortcuts for configuration value access.
     *
     * These shortcuts may be used for property access to the configuration
     * values. The shortcut array has the following structure:
     *
     * <code>
     *  array(
     *      'shortcut' => array( 'group', 'value' ),
     *      ...
     *  )
     * </code>
     *
     * @var array
     */
    protected $shortcuts = array(
        'name'           => array( 'project', 'name' ),
        'description'    => array( 'project', 'description' ),
        'language'       => array( 'project', 'language' ),
        'modules'        => array( 'project', 'module' ),
        'administrators' => array( 'user', 'administrator' ),
        'auth'           => array( 'user', 'auth' ),
    );

    /**
     * Construct from ezc configuration manager
     *
     * @param ezcConfigurationManager $config
     * @param string $project
     * @return void
     */
    public function __construct( ezcConfigurationManager $config, $project )
    {
        $this->iniFile = $project . '/project';

        parent::__construct( $config );
    }
}

