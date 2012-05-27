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
     * User model
     *
     * @var Model\User
     */
    protected $userModel;

    /**
     * Twig
     *
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * Construct from recipe model
     *
     * @param Model\Recipe $model
     * @return void
     */
    public function __construct( Model\Recipe $model, Model\User $userModel, \Twig_Environment $twig )
    {
        $this->model     = $model;
        $this->userModel = $userModel;
        $this->twig      = $twig;
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
            $this->model->getLatest()
        );
    }

    /**
     * Show my recipes
     *
     * @param RMF\Request $request
     * @return Struct\Response
     */
    public function mine( RMF\Request $request )
    {
        $recipes = $this->model->getRecipesByUser();

        $result = new Struct\Mine();
        foreach ( $recipes as $user => $set )
        {
            $grouped = array();
            foreach ( $set as $recipe )
            {
                if ( $recipe )
                {
                    $grouped[strtolower( $recipe->title[0] )][] = $recipe;
                }
            }

            if ( $user === $request->session['user'] )
            {
                $result->mine = $grouped;
            }
            else
            {
                $result->perUser[$user] = $grouped;
            }
        }

        foreach ( $result->perUser as $user => $recipes )
        {
            $result->user[$user] = $this->userModel->load( $user );
        }

        return $result;
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
        $tag = urldecode( $request->variables['tag'] );
        return new Struct\Tag(
            $tag,
            $this->model->getRecipesByTag( $tag )
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
        return new Struct\Listing(
            $this->model->getUnits(
                urldecode( $request->variables['unit'] )
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
        return new Struct\Listing(
            $this->model->getIngredients(
                urldecode( $request->variables['ingredient'] )
            )
        );
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
        return new Struct\Export(
            $request->variables['recipe']
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
        $recipe   = $this->model->load( $request->variables['recipe'] );
        $filename = $this->normalizeName( $recipe->title ) . '.' . $request->variables['format'];

        $docbookXml = $this->twig->render( 'docbook.twig', array( 'recipe' => $recipe ) );
        $docbook    = new \ezcDocumentDocbook();
        $docbook->loadString( $docbookXml );

        switch ( $request->variables['format'] )
        {
            case 'html':
                $html = new \ezcDocumentXhtml();
                $html->createFromDocbook( $docbook );
                return new Struct\File( (string) $html, 'text/html', $filename );

            case 'txt':
                $txt = new \ezcDocumentRst();
                $txt->createFromDocbook( $docbook );
                return new Struct\File( (string) $txt, 'text/text', $filename );

            case 'odt':
                $converter = new \ezcDocumentDocbookToOdtConverter();
                $converter->options->styler->addStylesheetFile( __DIR__ . '/../../../config/recipe.css' );
                $odt = $converter->convert( $docbook );
                return new Struct\File( (string) $odt, 'application/vnd.oasis.opendocument.text', $filename );

            case 'xml':
                return new Struct\File( $docbook, 'application/docbook+xml', $filename );

            case 'pdf':
            default:
                $options = new \ezcDocumentPdfOptions();
                $options->driver = new \ezcDocumentPdfTcpdfDriver();
                $pdf = new \ezcDocumentPdf( $options );
                $pdf->loadStyles( __DIR__ . '/../../../config/recipe.css' );
                $pdf->options->errorReporting = E_PARSE | E_ERROR;
                $pdf->createFromDocbook( $docbook );
                return new Struct\File( (string) $pdf, 'application/pdf', $filename );
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
        $recipe = $this->model->load( $request->variables['recipe'] );
        $recipe->delete();

        return new Struct\Message( "Recipe deleted." );
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
        if ( $request->body['attach'] !== null )
        {
            try
            {
                $files = recipeHttpTools::getFiles( 'attachment' );
                recipeLogger::dump( $files );
                $recipe  = new recipeRecipeModel( $request->subaction );
                $recipe->attachFile(
                    $request->body['name'], key( $files ), reset( $files )
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
     * Edit action
     *
     * Allows registered users to edit a recipe
     *
     * @param RMF\Request $request
     * @return recipeViewModel
     */
    public function edit( RMF\Request $request )
    {
        $result = new Struct\Edit();
        $recipe = $this->model;
        $id     = null;
        if ( isset( $request->variables['recipe'] ) )
        {
            $id = $request->variables['recipe'];
            $recipe = $recipe->load( $id );
            $result->recipe = $recipe;
            $result->ingredients = $recipe->ingredients ? json_encode( $recipe->ingredients ) : 'null';
        }

        if ( !isset( $request->body['store'] ) )
        {
            return $result;
        }

        try
        {
            $recipe->title        = $request->body['title'];
            $recipe->amount       = (int) $request->body['amount'];
            $recipe->description  = $request->body['description'];
            $recipe->ingredients  = $this->convertIngredientList( $request->body['ingredients'] );
            $recipe->preparation  = (int) $request->body['preparation'];
            $recipe->cooking      = (int) $request->body['cooking'];
            $recipe->instructions = $request->body['instructions'];
            $recipe->tags         = preg_split( '(\s*,\s*)', trim( $request->body['tags'] ) );
            $recipe->user         = $request->session['user'];

            // Force creation, if it is a new recipe
            if ( $id === null )
            {
                $recipe->create();
                $id = $recipe->_id;
            }

            $recipe->storeChanges();
            $result->success = 'Your recipe has been successfully stored.';
        }
        catch ( \Exception $e )
        {
            $result->errors[] = $e->getMessage();
        }

        $result->recipe = $recipe;
        $result->ingredients = $recipe->ingredients ? json_encode( $recipe->ingredients ) : 'null';
        return $result;
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
}

