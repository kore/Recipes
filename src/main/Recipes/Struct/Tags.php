<?php
/**
 * This file is part of recipes.
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

namespace Recipes\Struct;
use Recipes\Struct;

/**
 * Overview struct class
 *
 * @version $Revision$
 */
class Tags extends Struct
{
    /**
     * Popular tags
     *
     * @var array
     */
    public $popular = array();

    /**
     * All tags
     *
     * @var array
     */
    public $grouped = array();

    /**
     * Construct
     *
     * @param array $popular
     * @param array $grouped
     * @return void
     */
    public function __construct( array $popular = array(), array $grouped = array() )
    {
        $this->popular = $popular;
        $this->grouped = $grouped;
    }
}

