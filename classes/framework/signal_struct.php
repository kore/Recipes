<?php
/**
 * arbit signal base struct
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
 * Arbit signal data base struct.
 *
 * Each signal may only contain one single clearly defined parameter, a signal
 * specific struct, which offers more information about the thrown signal. See
 * protocol from 14.02.08 for details. The signal struct always has the name
 * <signalName>Struct, so that it will be easy to find more documentation about
 * the signals data.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class arbitSignalSlotStruct extends arbitBaseStruct
{
    /**
     * Return an array with the available properties
     *
     * Returns an array containing the keys of all properties which are set in
     * the view model. This include properties set tu null.
     *
     * @return array
     */
    public function getProperties()
    {
        return array_keys( $this->properties );
    }
}

