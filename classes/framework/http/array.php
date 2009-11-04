<?php
/**
 * arbit http array input conversions class
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
 * Arbit HTTP array input conversions
 *
 * Handle user input data as array. Ensure the passed data is an array, and
 * return an empty array otherwise.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitHttpArrayConverter extends arbitHttpInputConverter
{
    /**
     * Default value
     *
     * Return the default value of the conversion class. The default value is
     * used, if the form is not valid, or no value has been provided.
     *
     * @return mixed
     */
    public static function defaultValue()
    {
        return array();
    }

    /**
     * Convert client input
     *
     * Convert and clean up client input.
     *
     * @param mixed $input
     * @return mixed
     */
    public static function convert( $input )
    {
        if ( !is_array( $input ) )
        {
            return array();
        }

        return $input;
    }
}

