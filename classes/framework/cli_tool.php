<?php
/**
 * CLI tool base class
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
 * @version $Revision: 1469 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Base class for CLI tools, implementing the basic options and out handling
 *
 * @package Core
 * @version $Revision: 1469 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class arbitFrameworkCliTool
{
    /**
     * Input handler
     *
     * @var ezcConsoleInput
     */
    protected $in;

    /**
     * Output handler
     *
     * @var ezcConsoleOutput
     */
    protected $out;

    /**
     * Name of CLI tool
     *
     * @var string
     */
    protected $name = 'Unnamed CLI tool';

    /**
     * Short description of the purpose of the current CLI tool
     *
     * @var string
     */
    protected $description = null;

    /**
     * Grouping of the common options
     *
     * @var array
     */
    protected $commonOptionGroups = array(
        'Common options' => array(
            'help',
            'version',
            'verbose',
            'quiet',
            'output',
            'no-colors',
        ),
        'Project options' => array(
            'project',
            'user',
        ),
    );

    /**
     * Create cli tool and configure basic in and out handling
     *
     * @return void
     */
    public function __construct()
    {
        $this->out = new ezcConsoleOutput();
        $this->out->options->verbosityLevel = 10;

        // Errors should go to STDERR
        $this->out->formats->error->target = ezcConsoleOutput::TARGET_STDERR;

        $this->in = new ezcConsoleInput();
        $this->registerDefaultOptions();
        $this->registerOptions();

        // Initialize file log writer
        $log     = ezcLog::getInstance();
        $fileLog = new ezcLogUnixFileWriter( ARBIT_BASE . 'var/log', 'error.log' );
        $filter  = new ezcLogFilter();
        $filter->severity = ezcLog::WARNING | ezcLog::ERROR | ezcLog::FATAL;
        $log->getMapper()->appendRule(
            new ezcLogFilterRule( $filter, $fileLog, true )
        );

        // Add CLI logger, to output all messages directly
        $log->getMapper()->appendRule(
            new ezcLogFilterRule( new ezcLogFilter(), new arbitCliLogger( $this->out ), true )
        );
    }

    /**
     * Register default options
     *
     * Register a set of default options, each arbit CLI tool should always
     * implement.
     *
     * @return void
     */
    protected function registerDefaultOptions()
    {
        // Basic generic options for all CLI tools
        $this->in->registerOption( new ezcConsoleOption(
            'h', 'help',
            ezcConsoleInput::TYPE_NONE, false, false,
            'Display this help message'
        ) );
        $this->in->registerOption( new ezcConsoleOption(
            'V', 'version',
            ezcConsoleInput::TYPE_NONE, false, false,
            'Display version and exit'
        ) );
        $this->in->registerOption( new ezcConsoleOption(
            'v', 'verbose',
            ezcConsoleInput::TYPE_NONE, false, false,
            'Increase output verbosity to 100 (default: 10)'
        ) );
        $this->in->registerOption( new ezcConsoleOption(
            'q', 'quiet',
            ezcConsoleInput::TYPE_NONE, false, false,
            'Decrease output verbosity to 0 (default: 10)'
        ) );
        $this->in->registerOption( new ezcConsoleOption(
            'n', 'no-colors',
            ezcConsoleInput::TYPE_NONE, false, false,
            'Not use colored output for error messages, warnings, etc.'
        ) );
        $this->in->registerOption( new ezcConsoleOption(
            'o', 'output',
            ezcConsoleInput::TYPE_STRING, 'txt', false,
            'Markup used for output, one of: js, xml or txt. Defaults to txt.'
        ) );

        // General arbit related CLI options
        $this->in->registerOption( new ezcConsoleOption(
            'u', 'user',
            ezcConsoleInput::TYPE_STRING, null, false,
            'User the current command is run as'
        ) );
        $this->in->registerOption( new ezcConsoleOption(
            'p', 'project',
            ezcConsoleInput::TYPE_STRING, null, true,
            'Projects the command is run for'
        ) );
    }

    /**
     * Register options
     *
     * Register a set of options, which are special for this CLI tool. May be
     * left empty, if no additional options are required.
     *
     * @return void
     */
    protected function registerOptions()
    {
        // Do nothing by default
    }

    /**
     * Get controller
     *
     * Return controller to execute for the current command
     *
     * @param arbitRequest $request
     * @param array $options
     * @return arbitController
     */
    abstract protected function createController( arbitRequest $request, array $options );

    /**
     * Get option values
     *
     * Return an array with all option values, indexed by the long names of the
     * options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = array();
        foreach ( $this->in->getOptions() as $option )
        {
            $options[$option->long] = $option->value;
        }

        return $options;
    }

    /**
     * Return the current version
     *
     * @return string
     */
    protected function getVersion()
    {
        $version = '$Revision: 1469 $';
        if ( preg_match( '(\\$Revision:\\s+(?P<revision>\\d+)\\s*\\$)', $version, $match ) )
        {
            $version = '0.0.' . $match['revision'] . '-svn';
        }

        return $version;
    }

    /**
     * Get non-common options
     *
     * Get options declared by specific tool implementations, which are not the
     * common options declared by the base tool.
     *
     * @return array
     */
    protected function getNonCommonOptions()
    {
        $options = $this->getOptions();
        foreach ( $this->commonOptionGroups as $group )
        {
            foreach ( $group as $option )
            {
                unset( $options[$option] );
            }
        }

        return array_keys( $options );
    }

    /**
     * Process default options
     *
     * Handle the default options. Returns true, if the the execution should
     * stop afterwards, and false if the execution may continue.
     *
     * @param array $options
     * @return bool
     */
    protected function processDefaultOptions( array &$options )
    {
        // Print version information only?
        if ( $options['version'] )
        {
            $this->out->outputLine( $this->name );
            $this->out->outputLine( 'Version ' . $this->getVersion() );
            return true;
        }

        // Help output?
        if ( $options['help'] )
        {
            echo $this->in->getHelpText(
                $this->name . "\n\n" . $this->description,
                78,
                false, // Long help texts,
                null, // Show all options,
                array_merge(
                    $this->commonOptionGroups,
                    array(
                        $this->name => $this->getNonCommonOptions(),
                    )
                )
            );
            return true;
        }

        // Colorize output, if not deactivated
        if ( !$options['no-colors'] )
        {
            $this->out->formats->fatal->color   = 'red';
            $this->out->formats->fatal->style   = array( 'bold' );
            $this->out->formats->error->color   = 'red';
            $this->out->formats->warning->color = 'yellow';
            $this->out->formats->notice->color  = 'blue';
            $this->out->formats->debug->color   = 'gray';
        }

        // Handle quiet / verbose options
        if ( $options['quiet'] )
        {
            $this->out->options->verbosityLevel = 1;
        }
        elseif ( $options['verbose'] )
        {
            $this->out->options->verbosityLevel = 100;
        }

        return false;
    }

    /**
     * Process module options
     *
     * Handle the module options. Returns true, if the the execution should
     * stop afterwards, and false if the execution may continue.
     *
     * @param array $options
     * @return bool
     */
    protected function processOptions( array &$options )
    {
        // Just do nothing by default
        return false;
    }

    /**
     * Login user
     *
     * Either login a default user, with full access to everything, or use the
     * specified user to execute the request.
     *
     * @param arbitRequest $request
     * @param array $options
     * @return void
     */
    protected function login( arbitRequest $request, array $options )
    {
        if ( $options['user'] !== false )
        {
            arbitSession::login( new arbitModelUser( $options['user'] ) );
        }

        // Login as a default shell user, with all permissions
        $userName = getenv( 'USER' );

        $conf = arbitBackendIniConfigurationManager::getProjectConfiguration( $request->controller );
        foreach ( $conf->administrators as $admin )
        {
            try
            {
                $user = new arbitModelUser( $admin );

                if ( $user->valid === '1' )
                {
                    ezcLog::getInstance()->log( "Log in as user '$admin'.", ezcLog::INFO );
                    return arbitSession::login( $user );
                }
            }
            catch ( arbitFacadeNotFoundException $e )
            {
                // Gracefully handle non existing users in log file
                ezcLog::getInstance()->log( $e->getMessage(), ezcLog::WARNING );
            }
        }

        ezcLog::getInstance()->log( "No valid admin user found for project '{$request->controller}'.", ezcLog::WARNING );
    }

    /**
     * Execute request for one project
     *
     * @param array $projects
     * @param array $options
     * @return void
     */
    protected function executeRequests( array $projects, array $options )
    {
        $aggregator = new arbitViewCliContextModel();
        $views      = array();

        foreach ( $projects as $project )
        {
            ezcLog::getInstance()->log( "Execute request for project '$project'.", ezcLog::INFO );

            // Create request struct and call controller
            $request = new arbitCliRequest( $project );
            $request->variables = $options;
            $request->extension = $options['output'];

            // Initialize session
            arbitSession::setBackend( new arbitMemorySessionBackend() );
            arbitSession::initialize( $request, true );

            // Set current project / controller dependant default controller
            arbitCacheRegistry::setCache( $request->controller );
            arbitCacheRegistry::setDefaultCache( $request->controller );

            if ( ( $request->controller !== 'core' ) &&
                 ( $request->controller !== 'error' ) )
            {
                // Embed module context in request
                $projectConfig = arbitBackendIniConfigurationManager::getProjectConfiguration( $project );
                $modules = array();
                foreach ( $projectConfig->modules as $name => $type )
                {
                    $modules[arbitProjectController::normalizeModuleName( $name )] = $type;
                }
                $request->variables['arbit_modules'] = $modules;

                arbitProjectController::initialize( $request );

                // Login requested or default user
                $this->login( $request, $options );
            }

            $controller = $this->createController( $request, $options );
            $views[]    = $controller->createResult()->view;
        }

        $aggregator->views = $views;
        $viewManager       = new arbitViewManager( $request, new arbitResult( $aggregator ) );
        $response          = $viewManager->createResponse();

        $writer = new arbitCliResponseWriter( $response, $this->out );
        $writer->handleResponse();
    }

    /**
     * Run cli command
     *
     * @return void
     */
    public function run()
    {
        try
        {
            $this->in->process();
            $options = $this->getOptions();

            if ( $this->processDefaultOptions( $options ) )
            {
                return;
            }

            if ( $this->processOptions( $options ) )
            {
                return;
            }

            // Register modules
            $conf = arbitBackendIniConfigurationManager::getMainConfiguration();
            arbitCacheRegistry::setCache( 'core' );
            arbitModuleManager::registerModule( 'core' );
            arbitModuleManager::activateModule( 'core' );
            foreach ( $conf->modules as $module )
            {
                arbitModuleManager::registerModule( $module );
            }

            // If no project is given, default to all projects
            if ( $options['project'] === false )
            {
                $options['project'] = array_merge(
                    array( 'core' ),
                    $conf->projects
                );
            }

            // Execute request for each specified project
            $this->executeRequests( $options['project'], $options );
        }
        catch ( Exception $e )
        {
            $this->out->outputLine( $e->getMessage(), 'error', 0 );
            $this->out->outputLine( (string) $e, 'debug', 100 );
        }
    }
}

