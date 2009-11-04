<?php
/**
 * arbit CLI request class
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
 * The HTTP request class extends the ase request class by user submitted
 * information on accepted languages and content types.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitCliRequest extends arbitRequest
{
    /**
     * Serialize URL
     *
     * Return a string representation of the URL for the requests connection
     * type.
     *
     * @param bool $absolute
     * @return string
     */
    public function serialize( $absolute = false )
    {
        throw new arbitRuntimeException( 'Cannot serialize Cli request structs' );
    }
}

