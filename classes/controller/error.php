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
class arbitErrorController extends arbitController
{
    /**
     * Handle the given error
     *
     * @return ezcMvcResult
     */
    public function index()
    {
        if ( !isset( $this->request->variables['exception'] ) )
        {
            $exception = new arbitRuntimeException( 'Missing exception' );
        }
        else
        {
            $exception = $this->request->variables['exception'];
        }

        // Return the error view model depending on the marker interface
        switch ( true )
        {
            case $exception instanceof arbitExceptionNotFoundMarker:
                ezcLog::getInstance()->log( $exception->getMessage(), ezcLog::WARNING );
                return new arbitViewErrorNotFoundContextModel( $exception );

            default:
                // Just create and return exception view data struct from exception.
                ezcLog::getInstance()->log( $exception->getMessage(), ezcLog::ERROR );
                return new arbitViewErrorContextModel( $exception );
        }
    }
}

