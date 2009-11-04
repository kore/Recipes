<?php
/**
 * arbit session backend
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
 * @version $Revision: 1477 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Arbit session backend
 *
 * Backend performing read and write operations to the session backend. Also 
 * responsible for initializing the session, if necessary. For example for the 
 * default PHP backend this means calling session_start() to send out the 
 * cookie.
 *
 * @package Core
 * @version $Revision: 1477 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class arbitSessionBackend implements ArrayAccess
{
    /**
     * Initializes the sessioInitializes the session
     * 
     * @return void
     */
    abstract public function initialize();

    /**
     * Close the session
     *
     * After a call to this method no more write operations are allowed to the 
     * session.
     * 
     * @return void
     */
    abstract public function writeClose();

    /**
     * Regenerate ID
     *
     * Special function to regenerate optional user visible session IDs on 
     * certain circumstances like permission changes.
     *
     * Important for HTTP based sessions, default implementation just does 
     * nothing, because other backend most probably won't care.
     * 
     * @return void
     */
    public function regenerateId()
    {
        return;
    }
}

