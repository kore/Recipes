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
 * Abstract base class for stackable property validators, to be used in model
 * classes.
 *
 * The static method create() for validator construction allows direct
 * dereferencing of the configured validor, so that it may be used like the
 * following example shows to validate an array where the values match the
 * defined regular expression.
 *
 * <code>
 *  $validated = arbitModelArrayValidator::create(
 *          arbitModelRegexpValidator::create( '(^[a-z]+$)' )
 *      )->validate( 'some_property', $input, 'array( string )' );
 * </code>
 *
 * @package Core
 * @subpackage Model
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class arbitModelValidatorBase
{
    /**
     * Create validator.
     *
     * Create a validator object and configure it with the parameters passed to
     * this method. We use a static method for this instead of the common
     * constructor, because calls to constructors do not work with direct
     * dereferencing, which would make the usage of the validator classes more
     * complex.
     *
     * You may pass any number of parameters to the create method, they will
     * all passed through to the configure method of the called validator. For
     * the exact parameters check the documentation of the configure method of
     * the respective validator.
     *
     * @return void
     */
    public static function create()
    {
        $validator = new static();
        call_user_func_array(
            array( $validator, 'configure' ),
            func_get_args()
        );
        return $validator;
    }

    /**
     * Configure validator
     *
     * Method to configure the validator, if it requires configuration at all.
     *
     * @return void
     */
    public function configure()
    {
        // Do nothing by default
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
    abstract public function validate( $name, $value, $expectation );
}

