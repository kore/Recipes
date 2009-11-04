<?php
/**
 * arbit logger
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
 * Generic logger for all log messages
 *
 * Generic logger, which consumes all log messages, and annotates the with some
 * basic context information.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitLogger implements ezcLogWriter
{
    /**
     * Log message container
     *
     * Aggregates all log messages, occured during application run.
     *
     * @var array
     */
    protected static $messages = array();

    /**
     * Writes the message $message to the log.
     *
     * The writer can use the severity, source, and category to filter the
     * incoming messages and determine the location where the messages should
     * be written.
     *
     * The array $optional contains extra information that can be added to the log. For example:
     * line numbers, file names, usernames, etc.
     *
     * @throws ezcLogWriterException
     *         If the log writer was unable to write the log message
     *
     * @param string $message
     * @param int $severity
     *        ezcLog::DEBUG, ezcLog::SUCCESS_AUDIT, ezcLog::FAILED_AUDIT, ezcLog::INFO, ezcLog::NOTICE,
     *        ezcLog::WARNING, ezcLog::ERROR or ezcLog::FATAL.
     * @param string $source
     * @param string $category
     * @param array(string=>string) $extraInfo
     */
    public function writeLogMessage( $message, $severity, $source, $category, $extraInfo = array() )
    {
        // Do not log anything, if debug mode is off.
        if ( !ARBIT_DEBUG )
        {
            return false;
        }

        // Fetch current call backtrace to embed information about real source
        // of debugging information
        $backtrace = debug_backtrace();

        // Just store the debug information in static variable
        self::$messages[] = array(
            'message'  => $message,
            'severity' => $severity,
            'source'   => $source,
            'category' => $category,
            'info'     => $extraInfo,
            'file'     => $backtrace[1]['file'],
            'line'     => $backtrace[1]['line'],
            'method'   => isset( $backtrace[2]['class'] ) && isset( $backtrace[2]['type'] ) && isset( $backtrace[2]['function'] ) ?
                $backtrace[2]['class'] . $backtrace[2]['type'] . $backtrace[2]['function'] : ''
        );
    }

    /**
     * Dump all given variables
     *
     * Dump all passed variables, and create a user readable debug output.
     *
     * @return void
     */
    public static function dump()
    {
        // Fetch current call backtrace to embed information about real source
        // of debugging information
        $backtrace = debug_backtrace();

        // Just store the debug information in static variable
        $message = array(
            'severity' => ezcLog::DEBUG,
            'source'   => 'default',
            'category' => 'dump',
            'info'     => array(),
            'file'     => $backtrace[0]['file'],
            'line'     => $backtrace[0]['line'],
            'method'   => isset( $backtrace[1]['class'] ) && isset( $backtrace[1]['type'] ) && isset( $backtrace[1]['function'] ) ?
                $backtrace[1]['class'] . $backtrace[1]['type'] . $backtrace[1]['function'] : ''
        );

        $variables = func_get_args();
        foreach ( $variables as $variable )
        {
            ob_start();
            var_dump( $variable );
            $message['message'] = ob_get_clean();

            // If xdebug is installed we need to strip the HTML and decode
            // stuff, to get readable output.
            if ( extension_loaded( 'xdebug' ) )
            {
                $message['message'] = html_entity_decode( strip_tags( $message['message'] ) );
            }

            self::$messages[] = $message;
        }
    }

    /**
     * Receive debug messages
     *
     * Receive array with all aggregated debug messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return self::$messages;
    }
}

