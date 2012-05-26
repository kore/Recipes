<?php
/**
 * recipe model
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
 * @subpackage Model
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

namespace Recipes\Model;
use Recipes\Gateway;
use Recipes\Model;

/**
 * Recipe model
 *
 * @package Core
 * @subpackage Model
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class Recipe extends Model
{
    /**
     * Array containing the recipes properties
     *
     * @var array
     */
    protected $properties = array(
        'id'           => null,
        'title'        => null,
        'amount'       => null,
        'description'  => null,
        'ingredients'  => null,
        'preparation'  => null,
        'cooking'      => null,
        'instructions' => null,
        'user'         => null,
        'tags'         => null,
        'attachments'  => array(),
    );

    /**
     * Method from the model class implementation used to fetch the requested
     * data value for the constructed model. The method is called lazy, when
     * the data is actually requested from the model.
     *
     * The here given method is used, when there is nor special callback
     * defined in the $specialFetchMethods array.
     *
     * @var string
     */
    protected $defaultFetchMethod = 'fetchRecipeData';

    /**
     * Properties containing user IDs
     *
     * Properties containing user IDs, which should be transformed into user
     * models. If the revision property is set with older revisions of the
     * current model, all properties with this name are also transformed.
     *
     * @var array
     */
    protected $userModelProperties = array(
        'user',
    );

    /**
     * recipe gateway
     *
     * @var Gateway\Recipe
     */
    protected $gateway;

    /**
     * Construct from recipe gateway
     *
     * @param Gateway\Recipe $gateway
     * @return void
     */
    public function __construct( Gateway\Recipe $gateway, $id = null )
    {
        parent::__construct( $id );
        $this->gateway = $gateway;
    }

    /**
     * Get tags
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
     * Get most popular tags
     *
     * Return a the $count most popular tags, sorted alphabetcally.
     *
     * @return array
     */
    public function getMostPopularTags( $count = 30 )
    {
        $tags = $this->gateway->getTags();
        arsort( $tags );
        $mostPopular = array_splice( $tags, 0, $count );
        ksort( $mostPopular );
        return $mostPopular;
    }

    /**
     * Get all recipes
     *
     * Return an alphabetical list of all recipes.
     *
     * @return array
     */
    public function getAll()
    {
        $recipes = $this->gateway->getAll();
        $gateway = $this->gateway;
        return array_map( function( $doc ) use ( $gateway )
            {
                $recipe = new Recipe( $gateway, $doc['_id'] );

                foreach ( $doc as $property => $value )
                {
                    $recipe->properties[$property] = $value;
                }

                return $recipe;
            },
            $recipes
        );
    }

    /**
     * Get recipes by tag
     *
     * Return recipe models of all recipes, which contain the given tag,
     *
     * @param string $tag
     * @return array
     */
    public function getRecipesByTag( $tag )
    {
        $recipes = $this->gateway->getRecipesByTag( $tag );
        $gateway = $this->gateway;
        return array_map( function( $id ) use ( $gateway )
            {
                return new Recipe( $gateway, $id );
            },
            $recipes
        );
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
    public function getUnits( $string = '' )
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
    public function getIngredients( $string = '' )
    {
        return $this->gateway->getIngredients( $string );
    }

    /**
     * Get recipes by ingredient
     *
     * Return recipe models of all recipes, which contain the given ingredient,
     *
     * @param string $ingredient
     * @return array
     */
    public function getRecipesByIngredient( $ingredient )
    {
        $recipes = $this->gateway->getRecipesByIngredient( $ingredient );
        return array_map( function( $id )
            {
                return new recipeRecipeModel( $id );
            },
            $recipes
        );
    }

    /**
     * Load recipe
     *
     * Return recipe model
     *
     * @param string $ingredient
     * @return Recipe
     */
    public function load( $id )
    {
        return new Recipe( $this->gateway, $id );
    }

    /**
     * Return a docbook representation of the recipe.
     * 
     * @param recipeRequest $request 
     * @return ezcDocumentDocbook
     */
    public function getAsDocbook( recipeRequest $request )
    {
        $viewHandler = new recipeViewDocbookHandler( $request );

        // Let view handler generate output
        $docbook  = $viewHandler->showRecipe( new recipeRecipeViewModel( $this ) );
        $document = new ezcDocumentDocbook();
        $document->loadString( $docbook );

        return $document;
    }

    /**
     * Method called to create a new instance in the backend.
     *
     * Method called when the model should be created in the backend the first
     * time. This will normally throw an error if a model with the same
     * identifier already exists in the backend.
     *
     * Returns the backend dependant identifier of the created recipe.
     *
     * @return mixed
     */
    public function create()
    {
        return $this->id = $this->gateway->createRecipe( $this->title );
    }

    /**
     * Method used to delete an instance from the backend.
     *
     * Method, which removes the current instance from the backend, without any
     * possibility to undo this.
     *
     * @return void
     */
    public function delete()
    {
        $this->gateway->delete( $this->id );
    }

    /**
     * Fetch the basic recipe data
     *
     * Fetch the basic recipe data
     *
     * @return void
     */
    protected function fetchRecipeData()
    {
        $data = $this->gateway->getRecipeData( $this->id );

        foreach ( $data as $key => $value )
        {
            if ( $value !== null )
            {
                $this->properties[$key] = $value;
            }
        }
    }

    /**
     * Attach file to page
     *
     * @param string $name
     * @param string $fileName
     * @param array $info
     * @return void
     */
    public function attachFile( $name, $fileName, array $info = array() )
    {
        // Generate normalized file name
        $name = preg_replace( '([^A-Za-z0-9-]+)', '_', $name ) . $info['extension'];

        // Rename file and guess its mime type
        rename( $fileName, $fileName = ARBIT_TMP_PATH . $name );
        $info['type'] = recipeFrameworkMimeTypeGuesser::guess( $fileName, $info['type'] );

        $this->gateway->attachFile( $this->id, $fileName, $info['type'] );

        // Remove file from temp dir
        unlink( $fileName );
    }

    /**
     * Get attachment contents
     *
     * Return the contents of the attachment specified by its storage file
     * name.
     *
     * @param string $name
     * @return string
     */
    public function getAttachment( $name )
    {
        return $this->gateway->getFileContents( $this->id, $name );
    }

    /**
     * Method called to store changes to the model.
     *
     * Method called to store changes in the model to the backend. The method
     * should only modify the backend data, if something really has been
     * changed in the model. Use the __set() method, which should wrap all
     * write access to the model, to remember write access.
     *
     * @return void
     */
    public function storeChanges()
    {
        // Update editor data
        $this->properties['user'] = new recipeModelUser( recipeSession::get( 'login' ) );
        $this->modifiedProperty[] = 'user';

        $this->gateway->updateRecipeData( $this->id, $this->getModifiedValues() );

        // As we now stored everything in backend, nothing has to be considered
        // modified anymore...
        $this->modifiedProperty = array();
        $this->properties['revisions'] = null;
    }

    /**
     * Read property from struct
     *
     * Read property from struct
     *
     * @ignore
     * @param string $property
     * @return mixed
     */
    public function __get( $property )
    {
        switch ( $property )
        {
            case 'html':
                // Compile RST to HTML.
                $document = new \ezcDocumentRst();
                $document->options->errorReporting                   = E_ERROR | E_PARSE;
                $document->options->xhtmlVisitor                     = 'ezcDocumentRstXhtmlBodyVisitor';
                $document->options->xhtmlVisitorOptions->headerLevel = 3;

                try
                {
                    $document->loadString( $this->instructions );
                    $html = $document->getAsXhtml();
                    return $html->save();
                }
                catch ( \ezcDocumentParserException $e )
                {
                    return null;
                }

            case 'docbookBody':
                // Compile RST to Docbook.
                $document = new \ezcDocumentRst();
                $document->options->errorReporting                   = E_ERROR | E_PARSE;

                try
                {
                    $document->loadString( $this->instructions );
                    $docbook = $document->getAsDocbook();

                    $result = '';
                    foreach ( $docbook->getDomDocument()->documentElement->childNodes as $child )
                    {
                        $result .= simplexml_import_dom( $child )->asXml();
                    }
                    return $result;
                }
                catch ( \ezcDocumentParserException $e )
                {
                    return 'Errors occured while parsing the text.';
                }

            default:
                return parent::__get( $property );
        }
    }

    /**
     * Check if property exists in struct
     *
     * Check if property exists in struct
     *
     * @ignore
     * @param string $property
     * @return mixed
     */
    public function __isset( $property )
    {
        switch ( $property )
        {
            case 'html':
            case 'docbookBody':
                return true;

            default:
                return parent::__isset( $property );
        }
    }

    /**
     * Set property value
     *
     * Set property value and set the property modified. Property value checks
     * should be done by inheriting methods, which call this parent method for
     * actually setting the value.
     *
     * @ignore
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public function __set( $property, $value )
    {
        switch ( $property )
        {
            case 'title':
            case 'description':
            case 'instructions':
                parent::__set(
                    $property,
                    Validator\StringValidator::create()
                        ->validate( $property, $value, 'string' )
                );
                break;

            case 'amount':
            case 'cooking':
            case 'preparation':
                parent::__set(
                    $property,
                    Validator\IntegerValidator::create()
                        ->validate( $property, $value, 'integer' )
                );
                break;

            case 'ingredients':
                parent::__set(
                    $property,
                    Validator\ArrayValidator::create(
                            Validator\StringValidator::create(),
                            Validator\ArrayValidator::create(
                                Validator\IntegerValidator::create(),
                                Validator\ArrayValidator::create(
                                    Validator\StringValidator::create(),
                                    Validator\StringValidator::create()
                                )
                            )
                        )
                        ->validate( $property, $value, 'array( string => array( array( string ) ) )' )
                );
                break;

            case 'tags':
                parent::__set(
                    $property,
                    Validator\ArrayValidator::create(
                            Validator\IntegerValidator::create(),
                            Validator\StringValidator::create()
                        )
                        ->validate( $property, $value, 'array( string )' )
                );
                break;

            default:
                // Default to just setting, the parent will throw an exception,
                // or the value is just used unchecked.
                parent::__set( $property, $value );
                break;
        }
    }

    /**
     * Returns all the object's properties so that they can be stored or indexed.
     *
     * @return array(string=>mixed)
     */
    public function getState()
    {
        $this->fetchRecipeData();
        $properties = array_diff_key(
            $this->properties,
            array(
                'amount'      => true,
                'preparation' => true,
                'cooking'     => true,
                'ingredients' => true,
                'user'        => true,
                'tags'        => true,
            )
        );
        $properties['id'] = $this->id;

        return $properties;
    }

    /**
     * Accepts an array containing data for one or more of the class' properties.
     *
     * @param array $properties
     */
    public function setState( array $properties )
    {
        $this->properties['id'] = $this->id = $properties['id'];
    }
}

