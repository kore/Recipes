<?php
/**
 * CLI response writer class
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
 * Response writer for CLI output
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitCliResponseWriter extends ezcMvcResponseWriter
{
    /**
     * Output handler for console tools
     *
     * @var ezcConsoleOutput
     */
    protected $output;

    /**
     * Response, which should be outputted
     *
     * @var ezcMvcResponse
     */
    protected $response;

    /**
     * Creates a new response writer object
     *
     * @param ezcMvcResponse $response
     * @param ezcConsoleOutput $output
     * @return void
     */
    public function __construct( ezcMvcResponse $response, ezcConsoleOutput $output = null )
    {
        $this->output   = $output === null ? new ezcConsoleOutput() : $output;
        $this->response = $response;
    }

    /**
     * Takes the raw protocol depending response body, and the protocol
     * abstract response headers and forges a response to the client. Then it sends
     * the assembled response to the client.
     */
    public function handleResponse()
    {
        $this->output->outputLine( rtrim( $this->response->body ), 'default', 10 );
    }
}

