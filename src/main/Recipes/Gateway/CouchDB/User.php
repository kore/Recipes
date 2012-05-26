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
 * @subpackage Gateway
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

namespace Recipes\Gateway\CouchDB;
use Recipes\Gateway;

/**
 * User gateway defining all methods required to access user related data in the
 * backend.
 *
 * @package Core
 * @subpackage Gateway
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class User implements Gateway\User
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
    public function __construct( \phpillowConnection $connection, Gateway\CouchDB\User\View $view )
    {
        $this->connection = $connection;
        $this->view       = $view;
    }

    /**
     * Get array of users
     *
     * Return an array with the IDs of all available users in the database.
     *
     * @return array
     */
    public function getAllUserIDs()
    {
        $result = $this->view->query( 'user' );

        if ( !isset( $result->rows ) ||
             ( count( $result->rows ) < 1 ) )
        {
            return array();
        }

        $userIDs = array();
        foreach ( $result->rows as $row )
        {
            $userIDs[] = $row['id'];
        }

        return $userIDs;
    }

    /**
     * Get user by login
     *
     * Get the user id for the user given by its login name.
     *
     * @param string $login
     * @return array
     */
    public function getUserDataByLogin( $login )
    {
        try
        {
            $result = $this->view->query( 'user', array(
                'key' => $login,
            ) );
        }
        catch ( phpillowResponseNotFoundErrorException $e )
        {
            throw new recipeGatewayNotFoundException(
                "The user '%user' could not be found.",
                array(
                    'user' => $user,
                )
            );
        }

        // Check if we got a result at all
        if ( count( $result->rows ) !== 1 )
        {
            throw new recipeGatewayNotFoundException(
                "The user '%user' could not be found.",
                array(
                    'user' => $login,
                )
            );
        }

        return $result->rows[0]['id'];
    }

    /**
     * Get user data
     *
     * Get data for the given user id. The data should be returned as an array,
     * and should contain the following keys:
     *  - login
     *  - email
     *  - name
     *  - valid
     *  - auth_type
     *  - auth_infos
     *  - settings
     *  - persitenceToken
     *  - revisions
     *
     * @param string $user
     * @return array
     */
    public function getUserData( $user )
    {
        try
        {
            $doc = phpillowManager::fetchDocument( 'user', $user );
        }
        catch ( phpillowResponseNotFoundErrorException $e )
        {
            throw new recipeGatewayNotFoundException(
                "The user '%user' could not be found.",
                array(
                    'user' => $user,
                )
            );
        }

        return array(
            'login'           => $doc->login,
            'email'           => $doc->email,
            'name'            => $doc->name,
            'valid'           => $doc->valid,
            'auth_type'       => $doc->auth_type,
            'auth_infos'      => $doc->auth_infos,
            'revisions'       => $doc->revisions,
            'persitenceToken' => isset( $doc->persitenceToken ) ? $doc->persitenceToken : '',
            'settings'        => isset( $doc->settings ) ? $doc->settings : array(),
        );
    }

    /**
     * Create a new user
     *
     * Create a new user with the given name. An exception will be thrown if
     * there already is a user with the given name.
     *
     * @param string $name
     * @return void
     */
    public function createUser( $name )
    {
        try
        {
            $user = phpillowManager::createDocument( 'user' );
            $user->login = $name;
            $user->save();
        }
        catch ( phpillowResponseConflictErrorException $e )
        {
            throw new recipeGatewayUserExistsException( $name );
        }

        // Return generated ID
        return $user->_id;
    }

    /**
     * Update stored information for the given user
     *
     * The array with the information to update may any number of the common
     * keys, and only the given keys will be updated in the storage backend.
     *
     * @param string $user
     * @param array $data
     * @return void
     */
    public function updateUserData( $user, $data )
    {
        try
        {
            $doc = phpillowManager::fetchDocument( 'user', $user );
        }
        catch ( phpillowResponseNotFoundErrorException $e )
        {
            throw new recipeGatewayNotFoundException(
                "The user '%user' could not be found.",
                array(
                    'user' => $user,
                )
            );
        }

        // Set data, which will be validated internally, and store.
        foreach ( $data as $key => $value )
        {
            $doc->$key = $value;
        }
        $doc->save();
    }
}

