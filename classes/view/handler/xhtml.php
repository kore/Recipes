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
 * XHtml view handler generating XHtml from the provided view model.
 *
 * @package Core
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitViewXHtmlHandler extends arbitTemplateViewHandler
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
        'showMain'                      => 'html/layout.tpl',
        'showError'                     => 'html/exception.tpl',
        'showNotFoundError'             => 'html/not_found.tpl',
        'showMessage'                   => 'html/message.tpl',
        'showCoreUserRegistrationModel' => 'html/core/user/registration.tpl',
        'showCoreUserRegisteredModel'   => 'html/core/user/registered.tpl',
        'showCoreUserLoginModel'        => 'html/core/user/login.tpl',
        'showCoreUserModel'             => 'html/core/user.tpl',
        'showRecipeViewModel'           => 'html/recipe/view.tpl',
        'showRecipeEditModel'           => 'html/recipe/edit.tpl',
        'showRecipeTagsModel'           => 'html/recipe/tags.tpl',
        'showRecipeOverviewModel'       => 'html/recipe/overview.tpl',
        'showRecipeSearchModel'         => 'html/recipe/search.tpl',
        'showRecipeTagModel'            => 'html/recipe/tag.tpl',
        'showRecipeIngredientModel'     => 'html/recipe/ingredient.tpl',
        'showRecipeListModel'           => 'html/recipe/list.tpl',
        'showRecipeIndexModel'          => 'html/recipe/index.tpl',
    );

    /**
     * Template extensions used by the view handler.
     *
     * @var array
     */
    protected $extensions = array(
        'arbitViewTemplateFunctions',
        'arbitViewXHtmlTemplateFunctions',
    );

    /**
     * List with additional CSS files to include in the output. May be extended
     * by modules and is filled with the configuration values by default.
     *
     * @var array
     */
    protected static $cssFiles = array();

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

        // Load CSS files, which should be included, from configuration.
        $conf = arbitBackendIniConfigurationManager::getMainConfiguration();
        self::$cssFiles = $conf->get( 'layout', 'css' );

        $this->config->context = new ezcTemplateXhtmlContext();
    }

    /**
     * Add CSS files
     *
     * Add additional CSS files included in HTML output.
     *
     * @param string $name
     * @return void
     */
    public static function addCssFile( $name )
    {
        array_shift( self::$cssFiles, $name );
    }

    /**
     * Enriches view model with common context information
     *
     * @param arbitDecorateable $model
     * @return arbitDecorateable
     */
    public function addContextInformation( arbitDecorateable $model )
    {
        $model = parent::addContextInformation( $model );
        $model->cssFiles  = self::$cssFiles;

        return $model;
    }
}

