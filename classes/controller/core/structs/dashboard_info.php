<?php
/**
 * arbit signal struct
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
 * Dashboard information structure, delivered with the DashboardInfo signal.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitCoreDashboardInfoStruct extends arbitSignalSlotStruct
{
    /**
     * Array containing the structs properties.
     *
     * @var array
     */
    protected $properties = array(
        'project' => null,
        'module'  => null,
        'message' => null,
        'state'   => true,
        'quality' => 1.,
    );

    /**
     * Construct signal struct from its major data fields.
     *
     * @param string $project
     * @param string $module
     * @param string $message
     * @param bool $state
     * @param float $quality
     * @return void
     */
    public function __construct( $project = null, $module = null, arbitViewUserMessageModel $message = null, $state = true, $quality = 1. )
    {
        $this->project = $project;
        $this->module  = $module;
        $this->message = $message;
        $this->state   = $state;
        $this->quality = $quality;
    }
}

