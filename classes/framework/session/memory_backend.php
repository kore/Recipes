<?php
/**
 * arbit memory session backend
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
 * Arbit memory session backend
 *
 * Session "backend" which keeps the data just in memory to replay it in the 
 * same request. Does not persist any data. Useful for CLI scripts and testing.
 *
 * @package Core
 * @version $Revision: 1477 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitMemorySessionBackend extends arbitSessionBackend
{
    /**
     * Session data container
     * 
     * @var array
     */
    protected $data = array();

    /**
     * Initializes the sessioInitializes the session
     * 
     * @return void
     */
    public function initialize()
    {
        $this->data = array();
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
        // No need to do anything here
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
        return isset( $this->data[$offset] );
    }

    /**
     * Get contents of the requested session item
     * 
     * @param string $offset 
     * @return mixed
     */
    public function offsetGet( $offset )
    {
        return $this->data[$offset];
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
        $this->data[$offset] = $value;
    }

    /**
     * Removes item from session
     * 
     * @param string $offset 
     * @return void
     */
    public function offsetUnset( $offset )
    {
        unset( $this->data[$offset] );
    }
}

