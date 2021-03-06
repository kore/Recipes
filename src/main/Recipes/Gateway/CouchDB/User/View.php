<?php
/**
 * recipe CouchDB backend
 *
 * This file is part of recipe.
 *
 * recipe is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * recipe is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with recipe; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @subpackage CouchDbBackend
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

namespace Recipes\Gateway\CouchDB\User;

/**
 * Wrapper for user views
 *
 * @package Core
 * @subpackage CouchDbBackend
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class View extends \phpillowFileView
{
    /**
     * Construct view
     *
     * Construct view
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->viewFunctions = array(
            'all' => array(
                'map'    => __DIR__ . '/map/user_all.js',
            ),
            'user' => array(
                'map'    => __DIR__ . '/map/user_list.js',
            ),
            'unregistered' => array(
                'map'    => __DIR__ . '/map/user_unregistered.js',
            ),
        );
    }

    /**
     * Get name of view
     *
     * Get name of view
     *
     * @return string
     */
    protected function getViewName()
    {
        return 'users';
    }
}

