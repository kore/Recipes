<?php
/**
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
use Recipes\Model;
use Recipes\Gateway;

/**
 * User model
 *
 * @package Core
 * @subpackage Model
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class User extends Model
{
    /**
     * Array containing the users properties
     *
     * @var array
     */
    protected $properties = array(
        'login'           => null,
        'email'           => null,
        'name'            => null,
        'valid'           => null,
        'auth_type'       => null,
        'auth_infos'      => null,
        'settings'        => null,
        'persitenceToken' => null,
        'revisions'       => null,
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
    protected $defaultFetchMethod = 'fetchUserData';

    /**
     * User gateway
     *
     * @var Gateway\User
     */
    protected $gateway;

    /**
     * Construct from user gateway
     *
     * @param Gateway\User $userGateway
     * @return void
     */
    public function __construct( Gateway\User $gateway, $id = null )
    {
        parent::__construct( $id );
        $this->gateway = $gateway;
    }

    /**
     * Find user by login
     *
     * Find a user in the database by its login name and return the
     * User, or throw an exception.
     *
     * @param string $login
     * @return User
     */
    public function findByLogin( $login )
    {
        $userId = $this->gateway->getUserDataByLogin( $login );
        return new User( $this->gateway, $userId );
    }

    /**
     * Fetch all users
     *
     * Static function to fetch an array with all users in the database. In
     * the returned users only the ID is set, and all other data will be
     * fetched lazy on explicit request.
     *
     * @return array
     */
    public function fetchAll()
    {
        $userIDs = $this->gateway->getAllUserIDs();

        $users = array();
        foreach ( $userIDs as $id )
        {
            $users[] = new User( $this->gateway, $id );
        }
        return $users;
    }

    /**
     * Method called to create a new instance in the backend.
     *
     * Method called when the model should be created in the backend the first
     * time. This will normally throw an error if a model with the same
     * identifier already exists in the backend.
     *
     * Returns the backend dependant identifier of the created user.
     *
     * @return mixed
     */
    public function create()
    {
        return $this->id = $this->gateway->createUser( $this->login );
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
        $this->gateway->updateUserData( $this->id, $this->getModifiedValues() );

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
    protected function fetchUserData()
    {
        $data = $this->gateway->getUserData( $this->id );

        foreach ( $data as $key => $value )
        {
            if ( $value !== null )
            {
                $this->properties[$key] = $value;
            }
        }
    }

    /**
     * Create and return new persistence token
     *
     * Creates a sufficently random persistence token, stores it for the user
     * account and returns it, so it can be used for further actions.
     *
     * @return string
     */
    public function getPersistenceToken()
    {
        $this->persitenceToken = $token = md5(
            microtime() .
            uniqid( mt_rand(), true ) .
            implode( '', fstat( fopen( __FILE__, 'r' ) ) )
        );
        $this->storeChanges();
        return $token;
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
            case 'login':
            case 'email':
            case 'name':
            case 'valid':
            case 'auth_type':
                parent::__set(
                    $property,
                    Validator\StringValidator::create()
                        ->validate( $property, $value, 'string' )
                );
                break;

            case 'auth_infos':
                parent::__set(
                    $property,
                    Validator\ArrayValidator::create()
                        ->validate( $property, $value, 'array( mixed )' )
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

