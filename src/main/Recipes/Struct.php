<?php
/**
 * This file is part of recipes.
 *
 * recipes is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * recipes is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with recipes; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @version $Revision: 1469 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

namespace Recipes;

/**
 * Base struct class
 *
 * @version $Revision$
 */
abstract class Struct
{
    /**
     * Creates the struct optionally with the given values.
     *
     * @param array $record
     */
    public function __construct( array $record = array() )
    {
        foreach ( $record as $name => $value )
        {
            if ( property_exists( $this, $name ) )
            {
                $this->{$name} = $value;
            }
        }
    }

    /**
     * Disable read access to unknown prioperties
     *
     * @param string $property
     * @return mixed
     */
    public function __get( $property )
    {
        throw new \InvalidArgumentException( 'Trying to get non-existing property ' . $property );
    }

    /**
     * Disable set access to unknwon properties
     *
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public function __set( $property, $value )
    {
        throw new \InvalidArgumentException( 'Trying to set non-existing property ' . $property );
    }

    /**
     * Default clone method
     *
     * @return void
     */
    public function __clone()
    {
        foreach ( $this as $property => $value )
        {
            if ( is_object( $value ) )
            {
                $this->$property = clone $value;
            }
        }
    }

    /**
     * Default recreation method from var_export
     *
     * @param array $properties
     * @return Struct
     */
    public static function __set_state( array $properties )
    {
        $struct = new static();
        foreach ( $properties as $property => $value )
        {
            $struct->$property = $value;
        }

        return $struct;
    }
}

