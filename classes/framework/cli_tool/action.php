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
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * CLI tool class, which handles various adimistrative tasks.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class arbitFrameworkActionCliTool extends arbitFrameworkCliTool
{
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
    protected $actions = array();

    /**
     * Register options
     *
     * Register a set of options, which are special for this CLI tool. May be
     * left empty, if no additional options are required.
     *
     * @return void
     */
    protected function registerDefaultOptions()
    {
        parent::registerDefaultOptions();

        // Register arguments
        $this->in->argumentDefinition = new ezcConsoleArguments();
        $this->in->argumentDefinition[0] = new ezcConsoleArgument(
            'action',
            ezcConsoleInput::TYPE_STRING,
            'Administrative action',
            "Administrative action to execute, which should be one of: " . implode( ', ', array_keys( $this->actions ) ),
            false, // Mandatory
            false  // Multiple
        );

        // Append dynamically created argument description to tool description.
        $this->description .= "\n\n";
        $maxLength = array_reduce( array_map( 'strlen', array_keys( $this->actions ) ), 'max' );
        foreach ( $this->actions as $action => $data )
        {
            $this->description .= str_pad( $action, $maxLength ) . '  ' . wordwrap( $data['description'], 77 - $maxLength - 2, "\n" . str_repeat( ' ', $maxLength + 2 ) ) . "\n";
        }
    }

    /**
     * Process default options
     *
     * Handle the default options. Returns true, if the the execution should
     * stop afterwards, and false if the execution may continue.
     *
     * @param array $options
     * @return bool
     */
    protected function processDefaultOptions( array &$options )
    {
        if ( parent::processDefaultOptions( $options ) )
        {
            return true;
        }

        if ( ( $action = $this->in->argumentDefinition['action']->value ) === null )
        {
            throw new arbitRuntimeException( 'No action specified, try -h / --help to get a list of available actions.' );
        }

        if ( !isset( $this->actions[$action] ) )
        {
            throw new arbitControllerUnknownActionException( $action );
        }

        return false;
    }
}

