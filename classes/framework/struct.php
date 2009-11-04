<?php
/**
 * arbit base struct
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
 * Basic struct class with optional value validation when the __set method gets
 * overwritten in the child classes.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitBaseStruct implements arbitCacheable
{
    /**
     * Array containing the structs properties.
     *
     * @var array
     */
    protected $properties = array();

    /**
     * Set property value
     *
     * Set property value. This does no value checks or whitelisting for
     * properties by default.
     *
     * @ignore
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public function __set( $property, $value )
    {
        // Just set without any checks.
        $this->properties[$property] = $value;
    }

    /**
     * Read property from struct
     *
     * Read property from struct
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
            throw new arbitPropertyException( $property );
        }

        return $this->properties[$property];
    }

    /**
     * Check if property exists in struct
     *
     * Check if property exists in struct
     *
     * @ignore
     * @param string $property
     * @return mixed
     */
    public function __isset( $property )
    {
        // Check if the property exists at all - use array_key_exists, to let
        // this check pass, even if the property is set to null.
        return array_key_exists( $property, $this->properties );
    }

    /**
     * Recreate struct exported by var_export()
     *
     * Recreate struct exported by var_export()
     *
     * @ignore
     * @param array $properties
     * @return arbitBaseStruct
     */
    public static function __set_state( array $properties )
    {
        $struct = new static();

        foreach ( $properties as $key => $value )
        {
            $struct->$key = $value;
        }

        return $struct;
    }
}

