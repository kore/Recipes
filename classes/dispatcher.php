<?php
/**
 * arbit dispatcher configuration
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
 * Basic arbit request dispatcher configuration
 *
 * @package Core
 * @version $Revision: 1469 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitDispatcherConfiguration implements ezcMvcDispatcherConfiguration
{
    /**
     * Creates the request parser able to produce a relevant request object
     * for this session.
     *
     * @return ezcMvcRequestParser
     */
    public function createRequestParser()
    {
        return new arbitHttpRequestParser();
    }

    /**
     * Create the router able to instantiate a relevant controller for this
     * request.
     *
     * @param ezcMvcRequest $request
     * @return ezcMvcRouter
     */
    public function createRouter( ezcMvcRequest $request )
    {
        // Build router for selected controller
        return new arbitHttpRouter( $request );
    }

    /**
     * Creates the view handler that is able to process the result.
     *
     * @param ezcMvcRoutingInformation $routeInfo
     * @param ezcMvcRequest $request
     * @param ezcMvcResult $result
     * @return ezcMvcViewHandler
     */
    public function createView( ezcMvcRoutingInformation $routeInfo, ezcMvcRequest $request, ezcMvcResult $result )
    {
        return new arbitViewManager( $request, $result );
    }

    /**
     * Creates a response writer that uses the response and sends its
     * output.
     *
     * This method should be able to pick different response writers, but the
     * response writer itself will only know about the $response.
     *
     * @param ezcMvcRoutingInformation $routeInfo
     * @param ezcMvcRequest $request
     * @param ezcMvcResult $result
     * @param ezcMvcResponse $response
     * @return ezcMvcResponseWriter
     */
    public function createResponseWriter( ezcMvcRoutingInformation $routeInfo, ezcMvcRequest $request, ezcMvcResult $result, ezcMvcResponse $response )
    {
        return new ezcMvcHttpResponseWriter( $response );
    }

    /**
     * Create the default internal redirect object in case something goes
     * wrong in the views.
     *
     * @param ezcMvcRequest $request
     * @param ezcMvcResult $result
     * @param ezcMvcResponse $response
     * @return ezcMvcInternalRedirect
     */
    public function createFatalRedirectRequest( ezcMvcRequest $request, ezcMvcResult $result, Exception $response )
    {
        $errorRequest = clone $request;
        $errorRequest->controller = 'error';
        $errorRequest->action     = 'index';
        $errorRequest->variables = array(
            'exception' => $response,
            'original'  => $request,
        );

        return $errorRequest;
    }

    /**
     * Runs all the pre-routing filters that are deemed necessary depending on
     * information in $request.
     *
     * The pre-routing filters could modify the request data so that a
     * different router can be chosen.
     *
     * @param ezcMvcRequest $request
     * @return void
     */
    public function runPreRoutingFilters( ezcMvcRequest $request )
    {
        ezcLog::getInstance()->log( "Parsed request: " . $request->serialize(), ezcLog::INFO );
    }

    /**
     * Runs all the request filters that are deemed necessary depending on
     * information in $routeInfo and $request.
     *
     * This method can return an object of class ezcMvcInternalRedirect in case
     * the filters require this. A reason for this could be in case an
     * authentication filter requires authentication credentials to be passed
     * in through a login form. The method can also not return anything in case
     * no redirect is necessary.
     *
     * @param ezcMvcRoutingInformation $routeInfo
     * @param ezcMvcRequest $request
     * @return ezcMvcInternalRedirect|null
     */
    public function runRequestFilters( ezcMvcRoutingInformation $routeInfo, ezcMvcRequest $request )
    {
        ezcLog::getInstance()->log( "Inferenced route: {$routeInfo->controllerClass}::{$routeInfo->action}.", ezcLog::INFO );

        // Set current project / controller dependant default controller
        arbitCacheRegistry::setDefaultCache( $request->controller );

        // Start session, within currently selected project / controller
        // context
        arbitSession::setBackend( new arbitHttpSessionBackend() );
        arbitSession::initialize( $request );

        // No filters required yet.
        arbitUserController::checkPersitentLogin( $request );
    }

    /**
     * Runs all the request filters that are deemed necessary depending on
     * information in $routeInfo, $request and $result.
     *
     * @param ezcMvcRoutingInformation $routeInfo
     * @param ezcMvcRequest $request
     * @param ezcMvcResult $result
     * @return void
     */
    public function runResultFilters( ezcMvcRoutingInformation $routeInfo, ezcMvcRequest $request, ezcMvcResult $result )
    {
        $result->view = new arbitViewMainModel( $result->view );
    }

    /**
     * Runs all the request filters that are deemed necessary depending on
     * information in $routeInfo, $request, $result and $response.
     *
     * @param ezcMvcRoutingInformation $routeInfo
     * @param ezcMvcRequest $request
     * @param ezcMvcResultt $result
     * @param ezcMvcResponse $response
     * @return void
     */
    public function runResponseFilters( ezcMvcRoutingInformation $routeInfo, ezcMvcRequest $request, ezcMvcResult $result, ezcMvcResponse $response )
    {
        // No filters required yet.
    }
}

