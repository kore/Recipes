<?php
/**
 * arbit view
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
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Base model struct for exception data.
 *
 * Does not enforce anything, just a root class for object identification and
 * possible future checks / unifications.
 *
 * @package Core
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitViewErrorContextModel extends arbitViewModel
{
    /**
     * Array containing the actual view data.
     *
     * @var array
     */
    protected $properties = array(
        'exception' => null,
    );

    /**
     * Create view data struct from exception
     *
     * @param Exception $e
     * @return void
     */
    public function __construct( Exception $e = null )
    {
        $this->properties['exception'] = $e;
    }
}

