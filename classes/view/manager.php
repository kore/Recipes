<?php
/**
 * arbit view manager
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
 * View manager, which handles a set of configured views and displays them
 * using the configured view handler.
 *
 * @package Core
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitViewManager extends ezcMvcView
{
    /**
     * Configured view handler, which is used to display the template with the
     * parameters given by the controller for a set of mimetypes.
     *
     * @var array
     */
    protected $viewHandler = array(
        'text/html'             => 'arbitViewXHtmlHandler',
        'text/xml'              => 'arbitViewXmlHandler',
        'text/text'             => 'arbitViewTextHandler',
        'application/json'      => 'arbitViewJsonHandler',
    );

    /**
     * List with extension mimetype assoziations, which force the view manager
     * to use the configured view handler for the here given mimetype.
     *
     * @var array
     */
    protected $forcedMimetypes = array(
        'xml'  => 'text/xml',
        'html' => 'text/html',
        'txt'  => 'text/text',
        'js'   => 'application/json',
    );

    /**
     * The preferred mimetype to use, if the client did not provide any
     * information or does not provide any valid mimetypes, or the client
     * accepts all mimetype at the same priority.
     *
     * @var string
     */
    protected $defaultMimetype = 'text/html';

    /**
     * As it is not possible to get a list with supported charsets from the
     * iconv extension, we just maintain a list of commonly available charsets
     * here and hope they are supported everywhere.
     *
     * If you notice some client does not support any of the here listed
     * charsets, feel free to add new ones. They should all be lowercase.
     *
     *  @var array
     */
    protected $commonCharsets = array(
        // Very common charsets
        'utf-8', 'iso-8859-1', 'koi-8',
        // Common MS IE charsets
        'windows-1250', 'windows-1251', 'windows-1252',
    );

    /**
     * Construct view handler
     *
     * @param ezcMvcRequest $request
     * @param ezcMvcResult $result
     * @return void
     */
    public function __construct( ezcMvcRequest $request, ezcMvcResult $result )
    {
        $this->request = $request;
        $this->result  = $result->view;
    }

    /**
     * The user-implemented that returns the zones.
     *
     * This method creates all the zones that are needed to render a view. A
     * zone is an array of elements that implement a view handler. The view
     * handlers do not have to be of the same type, as long as they implement
     * the ezcMvcViewHandler interface.
     *
     * The $layout parameter can be used to determine whether a "page layout" should
     * be added to the list of zones. This can be useful in case you're incorporating
     * many different applications. The $layout parameter will be set to true automatically
     * for the top level createZones() method, which can then chose to add zones from
     * other views as well. The createZones() methods from those other views should
     * have the $layout parameter set to false.
     *
     * @param bool $layout
     *
     * @return array(ezcMvcViewHandler)
     */
    public function createZones( $layout )
    {
        return array();
    }

    /**
     * Set view handler for mimetype
     *
     * Set the view handler for a specific mimetype, like a renderer for
     * text/xhtml content, or a renderer for text/xml or application/json.
     *
     * @param string $mimetype
     * @param string $handler
     * @return void
     */
    public function setViewHandler( $mimetype, $handler )
    {
        $this->viewHandler[$mimetype] = $handler;
    }

    /**
     * Get preferred accept header value
     *
     * Matches the user provided priorized accept header list against the list
     * of available mimtyoes / encodings /... specific renderers and returns
     * the best matching value, or the default one, if no proper matches were
     * found.
     *
     * @param array $supported
     * @param array $available
     * @param string $default
     * @return string
     */
    protected function getPreferred( array $supported, array $available, $default )
    {
        // The mimetypes are sorted by priority, so we can just use the first
        // one we find.
        foreach ( $supported as $type )
        {
            // If the clients accepts any value, indicated by am asterisk, we
            // skip processing and just return our default value.
            if ( $type['value'] === '*' )
            {
                break;
            }

            // Check if the value with the currently highest priority is
            // element of the values we support in our application.;
            if ( in_array( $type['value'], $available, true ) )
            {
                return $type['value'];
            }
        }

        // If we did not find anything, use the default
        return $default;
    }

    /**
     * Get preferred encoding
     *
     * Check if the client support UTF-8, and uses it. Otherwise we will use to
     * a value we and the client supports.
     *
     * Once determined the encoding value is stored in the session, so that we
     * know about it, when we need to decode client input values.
     *
     * @param array $supported
     * @param string $default
     * @return string
     */
    protected function getClientEncoding( array $supported, $default )
    {
        try
        {
            // Return the determined encoding from the client session
            return arbitSession::getGlobal( 'ClientEncoding' );
        }
        catch ( arbitPropertyException $e )
        {
            // Recalculate session value, if the setting is not yet available.
        }

        // If the client does support the given default encoding, just use it
        // to minimize conversions in the backend.
        foreach ( $supported as $type )
        {
            if ( ( $type['value'] === $default ) ||
                 ( $type['value'] === '*' ) )
            {
                return arbitSession::setGlobal( 'ClientEncoding', $default );
            }
        }

        // The encodings are sorted by priority, so we can just use the first
        // one we find.
        //
        // If we did not find anything, use the default
        return arbitSession::setGlobal(
            'ClientEncoding',
            $this->getPreferred( $supported, $this->commonCharsets, $default )
        );
    }

    /**
     * Display controller result
     *
     * Lets the router parse the given URL and calls the controller depending
     * on the router results.
     *
     * The sending of headers may be supressed by setting the optional third
     * parameter to false, but this should normally only be used during
     * testing.
     *
     * Optionally a ezcMvcResult may be passed, which then is processed instead
     * of the result passed to the constructor.
     *
     * @param ezcMvcResult $result
     * @return void
     */
    public function createResponse( ezcMvcResult $result = null )
    {
        // Default to response passed to the constructor
        if ( $result === null )
        {
            $result = $this->result;
        }

        // For binary data we do not need to check anything, but just output it
        if ( $result instanceof arbitViewDataModel )
        {
            $response = new ezcMvcResponse();
            $response->body = $result->content;
            $response->content = new ezcMvcResultContent(
                'en', $result->mimetype
            );

            if ( $result->filename )
            {
                $response->content->disposition = new ezcMvcResultContentDisposition(
                    'attachment', $result->filename
                );
            }

            return $response;
        }

        // Otherwise check the designated mime type on base of extension and
        // preferred mime types
        if ( ( $this->request->extension !== null ) &&
             ( isset( $this->forcedMimetypes[$this->request->extension] ) ) )
        {
            // Use mimetype forced by extension, if mapping is available;
            $mimetype = $this->forcedMimetypes[$this->request->extension];
        }
        else
        {
            // Get preferred mimetype
            $mimetype = $this->getPreferred(
                $this->request->accept->types,
                array_keys( $this->viewHandler ),
                $this->defaultMimetype
            );
        }

        // Get preferred encoding
        $convert = $this->getClientEncoding(
            $this->request->accept->charsets,
            $charset = 'utf-8'
        );

        // Once we start creating the view, we can close the session, since
        // writes to the session should only occur in the controllers anyways.
        arbitSession::close();

        // Add charset and mimetype information to view model
        $result->charset  = $convert;
        $result->mimetype = $mimetype;

        // Let selected view handler generate the output
        $viewHandler = $this->viewHandler[$mimetype];
        $handler     = new $viewHandler( $this->request );

        // Set view handler locale, if set in the result
        if ( $result->language !== null )
        {
            $handler->setLocale( $result->language );
        }

        // Let view handler generate output
        $body = $handler->display( $result );

        // If clients wants another outpout encoding then UTF-8, convert using
        // iconv
        if ( $convert !== $charset )
        {
            try
            {
                // Transliterate characters, which cannot be converted lossless
                $body    = iconv( 'utf-8', $convert . '//TRANSLIT', $body );
                $charset = $convert;
            }
            catch ( arbitPhpErrorException $e )
            {
                // Do nothing, conversion failed, so we stay with originally
                // provided string.
            }
        }

        $response = new ezcMvcResponse();
        $response->generator = 'Arbit $Revision: 1236 $';
        $response->body      = $body;
        $response->content   = new ezcMvcResultContent(
            'en', $mimetype, $charset
        );
        return $response;
    }
}

