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
 * @version $Revision: 1385 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Basic struct class with optional value validation when the __set method gets
 * overwritten in the child classes.
 *
 * @package Core
 * @subpackage Facade
 * @version $Revision: 1385 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitFacadeManager
{
    /**
     * Per backend list of facade classes
     *
     * @var array
     */
    protected static $facadeClasses = array(
        'couchdb' => array(
            'user'   => 'arbitCouchDbUserFacade',
            'group'  => 'arbitCouchDbGroupFacade',
            'recipe' => 'arbitCouchDbRecipeFacade',
        ),
    );

    /**
     * Already instantiated facades, which may just be returned on request,
     * without reinstantiation.
     *
     * @var array
     */
    protected static $instances = array();

    /**
     * Per backend list of facade managers, which are responsible for creating
     * or removing projects.
     *
     * @var array
     */
    protected static $manager = array(
        'couchdb' => 'arbitCouchDbFacadeProjectManager',
    );

    /**
     * Manager instance depending on the selected backend.
     *
     * @var arbitFacadeProjectManager
     */
    protected static $managerInstance = null;

    /**
     * Used backend implementation
     *
     * @var string
     */
    protected static $backend = null;

    /**
     * Check backend
     *
     * Sets the backend from the configuration file, if not yet done. After
     * executing this method you can expect the backend tobe properly set.
     *
     * @return void
     */
    protected static function checkBackend()
    {
        // Initialize backend type, if not set yet.
        if ( self::$backend === null )
        {
            self::$backend = arbitBackendIniConfigurationManager::getMainConfiguration()->backendType;
        }
    }

    /**
     * Get facede of requested type.
     *
     * Get the facade class name for a requested backend, identified by its
     * name.
     *
     * @param string $name
     * @return string
     */
    public static function getFacade( $name )
    {
        // Check if cache with already instantiated class exists.
        if ( isset( self::$instances[$name] ) )
        {
            return self::$instances[$name];
        }

        self::checkBackend();

        // Check if we have assignements for the configured backend. If this is
        // not the case, this probably means a typo in the main.ini.
        if ( !isset( self::$facadeClasses[self::$backend] ) )
        {
            throw new arbitFacadeUnknownBackendException( $backend );
        }

        // Check if class exists in selected backend
        if ( !isset( self::$facadeClasses[self::$backend][$name] ) )
        {
            throw new arbitFacadeUnknownClassException( $name );
        }

        $class = self::$facadeClasses[self::$backend][$name];
        return self::$instances[$name] = new $class( self::getSetUpManager() );
    }

    /**
     * Set facade class for one backend
     *
     * Sets a facade class used for a specified facade ind the specified
     * backend.
     *
     * @param string $backend
     * @param string $name
     * @param string $class
     * @return void
     */
    public static function setFacade( $backend, $name, $class )
    {
        // We perform no checks here, because we would need reflection, which
        // is to expensive.
        self::$facadeClasses[$backend][$name] = $class;
    }

    /**
     * Get facade manager
     *
     * Get the facade setup manager to select / create and remove projects.
     *
     * @return arbitFacadeProjectManager
     */
    public static function getSetUpManager()
    {
        // If existing immediatly return already created manager instance
        if ( self::$managerInstance !== null )
        {
            return self::$managerInstance;
        }

        self::checkBackend();

        // Check if we have assignements for the configured backend. If this is
        // not the case, this probably means a typo in the main.ini.
        if ( !isset( self::$manager[self::$backend] ) )
        {
            throw new arbitFacadeUnknownBackendException( $backend );
        }

        $class = self::$manager[self::$backend];
        return self::$managerInstance = new $class();
        return self::$instances[$name] = new $class( self::getSetUpManager() );
    }

    /**
     * Select project
     *
     * Select the project currently selected in the UI, where we want to
     * operate on.
     *
     * @param string $project
     * @return void
     */
    public static function selectProject( $project )
    {
        self::getSetUpManager()->selectProject( $project );
    }

    /**
     * Create project
     *
     * Create a new project. This should create required databases and alike in
     * the backend.
     *
     * @param string $project
     * @return void
     */
    public static function createProject( $project )
    {
        self::getSetUpManager()->createProject( $project );

        // Initialize required groups, maybe perform other project
        // initializations here, too.
        $group = self::getFacade( 'group' );

        $group->updateGroupData(
            $group->createGroup( 'Users' ),
            array(
                'description' => 'Default group for all registered users.',
                'users' => array(),
            )
        );

        $group->updateGroupData(
            $group->createGroup( 'Anonymous' ),
            array(
                'description' => 'Group for anonymous users.',
                'users' => array(),
            )
        );
    }

    /**
     * Remove project
     *
     * Clean all project related data in the backend.
     *
     * @param string $project
     * @return void
     */
    public static function removeProject( $project )
    {
        self::getSetUpManager()->removeProject( $project );
    }
}

