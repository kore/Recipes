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
 * arbit module configuration
 *
 * @package Core
 * @subpackage IniBackend
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitBackendIniModuleConfiguration extends arbitBackendIniConfigurationBase
{
    /**
     * Construct from ezc configuration manager
     *
     * @param ezcConfigurationManager $config
     * @param string $project
     * @param string $module
     * @return void
     */
    public function __construct( ezcConfigurationManager $config, $project, $module )
    {
        $this->iniFile = $project . '/' . $this->getConfigurationFileName( $module );

        parent::__construct( $config );
    }

    /**
     * Get configuration file name from module name
     *
     * The module identifier is just the configured lowercase name of the
     * module, where all non alphanumeric characters are replaced by
     * underscores, eg.: "Foo - Bar" -> "foo_bar"
     *
     * @param string $module
     * @return string
     */
    protected function getConfigurationFileName( $module )
    {
        return preg_replace( '([^a-z]+)', '_', strtolower( $module ) );
    }
}

