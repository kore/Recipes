<?php
/**
 * arbit model
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
 * @subpackage Model
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Recipe model
 *
 * @package Core
 * @subpackage Model
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitRecipeModel extends arbitModelBase
{
    /**
     * Array containing the recipes properties
     *
     * @var array
     */
    protected $properties = array(
        'title'        => null,
        'amount'       => null,
        'description'  => null,
        'ingredients'  => null,
        'preparation'  => null,
        'cooking'      => null,
        'instructions' => null,
        'user'         => null,
        'tags'         => null,
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
        $recipe = arbitFacadeManager::getFacade( 'recipe' );
        return $this->id = $recipe->createRecipe( $this->title );
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
        $recipe = arbitFacadeManager::getFacade( 'recipe' );
        $recipe->updateRecipeData( $this->id, $this->getModifiedValues() );

        // Clear associated cache
        arbitCacheRegistry::getCache()->purge( 'model', 'recipe/' . $this->id );

        // As we now stored everything in backend, nothing has to be considered
        // modified anymore...
        $this->modifiedProperty = array();
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
        $cacheId = 'recipe/' . $this->id;
        if ( ( $data = arbitCacheRegistry::getCache()->get( 'model', $cacheId ) ) === false )
        {
            $recipe = arbitFacadeManager::getFacade( 'recipe' );
            $data = $recipe->getRecipeData( $this->id );

            // Cache retrieved project data
            arbitCacheRegistry::getCache()->cache( 'model', $cacheId, $data );
        }

        foreach ( $data as $key => $value )
        {
            if ( $value !== null )
            {
                $this->properties[$key] = $value;
            }
        }
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
        if ( $property !== 'html' )
        {
            return parent::__get( $property );
        }

        // Compile RST to HTML.
        $document = new ezcDocumentRst();
        $document->options->errorReporting                   = E_ERROR | E_PARSE;
        $document->options->xhtmlVisitor                     = 'ezcDocumentRstXhtmlBodyVisitor';
        $document->options->xhtmlVisitorOptions->headerLevel = 3;

        try
        {
            $document->loadString( $this->text );
            $html = $document->getAsXhtml();
            return $html->save();
        }
        catch ( ezcDocumentParserException $e )
        {
            return null;
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
                    arbitModelStringValidator::create()
                        ->validate( $property, $value, 'string' )
                );
                break;

            case 'amount':
            case 'cooking':
            case 'preparation':
                parent::__set(
                    $property,
                    arbitModelIntegerValidator::create()
                        ->validate( $property, $value, 'integer' )
                );
                break;

            case 'ingredients':
                parent::__set(
                    $property,
                    arbitModelArrayValidator::create(
                            arbitModelStringValidator::create(),
                            arbitModelArrayValidator::create(
                                arbitModelIntegerValidator::create(),
                                arbitModelArrayValidator::create(
                                    arbitModelStringValidator::create(),
                                    arbitModelStringValidator::create()
                                )
                            )
                        )
                        ->validate( $property, $value, 'array( string => array( array( string ) ) )' )
                );
                break;

            case 'tags':
                parent::__set(
                    $property,
                    arbitModelArrayValidator::create(
                            arbitModelIntegerValidator::create(),
                            arbitModelStringValidator::create()
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
}

