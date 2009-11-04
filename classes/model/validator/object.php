<?php
/**
 * arbit base model validation
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
 * @subpackage Model
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Object validator class
 *
 * @package Core
 * @subpackage Model
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitModelObjectValidator extends arbitModelValidatorBase
{
    /**
     * Name of class, which should be inherited by the object.
     *
     * @var string
     */
    protected $className;

    /**
     * Configure validator
     *
     * The object validator will optionally take a class name, which causes the
     * passed objects are checked that they inherit from the specified class.
     *
     * @param string $className
     * @return void
     */
    public function configure( $className = null )
    {
        $this->className = $className;
    }

    /**
     * Validate value
     *
     * Validates the given input. Returns the input, when it matches the
     * validation constraints and throws a arbitPropertyValidationException
     * exception otherwise.
     *
     * The name and expectation paramters are used to generate a better user
     * error message. The name should be the name of the property, and the
     * expectation should be a string somehow describing what kind of content
     * was expected from validation.
     *
     * @throws arbitPropertyValue If validation constraints are not met.
     * @param string $name
     * @param mixed $value
     * @param string $expectation
     * @return mixed
     */
    public function validate( $name, $value, $expectation )
    {
        if ( !is_object( $value ) ||
             ( ( $this->className !== null ) &&
               ( !$value instanceof $this->className ) ) )
        {
            throw new arbitPropertyValidationException( $name, $expectation );
        }

        return $value;
    }
}

