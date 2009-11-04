<?php
/**
 * arbit abstract base command
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
 * @package Source
 * @version $Revision: 1434 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Arbit abstract base command
 *
 * @package Source
 * @version $Revision: 1434 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class arbitScheduledTaskCommand extends periodicCommand
{
    /**
     * Get current request
     * 
     * Get the current request, optionally for the specified module.
     * 
     * @param string $module 
     * @return arbitRequest
     */
    protected function getRequest( $module = null )
    {
        $request         = arbitSession::getCurrentRequest();

        if ( $module !== null )
        {
            // Find name of source module
            $config = arbitBackendIniConfigurationManager::getProjectConfiguration(
                $request->controller
            );

            $request->action = arbitProjectController::normalizeModuleName(
                array_search( $module, $config->modules )
            );
        }

        return $request;
    }
}
 
