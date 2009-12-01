<?php
/**
 * arbit core controller
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
 * Main controller for the arbit project tracker, implementing all user and
 * group related functionality.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitReceiptController extends arbitController
{
    /**
     * Index action
     *
     * Dispatches to the default action
     *
     * @param arbitRequest $request
     * @return arbitViewModel
     */
    public function index( arbitRequest $request )
    {
        return $this->overview( $request );
    }

    /**
     * Overview action
     *
     * Gives an overview on the currently available receipts
     *
     * @param arbitRequest $request
     * @return arbitViewModel
     */
    public function overview( arbitRequest $request )
    {
        return new arbitViewUserMessageModel( 'Hello Receipts!' );
    }
}

