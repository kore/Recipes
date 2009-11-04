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
class arbitHttpRequest extends arbitRequest
{
    /**
     * URL base path.
     *
     * When arbit does not reside in the document root, this variable point to
     * the location of the arbit directory. This is autodetected from PHP_SELF.
     *
     * @var string
     */
    public $root;

    /**
     * Path as extracted from request URL.
     *
     * @var string
     */
    public $path;

    /**
     * Serialize URL
     *
     * Return a string representation of the URL for the requests connection
     * type.
     *
     * @param bool $absolute
     * @return string
     */
    public function serialize( $absolute = false )
    {
        $url = $absolute ? 'http://' . arbitHttpTools::serverVariable( 'HTTP_HOST' ) : '';
        $url .= $this->root;

        // Sorry for these stacked if statements, but each parameter should
        // only be added, if all the URL parameters above are available.
        if ( isset( $this->controller ) )
        {
            $url .= '/' . $this->controller;

            if ( $this->action !== 'index' )
            {
                $url .= '/' . $this->action;

                // Append file extension, if set
                if ( isset( $this->extension ) )
                {
                    $url .= '.' . $this->extension;
                }

                if ( $this->subaction !== 'index' )
                {
                    $url .= '/' . $this->subaction;
                }

                if ( isset( $this->path ) )
                {
                    $url .= $this->path;
                }
            }
        }

        // Append GET parameters, if set
        if ( count( $this->variables ) )
        {
            $url .= '?' . http_build_query( $this->variables );
        }

        return $url;
    }
}

