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
use Recipes\Model;

/**
 * Overview struct class
 *
 * @version $Revision$
 */
class Search extends Struct
{
    /**
     * Search phrase
     *
     * @var string
     */
    public $phrase;

    /**
     * Count
     *
     * @var int
     */
    public $count;

    /**
     * Offset
     *
     * @var int
     */
    public $offset;

    /**
     * Search result
     *
     * @var array
     */
    public $result;

    /**
     * Construct
     *
     * @param string $phrase
     * @param string $count
     * @param string $offset
     * @param array $result
     * @return void
     */
    public function __construct( $phrase = null, $count = null, $offset = null, array $result = array() )
    {
        $this->phrase = $phrase;
        $this->count  = $count;
        $this->offset = $offset;
        $this->result = $result;
    }
}

