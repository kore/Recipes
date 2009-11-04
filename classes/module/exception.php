<?php
/**
 * arbit modules
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
 * Basic arbit module exception
 *
 * @package Core
 * @subpackage Module
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class arbitModuleException extends arbitException
{
}

/**
 * Exception thrown when a requested module could not be found.
 *
 * @package Core
 * @subpackage Module
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitModuleFileNotFoundException extends arbitModuleException
{
    /**
     * Create exception from module name
     *
     * @param string $module
     */
    public function __construct( $module )
    {
        parent::__construct(
            "The module definition file '%module' could not be found. Please recheck the file name.",
            array(
                'module' => $module,
            )
        );
    }
}

/**
 * Exception thrown when a requested module could not be found.
 *
 * @package Core
 * @subpackage Module
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitModuleDefinitionNotFoundException extends arbitModuleException
{
    /**
     * Create exception from module name
     *
     * @param string $module
     */
    public function __construct( $module )
    {
        parent::__construct(
            "The module definition class '%module' is not registered after module definition file inclusion. Please recheck the name.",
            array(
                'module' => $module,
            )
        );
    }
}


/**
 * Exception thrown when a module is requested from the module manager which
 * has not been registerd.
 *
 * @package Core
 * @subpackage Module
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitUnknownModuleException extends arbitModuleException
{
    /**
     * Create exception from module name
     *
     * @param string $module
     */
    public function __construct( $module )
    {
        parent::__construct(
            "No module '%module' is available.",
            array(
                'module' => $module,
            )
        );
    }
}

