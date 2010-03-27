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
class arbitCliLogger implements ezcLogWriter
{
    /**
     * Output handler
     *
     * @var ezcConsoleOutput
     */
    protected $out;

    /**
     * Severity mapping
     *
     * @var array
     */
    protected $severities = array(
        1   => 'Debug',
        2   => 'Success',
        4   => 'Failed',
        8   => 'Info',
        16  => 'Notice',
        32  => 'Warning',
        64  => 'Error',
        128 => 'Fatal',
    );

    /**
     * Mapping of severities to output verbosity
     *
     * @var array
     */
    protected $verbosity = array(
        1   => 100,
        2   => 100,
        4   => 100,
        8   => 50,
        16  => 10,
        32  => 10,
        64  => 0,
        128 => 0,
    );

    /**
     * Request start time
     *
     * @var float
     */
    protected $start;

    /**
     * Create logger from output handler
     *
     * @param ezcConsoleOutput $output
     * @return void
     */
    public function __construct( ezcConsoleOutput $output )
    {
        $this->out   = $output;
        $this->start = microtime( true );
    }

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
        $this->out->outputLine(
            sprintf(
                '[%.6f] [%s] [%s] %s: %s',
                microtime( true ) - $this->start,
                $source,
                $category,
                $this->severities[$severity],
                $message
            ),
            strtolower( $this->severities[$severity] ),
            $this->verbosity[$severity]
        );
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

