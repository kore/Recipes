<?php
/**
 * arbit specific signal slot implementation
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
 * Arbit signal slot implementation
 *
 * The arbit specific signal slot implementation has some special requirements,
 * which are not commonly included in signal slot implementations, so that we
 * got our own implementation. The special requirements are:
 *
 * - Signal documentation
 *
 *   We enforce, that each signal is documented, because we want to optionally
 *   expose the signals to the user, so that they may be notified about emitted
 *   signals.
 *
 * - Signal module association
 *
 *   Each signal stays associated with the module (or core), which threw the
 *   signal, so that we can generate a clear signal overview page.
 *
 * - Invalid signal detection
 *
 *   To ensure documentation and make debugging easier we only signals to be
 *   emitted, which are defined by the modules.
 *
 * - Signal parameter enforcement
 *
 *   Each signal may only contain one single clearly defined parameter, a
 *   signal specific struct, which offers more information about the thrown
 *   signal. See protocol from 14.02.08 for details. The signal struct always
 *   has the name <signalName>Struct, so that it will be easy to find more
 *   documentation about the signals data.
 *
 * The class provides core functionality and is mostly called statically. It is
 * impossible to replace this class and / or extend this class in arbit.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitSignalSlot
{
    /**
     * Array of defined signals for fast checks, if the provided signal is
     * valid. The array looks like:
     *
     * <code>
     *  array(
     *      'signalName' => true,
     *      ...
     *  )
     * </code>
     *
     * @var array
     */
    protected static $signals = array();

    /**
     * Array with slots for each signal. The array looks like:
     *
     * <code>
     *  array(
     *      'signalName' = array(
     *          'slot1',
     *          ...
     *      ),
     *      ...
     *  )
     * </code>
     *
     * @var array
     */
    protected static $slots = array();

    /**
     * Array containing the signal module assiciation and the signal
     * descriptions. This array is maintained mainly for application user
     * documentation purpose.
     *
     * @var array
     */
    protected static $moduleSignals = array();

    /**
     * Constant used as a placeholder, if somethings wants to register for all
     * availaable slots.
     */
    const ALL_SLOTS = 1;

    /**
     * Register a set of signals.
     *
     * Registers a set of signals for a module. The signal array has to look
     * like specified in the arbitModuleDefintion class:
     *
     * <code>
     *  array(
     *      'signalName' => 'Signal description.',
     *      ...
     *  )
     * </code>
     *
     * @param string $module
     * @param array $signals
     * @return void
     */
    public static function registerSignals( $module, array $signals )
    {
        foreach ( $signals as $name => $desc )
        {
            if ( strpos( $name, $module ) !== 0 )
            {
                throw new arbitInvalidSignalNameException( $module, $name );
            }

            self::$signals[$name] = true;
            self::$moduleSignals[$module][$name] = $desc;
        }
    }

    /**
     * Register a set of slots.
     *
     * Registers a set of slots for the given signals. The provided array has
     * to look like specified in the arbitModuleDefintion class:
     *
     * <code>
     *  array(
     *      'signalName' => 'arbitFooModuleController::signalHandler',
     *      ...
     *  )
     * </code>
     *
     * @param array $slots
     * @return void
     */
    public static function registerSlots( array $slots )
    {
        foreach ( $slots as $signal => $callback )
        {
            // We just register the slot, no matter of we know about the given
            // signal name. The signal emitting module may not be installed, or
            // just not yet registered.
            self::$slots[$signal][] = $callback;
        }
    }

    /**
     * Emit a signal
     *
     * This method is used to emit signals from a module. You must provide a
     * parameter containing the signal data, which needs to be an object of the
     * class arbit<signalName>Struct, which extends the arbitSignalSlotStruct base
     * struct.
     *
     * All registered slots will be called in any order.
     *
     * @param string $signal
     * @param arbitSignalSlotStruct $data
     * @return void
     */
    public static function emit( $signal, arbitSignalSlotStruct $data )
    {
        $structClassName = 'arbit' . $signal . 'Struct';
        if ( !( $data instanceof $structClassName ) )
        {
            throw new arbitInvalidSignalDataException( $signal, $structClassName );
        }

        // We enforce that all signals are registered, because we want all;
        // signals to be documented and specified.
        if ( !isset( self::$signals[$signal] ) )
        {
            throw new arbitInvalidSignalException( $signal );
        }

        ezcLog::getInstance()->log( "Emitted signal $signal.", ezcLog::INFO );

        // Call all slots, if there are any...
        if ( isset( self::$slots[$signal] ) )
        {
            foreach( self::$slots[$signal] as $slot )
            {
                call_user_func( $slot, $signal, $data );
            }
        }

        // Also send signal to all slots which registered for all signals
        if ( isset( self::$slots[self::ALL_SLOTS] ) )
        {
            foreach( self::$slots[self::ALL_SLOTS] as $slot )
            {
                call_user_func( $slot, $signal, $data );
            }
        }
    }

    /**
     * Get list of defined signals.
     *
     * Returns a list with all defined signals for all modules, associated with
     * their description. The array will look like:
     *
     * <code>
     *  array(
     *      'core' => array(
     *          'signalName' => 'Signal description.',
     *          ...
     *      ),
     *      ...
     *  )
     * </code>
     *
     * @return array
     */
    public static function getSignals()
    {
        return self::$moduleSignals;
    }
}

