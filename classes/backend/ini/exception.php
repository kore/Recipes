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
 * Base exception for ini configuration backend
 *
 * @package Core
 * @subpackage IniBackend
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class arbitBackendIniException extends arbitException
{
}

/**
 * Exception thrown, when a project does not exist.
 *
 * @package Core
 * @subpackage IniBackend
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitBackendNoSuchProjectException extends arbitBackendIniException
{
    /**
     * Construct exception from project and module
     *
     * @param string $project
     * @return void
     */
    public function __construct( $project )
    {
        parent::__construct(
            'Project %project does not exist.',
            array(
                'project' => $project,
            )
        );
    }
}

/**
 * Exception thrown, when a module does not exist for the given project
 *
 * @package Core
 * @subpackage IniBackend
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitBackendNoSuchModuleException extends arbitBackendIniException
{
    /**
     * Construct exception from project and module
     *
     * @param string $project
     * @param string $module
     * @return void
     */
    public function __construct( $project, $module )
    {
        parent::__construct(
            'Module %module does not exist in project %project.',
            array(
                'module'    => $module,
                'project'   => $project,
            )
        );
    }
}

/**
 * Exception thrown, when a requested configuration setting does not exist.
 *
 * @package Core
 * @subpackage IniBackend
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitBackendUnknownConfigurationException extends arbitBackendIniException
{
    /**
     * Construct exception from project and module
     *
     * @param string $file
     * @param string $group
     * @param string $value
     * @return void
     */
    public function __construct( $file, $group, $value )
    {
        parent::__construct(
            'The configuration setting %value in %group does not exist in the ini file %file.',
            array(
                'file'      => $file,
                'group'     => $group,
                'value'     => $value,
            )
        );
    }
}

