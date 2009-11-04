<?php
/**
 * arbit view handler
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
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Text view handler generating emails from the provided view model.
 *
 * @package Core
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class arbitTemplateViewHandler extends arbitViewHandler
{
    /**
     * Method template association for default calls.
     *
     * In most cases we just assign some template to a view more, for which a
     * method on this class is called. The normal plain procedure may just be
     * mapped using this array and is handled inside the __call method of this
     * class.
     *
     * @var array
     */
    protected $templates = array(
    );

    /**
     * Template extensions used by the view handler.
     *
     * @var array
     */
    protected $extensions = array(
        'arbitViewTemplateFunctions',
    );

    /**
     * Storage for tempalte return values from last call.
     *
     * @var array
     */
    protected static $returnValues = array();

    /**
     * Template handler configuration
     *
     * @var ezcTemplateConfiguration
     */
    protected $config = null;

    /**
     * Construct view handler from reuqest object.
     *
     * Construct view handler from reuqest object.
     *
     * @ignore
     * @param arbitRequest $request
     * @return void
     */
    public function __construct( arbitRequest $request )
    {
        parent::__construct( $request );

        // Configure the sued templating system
        $this->config = ezcTemplateConfiguration::getInstance();
        $this->config->templatePath = ARBIT_BASE . 'templates';
        $this->config->compilePath  = ARBIT_CACHE_PATH . 'templates';
        $this->config->context = new ezcTemplateNoContext();

        // Add translation capabilities to template handler
        $this->config->translation = ezcTemplateTranslationConfiguration::getInstance();
        $this->config->translation->manager = new arbitTranslationManager();

        // Add custom arbit template extensions
        foreach ( $this->extensions as $extension )
        {
            $this->config->addExtension( $extension );
        }
    }

    /**
     * Set view locale
     *
     * Set the view locale to the given locale string.
     *
     * @param string $locale
     * @return void
     */
    public function setLocale( $locale )
    {
        $this->config->translation->locale = $locale;
    }

    /**
     * Enriches view model with common context information
     *
     * @param arbitDecorateable $model
     * @return arbitDecorateable
     */
    public function addContextInformation( arbitDecorateable $model )
    {
        $model->request   = $this->request;
        $model->debugMode = ARBIT_DEBUG;
        $model->loggedIn  = arbitSession::get( 'login' );

        return $model;
    }

    /**
     * Display controller result
     *
     * Select the view, which should be used to display the controller results,
     * process and return the view results.
     *
     * @param arbitDecorateable $output
     * @return string
     */
    public function display( arbitDecorateable $output )
    {
        arbitViewModelDecorationDependencyInjectionManager::setViewHandler( $this );
        return arbitViewModelDecorationDependencyInjectionManager::decorate( $output );
    }

    /**
     * Show default model
     *
     * Display an arbitrary model with some default view.
     *
     * @param arbitViewErrorModel $model
     * @return string
     */
    public function showDefaultModel( arbitViewModel $model )
    {
        throw new arbitRuntimeException(
            'No specific display handler for model ' . get_class( $model ) . '.'
        );
    }

    /**
     * Default template handler
     *
     * The default template handlers checks if a template has been registered
     * for the received call and sends the module to the registered template
     * without any modification or enhancement. The registered templates for
     * the calls may be found in the protected class property $templates.
     *
     * @param string $method
     * @param array $parameters
     * @return string
     */
    public function __call( $method, array $parameters )
    {
        // Perform parameter check, as we cannot do this via a typehint.
        if ( !isset( $parameters[0] ) ||
             ( !$parameters[0] instanceof arbitDecorateable ) )
        {
            throw new arbitRuntimeException( 'Broken model passed to renderer.' );
        }

        // Check that we really have a template
        if ( !isset( $this->templates[$method] ) )
        {
            throw new arbitRuntimeException( "No template registered for call '$method'." );
        }

        // Call template system
        $template = new ezcTemplate();
        $template->send->model = $parameters[0];
        $template->process(
            arbitViewTemplateFunctions::getTemplatePath( $this->templates[$method] )
        );

        static::$returnValues = $template->receive;
        return $template->output;
    }

    /**
     * Get Template return value from last processed template.
     *
     * @param string $property
     * @return mixed
     */
    public function getReturnValue( $property )
    {
        return static::$returnValues->$property;
    }
}


