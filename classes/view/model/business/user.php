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
 * Base model struct for all project module views
 *
 * @package Core
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitViewUserModel extends arbitViewModel
{
    /**
     * Array containing the actual view data.
     *
     * @var array
     */
    protected $properties = array(
        'id'          => null,
        'login'       => null,
        'email'       => null,
        'name'        => null,
        'valid'       => null,
        'auth_type'   => null,
        'privileges'  => array(),
        'settings'    => null,
    );

    /**
     * Construct project module view model from common values
     *
     * @param arbitModelUser $user
     * @return void
     */
    public function __construct( arbitModelUser $user = null )
    {
        if ( $user !== null )
        {
            foreach ( $this->properties as $name => $value )
            {
                if ( $name === 'id' )
                {
                    $this->id = $user->_id;
                    continue;
                }

                $this->$name = $user->$name;
            }
        }

        // Default to the login, if no name has been provided.
        if ( empty( $this->name ) )
        {
            $this->name = $this->login;
        }
    }

    /**
     * To string method for user view model
     *
     * Return the user name as a string, if somebody tries to convert the user
     * model into a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}

