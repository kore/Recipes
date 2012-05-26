<?php
/**
 * This file is part of recipes
 *
 * recipes is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * recipes is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with recipes; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @version $Revision: 1469 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

namespace Recipes\Controller;

use Recipes\Struct;
use Recipes\Model;
use Qafoo\RMF;

/**
 * Recipe controller
 *
 * @version $Revision$
 */
class Recipe
{
    /**
     * Recipe model
     *
     * @var Model\Recipe
     */
    protected $model;

    /**
     * Construct from recipe model
     *
     * @param Model\Recipe $model
     * @return void
     */
    public function __construct( Model\Recipe $model )
    {
        $this->model = $model;
    }

    /**
     * Show recipe overview
     *
     * @param RMF\Request $request
     * @return Struct\Response
     */
    public function showOverview( RMF\Request $request )
    {
        return new Struct\Overview(
            $this->model->getMostPopularTags( 30 ),
            $this->model->getTags()
        );
    }

    /**
     * Tag listing action
     *
     * Provides an overview on the used tags
     *
     * @param RMF\Request $request
     * @return recipeViewModel
     */
    public function tags( RMF\Request $request )
    {
        $grouped = array();
        foreach ( $this->model->getTags() as $tag => $value )
        {
            if ( $tag )
            {
                $grouped[strtolower( $tag[0] )][] = $tag;
            }
        }

        return new Struct\Tags(
            $this->model->getMostPopularTags( 30 ),
            $grouped
        );
    }

    /**
     * Full alphabetical index
     *
     * Provides an alphabetical index of all recipes
     *
     * @param RMF\Request $request
     * @return recipeViewModel
     */
    public function all( RMF\Request $request )
    {
        $grouped = array();
        foreach ( $this->model->getAll() as $recipe )
        {
            if ( $recipe )
            {
                $grouped[strtolower( $recipe->title[0] )][] = $recipe;
            }
        }

        return new Struct\Recipes(
            $grouped
        );
    }

    /**
     * Tag listing action
     *
     * Lists all recipes, which belong to a tag
     *
     * @param RMF\Request $request
     * @return recipeViewModel
     */
    public function tag( RMF\Request $request )
    {
        return new Struct\Tag(
            $request->variables['tag'],
            $this->model->getRecipesByTag( $request->variables['tag'] )
        );
    }

    /**
     * Ingredient listing action
     *
     * Lists all recipes, which belong to a ingredient
     *
     * @param RMF\Request $request
     * @return recipeViewModel
     */
    public function ingredient( RMF\Request $request )
    {
        $ingredient = urldecode( $request->variables['ingredient'] );
        return new Struct\Ingredient(
            $ingredient,
            $this->model->getRecipesByIngredient( $ingredient )
        );
    }

    /**
     * Units actions
     *
     * Returns a list of the units, starting with the string, specified
     * in the subaction
     *
     * @param RMF\Request $request
     * @return recipeViewModel
     */
    public function units( RMF\Request $request )
    {
        return new recipeViewListModel(
            $this->model->getUnits(
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
     * @param RMF\Request $request
     * @return recipeViewModel
     */
    public function ingredients( RMF\Request $request )
    {
        return new recipeViewListModel(
            $this->model->getIngredients(
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
     * @param RMF\Request $request
     * @return recipeViewModel
     */
    public function view( RMF\Request $request )
    {
        if ( strlen( $request->path ) > 1 )
        {
            $recipe = $this->model->load( $request->variables['recipe'] );
        }

        return new Struct\Recipe( $recipe );
    }

    /**
     * List exports
     *
     * Provide a list of available export formats for the recipe
     *
     * @param RMF\Request $request
     * @return recipeViewModel
     */
    public function listExports( RMF\Request $request )
    {
        return new recipeViewRecipeExportModel(
            new recipeRecipeModel( $request->subaction )
        );
    }

    /**
     * Return a normalized name
     *
     * Return a normalized anme, which can be used as a filename on most file
     * systems.
     *
     * @param string $string
     * @return string
     */
    public function normalizeName( $string )
    {
        $string = iconv( 'UTF-8', 'ASCII//TRANSLIT', $string );
        return trim( preg_replace( '([^A-Za-z0-9]+)', '_', $string ), '_' );
    }

    /**
     * Export action
     *
     * Allows to export a given recipe to a selected format, and a given amount
     * of portions.
     *
     * @param RMF\Request $request
     * @return recipeViewModel
     */
    public function export( RMF\Request $request )
    {
        $recipe   = new recipeRecipeModel( $request->subaction );
        $docbook  = $recipe->getAsDocbook( $request );
        $filename = $this->normalizeName( $recipe->title ) . $request->variables['format'];

        switch ( $request->variables['format'] )
        {
            case '.html':
                $html = new ezcDocumentXhtml();
                $html->createFromDocbook( $docbook->getAsDocbook() );
                return new recipeViewDataModel( (string) $html, 'text/html', $filename );

            case '.txt':
                $txt = new ezcDocumentRst();
                $txt->createFromDocbook( $docbook->getAsDocbook() );
                return new recipeViewDataModel( (string) $txt, 'text/text', $filename );

            case '.odt':
                $converter = new ezcDocumentDocbookToOdtConverter();
                $converter->options->styler->addStylesheetFile( __DIR__ . '/recipe.css' );
                $odt = $converter->convert( $docbook );
                return new recipeViewDataModel( (string) $odt, 'application/vnd.oasis.opendocument.text', $filename );

            case '.xml':
                return new recipeViewDataModel( (string) $docbook, 'application/docbook+xml', $filename );

            case '.pdf':
            default:
                $options = new ezcDocumentPdfOptions();
                $options->driver = new ezcDocumentPdfTcpdfDriver();
                $pdf = new ezcDocumentPdf( $options );
                $pdf->loadStyles( __DIR__ . '/recipe.css' );
                $pdf->options->errorReporting = E_PARSE | E_ERROR;
                $pdf->createFromDocbook( $docbook->getAsDocbook() );
                return new recipeViewDataModel( (string) $pdf, 'application/pdf', $filename );
        }
    }

    /**
     * Delete action
     *
     * Allows registered users to remove a recipe
     *
     * @param RMF\Request $request
     * @return recipeViewModel
     */
    public function delete( RMF\Request $request )
    {
        $recipe = new recipeRecipeModel( $request->subaction );
        $recipe->delete();

        return new recipeViewUserMessageModel( "Recipe deleted." );
    }

    /**
     * Delete action
     *
     * Allows registered users to remove a recipe
     *
     * @param RMF\Request $request
     * @return recipeViewModel
     */
    public function attach( RMF\Request $request )
    {
        $errors = array();
        if ( recipeHttpTools::get( 'attach' ) !== null )
        {
            try
            {
                $files = recipeHttpTools::getFiles( 'attachment' );
                recipeLogger::dump( $files );
                $recipe  = new recipeRecipeModel( $request->subaction );
                $recipe->attachFile(
                    recipeHttpTools::get( 'name' ), key( $files ), reset( $files )
                );

                return new recipeViewRecipeViewModel(
                    new recipeRecipeModel( $request->subaction )
                );
            }
            catch ( recipeException $e )
            {
                $errors[] = $e;
            }
        }

        return new recipeViewRecipeViewModel(
            new recipeRecipeModel( $request->subaction ),
            $errors
        );
    }

    /**
     * Delete action
     *
     * Allows registered users to remove a recipe
     *
     * @param RMF\Request $request
     * @return recipeViewModel
     */
    public function listRecipe( RMF\Request $request )
    {
        $list = recipeSession::is_set( 'list' ) ? recipeSession::get( 'list' ) : array();

        if ( $request->subaction !== 'index' )
        {
            $recipe = new recipeRecipeModel( $request->subaction );
            $list[$recipe->_id] = $recipe->amount;
            recipeSession::set( 'list', $list );
        }

        if ( recipeHttpTools::get( 'update' ) !== null )
        {
            $list = recipeHttpTools::get( 'amount', recipeHttpTools::TYPE_ARRAY );

            foreach ( $list as $recipe => $amount )
            {
                if ( $amount <= 0 )
                {
                    unset( $list[$recipe] );
                }
                elseif ( preg_match( '((?P<top>\\d+)\\s*/\\s*(?P<bottom>\\d+))', trim( $amount ), $match ) )
                {
                    $list[$recipe] = $match['top'] / $match['bottom'];
                }
                else
                {
                    $list[$recipe] = (float) $amount;
                }
            }

            recipeSession::set( 'list', $list );
        }

        return new recipeViewRecipeListModel( $list );
    }

    /**
     * Get search seassion
     *
     * Get the search session based on the configuration values.
     *
     * @param RMF\Request $request
     * @return ezcSearchSession
     */
    protected function getSearchSession( RMF\Request $request )
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
     * @param RMF\Request $request
     * @return recipeViewModuleModel
     */
    public function search( RMF\Request $request )
    {
        $searchTerm = isset( $request->variables['search'] ) ? $request->variables['search'] : null;

        if ( $searchTerm !== null )
        {
            $offset = isset( $request->variables['offset'] ) ? (int) $request->variables['offset'] : 0;
            $limit  = 10;

            $search = $this->getSearchSession( $request );
            $query  = $search->createFindQuery( 'recipeRecipeModel' );

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

        return new recipeViewRecipeSearchModel(
            $searchTerm,
            new recipeViewSearchResultModel( $result ),
            $offset
        );
    }

    /**
     * Edit action
     *
     * Allows registered users to edit a recipe
     *
     * @param RMF\Request $request
     * @return recipeViewModel
     */
    public function edit( RMF\Request $request )
    {
        $model  = new recipeViewRecipeEditModel();
        $id     = $request->subaction !== 'index' ? $request->subaction : null;
        $recipe = new recipeRecipeModel( $id );

        if ( recipeHttpTools::get( 'store' ) !== null )
        {
            try
            {
                $recipe->title        = recipeHttpTools::get( 'title' );
                $recipe->amount       = (int) recipeHttpTools::get( 'amount', recipeHttpTools::TYPE_NUMERIC );
                $recipe->description  = recipeHttpTools::get( 'description' );
                $recipe->ingredients  = $this->convertIngredientList( recipeHttpTools::get( 'ingredients', recipeHttpTools::TYPE_ARRAY ) );
                $recipe->preparation  = (int) recipeHttpTools::get( 'preparation', recipeHttpTools::TYPE_NUMERIC );
                $recipe->cooking      = (int) recipeHttpTools::get( 'cooking', recipeHttpTools::TYPE_NUMERIC );
                $recipe->instructions = recipeHttpTools::get( 'instructions' );
                $recipe->tags         = preg_split( '(\s*,\s*)', trim( recipeHttpTools::get( 'tags' ) ) );

                // Force creation, if it is a new recipe
                if ( $id === null )
                {
                    $recipe->create();
                    $id = $recipe->_id;
                }

                $recipe->storeChanges();

                $model->success = array( new recipeViewUserMessageModel( 'Your recipe has been successfully stored.' ) );

                // Update recipe in search index
                $search = $this->getSearchSession( $request );
                $search->index( $recipe );
            }
            catch ( recipeException $e )
            {
                $model->errors = array( $e );
            }
        }

        // Assign recipe to model to keep already validated data.
        if ( $id )
        {
            $model->recipe = new recipeRecipeViewModel( $recipe );
        }

        return $model;
    }
}

