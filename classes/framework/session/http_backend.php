<?php
/**
 * arbit HTTP session backend
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
 * Arbit HTTP session backend
 *
 * Default HTTP session backend, using the PHP session_* functions to operate a 
 * statefull session over HTTP.
 *
 * @package Core
 * @version $Revision: 1477 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitHttpSessionBackend extends arbitSessionBackend
{
    /**
     * Initializes the sessioInitializes the session
     * 
     * @return void
     */
    public function initialize()
    {
        session_save_path( ARBIT_TMP_PATH );
        // We ignore warnings about an already started session, since there is 
        // no sane way to detect this and it won't hurt anyways.
        @session_start();
    }

    /**
     * Close the session
     *
     * After a call to this method no more write operations are allowed to the 
     * session.
     * 
     * @return void
     */
    public function writeClose()
    {
        session_write_close();
    }

    /**
     * Check if variable is stored in the session.
     *
     * Returns false if the variable is not known in the session.
     * 
     * @param string $offset 
     * @return boolean
     */
    public function offsetExists( $offset )
    {
        return isset( $_SESSION[$offset] );
    }

    /**
     * Get contents of the requested session item
     * 
     * @param string $offset 
     * @return mixed
     */
    public function offsetGet( $offset )
    {
        return $_SESSION[$offset];
    }

    /**
     * Set variable in session
     *
     * Adds new item to the session, or overwrites an existing entry. The added 
     * value should be serializable.
     * 
     * @param string $offset 
     * @param mixed $value 
     * @return void
     */
    public function offsetSet( $offset, $value )
    {
        $_SESSION[$offset] = $value;
    }

    /**
     * Removes item from session
     * 
     * @param string $offset 
     * @return void
     */
    public function offsetUnset( $offset )
    {
        unset( $_SESSION[$offset] );
    }
}

