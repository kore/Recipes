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
 * Facade manager, responsible for selecting and modifying projects.
 *
 * @package Core
 * @subpackage Facade
 * @version $Revision: 1385 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitCouchDbFacadeProjectManager extends arbitCouchDbFacadeBase implements arbitFacadeProjectManager
{
    /**
     * Prefix to use for database names.
     *
     * @var string
     */
    protected $prefix = '';

    /**
     * Currently selected project.
     *
     * @var string
     */
    protected $project = null;

    /**
     * Constructor
     *
     * The constructor ensures the correct configuration for the conenction by
     * connecting on the first request to on of the facde classes.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct( $this );
    }

    /**
     * Connect to database
     *
     * Connect to the database with the connection parameters read from the 
     * configuration.
     * 
     * @return void
     */
    public function connect()
    {
        $this->registerBaseDocuments();

        list( $host, $port ) = $this->getConnectionInformation();
        phpillowConnection::createInstance(
            $host, $port
        );
        $db = phpillowConnection::getInstance();

        self::$connected = true;
    }

    /**
     * Set the used project
     *
     * Sets the used project and perfom necessary initializations to the
     * backend.
     *
     * @param mixed $project
     * @return void
     */
    protected function setProject( $project )
    {
        $this->project = $project;

        // Ensure prefix is actually initilized
        $this->getConnectionInformation();
        phpillowConnection::setDatabase( $this->getDatabaseName() );
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
    public function selectProject( $project )
    {
        $this->setProject( $project );

        // Check if database exists, otherwise reinitialize it. This may be
        // handled without an explicit request to the backend, see CouchDB
        // issue #41 for details:
        // https://issues.apache.org/jira/browse/COUCHDB-41
        try
        {
            $db = phpillowConnection::getInstance();
            $db->get( '/' . $this->getDatabaseName() );
        }
        catch ( phpillowResponseNotFoundErrorException $e )
        {
            // Recreate the database using the common project creation method,
            // when the request for the project database failed.
            arbitFacadeManager::createProject( $project );
        }
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
    public function createProject( $project )
    {
        $this->setProject( $project );

        // We just need to create the database. CouchDB does not enforce any
        // structure.
        try
        {
            phpillowConnection::getInstance()->put(
                '/' . $this->getDatabaseName()
            );
        }
        catch ( phpillowResponseConflictErrorException $e )
        {
            throw new arbitFacadeProjectExistsException( $project );
        }
    }

    /**
     * Remove project
     *
     * Clean all project related data in the backend.
     *
     * @param string $project
     * @return void
     */
    public function removeProject( $project )
    {
        $this->setProject( $project );

        // Just remove the complete database
        try
        {
            phpillowConnection::getInstance()->delete(
                '/' . $this->getDatabaseName()
            );
        }
        catch ( phpillowResponseNotFoundErrorException $e )
        {
            throw new arbitFacadeUnknownProjectException( $project );
        }
    }

    /**
     * Verify backend integrity
     *
     * @return void
     */
    public function verifyIntegrity()
    {
        // Remove all stored views from database to ensure they are properly
        // regenerated
        $conn = phpillowConnection::getInstance();

        $designs = $conn->get(
            '/' . ( $db = $this->getDatabaseName() ) .
            '/_all_docs?startkey=%22_design%2F%22&endkey=%22_design0%22'
        );

        foreach ( $designs->rows as $row )
        {
            $conn->delete( '/' . $db . '/' . $row['key'] . '?rev=' . $row['value']['rev'] );
        }
    }

    /**
     * Register all CouchDB core documents
     *
     * Register all CouchDB core documents and views, once a connection is
     * requested.
     *
     * @return void
     */
    private function registerBaseDocuments()
    {
        $documents = array(
            'user'   => 'arbitBackendCouchDbUserDocument',
            'group'  => 'arbitBackendCouchDBGroupDocument',
            'recipe' => 'arbitBackendCouchDbRecipeDocument',
        );

        foreach ( $documents as $name => $class )
        {
            phpillowManager::setDocumentClass( $name, $class );
        }

        $views = array(
            'group' => 'arbitBackendCouchDbGroupView',
            'user'  => 'arbitBackendCouchDbUserView',
        );

        foreach ( $views as $name => $class )
        {
            phpillowManager::setViewClass( $name, $class );
        }
    }

    /**
     * Parse the connection string
     *
     * Parse the usual URL into the required components. Sets the prefix used
     * for the databases and returns an array containing the host and port.
     *
     * @return array
     */
    protected function getConnectionInformation()
    {
        $url = arbitBackendIniConfigurationManager::getMainConfiguration()->backendUrl;
        $parts = parse_url( $url );

        // Ommit the starting /
        $this->prefix = substr( $parts['path'], 1 );

        return array( $parts['host'], $parts['port'] );
    }

    /**
     * Get current database name
     *
     * Build and return the name of the database from the prefix and the 
     * project name.
     * 
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->prefix . $this->project;
    }
}

