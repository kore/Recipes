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
class arbitRecipeViewModel extends arbitViewModel
{
    /**
     * Array containing the actual view data.
     *
     * @var array
     */
    protected $properties = array(
        'id'           => null,
        'title'        => null,
        'amount'       => null,
        'description'  => null,
        'ingredients'  => array(),
        'preparation'  => null,
        'cooking'      => null,
        'instructions' => null,
        'user'         => null,
        'tags'         => array(),
    );

    /**
     * Construct project module view model from common values
     *
     * @param arbitRecipeModel $recipe
     * @return void
     */
    public function __construct( arbitRecipeModel $recipe = null )
    {
        if ( $recipe !== null )
        {
            foreach ( $this->properties as $name => $value )
            {
                if ( $name === 'id' )
                {
                    $this->id = $recipe->_id;
                    continue;
                }

                $this->$name = $recipe->$name;
            }
        }
    }
}

