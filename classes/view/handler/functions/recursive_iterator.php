<?php
/**
 * arbit recursive iterator iterator
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
 * Extension of the default SPL RecursiveIteratorIterator to expose the
 * getDepth() method as a public property, because the tempaltes may only
 * access properties, but no methods.
 *
 * @package Core
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitTemplateRecursiveIteratorIterator extends RecursiveIteratorIterator
{
    /**
     * Magic method wrapping property access
     *
     * Expose the method return value of getDepth() as 'depth' property.
     *
     * @param string $property
     * @return mixed
     */
    public function __get( $property )
    {
        if ( $property === 'depth' )
        {
            return $this->getDepth();
        }

        return $this->$property;
    }
}

