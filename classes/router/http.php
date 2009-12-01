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
class arbitHttpRouter extends ezcMvcRouter
{
    /**
     * User implemented method that should provide all the routes.
     *
     * It should return an array of objects that implement the ezcMvcRoute
     * interface. This could be objects of the ezcMvcRegexpRoute class for
     * example.
     *
     * @return array(ezcMvcRoute)
     */
    public function createRoutes()
    {
        $this->routes[] = new arbitRoute( 'core', 'arbitMainController' );
        $this->routes[] = new arbitRoute( 'user', 'arbitUserController' );
        $this->routes[] = new arbitRoute( 'recipes', 'arbitRecipeController' );
        $this->routes[] = new arbitRoute( 'error', 'arbitErrorController' );
        arbitCacheRegistry::setCache( 'core' );

        return $this->routes;
    }
}

