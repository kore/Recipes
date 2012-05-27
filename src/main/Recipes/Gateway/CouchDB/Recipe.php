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

namespace Recipes\Gateway\CouchDB;
use Recipes\Gateway;

/**
 * Recipe gateway defining all methods required to access recipe related data in the
 * backend.
 *
 * @package Core
 * @subpackage Gateway
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class Recipe implements Gateway\Recipe
{
    /**
     * PHPillow connection handler
     *
     * @var phpillowConnection
     */
    protected $connection;

    /**
     * PHPillow view
     *
     * @var phpillowView
     */
    protected $view;

    /**
     * Construct from conneciton and view
     *
     * @param phpillowConnection $connection
     * @param phpillowView $view
     * @return void
     */
    public function __construct( \phpillowConnection $connection, Gateway\CouchDB\Recipe\View $view )
    {
        $this->connection = $connection;
        $this->view       = $view;
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
        $result = $this->view->query( 'all', array( 'include_docs' => true ) );

        if ( !count( $result->rows ) )
        {
            return array();
        }

        $docs = array();
        foreach ( $result->rows as $row )
        {
            $docs[] = $row['doc'];
        }

        return $docs;
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
        $result = $this->view->query( 'tags', array(
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
     * Get latest recipes
     *
     * Return a list of recipes with most recent changes
     *
     * @return array
     */
    public function getLatest( $count = 10 )
    {
        $result = $this->view->query( 'latest', array(
            'descending'   => true,
            'include_docs' => true,
            'count'        => $count,
        ) );

        if ( !count( $result->rows ) )
        {
            return array();
        }

        $recipes = array();
        foreach ( $result->rows as $row )
        {
            $recipes[] = $row['doc'];
        }

        return $recipes;
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
        $result = $this->view->query( 'perUser', array( 'reduce' => true, 'group' => true ) );

        if ( !count( $result->rows ) )
        {
            return array();
        }

        $recipes = array();
        foreach ( $result->rows as $row )
        {
            $recipes[$row['key'][0]][] = $row['key'][1];
        }

        return $recipes;
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
        $result = $this->view->query( 'tags', array(
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
        $result = $this->view->query( 'units', array(
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
        $result = $this->view->query( 'ingredients', array(
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
     * Get recipes by ingredient
     *
     * Return the IDs of all recipes, which contain the given ingredient,
     *
     * @param string $ingredient
     * @return array
     */
    public function getRecipesByIngredient( $ingredient )
    {
        $result = $this->view->query( 'ingredients', array(
            'reduce' => false,
            'key'    => $ingredient,
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
        try
        {
            $doc = new Recipe\Document();
            $doc->fetchById( $recipe );
        }
        catch ( phpillowResponseNotFoundErrorException $e )
        {
            throw new recipeGatewayNotFoundException(
                "The recipe '%recipe' could not be found.",
                array(
                    'recipe' => $recipe,
                )
            );
        }

        return array(
            'id'           => $doc->_id,
            'title'        => $doc->title,
            'amount'       => $doc->amount,
            'description'  => $doc->description,
            'ingredients'  => $doc->ingredients,
            'preparation'  => $doc->preparation,
            'cooking'      => $doc->cooking,
            'instructions' => $doc->instructions,
            'user'         => $doc->user,
            'tags'         => $doc->tags,
            'attachments'  => $doc->_attachments,
        );
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
        try
        {
            $doc = new Recipe\Document();
            $doc->fetchById( $id );
        }
        catch ( phpillowResponseNotFoundErrorException $e )
        {
            throw new recipeGatewayNotFoundException(
                "The recipe '%recipe' could not be found.",
                array(
                    'recipe' => $id,
                )
            );
        }

        try
        {
            return $doc->getFile( $fileName );
        }
        catch ( phpillowNoSuchPropertyException $e )
        {
            throw new recipeGatewayNotFoundException(
                "The recipe attachment '%file' could not be found.",
                array(
                    'file' => $fileName,
                )
            );
        }
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
            $recipe = Recipe\Document::createNew();
            $recipe->title = $name;
            $recipe->save();
        }
        catch ( phpillowResponseConflictErrorException $e )
        {
            throw new recipeGatewayExistsException( $name );
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
            $doc = new Recipe\Document();
            $doc->fetchById( $recipe );
        }
        catch ( phpillowResponseNotFoundErrorException $e )
        {
            throw new recipeGatewayNotFoundException(
                "The recipe '%recipe' could not be found.",
                array(
                    'recipe' => $recipe,
                )
            );
        }

        // Get ID from user user model
        if ( isset( $data['user'] ) &&
             ( $data['user'] instanceof User ) )
        {
            $data['user'] = $data['user']->_id;
        }


        // Set data, which will be validated internally, and store.
        foreach ( $data as $key => $value )
        {
            $doc->$key = $value;
        }
        $doc->save();
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
        try
        {
            $doc = new Recipe\Document();
            $doc->fetchById( $id );
            $doc->delete();
        }
        catch ( phpillowResponseNotFoundErrorException $e )
        {
            throw new recipeGatewayNotFoundException(
                "The recipe '%recipe' could not be found.",
                array(
                    'recipe' => $recipe,
                )
            );
        }

    }
}

