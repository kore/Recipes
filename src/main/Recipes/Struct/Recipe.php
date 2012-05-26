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
class Recipe extends Struct
{
    /**
     * Recipe
     *
     * @var Model\Recipe
     */
    public $recipe = array();

    /**
     * Construct
     *
     * @param string $recipes
     * @return void
     */
    public function __construct( Model\Recipe $recipe = null )
    {
        $this->recipe = $recipe;
    }
}

