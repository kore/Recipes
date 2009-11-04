<?php
/**
 * arbit controller result
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
class arbitResult extends ezcMvcResult
{
    /**
     * View contents wrapped by arbitViewModel models.
     *
     * @var arbitViewModel
     */
    public $view;

    /**
     * Result status
     *
     * Set this to an object that implements the ezcMvcResultStatusObject, for
     * example ezcMvcResultUnauthorized or ezcMvcExternalRedirect. These status
     * objects are used by the response writers to take appropriate actions.
     *
     * @var ezcMvcResultStatusObject
     */
    public $status = 0;

    /**
     * Generator string, f.e. "eZ Components MvcTools"
     *
     * @var string
     */
    public $generator = 'Arbit $Revision: 1236 $';

    /**
     * Constructs a new arbitResult.
     *
     * @param arbitViewModel $view
     */
    public function __construct( arbitViewModel $view )
    {
        $this->view = $view;
    }
}

