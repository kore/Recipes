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
use Recipes\Model;
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
     * User model
     *
     * @var Model\User
     */
    protected $user;

    /**
     * Requests, which may pass without authorization
     *
     * Array of regular expressions
     *
     * @var array
     */
    protected $unauthorized = array();

    /**
     * Construct from aggregated controller, which performs authorized actions
     *
     * @param Model\User $user
     * @param mixed $controller
     * @param array $unauthorized
     * @return void
     */
    public function __construct( Model\User $user, $controller, array $unauthorized = array() )
    {
        $this->user         = $user;
        $this->controller   = $controller;
        $this->unauthorized = $unauthorized;
    }

    /**
     * Login user
     *
     * @param RMF\Request $request
     * @return Struct\Response
     */
    public function login( RMF\Request $request )
    {
        $errors = array();
        if ( isset( $request->body['submit'] ) )
        {
            try
            {
                $user = $this->user->findByLogin( $request->body['login'] );
                if ( $user->auth_infos !== $this->getPasswordHash( $request->body['password'] ) )
                {
                    throw new \Exception( 'Invalid password provided.' );
                }
                $request->session['user'] = $user->_id;

                header( 'Location: /' );
                exit( 0 );
            }
            catch ( \Exception $e )
            {
                $errors[] = "Could not login with the provided data.";
            }
        }

        return new Struct\Login( $errors );
    }

    /**
     * Generate a password hash
     *
     * Generates one or more hashes from a password. Must be an injective
     * function.
     *
     * @param string $password
     * @return array
     */
    protected function getPasswordHash( $password )
    {
        return array(
            md5( 'arbit_' . $password ),
            sha1( 'arbit_' . $password ),
        );
    }

    /**
     * Logout user
     *
     * @param RMF\Request $request
     * @return Struct\Response
     */
    public function logout( RMF\Request $request )
    {
        unset( $request->session['user'] );
        return $this->login( $request );
    }

    /**
     * Dispatch request, which requires authentification to subcontroller
     *
     * @param string $method
     * @param array $arguments
     * @return void
     */
    public function __call( $method, array $arguments )
    {
        // @TODO: Check auth
        $request = $arguments[0];

        foreach ( $this->unauthorized as $regexp )
        {
            if ( preg_match( $regexp, $request->path ) )
            {
                return $this->controller->$method( $request );
            }
        }

        if ( !isset( $request->session['user'] ) )
        {
            return $this->login( $request );
        }

        return $this->controller->$method( $request );
    }
}

