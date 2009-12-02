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
class arbitCouchDbRecipeFacade extends arbitCouchDbFacadeBase implements arbitRecipeFacade
{
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
        $issues = phpillowManager::getView( 'recipe' );
        $result = $issues->query( 'tags', array(
            'group' => true,
        ) );

        if ( !count( $result->rows ) )
        {
            return array();
        }

        $tags = array();
        foreach ( $result->rows as $row )
        {
            $tags[$row['key']] = (int) is_array( $row['value'] ) ? reset( $row['value'] ) : $row['value'];
        }

        return $tags;
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
        $issues = phpillowManager::getView( 'recipe' );
        $result = $issues->query( 'tags', array(
            'reduce' => false,
            'key'    => $tag,
        ) );

        if ( !count( $result->rows ) )
        {
            return array();
        }

        $docs = array();
        foreach ( $result->rows as $row )
        {
            $docs[] = is_array( $row['value'] ) ? reset( $row['value'] ) : $row['value'];
        }

        return $docs;
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
        $issues = phpillowManager::getView( 'recipe' );
        $result = $issues->query( 'units', array(
            'group'    => true,
            'startkey' => $string,
            'endkey'   => $string . "\xE9\xA6\x99",
        ) );

        if ( !count( $result->rows ) )
        {
            return array();
        }

        $units = array();
        foreach ( $result->rows as $row )
        {
            $units[$row['key']] = (int) is_array( $row['value'] ) ? reset( $row['value'] ) : $row['value'];
        }
        arsort( $units );

        return array_splice( $units, 0, 10 );
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
        $issues = phpillowManager::getView( 'recipe' );
        $result = $issues->query( 'ingredients', array(
            'group'    => true,
            'startkey' => $string,
            'endkey'   => $string . "\xE9\xA6\x99",
        ) );

        if ( !count( $result->rows ) )
        {
            return array();
        }

        $ingredients = array();
        foreach ( $result->rows as $row )
        {
            $ingredients[$row['key']] = (int) is_array( $row['value'] ) ? reset( $row['value'] ) : $row['value'];
        }
        arsort( $ingredients );

        return array_splice( $ingredients, 0, 10 );
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
     *
     * @param string $recipe
     * @return array
     */
    public function getRecipeData( $recipe )
    {
        try
        {
            $doc = phpillowManager::fetchDocument( 'recipe', $recipe );
        }
        catch ( phpillowResponseNotFoundErrorException $e )
        {
            throw new arbitFacadeNotFoundException(
                "The recipe '%recipe' could not be found.",
                array(
                    'recipe' => $recipe,
                )
            );
        }

        return array(
            'title'        => $doc->title,
            'amount'       => $doc->amount,
            'description'  => $doc->description,
            'ingredients'  => $doc->ingredients,
            'preparation'  => $doc->preparation,
            'cooking'      => $doc->cooking,
            'instructions' => $doc->instructions,
            'user'         => $doc->user,
            'tags'         => $doc->tags,
        );
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
        try
        {
            $recipe = phpillowManager::createDocument( 'recipe' );
            $recipe->title = $name;
            $recipe->save();
        }
        catch ( phpillowResponseConflictErrorException $e )
        {
            throw new arbitFacadeRecipeExistsException( $name );
        }

        // Return generated ID
        return $recipe->_id;
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
        try
        {
            $doc = phpillowManager::fetchDocument( 'recipe', $recipe );
        }
        catch ( phpillowResponseNotFoundErrorException $e )
        {
            throw new arbitFacadeNotFoundException(
                "The recipe '%recipe' could not be found.",
                array(
                    'recipe' => $recipe,
                )
            );
        }

        // Set data, which will be validated internally, and store.
        foreach ( $data as $key => $value )
        {
            $doc->$key = $value;
        }
        $doc->save();
    }
}

