<?php
/**
 * arbit views
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
 * Basic arbit view exception
 *
 * @package Core
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class arbitViewException extends arbitException
{
}

/**
 * Exception thrown when no decorators could be found for a view model.
 *
 * @package Core
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitViewNoDecoratorsExceptions extends arbitViewException
{
    /**
     * Create exception from handler name
     *
     * @param string $model
     * @param string $handler
     */
    public function __construct( $model, $handler )
    {
        parent::__construct(
            "There are no configured decorators for '%model' for the handler '%handler'.",
            array(
                'model'   => $model,
                'handler' => $handler,
            )
        );
    }
}

/**
 * Exception thrown when a view model could not be decorated, which means that
 * no decorated created a string from the given view model.
 *
 * @package Core
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitViewDecorationFailedException extends arbitViewException
{
    /**
     * Create exception from view model class name
     *
     * @param string $model
     */
    public function __construct( $model )
    {
        parent::__construct(
            "The decoration of view model '%model' failed - no decorator returned a valid string.",
            array(
                'model' => $model,
            )
        );
    }
}

