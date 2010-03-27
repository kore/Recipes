<?php
/**
 * CLI tool class for administrative tasks
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
 * CLI tool class, which handles admintrative tasks regarding the CouchDB
 * backend.
 *
 * @package Core
 * @version $Revision: 1434 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitFrameworkCouchdbCliTool extends arbitFrameworkActionCliTool
{
    /**
     * Name of CLI tool
     *
     * @var string
     */
    protected $name = 'Arbit CouchDB management tool';

    /**
     * Short description of the purpose of the current CLI tool
     *
     * @var string
     */
    protected $description = "Arbit CouchDB management tool, able to perform different administrative actions, like priming the views and ex- and importing backups.\n\nThe available actions are:";

    /**
     * Available actions.
     *
     * Mapping of the CLI action identifiers to their action names, the array
     * should be structured like:
     *
     * <code>
     *  array(
     *      'action' => array(
     *          'action' => (string) Method name in controller,
     *          'description' => (string) Action description,
     *      ), ...
     *  )
     * </code>
     *
     * @var array
     */
    protected $actions = array(
        'export' => array(
            'action'      => 'exportBackup',
            'description' => 'Export backups for the given projects',
        ),
        'import' => array(
            'action'      => 'importBackup',
            'description' => 'Import backup for the given project.',
        ),
        'prime' => array(
            'action'      => 'primeViews',
            'description' => 'Prime all view caches in the database.',
        ),
    );

    /**
     * Register options
     *
     * Register a set of options, which are special for this CLI tool. May be
     * left empty, if no additional options are required.
     *
     * @return void
     */
    protected function registerOptions()
    {
        $this->in->registerOption( new ezcConsoleOption(
            'f', 'file',
            ezcConsoleInput::TYPE_STRING, null, true,
            'Files to export or import.'
        ) );
    }

    /**
     * Get controller
     *
     * Return controller to execute for the current command
     *
     * @param arbitRequest $request
     * @param array $options
     * @return arbitController
     */
    protected function createController( arbitRequest $request, array $options )
    {
        $action = $this->in->argumentDefinition['action']->value;
        return new arbitAdminCouchdbController( $this->actions[$action]['action'], $request );
    }
}

