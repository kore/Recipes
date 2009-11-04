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
 * @package Tracker
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * XHtml view handler generating XHtml from the provided view model.
 *
 * @package Tracker
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class arbitModuleTemplateViewHandler extends arbitTemplateViewHandler
{
    /**
     * Method template association for default calls.
     *
     * In most cases we just assign some template to a view more, for which a
     * method on this class is called. The normal plain procedure may just be
     * mapped using this array and is handled inside the __call method of this
     * class.
     *
     * For callStatic all method names need to be lowercase.
     *
     * @var array
     */
    protected static $moduleTemplates = array(
    );

    /**
     * Configure tempalte library
     *
     * @return void
     */
    protected static function configureTemplate()
    {
        // Configure the used templating system
        $config = ezcTemplateConfiguration::getInstance();
        $config->templatePath = ARBIT_BASE . 'templates';
        $config->compilePath  = ARBIT_CACHE_PATH . 'templates';
        $config->context      = new ezcTemplateNoContext();

        // Add translation capabilities to template handler
        $config->translation = ezcTemplateTranslationConfiguration::getInstance();
        $config->translation->manager = new arbitTranslationManager();
    }

    /**
     * Default template handler
     *
     * The default template handlers checks if a template has been registered
     * for the received call and sends the module to the registered template
     * without any modification or enhancement. The registered templates for
     * the calls may be found in the protected class property $moduleTemplates.
     *
     * @param string $method
     * @param array $parameters
     * @return string
     */
    public static function __callStatic( $method, array $parameters )
    {
        // Perform parameter check, as we cannot do this via a typehint.
        if ( !isset( $parameters[0] ) ||
             ( !$parameters[0] instanceof arbitDecorateable ) )
        {
            throw new arbitRuntimeException( 'Broken model passed to renderer.' );
        }
        $model = $parameters[0];

        // Check that we really have a template
        if ( !isset( static::$moduleTemplates[$method] ) )
        {
            throw new arbitRuntimeException( "No template registered for call '$method'." );
        }

        // Convert issue business models into view models
        if ( isset( $model->issue ) )
        {
            $model->issue = $model->issue;
        }

        // Call template system
        static::configureTemplate();
        $template = new ezcTemplate();
        $template->send->model = $model;
        $template->process(
            arbitViewTemplateFunctions::getTemplatePath( static::$moduleTemplates[$method] )
        );

        static::$returnValues = $template->receive;
        return $template->output;
    }
}

