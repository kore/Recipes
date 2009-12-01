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
        return new arbitViewUserMessageModel( 'Hello Recipes!' );
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
            arbitRecipeModel::getIngredients(
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
                    if ( !isset( $ingredient[$field] ) ||
                         empty( $ingredient[$field] ) )
                    {
                        continue 2;
                    }
                }

                $return[$title][] = $ingredient;
            }
        }

        return $return;
    }

    /**
     * Overview action
     *
     * Gives an overview on the currently available recipes
     *
     * @param arbitRequest $request
     * @return arbitViewModel
     */
    public function edit( arbitRequest $request )
    {
        $model  = new arbitRecipeCreateModel();
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
                $recipe->tags         = array();

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
                arbitCacheRegistry::getCache()->clearCache( 'tracker_reports' );
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
