<?php
/**
 * This file is part of recipes
 *
 * recipes is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * recipes is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with recipes; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @version $Revision: 1469 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

namespace Recipes\Controller;
use Recipes\Struct;
use Qafoo\RMF;

/**
 * Auth controller
 *
 * @version $Revision$
 */
class Auth
{
    /**
     * Aggregated controller
     *
     * @var Controller
     */
    protected $controller;

    /**
     * Construct from aggregated controller, which performs authorized actions
     *
     * @param mixed $controller
     * @return void
     */
    public function __construct( $controller )
    {
        $this->controller = $controller;
    }

    /**
     * Login user
     *
     * @param RMF\Request $request
     * @return Struct\Response
     */
    public function login( RMF\Request $request )
    {
        if ( isset( $request->body['submit'] ) )
        {
            // Handle post
        }

        return new Struct\Login();
    }

    /**
     * Logout user
     *
     * @param RMF\Request $request
     * @return Struct\Response
     */
    public function logut( RMF\Request $request )
    {
        unset( $request->session->user );
        return $this->login( $request );
    }

    /**
     * Dispatch request, which requires authentification to subcontroller
     *
     * @param RMF\Request $request
     * @return Struct\Response
     */
    public function __call( $method, array $arguments )
    {
        // @TODO: Check auth
        $request = $arguments[0];

        if ( !isset( $request->session->user ) )
        {
            return $this->login( $request );
        }

        return $this->controller->$method( $request );
    }
}

