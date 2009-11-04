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
 * Base model struct for all project views
 *
 * @package Core
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class arbitViewCoreModel extends arbitViewModel
{
    /**
     * Set property in model
     *
     * Check for property values on setting in view model. Performs an
     * automatic conversion of arbitModelGroup objects to arbitViewGroupModel
     * for the groups property and arbitModelUser objects to arbitViewUserModel
     * for the users property.
     *
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public function __set( $property, $value )
    {
        // We always accept null as a vaqlue for all properties.
        if ( $value === null )
        {
            $this->properties[$property] = $value;
            return;
        }

        switch ( $property )
        {
            case 'groups':
                if ( !is_array( $value ) )
                {
                    throw new arbitPropertyValidationException( $property, 'array( arbitViewGroupModel )' );
                }

                foreach ( $value as $nr => $user )
                {
                    // Convert all arbitModelGroup objects to their respective
                    // view model structure, and throw an error for all invalid
                    // contents.
                    if ( $user instanceof arbitModelGroup )
                    {
                        $value[$nr] = new arbitViewGroupModel( $user );
                    }
                    elseif ( !$user instanceof arbitViewGroupModel )
                    {
                        throw new arbitPropertyValidationException( $property, 'array( arbitViewGroupModel )' );
                    }
                }

                $this->properties[$property] = $value;
                break;

            case 'users':
                if ( !is_array( $value ) )
                {
                    throw new arbitPropertyValidationException( $property, 'array( arbitViewUserModel )' );
                }

                foreach ( $value as $nr => $user )
                {
                    // Convert all arbitModelUser objects to their respective
                    // view model structure, and throw an error for all invalid
                    // contents.
                    if ( $user instanceof arbitModelUser )
                    {
                        $value[$nr] = new arbitViewUserModel( $user );
                    }
                    elseif ( !$user instanceof arbitViewUserModel )
                    {
                        throw new arbitPropertyValidationException( $property, 'array( arbitViewUserModel )' );
                    }
                }

                $this->properties[$property] = $value;
                break;

            default:
                return parent::__set( $property, $value );
        }
    }
}

