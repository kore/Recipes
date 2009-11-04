<?php
/**
 * arbit date time formatter
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
 * Locale dependent date time formatter
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitDateTimeFormatter
{
    /**
     * Currently set locale
     *
     * @var string
     */
    protected $locale;

    /**
     * Locale data for date time formatting
     *
     * @var mixed
     */
    protected $localeData;

    /**
     * COnstruct formatter from two character locale abreviation
     *
     * @param string $locale
     * @return void
     */
    public function __construct( $locale )
    {
        $this->loadLocaleData( $this->locale = $locale );
    }

    /**
     * Load locale data
     *
     * Try to load locale data. If no data is available for the given locale a
     * arbitUnknownLocaleException is thrown.
     *
     * @param string $locale
     * @return void
     */
    protected function loadLocaleData( $locale )
    {
        if ( !is_file( $path = __DIR__ . '/data/' . $locale . '.php' ) ||
             !is_readable( $path ) )
        {
            throw new arbitUnknownLocaleException( $locale );
        }

        $this->localeData = include $path;
    }

    /**
     * Format date
     *
     * Format date given as a PHP DateTime object, which already includes time
     * and timezon information.
     *
     * The format string may be one of:
     *
     * - full, long, medium, short
     *
     *   For a combined date and time string
     *
     * - fullDate, longDate, mediumDate, shortDate
     *
     *   For a date only without time specification
     *
     * - fullTime, longTime, mediumTime, shortTime
     *
     *   For a time only, without date specification
     *
     * The method returns a formatted date, depending on set locale.
     *
     * @param string $format
     * @param DateTime $time
     * @return string
     */
    public function format( $format, DateTime $time )
    {
        // Check for existance of the date
        if ( !isset( $this->localeData['DateTimePatterns'][$format] ) &&
             !in_array( $format, array( 'full', 'long', 'medium', 'short' ) ) )
        {
            throw new arbitUnknownDateFormatException( $format );
        }

        // If a full date is requested including date and time patterns create
        // a combined format depending on the locale settings.
        if ( isset( $this->localeData['DateTimePatterns'][$format] ) )
        {
            $pattern = $this->localeData['DateTimePatterns'][$format];
        }
        else
        {
            $pattern = str_replace(
                array( '{0}', '{1}' ),
                array(
                    $this->localeData['DateTimePatterns'][$format . 'Time'],
                    $this->localeData['DateTimePatterns'][$format . 'Date'],
                ),
                $this->localeData['DateTimePatterns']['DateTimeOrder']
            );
        }

        return $this->patternFormat( $pattern, $time );
    }

    /**
     * Pattern based date formatting
     *
     * Return a date formatted based on the given patter. Textual names
     * specified by the pattern are replaced by their language specific form,
     * but the pattern is not modified for the current locale.
     *
     * @param string $pattern
     * @param DateTime $time
     * @return string
     */
    public function patternFormat( $pattern, DateTime $time )
    {
        $date          = '';
        $token         = null;
        $patternLength = strlen( $pattern );
        for ( $i = 0; $i < $patternLength; ++$i )
        {
            switch ( true )
            {
                // End of current pattern, replace by date time value
                case ( $token !== null ) &&
                     ( $token[0] !== $pattern[$i] ):
                    $date .= $this->tokenToValue( $token, $time );
                    $token = null;
                    --$i;
                    break;

                // Pattern substring escaped by '
                case "'" === $pattern[$i]:
                    ++$i;
                    while ( $pattern[$i] !== "'" )
                    {
                        $date .= $pattern[$i++];
                    }
                    $token = null;
                    break;

                // Check if current character is part of one of the known
                // formatting character sequences
                case ( strpos( "adDEFGhHkKmMswWyz", $pattern[$i] ) !== false ):
                    $token .= $pattern[$i];
                    break;

                // All other characters are jsut appended
                default:
                    $date .= $pattern[$i];
                    $token = null;
            }
        }

        // Convert last token, if existing
        if ( $token !== null )
        {
            $date .= $this->tokenToValue( $token, $time );
        }

        return $date;
    }

    /**
     * Convert pattern token to value
     *
     * Convert a pattern token into the value depending on the given DateTime
     * object
     *
     * @param string $token
     * @param DateTime $time
     * @return string
     */
    protected function tokenToValue( $token, DateTime $time )
    {
        switch ( $token )
        {
            // Year patterns
            case 'yy':
                return $time->format( 'y' );
            case 'yyyy':
                return $time->format( 'Y' );

            // Month patterns
            case 'M':
                return $time->format( 'n' );
            case 'MM':
                return $this->localeData['monthNames']['format']['narrow'][$time->format( 'n' ) - 1];
            case 'MMM':
                return $this->localeData['monthNames']['format']['abbreviated'][$time->format( 'n' ) - 1];
            case 'MMMM':
                return $this->localeData['monthNames']['format']['wide'][$time->format( 'n' ) - 1];

            // Week day patterns
            case 'E':
                return $time->format( 'w' );
            case 'EE':
                return $this->localeData['dayNames']['format']['narrow'][$time->format( 'w' )];
            case 'EEE':
                return $this->localeData['dayNames']['format']['abbreviated'][$time->format( 'w' )];
            case 'dddd':
            case 'EEEE':
                return $this->localeData['dayNames']['format']['wide'][$time->format( 'w' )];

            // Day of month patterns
            case 'd':
            case 'F':
                return $time->format( 'j' );
            case 'dd':
            case 'FF':
                return $time->format( 'd' );

            // Day of year patterns
            case 'D':
                return $time->format( 'z' );

            // Era pattern
            case 'G':
                return (string) (int) ($time->format( 'Y' ) > 0);

            // Hour patterns
            case 'H':
                return $time->format( 'G' );
            case 'HH':
                return $time->format( 'H' );
            case 'h':
                return $time->format( 'g' );
            case 'hh':
                return $time->format( 'h' );
            case 'a':
                return $this->localeData['AmPmMarkers'][(int) ( $time->format( 'a' ) === 'pm' )];
            case 'k':
                return ( ( $hour = $time->format( 'G' ) ) === '0' ? '24' : $hour );
            case 'K':
                return $time->format( 'g' );

            // Minutes patterns
            case 'm':
                return (string) (int) $time->format( 'i' );
            case 'mm':
                return $time->format( 'i' );

            // Second patterns
            case 's':
                return (string) (int) $time->format( 's' );
            case 'ss':
                return $time->format( 's' );

            // Timezone patterns
            case 'z':
                return $time->format( 'T' );

            // Week count patterns
            case 'w':
                return $time->format( 'W' );
            case 'W':
                $firstDayOfMonth = clone $time;
                $firstDayOfMonth->modify( 'first day' );
                return $time->format( 'W' ) - $firstDayOfMonth->format( 'W' );

            default:
                throw new arbitUnknownPatternTokenException( $token );
        }
    }
}

