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
 * Group model
 *
 * @package Core
 * @subpackage Model
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitModelGroup extends arbitModelBase
{
    /**
     * Array containing the users properties
     *
     * @var array
     */
    protected $properties = array(
        'name'        => null,
        'users'       => null,
        'description' => null,
        'permissions' => null,
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
    protected $defaultFetchMethod = 'fetchGroupData';

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
        $group = arbitFacadeManager::getFacade( 'group' );
        return $this->id = $group->createGroup( $this->name );
    }

    /**
     * Fetch all groups
     *
     * Static function to fetch an array with all groups in the database. In
     * the returned groups only the ID is set, and all other data will be
     * fetched lazy on explicit request.
     *
     * @return array
     */
    public static function fetchAll()
    {
        $group = arbitFacadeManager::getFacade( 'group' );
        $groupIDs = $group->getAllGroupIDs();

        $groups = array();
        foreach ( $groupIDs as $id )
        {
            $groups[] = new arbitModelGroup( $id );
        }
        return $groups;
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
        $group = arbitFacadeManager::getFacade( 'group' );
        $group->updateGroupData( $this->id, $this->getModifiedValues() );

        // Clear associated cache
        arbitCacheRegistry::getCache()->purge( 'model', 'group/' . $this->id );

        // In most cases group changes affect user privilegs, so prune privelegs
        // for all group users. This is a pragmatic approach, but it works for
        // the moment.
        foreach ( (array) $this->users as $user )
        {
            arbitCacheRegistry::getCache()->purge( 'model', 'privileges/' . $user->id );
        }

        // As we now stored everything in backend, nothing has to be considered
        // modified anymore...
        $this->modifiedProperty = array();
    }

    /**
     * Fetch the basic user data
     *
     * Fetch the basic user data
     *
     * @return void
     */
    protected function fetchGroupData()
    {
        $cacheId = 'group/' . $this->id;
        if ( ( $data = arbitCacheRegistry::getCache()->get( 'model', $cacheId ) ) === false )
        {
            $group = arbitFacadeManager::getFacade( 'group' );
            $data = $group->getGroupData( $this->id );

            // Cache retrieved project data
            arbitCacheRegistry::getCache()->purge( 'model', $cacheId, $data );
        }

        foreach ( $data as $key => $value )
        {
            if ( $value !== null )
            {
                $this->properties[$key] = $value;
            }
        }

        // Ensure user data is an array
        $this->properties['users'] = ( $this->properties['users'] === null ) ? array() : $this->properties['users'];

        // Convert user IDs to user models
        array_walk( $this->properties['users'], function ( &$user )
            {
                $user = new arbitModelUser( $user );
            }
        );
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
            case 'name':
            case 'description':
                parent::__set(
                    $property,
                    arbitModelStringValidator::create()
                        ->validate( $property, $value, 'string' )
                );
                break;

            case 'permissions':
                parent::__set(
                    $property,
                    arbitModelArrayValidator::create(
                        arbitModelIntegerValidator::create( 0 ),
                        arbitModelStringValidator::create()
                    )->validate( $property, $value, 'array( string )' )
                );
                break;

            case 'users':
                parent::__set(
                    $property,
                    arbitModelArrayValidator::create(
                        arbitModelIntegerValidator::create( 0 ),
                        arbitModelObjectValidator::create( 'arbitModelUser' )
                    )->validate( $property, $value, 'array( arbitModelUser )' )
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

