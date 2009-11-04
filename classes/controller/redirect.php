<?php
/**
 * arbit controller result for external redirects
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
 * Main controller result extending the ezcMvcResult
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitRedirectResult extends arbitResult
{
    /**
     * Constructs a new arbitResult.
     *
     * @param arbitRequest $target
     * @return void
     */
    public function __construct( arbitRequest $target )
    {
        arbitLogger::dump( $target->serialize( true ) );
        $this->status = new ezcMvcExternalRedirect( $target->serialize( true ) );
    }
}

