<?php
/**
 * arbit storage backend facade
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
 * @subpackage Facade
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Project facade defining all methods required to access project related data
 * in the backend.
 *
 * @package Core
 * @subpackage Facade
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
interface arbitProjectFacade
{
    /**
     * Get project data
     *
     * Return a sorted list with the version strings for the current project.
     * There should not be any assumptions made on version sorting, but the
     * order should be always returned the same way it has been provided by the
     * user. Each version is associated with its state, which is an integer in
     * (0 = inactive, 1 = active).
     *
     * The returned project data array also contains a list of components,
     * which do not have any structure or order associated.
     *
     * The array looks like:
     * <code>
     *  array(
     *      versions => array(
     *          '0.1' => 0,
     *          '1.0' => 1,
     *          ....
     *      ),
     *      components => array(
     *          'website' => array(),
     *          'project' => array(
     *              'backend' => array(),
     *              'frontend' => array(),
     *          ),
     *      ),
     *  ),
     * </code>
     *
     * @return array
     */
    public function getProjectData();

    /**
     * Set project data
     *
     * Store versions or component array (or both). The data has to be provided
     * in the same structure the getProjectData() returns.
     *
     * The array looks like:
     * <code>
     *  array(
     *      versions => array(
     *          '0.1' => 0,
     *          '1.0' => 1,
     *          ....
     *      ),
     *      components => array(
     *          'website' => array(),
     *          'project' => array(
     *              'backend' => array(),
     *              'frontend' => array(),
     *          ),
     *      ),
     *  ),
     * </code>
     *
     * @param array $data
     * @return void
     */
    public function setProjectData( array $data );
}

