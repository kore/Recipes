<?php
/**
 * recipe storage backend gateway
 *
 * This file is part of recipe.
 *
 * recipe is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * recipe is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with recipe; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @subpackage Gateway
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

namespace Recipes\Gateway\Cached;

use Recipes\Cache;
use Recipes\Gateway;

/**
 * Caching gateway.
 *
 * Wraps around another gateway and adds caching.
 *
 * @package Core
 * @subpackage Gateway
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class Recipe implements Gateway\Recipe
{
    /**
     * Cache
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Wrapped gateway
     *
     * @var Gateway\Recipe
     */
    protected $gateway;

    /**
     * Construct from cache and wrapped gateway
     *
     * @param Cache $cache
     * @param Gateway\Recipe $gateway
     * @return void
     */
    public function __construct( Cache $cache, Gateway\Recipe $gateway )
    {
        $this->cache   = $cache;
        $this->gateway = $gateway;
    }

    /**
     * Get full recipe list
     *
     * Return an alphabetical list of all recipes.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->gateway->getAll();
    }

    /**
     * Get tag list
     *
     * Return a list of tags, each associated with the number of occurences in
     * the recipes.
     *
     * @return array
     */
    public function getTags()
    {
        return $this->gateway->getTags();
    }

    /**
     * Get latest recipes
     *
     * Return a list of recipes with most recent changes
     *
     * @return array
     */
    public function getLatest( $count = 10 )
    {
        return $this->gateway->getLatest( $count );
    }

    /**
     * Get recipes by user
     *
     * Return a list of recipes per user
     *
     * @return array
     */
    public function getRecipesByUser()
    {
        return $this->gateway->getRecipesByUser();
    }

    /**
     * Get recipes by tag
     *
     * Return the IDs of all recipes, which contain the given tag,
     *
     * @param string $tag
     * @return array
     */
    public function getRecipesByTag( $tag )
    {
        return $this->gateway->getRecipesByTag( $tag );
    }

    /**
     * Get unit list
     *
     * Return a list of units, starting with the provided characters.
     * Sorted by popularity
     *
     * @param string $string
     * @return array
     */
    public function getUnits( $string )
    {
        return $this->gateway->getUnits( $string );
    }

    /**
     * Get ingredient list
     *
     * Return a list of ingredients, starting with the provided characters.
     * Sorted by popularity
     *
     * @param string $string
     * @return array
     */
    public function getIngredients( $string )
    {
        return $this->gateway->getIngredients( $string );
    }

    /**
     * Get recipes by ingredient
     *
     * Return the IDs of all recipes, which contain the given ingredient,
     *
     * @param string $ingredient
     * @return array
     */
    public function getRecipesByIngredient( $ingredient )
    {
        return $this->gateway->getRecipesByIngredient( $ingredient );
    }

    /**
     * Get recipe data
     *
     * Get data for the given recipe id. The data should be returned as an array,
     * and should contain the following keys:
     *  - id
     *  - title
     *  - amount
     *  - description
     *  - ingredients
     *  - preparation
     *  - cooking
     *  - instructions
     *  - user
     *  - tags
     *  - attachments
     *
     * @param string $recipe
     * @return array
     */
    public function getRecipeData( $recipe )
    {
        return $this->gateway->getRecipeData( $recipe );
    }

    /**
     * Get file contents
     *
     * Return the contents of the file specified by its storage file name.
     *
     * @param mixed $id
     * @param string $fileName
     * @return string
     */
    public function getFileContents( $id, $fileName )
    {
        return $this->gateway->getFileContents( $id, $fileName );
    }

    /**
     * Create a new recipe
     *
     * Create a new recipe with the given name. An exception will be thrown if
     * there already is a recipe with the given name.
     *
     * @param string $name
     * @return void
     */
    public function createRecipe( $name )
    {
        return $this->gateway->createRecipe( $name );
    }

    /**
     * Attach file to page
     *
     * @param string $recipe
     * @param string $file
     * @param string $name
     * @param string $mimeType
     * @return void
     */
    public function attachFile( $recipe, $file, $name, $mimeType )
    {
        return $this->gateway->attachFile( $recipe, $file, $name, $mimeType );
    }

    /**
     * Detach file from recipe
     *
     * @param string $recipe
     * @param string $file
     * @return void
     */
    public function detachFile( $recipe, $file )
    {
        return $this->gateway->detachFile( $recipe, $file );
    }

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
    public function updateRecipeData( $recipe, $data )
    {
        return $this->gateway->updateRecipeData( $recipe, $data );
    }

    /**
     * Method used to delete an instance from the backend.
     *
     * Method, which removes the current instance from the backend, without any
     * possibility to undo this.
     *
     * @param string $id
     * @return void
     */
    public function delete( $id )
    {
        return $this->gateway->delete( $id );
    }
}

