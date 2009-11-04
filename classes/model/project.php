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
 * Project model
 *
 * @package Core
 * @subpackage Model
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitModelProject extends arbitModelBase
{
    /**
     * Array containing the projects properties
     *
     * @var array
     */
    protected $properties = array(
        'versions'   => null,
        'components' => null,
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
    protected $defaultFetchMethod = 'fetchProjectData';

    /**
     * Create project model
     *
     * Just create the project model
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Method called to create a new instance in the backend.
     *
     * Method called when the model should be created in the backend the first
     * time. This will normally throw an error if a model with the same
     * identifier already exists in the backend.
     *
     * @return void
     */
    public function create()
    {
        // We do not need to create anything here, this is handled completely
        // transparent by the facade.
        return;
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
        $project = arbitFacadeManager::getFacade( 'project' );
        $project->setProjectData( $this->getModifiedValues() );

        // As we now stored everything in backend, nothing has to be considered
        // modified anymore...
        $this->modifiedProperty = array();
        arbitCacheRegistry::getCache()->purge( 'model', 'project' );
    }

    /**
     * Fetch the basic project data
     *
     * Fetch the basic project data
     *
     * @return void
     */
    protected function fetchProjectData()
    {
        if ( ( $data = arbitCacheRegistry::getCache()->get( 'model', 'project' ) ) === false )
        {
            $project = arbitFacadeManager::getFacade( 'project' );
            $data = $project->getProjectData();

            // Cache retrieved project data
            arbitCacheRegistry::getCache()->cache( 'model', 'project', $data );
        }

        $this->properties['versions']   = $data['versions'];
        $this->properties['components'] = $data['components'];
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
            case 'versions':
                parent::__set(
                    $property,
                    arbitModelArrayValidator::create(
                        arbitModelStringValidator::create(),
                        arbitModelIntegerValidator::create()
                    )->validate( $property, $value, 'array( string => int )' )
                );
                break;

            case 'components':
                parent::__set(
                    $property,
                    arbitModelArrayValidator::create(
                        arbitModelStringValidator::create(),
                        arbitModelArrayValidator::create()
                    )->validate( $property, $value, 'array( string => array )' )
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

