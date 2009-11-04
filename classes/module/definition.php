<?php
/**
 * arbit base module definition
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
 * @version $Revision: 1401 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Base class for module definitions.
 *
 * Each module is required to extend this class an return an instance of it
 * from the file definition.php in the modules root directory.
 *
 * This struct defines the way the core interacts with the module. The struct
 * may be extended in later releases to contain more information, but will
 * always provide meaningful default values in this case.
 *
 * When extending this class you normally should only need to extend the
 * property array to contain your module specific values. If your module has a
 * completely different structure you may also overwrite the __get() method and
 * do some magic in there to receive your values. But keep in mind, that this
 * class is instantiated on *each* call, so it should *not* perform any
 * performance intensive stuff.
 *
 * @package Core
 * @version $Revision: 1401 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 *
 * @property-read array $autoload
 *           Array with a class to filename mapping for autoloading of module
 *           classes.
 * @property-read array $permissions
 *           Array with the module specific permissions as a key and a
 *           description of the respective permission as a value.
 * @property-read array $slots
 *           Array containing the slots a module registered for and the
 *           associated callback, hwich should be called by the signal slot
 *           manager, when the signal has been trigered.
 * @property-read array $signals
 *           Array with all signals the module may emit. The key of teh array
 *           is the name of the signal, and should be prefixed with the module
 *           name, and the value is a description of the repective signal, eg.
 *           describes when the signal is thrown.
 * @property-read string $templateDirectory
 *           Folder where the module specific templates can be found. Normally
 *           you should NOT chnage this. The path is given relative to the
 *           modules root directory.
 * @property-read string $controller
 *           The class name of the modules main controller, where all requests
 *           to the module are dispatched too. This name should normally be
 *           arbit<ModuleName>Controller.
 * @property-read string $path
 *           The root path of the module. This should normally just point to
 *           __DIR__ in the implemented module definition.
 */
abstract class arbitModuleDefintion extends arbitBaseStruct
{
    /**
     * Default fallback values for the module structure properties.
     *
     * If a module does not define a certain value in the properties array, 
     * because it is a new value, or similar, the value from the defaults array 
     * is used.
     *
     * It is private, because changes in this array should only be applied in 
     * this class.
     *
     * @var array
     */
    private $defaults = array(
        'autoload'          => null,
        'permissions'       => array(
        ),
        'slots'             => array(
        ),
        'signals'           => array(
        ),

        'templateDirectory' => 'templates/',
        'controller'        => null,
        'path'              => null,
        // @TODO: This property may be subject to changes in name and structure
        'menu'              => array(
            'main' => true,
        ),
    );

    /**
     * Array containing the module structures properties.
     *
     * Take a look at the property descriptions in the class level
     * documentation for a more detailed description for each of the
     * properties.
     *
     * Do not add any properties to this array, which are not defined in this
     * base class, because those might be used later on by the core.
     *
     * @var array
     */
    protected $properties = array(
        'autoload'          => null,
        'permissions'       => array(
        ),
        'slots'             => array(
        ),
        'signals'           => array(
        ),

        'templateDirectory' => 'templates/',
        'controller'        => null,
        'path'              => null,
    );

    /**
     * Array with caches registered on initialization
     *
     * Each cache must have a unique name, normally prefixed with the module
     * identifier. The definition array should at least contain a path and a
     * time to live for the cache.
     *
     * <code>
     *  'name' => array(
     *      'path' => 'dir/',
     *      'ttl'  => arbitCache::INFINITE,
     *  ),
     *  ...
     * </code>
     *
     * @var array
     */
    protected $caches = array();

    /**
     * Array with facades registered on initialization
     *
     * Array with facedes for all known database backends linked with their
     * respective implementation.
     *
     * <code>
     *  'couchdb' => array(
     *      'name' => 'class',
     *  ),
     * </code>
     *
     * @var array
     */
    protected $facades = array();

    /**
     * CouchDB documents to be registered at the document manager.
     *
     * @var array
     */
    protected $couchDbDocuments = array();

    /**
     * CouchDB documents to be registered at the document manager.
     *
     * @var array
     */
    protected $couchDbViews = array();

    /**
     * List of view models used by the module
     *
     * List of used view handlers associated with a list of view
     * models used by the module, each associated with a callback to
     * the concrete handler implementation to visit the view model.
     *
     * <code>
     *  'arbitViewXHtmlHandler' => array(
     *      'myViewModel' => 'myXHtmlHandler::showMyModel',
     *      ...
     *  ),
     *  ...
     * </code>
     *
     * @var array
     */
    protected $viewModels = array();

    /**
     * List of command definitions of the module
     *
     * Array containing command names and their assiciated classes in the module 
     * definition.
     *
     * <code>
     *  array(
     *      'mymodule.mycommand' => 'myModuleMyCommand',
     *      ...
     *  )
     * </code>
     * 
     * @var array
     */
    protected $commands = array();

    /**
     * Get property value
     *
     * Get property values. May be used for delayed intialisation of some
     * properties on the first request to this property. This is for example
     * sued to load the autoload array only when it is requested.
     *
     * @param string $property
     * @return mixed
     */
    public function __get( $property )
    {
        switch ( $property )
        {
            case 'autoload':
                // Load autoload array from generated file on first request.
                if ( !isset( $this->properties[$property] ) ||
                     ( $this->properties[$property] === null ) )
                {
                    $this->properties[$property] = require $this->path . '/autoload.php';
                }

            default:
                // Check if the property exists at all - use array_key_exists, to let
                // this check pass, even if the property is set to null.
                if ( array_key_exists( $property, $this->properties ) )
                {
                    return $this->properties[$property];
                }
                elseif ( array_key_exists( $property, $this->defaults ) )
                {
                    return $this->defaults[$property];
                }

                throw new arbitPropertyException( $property );
        }
    }

    /**
     * Setting values in this struct is never permitted.
     *
     * @ignore
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public final function __set( $property, $value )
    {
        throw new arbitPropertyException( $property );
    }

    /**
     * Initialize module caches
     *
     * @return void
     */
    protected function initializeCaches()
    {
        foreach ( $this->caches as $name => $definition )
        {
            arbitCacheRegistry::getCache()->addCache(
                $name,
                $definition['path'],
                $definition['ttl']
            );
        }
    }

    /**
     * Initialize facades
     *
     * @return void
     */
    protected function initializeFacades()
    {
        foreach ( $this->facades as $db => $facades )
        {
            foreach ( $facades as $name => $implementation )
            {
                arbitFacadeManager::setFacade( $db, $name, $implementation );
            }
        }
    }

    /**
     * Initialize CouchDB document classes
     *
     * @return void
     */
    protected function initializeCouchDbDocuments()
    {
        foreach ( $this->couchDbDocuments as $name => $implementation )
        {
            phpillowManager::setDocumentClass( $name, $implementation );
        }
    }

    /**
     * Initialize CouchDB view classes
     *
     * @return void
     */
    protected function initializeCouchDbViews()
    {
        foreach ( $this->couchDbViews as $name => $implementation )
        {
            phpillowManager::setViewClass( $name, $implementation );
        }
    }

    /**
     * Initialize views
     *
     * @return void
     */
    protected function initializeViews()
    {
        foreach ( $this->viewModels as $handler => $views )
        {
            foreach ( $views as $model => $callback )
            {
                // Register configured handler
                arbitViewModelDecorationDependencyInjectionManager::addDecorator(
                    $handler, $model, $callback
                );

                // Always register handler for additional context information
                arbitViewModelDecorationDependencyInjectionManager::addDecorator(
                    $handler, $model, 'addContextInformation'
                );
            }
        }
    }

    /**
     * Initialize commands
     *
     * @return void
     */
    protected function initializeCommands()
    {
        foreach ( $this->commands as $command => $class )
        {
            periodicCommandRegistry::registerCommand( 'arbit.' . $command, $class );
        }
    }

    /**
     * Initialize module
     *
     * Initialize the module using the values defined in the module definition
     * class properties.
     *
     * @return void
     */
    public function initializeModule()
    {
        $this->initializeCaches();
        $this->initializeFacades();
        $this->initializeCouchDbDocuments();
        $this->initializeCouchDbViews();
        $this->initializeViews();
        $this->initializeCommands();
    }
}

