<?php
/**
 * arbit storage backend facade
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
 * @subpackage Facade
 * @version $Revision: 1385 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * CuuchDB facade base taking care of connections and common methods required
 * for the wrapping.
 *
 * @package Core
 * @subpackage Facade
 * @version $Revision: 1385 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class arbitCouchDbFacadeBase
{
    /**
     * Static variable containing the connection state to the backend. We
     * initiate the connection on the first request to one of the facade
     * classes.
     *
     * @var bool
     */
    protected static $connected = false;

    /**
     * Manager instance depending on the selected backend.
     *
     * @var arbitFacadeProjectManager
     */
    protected $manager;

    /**
     * Constructor
     *
     * The constructor ensures the correct configuration for the conenction by
     * connecting on the first request to on of the facde classes.
     *
     * @return void
     */
    public function __construct( $manager )
    {
        $this->manager = $manager;
        if ( self::$connected === false )
        {
            $this->manager->connect();
        }
    }
}

