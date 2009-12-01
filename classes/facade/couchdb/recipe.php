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

