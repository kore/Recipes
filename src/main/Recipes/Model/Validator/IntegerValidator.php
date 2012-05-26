<?php
/**
 * recipe base model validation
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
 * @subpackage Model
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

namespace Recipes\Model\Validator;
use Recipes\Model\Validator;

/**
 * Integer validator class
 *
 * @package Core
 * @subpackage Model
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class IntegerValidator extends Validator
{
    /**
     * Minimum value of the given integer
     *
     * @var int
     */
    protected $min = null;

    /**
     * Maximum value of the given integer
     *
     * @var int
     */
    protected $max = null;

    /**
     * Configure validator
     *
     * The integer validator may optionally assign maximum and minimum values
     * to the given content.
     *
     * @param int $min
     * @param int $max
     * @return void
     */
    public function configure( $min = null, $max = null )
    {
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * Validate value
     *
     * Validates the given input. Returns the input, when it matches the
     * validation constraints and throws a recipePropertyValidationException
     * exception otherwise.
     *
     * The name and expectation paramters are used to generate a better user
     * error message. The name should be the name of the property, and the
     * expectation should be a string somehow describing what kind of content
     * was expected from validation.
     *
     * @throws recipePropertyValue If validation constraints are not met.
     * @param string $name
     * @param mixed $value
     * @param string $expectation
     * @return mixed
     */
    public function validate( $name, $value, $expectation )
    {
        if ( !is_int( $value ) ||
             ( ( $this->min !== null ) &&
               ( $value < $this->min ) ) ||
             ( ( $this->max !== null ) &&
               ( $value > $this->max ) ) )
        {
            throw new recipePropertyValidationException( $name, $expectation );
        }

        return $value;
    }
}

