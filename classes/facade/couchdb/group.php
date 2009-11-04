<?php
/**
 * arbit storage backend facade
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
 * @subpackage Facade
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Group facade defining all methods required to access group related data in the
 * backend.
 *
 * @package Core
 * @subpackage Facade
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitCouchDbGroupFacade extends arbitCouchDbFacadeBase implements arbitGroupFacade
{
    /**
     * Get array of groups
     *
     * Return an array with the IDs of all available groups in the database.
     *
     * @return array
     */
    public function getAllGroupIDs()
    {
        $groups = phpillowManager::getView( 'group' );
        $result = $groups->query( 'group' );

        if ( !isset( $result->rows ) ||
             ( count( $result->rows ) < 1 ) )
        {
            return array();
        }

        $groupIDs = array();
        foreach ( $result->rows as $row )
        {
            $groupIDs[] = $row['id'];
        }

        return $groupIDs;
    }

    /**
     * Get group data
     *
     * Get data for the given group name. The data should be returned as an
     * array, and should contain the following keys:
     *  - name
     *  - description
     *  - users
     *  - permissions
     *
     * Where users and permissions are arrays containg the list of users (their
     * login names) repectively the permission string keys.
     *
     * @param string $group
     * @return array
     */
    public function getGroupData( $group )
    {
        try
        {
            $doc = phpillowManager::fetchDocument( 'group', $group );
        }
        catch ( phpillowResponseNotFoundErrorException $e )
        {
            throw new arbitFacadeNotFoundException(
                "The group '%group' could not be found.",
                array(
                    'group' => $group,
                )
            );
        }

        return array(
            'name'        => $doc->name,
            'description' => $doc->description,
            'users'       => $doc->users,
            'permissions' => $doc->permissions,
        );
    }

    /**
     * Create a new group
     *
     * Create a new group with the given name. An exception will be thrown if
     * there already is a group with the given name.
     *
     * @param string $name
     * @return void
     */
    public function createGroup( $name )
    {
        try
        {
            $group = phpillowManager::createDocument( 'group' );
            $group->name = $name;
            $group->save();
        }
        catch ( phpillowResponseConflictErrorException $e )
        {
            throw new arbitFacadeGroupExistsException( $name );
        }

        // Return generated ID
        return $group->_id;
    }

    /**
     * Update stored information for the given user
     *
     * The array with the information to update may any number of the common
     * keys, and only the given keys will be updated in the storage backend.
     *
     * @param string $group
     * @param array $data
     * @return void
     */
    public function updateGroupData( $group, $data )
    {
        try
        {
            $doc = phpillowManager::fetchDocument( 'group', $group );
        }
        catch ( phpillowResponseNotFoundErrorException $e )
        {
            throw new arbitFacadeNotFoundException(
                "The group '%group' could not be found.",
                array(
                    'group' => $group,
                )
            );
        }

        // Convert the user models given in the users property to their login
        // name, we do not want to store a complete object.
        if ( isset( $data['users'] ) )
        {
            foreach ( $data['users'] as $nr => $user )
            {
                if ( ( !$user instanceof arbitModelUser ) ||
                     ( ( $data['users'][$nr] = $user->_id ) === false ) )
                {
                    throw new arbitModelNotPersistantException();
                }
            }
        }

        // Set data, which will be validated internally, and store.
        foreach ( $data as $key => $value )
        {
            $doc->$key = $value;
        }
        $doc->save();
    }

    /**
     * Get privileges for user
     *
     * Get the privileges for a user, given the users ID, as an array of
     * strings defining the privileges the user has.
     *
     * @param string $user
     * @return array
     */
    public function getPrivilegesForUser( $user )
    {
        $groups = phpillowManager::getView( 'group' );
        $result = $groups->query( 'user_permissions', array( 'key' => $user ) );

        // Extract the permissions from result
        $permissions = array();
        foreach ( $result->rows as $row )
        {
            $permissions[] = $row['value'];
        }

        // Unique and sort for reproducability
        return array_values( array_unique( $permissions ) );
    }
}

