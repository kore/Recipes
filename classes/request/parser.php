<?php
/**
 * arbit HTTP request class
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
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * The HTTP request class extends the ase request class by user submitted
 * information on accepted languages and content types.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitHttpRequestParser extends ezcMvcRequestParser
{
    /**
     * URL base path.
     *
     * When arbit does not reside in the document root, this variable point to
     * the location of the arbit directory. This is autodetected from PHP_SELF.
     *
     * @var string
     */
    protected $basePath = null;

    protected $acceptDefaultValues = array(
        'HTTP_ACCEPT_LANGUAGE' => array(
            array(
                'value'    => 'en',
                'priority' => 1.,
            ),
        ),
        'HTTP_ACCEPT_CHARSET' => array(
            array(
                'value'    => 'utf-8',
                'priority' => 1.,
            ),
        ),
        'HTTP_ACCEPT_ENCODING' => array(
            array(
                'value'    => 'identity',
                'priority' => 1.,
            ),
        ),
        'HTTP_ACCEPT' => array(
            array(
                'value'    => '*/*',
                'priority' => 1.,
            ),
        ),
    );

    /**
     * Uses the data from the superglobals.
     *
     * @return ezcMvcRequest
     */
    public function createRequest()
    {
        $this->request = new arbitHttpRequest();

        $this->parseUrl(
            arbitHttpTools::serverVariable( 'REQUEST_URI' )
        );
        $this->processAcceptHeaders();

        return $this->request;
    }

    /**
     * Parse a URL
     *
     * Parse the given URL and return a set of controller, action and optional
     * parameters to be handled in the application. The result should be a
     * arbitRequest object, which (besides other optional information) contains
     * the name of the controller, action and the paramters of the reuqest.
     *
     * @param string $url
     * @return void
     */
    protected function parseUrl( $url )
    {
        // Sanitize URL by removing preceeding paths
        $url = $this->sanitizeUrl( $url );

        // Get project configuration to know about a list of valid controllers
        $conf = arbitBackendIniConfigurationManager::getMainConfiguration();
        $projects = $conf->projects;

        // Prepare projects array for use with regular expression
        $projects = array_map( 'preg_quote', $projects );

        // Try to match controller and action using a regualr expression
        if ( !preg_match( '(^
          (?# Optionally specified whitelisted controller )
            (?:/(?P<controller>core|' . implode( '|', $projects ) . '))?
          (?# Optionally specified action, falls back to "index" )
            (?:/(?P<action>[a-zA-Z0-9_-]+)?
              (?# Optionally specified action, falls back to "index" )
                (?:/(?P<subaction>[a-zA-Z0-9_-]+))?
            )?
          (?# Optionally provided file extension )
            (:?\.(?P<ext>[a-z]+))?
          (?# Trailing stuff, removing an optional trailing slash )
            (?P<path>[^?]*)? /?
          (?# Optional query string )
            (?:\\?(?P<query>.*))?
            $)x', $url, $match ) )
        {
            throw new arbitRouterInvalidUrlException( $url );
        }

        // Parse query string, which may not be automatically available.
        if ( !empty( $match['query'] ) )
        {
             parse_str( $match['query'], $parameters );
        }
        else
        {
             $parameters = array();
        }
        $this->request->variables = $parameters;
        $this->request->cookies   = $_COOKIE;

        // Extract controller and action from URL
        $this->request->controller = ( !empty( $match['controller'] ) ? $match['controller'] : 'core' );
        $this->request->action     = ( !empty( $match['action'] )     ? $match['action']     : 'index' );
        $this->request->subaction  = ( !empty( $match['subaction'] )  ? $match['subaction']  : 'index' );
        $this->request->path       = ( !empty( $match['path'] )       ? $match['path']       : '' );
        $this->request->extension  = ( !empty( $match['ext'] )        ? $match['ext']        : null );

        // Build basic request structure from URL information
        $this->request->uri =
            $this->request->controller . '/' . $this->request->action . '/' .
            $this->request->subaction . '/' . $this->request->path .
            $this->request->extension !== null  ? '.' . $this->request->extension : '';

        // Store base path in request
        $this->request->root = $this->basePath;

        // Extract used request method
        $this->request->protocol = 'http-' . strtolower( arbitHttpTools::serverVariable( 'REQUEST_METHOD' ) );
    }

    /**
     * Process HTTP accept headers
     *
     * @return void
     */
    protected function processAcceptHeaders()
    {
        // Add additional request information, if provided by the requesting
        // browser.
        $additionalInformation = array(
            'languages'  => 'HTTP_ACCEPT_LANGUAGE',
            'charsets'   => 'HTTP_ACCEPT_CHARSET',
            'encodings'  => 'HTTP_ACCEPT_ENCODING',
            'types'      => 'HTTP_ACCEPT',
        );

        $accept = new ezcMvcRequestAccept();
        foreach ( $additionalInformation as $property => $key )
        {
            $value = arbitHttpTools::serverVariable( $key );
            if ( empty( $value ) ||
                 ( $accept->$property = $this->parseAcceptHeader( $value ) ) === false )
            {
                $accept->$property = $this->acceptDefaultValues[$key];
            }
        }

        $this->request->accept = $accept;
    }

    /**
     * Parse HTTP Accept headers
     *
     * Parse the typical HTTP accept headers and return an array with the
     * extracted information sorted by priority. The returned array will look
     * like:
     *  array(
     *      array(
     *          'value'     => (string),
     *          'priority'  => (float),
     *          'country'   => (string | null),
     *      ), ...
     *  )
     *
     * @param string $header
     * @return array
     */
    protected function parseAcceptHeader( $header )
    {
        if ( !preg_match_all(
                '(
                    (?P<value>[a-z*][a-z0-9_/*+.-]*)
                        (?:;q=(?P<priority>[0-9.]+))?
                 \\s*(?:,|$))ix', $header, $matches, PREG_SET_ORDER ) )
        {
            return false;
        }

        // Fill array structure containing the pririty values.
        //
        // Also fill up a key array with the priority values, to sort the array
        // structures in a second pass by priority.
        $accept = array();
        $priority = array();
        foreach ( $matches as $values )
        {
            $accept[] = array(
                'value' => ( isset( $values['value'] ) ? strtolower( $values['value'] ) : null ),
                'priority' => ( $priority[] = ( isset( $values['priority'] ) ? (float) $values['priority'] : 1. ) ),
            );
        }

        // Sort array descending by priority array.
        array_multisort(
            $priority, SORT_NUMERIC, SORT_DESC,
            $accept
        );

        return $accept;
    }

    /**
     * Remove basepath from url
     *
     * If arbit resides in a subdirectory, and not in the htdocs folder itself,
     * remove the preceeding irrlevant stuff from the URL and store it for the
     * URL serialitzation.
     *
     * @param string $url
     * @return string
     */
    protected function sanitizeUrl( $url )
    {
        if ( ( $pos = strpos( $url, '.phar' ) ) !== false )
        {
            $this->basePath = substr( $url, 0, $pos + 5 );
        }
        else
        {
            $this->basePath = dirname( arbitHttpTools::serverVariable( 'PHP_SELF' ) );
        }

        // Dirname returns a single / for a root path, but we NEVER want a
        // trailing slash
        if ( $this->basePath === '/' )
        {
            $this->basePath = '';
        }

        // Use only the stuff following teh base path for routing
        return str_replace( $this->basePath, '', $url );
    }
}

