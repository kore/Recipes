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
 * arbit main configuration
 *
 * @package Core
 * @subpackage IniBackend
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitBackendIniMainConfiguration extends arbitBackendIniConfigurationBase
{
    /**
     * Array with default values for all required configuration directives. The
     * array has the followin structure:
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
        'main' => array(
            'email'         => false,
            'language'      => 'en',
        ),
        'backend' => array(
            'type'          => 'couchdb',
            'connection'    => 'couchdb://localhost:5984/arbit_',
        ),
        'layout' => array(
            'css'           => array(),
            'override'      => array(),
        ),
        'modules' => array(
            'module'        => array(),
        ),
        'projects' => array(
            'project'       => array(),
        ),
        'mail' => array(
            'transport'     => 'mta',
            'options'       => array(),
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
        'email'         => array( 'main', 'email' ),
        'language'      => array( 'main', 'language' ),

        'backendType'   => array( 'backend', 'type' ),
        'backendUrl'    => array( 'backend', 'connection' ),

        'modules'       => array( 'modules', 'module' ),
        'projects'      => array( 'projects', 'project' ),
    );

    /**
     * Construct from ezc configuration manager
     *
     * @param ezcConfigurationManager $config
     * @return void
     */
    public function __construct( ezcConfigurationManager $config )
    {
        $this->iniFile = 'main';

        parent::__construct( $config );
    }
}

