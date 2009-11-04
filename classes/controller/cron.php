<?php
/**
 * arbit admin controller
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
 * @version $Revision: 1434 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Controller fro cron related tasks, wrpping around periodic.
 *
 * @package Core
 * @version $Revision: 1434 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitCronController extends arbitController
{
    /**
     * Execute cron items
     *
     * Let peridoc execute all pending cron items
     *
     * @param arbitRequest $request
     * @return arbitViewModuleModel
     */
    public function execute( arbitRequest $request )
    {
        // Instantiate logger
        $logger = new periodicEzLogLogger();

        // Ensure temp directory for executor exists
        if ( !is_dir( $dir = ARBIT_TMP_PATH . 'periodic/' . $request->controller ) )
        {
            mkdir( $dir, 0700, true );
        }

        // Instantiate executor
        $executor = new periodicExecutor(
            file_get_contents( ARBIT_BASE . 'crontab' ),
            new periodicTaskFactory( ARBIT_BASE . 'tasks/' ),
            $logger,
            $dir
        );
        $executor->run();

        return new arbitViewUserMessageModel(
            "Executed cron tasks for project %project.",
            array(
                'project' => $request->controller,
            )
        );
    }
}

