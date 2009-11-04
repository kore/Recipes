<?php
/**
 * Arbit HTTP base router
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
 * Arbit HTTP base router
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitRoute implements ezcMvcRoute
{
    /**
     * Name of module, as occuring in the request.
     *
     * @var string
     */
    protected $module;

    /**
     * Name of controller class used for this module
     *
     * @var string
     */
    protected $controller;

    /**
     * Construct route from name of the module and the controller class name.
     *
     * @param string $module
     * @param string $controller
     * @return void
     */
    public function __construct( $module, $controller )
    {
        $this->module     = $module;
        $this->controller = $controller;
    }

    /**
     * Returns routing information if the route matched, or null in case the
     * route did not match.
     *
     * @param ezcMvcRequest $request Request to test.
     * @return null|ezcMvcRoutingInformation
     */
    public function matches( ezcMvcRequest $request )
    {
        if ( $request->controller === $this->module )
        {
            return new ezcMvcRoutingInformation(
                $this->module,
                $this->controller,
                $request->action
            );
        }
    }

    /**
     * Adds a prefix to the route.
     *
     * @param mixed $prefix Prefix to add, for example: '/blog'
     * @return void
     */
    public function prefix( $prefix )
    {
        // No purpose here.
    }
}

