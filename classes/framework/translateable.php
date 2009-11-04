<?php
/**
 * arbit translateable interface
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
 * Interface indicating that an object can be translated.
 *
 * This means, that all dynamic values in the string should be replaced by
 * placholders of the type %[a-z]+, and for each value an entry in an array is
 * given which contains the replacement value.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
interface arbitTranslateable
{
    /**
     * Get message
     *
     * Get raw exception message without replaced placeholders
     *
     * @return string
     */
    public function getText();

    /**
     * Get properties
     *
     * Get text properties containing the values, which should replace the
     * placeholders in the message.
     *
     * @return array
     */
    public function getTextValues();
}

