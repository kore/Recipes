<?php
/**
 * arbit core controller
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
 * Main controller for the arbit project tracker, implementing all user and
 * group related functionality.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitRecipeController extends arbitController
{
    /**
     * Index action
     *
     * Dispatches to the default action
     *
     * @param arbitRequest $request
     * @return arbitViewModel
     */
    public function index( arbitRequest $request )
    {
        return $this->overview( $request );
    }

    /**
     * Overview action
     *
     * Gives an overview on the currently available recipes
     *
     * @param arbitRequest $request
     * @return arbitViewModel
     */
    public function overview( arbitRequest $request )
    {
        return new arbitViewRecipeOverviewModel(
            arbitRecipeModel::getMostPopularTags( 20 ),
            arbitRecipeModel::getTags()
        );
    }

    /**
     * Tag listing action
     *
     * Provides an overview on the used tags
     *
     * @param arbitRequest $request
     * @return arbitViewModel
     */
    public function tags( arbitRequest $request )
    {
        return new arbitViewRecipeTagsModel(
            arbitRecipeModel::getMostPopularTags( 20 ),
            arbitRecipeModel::getTags()
        );
    }

    /**
     * Tag listing action
     *
     * Lists all recipes, which belong to a tag
     *
     * @param arbitRequest $request
     * @return arbitViewModel
     */
    public function tag( arbitRequest $request )
    {
        return new arbitViewRecipeTagModel(
            $request->subaction,
            arbitRecipeModel::getRecipesByTag( $request->subaction )
        );
    }

    /**
     * Ingredient listing action
     *
     * Lists all recipes, which belong to a ingredient
     *
     * @param arbitRequest $request
     * @return arbitViewModel
     */
    public function ingredient( arbitRequest $request )
    {
        return new arbitViewRecipeIngredientModel(
            $request->subaction,
            arbitRecipeModel::getRecipesByIngredient( $request->subaction )
        );
    }

    /**
     * Units actions
     *
     * Returns a list of the units, starting with the string, specified 
     * in the subaction
     *
     * @param arbitRequest $request
     * @return arbitViewModel
     */
    public function units( arbitRequest $request )
    {
        return new arbitViewListModel(
            arbitRecipeModel::getUnits(
                $request->subaction === 'index' ? '' : $request->subaction
            )
        );
    }

    /**
     * Ingredients actions
     *
     * Returns a list of the ingredients, starting with the string, specified 
     * in the subaction
     *
     * @param arbitRequest $request
     * @return arbitViewModel
     */
    public function ingredients( arbitRequest $request )
    {
        return new arbitViewListModel(
            arbitRecipeModel::getIngredients(
                $request->subaction === 'index' ? '' : $request->subaction
            )
        );
    }

    /**
     * Convert ingredient list
     *
     * Converts the ingredient list, passed from the HTML view into the format, 
     * used by the model
     * 
     * @param array $ingredients 
     * @return void
     */
    protected function convertIngredientList( array $ingredients )
    {
        $return = array();
        foreach ( $ingredients as $category )
        {
            if ( !isset( $category['title'] ) )
            {
                continue;
            }

            $title = $category['title'];
            unset( $category['title'] );
            
            foreach ( $category as $ingredient )
            {
                foreach ( array( 'amount', 'unit', 'ingredient' ) as $field )
                {
                    if ( !isset( $ingredient[$field] ) )
                    {
                        continue 2;
                    }
                }

                if ( empty( $ingredient['ingredient'] ) )
                {
                    continue;
                }

                $return[$title][] = $ingredient;
            }
        }

        return $return;
    }

    /**
     * View action
     *
     * Allows registered users to view a recipe
     *
     * @param arbitRequest $request
     * @return arbitViewModel
     */
    public function view( arbitRequest $request )
    {
        return new arbitViewRecipeViewModel(
            new arbitRecipeModel( $request->subaction )
        );
    }

    /**
     * Get search seassion
     *
     * Get the search session based on the configuration values.
     *
     * @param arbitRequest $request
     * @return ezcSearchSession
     */
    protected function getSearchSession( arbitRequest $request )
    {
        $path = ARBIT_CACHE_PATH . 'search/' . $request->controller . '/';

        if ( !file_exists( $path ) )
        {
            mkdir( $path, 0777, true );
            Zend_Search_Lucene::create( $path );
        }

        $handler = new ezcSearchZendLuceneHandler( $path );
        
        $manager = new ezcSearchXmlManager( __DIR__ . '/../../search/' );
        return new ezcSearchSession( $handler, $manager );
    }

    /**
     * Search action
     *
     * Search bug reports
     *
     * @param arbitRequest $request
     * @return arbitViewModuleModel
     */
    public function search( arbitRequest $request )
    {
        $searchTerm = isset( $request->variables['search'] ) ? $request->variables['search'] : null;

        if ( $searchTerm !== null )
        {
            $offset = isset( $request->variables['offset'] ) ? (int) $request->variables['offset'] : 0;
            $limit  = 10;
         
            $search = $this->getSearchSession( $request );
            $query  = $search->createFindQuery( 'arbitRecipeModel' );

            $queryBuilder  = new ezcSearchQueryBuilder();
            $queryBuilder->parseSearchQuery( $query, $searchTerm, array( 'title', 'description', 'instructions' ) ); 
            $query->offset = $offset;
            $query->limit  = 10;
            $result        = $search->find( $query );
        }
        else
        {
            $result = new ezcSearchResult();
            $offset = 0;
        }

        return new arbitViewRecipeSearchModel(
            $searchTerm,
            new arbitViewSearchResultModel( $result ),
            $offset
        );
    }

    /**
     * Edit action
     *
     * Allows registered users to edit a recipe
     *
     * @param arbitRequest $request
     * @return arbitViewModel
     */
    public function edit( arbitRequest $request )
    {
        $model  = new arbitViewRecipeEditModel();
        $id     = $request->subaction !== 'index' ? $request->subaction : null;
        $recipe = new arbitRecipeModel( $id );

        if ( arbitHttpTools::get( 'store' ) !== null )
        {
            try
            {
                $recipe->title        = arbitHttpTools::get( 'title' );
                $recipe->amount       = (int) arbitHttpTools::get( 'amount', arbitHttpTools::TYPE_NUMERIC );
                $recipe->description  = arbitHttpTools::get( 'description' );
                $recipe->ingredients  = $this->convertIngredientList( arbitHttpTools::get( 'ingredients', arbitHttpTools::TYPE_ARRAY ) );
                $recipe->preparation  = (int) arbitHttpTools::get( 'preparation', arbitHttpTools::TYPE_NUMERIC );
                $recipe->cooking      = (int) arbitHttpTools::get( 'cooking', arbitHttpTools::TYPE_NUMERIC );
                $recipe->instructions = arbitHttpTools::get( 'instructions' );
                $recipe->tags         = preg_split( '(\s*,\s*)', trim( arbitHttpTools::get( 'tags' ) ) );

                // Force creation, if it is a new recipe
                if ( $id === null )
                {
                    $recipe->create();
                    $id = $recipe->_id;
                }

                $recipe->storeChanges();

                $model->success = array( new arbitViewUserMessageModel( 'Your recipe has been successfully stored.' ) );

                // Update recipe in search index
                $search = $this->getSearchSession( $request );
                $search->index( $recipe );
            }
            catch ( arbitException $e )
            {
                $model->errors = array( $e );
            }
        }

        // Assign recipe to model to keep already validated data.
        if ( $id )
        {
            $model->recipe = new arbitRecipeViewModel( $recipe );
        }

        return $model;
    }
}

