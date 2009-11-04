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
 * Basic view model struct
 *
 * Does not enforce anything, just a root class for object identification and
 * possible future checks / unifications.
 *
 * @package Core
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitViewModel extends arbitBaseStruct implements arbitDecorateable
{
    /**
     * Read property from struct
     *
     * Read property from struct, but do not throw an exception on unavailable
     * properties.
     *
     * @ignore
     * @param string $property
     * @return mixed
     */
    public function __get( $property )
    {
        // Check if the property exists at all - use array_key_exists, to let
        // this check pass, even if the property is set to null.
        if ( !array_key_exists( $property, $this->properties ) )
        {
            // We do not throw an exception, but only return null, as many
            // presentation layers may not be able to handle exceptions
            // properly
            return null;
        }

        return $this->properties[$property];
    }

    /**
     * Return an array with the available properties
     *
     * Returns an array containing the keys of all properties which are set in
     * the view model. This include properties set tu null.
     *
     * @return array
     */
    public function getProperties()
    {
        return array_keys( $this->properties );
    }
}

