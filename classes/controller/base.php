<?php
/**
 * arbit base controller
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
 * Base controller, which just throws a dedicated exception, when an unknown
 * method (eg. action) is called on the controller.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class arbitController extends ezcMvcController
{
    /**
     * Creates a method name to call from an action name.
     *
     * @param string $action
     * @return string
     */
    public static function createActionMethodName( $action )
    {
        return $action;
    }

    /**
     * Runs the controller to process the query and return variables usable
     * to render the view.
     *
     * @throws arbitControllerUnknownActionException if the action method could not be found
     * @return ezcMvcResult|ezcMvcInternalRedirect
     */
    public function createResult()
    {
        $actionMethod = $this->createActionMethodName( $this->action );

        if ( method_exists( $this, $actionMethod ) )
        {
            $response = $this->$actionMethod( $this->request );

            // If a internal redirect has been returned, directly pass it up.
            if ( $response instanceof ezcMvcInternalRedirect )
            {
                return $response;
            }

            // If already a result object, directly pass up
            if ( $response instanceof arbitResult )
            {
                return $response;
            }

            // Otherwise just return the module response directly, which may for
            // example be a binary data response.
            return new arbitResult( $response );
        }
        throw new arbitControllerUnknownActionException( $this->action );
    }

    /**
     * Wrapper for unknown actions
     *
     * Wrapper for unknown actions, which throws an exception instead of
     * causing PHP to report a fatal error.
     *
     * @param string $action
     * @param array $parameters
     * @return void
     */
    public function __call( $action, array $parameters )
    {
        throw new arbitControllerUnknownActionException( $action );
    }

    /**
     * Don't copy shit here...
     *
     * @param ezcMvcRequest $request
     */
    protected function setRequestVariables( ezcMvcRequest $request )
    {
        $this->request = $request;
    }
}

