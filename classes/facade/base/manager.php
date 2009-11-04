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
 * Manager facade, which is responsible for the management of projects.
 *
 * @package Core
 * @subpackage Facade
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
interface arbitFacadeProjectManager
{
    /**
     * Select project
     *
     * Select the project currently selected in the UI, where we want to
     * operate on.
     *
     * @param string $project
     * @return void
     */
    public function selectProject( $project );

    /**
     * Create project
     *
     * Create a new project. This should create required databases and alike in
     * the backend.
     *
     * @param string $project
     * @return void
     */
    public function createProject( $project );

    /**
     * Remove project
     *
     * Clean all project related data in the backend.
     *
     * @param string $project
     * @return void
     */
    public function removeProject( $project );

    /**
     * Verify backend integrity
     *
     * @return void
     */
    public function verifyIntegrity();
}

