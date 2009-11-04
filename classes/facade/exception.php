<?php
/**
 * arbit model
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
 * Basic arbit facade exception
 *
 * @package Core
 * @subpackage Facade
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class arbitFacadeException extends arbitException
{
}

/**
 * Exception thrown when there is no implementation available for a requested
 * facade.
 *
 * @package Core
 * @subpackage Facade
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitFacadeUnknownClassException extends arbitFacadeException
{
    /**
     * Create exception from class name
     *
     * @param string $class
     */
    public function __construct( $class )
    {
        parent::__construct(
            "There is no implementation for the requested facade class '%class'.",
            array(
                'class' => $class,
            )
        );
    }
}

/**
 * Exception thrown when something was requested from a project, which in fact
 * does not (yet) exist.
 *
 * @package Core
 * @subpackage Facade
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitFacadeUnknownProjectException extends arbitFacadeException implements arbitExceptionNotFoundMarker
{
    /**
     * Create exception from project name
     *
     * @param string $project
     */
    public function __construct( $project )
    {
        parent::__construct(
            "The project '%project' does not exist.",
            array(
                'project' => $project,
            )
        );
    }
}

/**
 * Exception thrown when a document already exists in the system and cannot be
 * created from the given name.
 *
 * @package Core
 * @subpackage Facade
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitFacadeExistsException extends arbitFacadeException
{
    /**
     * Create exception from name
     *
     * @param string $name
     */
    public function __construct( $name )
    {
        parent::__construct(
            "A document with the name '%name' already exists.",
            array(
                'name' => $name,
            )
        );
    }
}

/**
 * Exception thrown when a user already exists in the system and cannot be
 * created from the given login name.
 *
 * @package Core
 * @subpackage Facade
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitFacadeUserExistsException extends arbitFacadeExistsException implements arbitExceptionConflictMarker
{
}

/**
 * Exception thrown when a group already exists in the system and cannot be
 * created from the given login name.
 *
 * @package Core
 * @subpackage Facade
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitFacadeGroupExistsException extends arbitFacadeExistsException implements arbitExceptionConflictMarker
{
}

/**
 * Exception thrown when an existing project is tried to be recreated.
 *
 * @package Core
 * @subpackage Facade
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitFacadeProjectExistsException extends arbitFacadeExistsException implements arbitExceptionConflictMarker
{
}

/**
 * Exception thrown when a model which is aggregated by some other omdel does
 * not have an identifier set and seems yet uncommitted.
 *
 * @package Core
 * @subpackage Facade
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitModelNotPersistantException extends arbitFacadeException
{
    /**
     * Create exception
     */
    public function __construct()
    {
        parent::__construct(
            "Not persistant model passed to facade.",
            array()
        );
    }
}

/**
 * Exception thrown when a requested object could not be found in the backend.
 *
 * @package Core
 * @subpackage Facade
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitFacadeNotFoundException extends arbitFacadeException implements arbitExceptionNotFoundMarker
{
}

