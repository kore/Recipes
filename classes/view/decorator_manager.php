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
 * View decoration manager.
 *
 * The view decoration manager knows about a list of decorators for view
 * models, which are stored dependant on the view handler, which allows
 * different decorators for different output types.
 *
 * The decorators for view models are specified through callbacks to the
 * decorators methods. If the callback is a string, which would normally just
 * call some global function, it is interpreted as a non-static call on the
 * view handler object.
 *
 * Each call to a callback may either return the same object again, a different
 * view model object, or a string.
 *
 * - If a string (or another scala, array or an object not inheriting from
 *   arbitDecorateable) has been returned, the view generation is considered
 *   finished for this substructure and may just be displayed.
 *
 * - If a different model has been returned the process restarts for the new
 *   model type.
 *
 * - If the same object, or a object of the same class, has been returned the
 *   next decorator in the callback stack will be called for this object.
 *
 * If there is no callback defined for a view model, the callbacks for the
 * parent classes of the view model will be used in descending depth. There
 * should always be a callback for the root class (arbitDecorateable), so that
 * everything will be displayed somehow.
 *
 * @package Core
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitViewModelDecorationDependencyInjectionManager
{
    /**
     * Array containing the decorator callbacks.
     *
     * Each callback is specified for the used view handler to allow view
     * handler specific callbacks, like a diffferent decoration for JSON and
     * XML output.
     *
     * A callback is specified for a specific view model struct. If there are
     * no callbacks found for one struct, the callbacks defined for its parent
     * classes are used. There may be multiple callbacks registered for one
     * struct, which are called subsequently.
     *
     * The array has the following structure:
     * <code>
     *  array(
     *      'arbitViewHandler' => array(
     *          'arbitViewModel' => array(
     *              Callback,
     *              Callback,
     *              ...
     *          ),
     *          ...
     *      ),
     *      ...
     *  )
     * </code>
     *
     * @var array
     */
    protected static $decorators = array(
        /**
         * For the XHtml handler we want to decorate nearly each structure
         * differently, so we get quite a lot entries here, but there is no
         * possibility for a general transformation handling and thise methods
         * get all dispatched to templates in the view handler.
         */
        'arbitViewXHtmlHandler' => array(
            // Main controller view model decorators
            'arbitViewModel' => array(
                // This just causes a runtime exception
                'showDefaultModel',
            ),
            'arbitViewMainModel' => array(
                'addContextInformation',
                'showMain',
            ),
            'arbitViewErrorContextModel' => array(
                'addContextInformation',
                'showError',
            ),
            'arbitViewErrorNotFoundContextModel' => array(
                'addContextInformation',
                'showNotFoundError',
            ),
            'arbitViewUserMessageModel' => array(
                'addContextInformation',
                'showMessage',
            ),
            'arbitViewDataModel' => array(
                array( 'arbitViewHttpBinaryHandler', 'showBinaryData' ),
            ),

            // Core controller special view model decorators
            'arbitViewCoreUserRegistrationModel' => array(
                'addContextInformation',
                'showCoreUserRegistrationModel',
            ),
            'arbitViewCoreUserLoginModel' => array(
                'addContextInformation',
                'showCoreUserLoginModel',
            ),
            'arbitViewCoreUserRegisteredModel' => array(
                'addContextInformation',
                'showCoreUserRegisteredModel',
            ),
            'arbitViewCoreUserModel' => array(
                'addContextInformation',
                'showCoreUserModel',
            ),

            // Core models for recipe handling
            'arbitViewRecipeViewModel' => array(
                'addContextInformation',
                'showRecipeViewModel',
            ),
            'arbitViewRecipeEditModel' => array(
                'addContextInformation',
                'showRecipeEditModel',
            ),
            'arbitViewRecipeOverviewModel' => array(
                'addContextInformation',
                'showRecipeOverviewModel',
            ),
            'arbitViewRecipeTagsModel' => array(
                'addContextInformation',
                'showRecipeTagsModel',
            ),
            'arbitViewRecipeSearchModel' => array(
                'addContextInformation',
                'showRecipeSearchModel',
            ),
            'arbitViewRecipeTagModel' => array(
                'addContextInformation',
                'showRecipeTagModel',
            ),
            'arbitViewRecipeIngredientModel' => array(
                'addContextInformation',
                'showRecipeIngredientModel',
            ),
            'arbitViewRecipeListModel' => array(
                'addContextInformation',
                'showRecipeListModel',
            ),
        ),

        /**
         * For the email handler we only need to decorate those structures,
         * which actually send via mail, but there is no general way to format
         * those mails, and they should all be customizeable through templates.
         */
        'arbitViewEmailHandler' => array(
            // Main controller view model decorators
            'arbitViewModel' => array(
                // This just causes a runtime exception
                'showDefaultModel',
            ),
            'arbitViewErrorContextModel' => array(
                'addContextInformation',
                'showError',
            ),
            'arbitViewCoreUserRegisteredModel' => array(
                'addContextInformation',
                'showCoreUserRegisteredModel',
            ),
        ),

        /**
         * The XML display handler does only need special assignements for
         * custom values. Most stff should just be ahndled by the default
         * decorator which serializes all PHP structures to XML.
         */
        'arbitViewXmlHandler' => array(
            'arbitViewModel' => array(
                'showDefaultModel',
            ),
            'arbitViewErrorContextModel' => array(
                'showError',
            ),
            'arbitViewUserModel' => array(
                'showUser',
            ),
        ),

        /**
         * The JSON display handler does only need special assignements for
         * custom values. Most stuff should just be handled by the default
         * decorator which serializes all PHP structures to JSON.
         */
        'arbitViewJsonHandler' => array(
            'arbitViewModel' => array(
                'showDefaultModel',
            ),
            'arbitViewErrorContextModel' => array(
                'showError',
            ),
            'arbitViewUserModel' => array(
                'showUser',
            ),
        ),
    );

    /**
     * Currently used view handler instance.
     *
     * @var arbitViewHandler
     */
    protected static $viewHandler = null;

    /**
     * Add a decorator
     *
     * Add a new or additional decorator for a view model.
     *
     * @param string $handler
     * @param string $model
     * @param callback $callback
     * @return void
     */
    public static function addDecorator( $handler, $model, $callback )
    {
        if ( !isset( self::$decorators[$handler] ) ||
             !isset( self::$decorators[$handler][$model] ) )
        {
            self::$decorators[$handler][$model] = array( $callback );
        }
        else
        {
            // New callbacks are prepended, so they are not in the list after
            // the last (string generating) callback.
            array_unshift(
                self::$decorators[$handler][$model],
                $callback
            );
        }
    }

    /**
     * Set current view handler
     *
     * Set the view handler to look up decorators for, and which will be used
     * to call non-static decorator callbacks on. (See class description for
     * details).
     *
     * @param arbitViewHandler $handler
     * @return void
     */
    public static function setViewHandler( arbitViewHandler $handler )
    {
        self::$viewHandler = $handler;
    }

    /**
     * Decorate a view model
     *
     * Applies available decorators to the given vew model until somthing
     * displayable has been generated. The algorithm used for this is described
     * in the class level documentation.
     *
     * The method will return the decorator result, which will never be a
     * arbitDecorateable again, but may depend on the used decorator
     * implementations. In most implementations a string will be returned.
     *
     * @param arbitDecorateable $model
     * @return mixed
     */
    public static function decorate( arbitDecorateable $model )
    {
        // Ensure that there are decorators for the current view handler.
        if ( !isset( self::$decorators[$handlerName = get_class( self::$viewHandler )] ) )
        {
            throw new arbitViewNoDecoratorsExceptions( get_class( $model ), $handlerName );
        }

        // Find (parent) class with defined decoratos
        $classToDecorate = get_class( $model );
        while ( !isset( self::$decorators[$handlerName][$classToDecorate] ) )
        {
            if ( ( $classToDecorate = get_parent_class( $classToDecorate ) ) === false )
            {
                throw new arbitViewNoDecoratorsExceptions( get_class( $model ), $handlerName );
            }
        }

        // Decorator called, if multiple decorators are available
        $nr = 0;
        $count = count( self::$decorators[$handlerName][$classToDecorate] );
        do {
            $callback = self::$decorators[$handlerName][$classToDecorate][$nr++];

            if ( is_string( $callback ) &&
                 ( strpos( $callback, '::' ) === false ) )
            {
                // String callbacks are considered as non-static method calls
                // on the handler class.
                $callback = array( self::$viewHandler, $callback );
            }

            // Call the decorator and use the result for further processing
            $model = call_user_func( $callback, $model );

            // We consider the rendering finished as soon as we receive
            // something differnt then a arbitDecorateable
            if ( !is_object( $model ) ||
                 ( is_object( $model ) && ( !$model instanceof arbitDecorateable ) ) )
            {
                return $model;
            }

            // If the model class changed, we reinit the decoration process
            if ( ! $model instanceof $classToDecorate )
            {
                return self::decorate( $model );
            }
        } while ( $nr < $count );

        // We did not find any iterator which returned something else the a
        // view model. The decoration process has not been finished.
        throw new arbitViewDecorationFailedException( $classToDecorate );
    }
}

