<?php
/**
 * arbit request base class
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
 * Basic structure containing request information. For more then the very basic
 * request information extend this struct and add validtions for these
 * additional properties to the __set method.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class arbitRequest extends ezcMvcRequest
{
    /**
     * Controller as extracted from request URL.
     *
     * @var string
     */
    public $controller;

    /**
     * Action as extracted from request URL.
     *
     * @var string
     */
    public $action;

    /**
     * Sub action as extracted from request URL.
     *
     * @var string
     */
    public $subaction;

    /**
     * Extension as extracted from request URL.
     *
     * @var string
     */
    public $extension = null;

    /**
     * Construct controller from given values
     *
     * @param string $controller
     * @param string $action
     * @param string $subaction
     * @return void
     */
    public function __construct( $controller = 'core', $action = 'index', $subaction = 'index' )
    {
        parent::__construct();

        $this->controller = $controller;
        $this->action     = $action;
        $this->subaction  = $subaction;

        $this->accept     = new ezcMvcRequestAccept();
    }

    /**
     * Serialize URL
     *
     * Return a string representation of the URL for the requests connection
     * type.
     *
     * @return string
     */
    abstract public function serialize();
}

