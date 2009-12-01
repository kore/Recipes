<?php
/**
 * arbit storage backend facade
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
 * @subpackage Facade
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Recipe facade defining all methods required to access recipe related data in the
 * backend.
 *
 * @package Core
 * @subpackage Facade
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
interface arbitRecipeFacade
{
    /**
     * Get unit list
     *
     * Return a lsit of units, starting with the provided characters. 
     * Sorted by popularity
     *
     * @param string $string
     * @return array
     */
    public function getUnits( $string );

    /**
     * Get ingredient list
     *
     * Return a lsit of ingredients, starting with the provided characters. 
     * Sorted by popularity
     *
     * @param string $string
     * @return array
     */
    public function getIngredients( $string );

    /**
     * Get recipe data
     *
     * Get data for the given recipe id. The data should be returned as an array,
     * and should contain the following keys:
     *  - title
     *  - amount
     *  - description
     *  - ingredients
     *  - preparation
     *  - cooking
     *  - instructions
     *  - user
     *  - tags
     *
     * @param string $recipe
     * @return array
     */
    public function getRecipeData( $recipe );

    /**
     * Create a new recipe
     *
     * Create a new recipe with the given name. An exception will be thrown if
     * there already is a recipe with the given name.
     *
     * The ID generated by the backend in some way is returned for later
     * reference. The type of the identifier depends on the backend, no
     * assumptions should be made here.
     *
     * @param string $name
     * @return mixed
     */
    public function createRecipe( $name );

    /**
     * Update stored information for the given recipe
     *
     * The array with the information to update may any number of the common
     * keys, and only the given keys will be updated in the storage backend.
     *
     * @param string $recipe
     * @param array $data
     * @return void
     */
    public function updateRecipeData( $recipe, $data );
}

