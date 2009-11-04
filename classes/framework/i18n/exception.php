<?php
/**
 * arbit i18n exceptions
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
 * Exception thrown for unknown locales
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitUnknownLocaleException extends arbitException
{
    /**
     * Construct exception from locale
     *
     * @param string $locale
     * @return void
     */
    public function __construct( $locale )
    {
        parent::__construct(
            "Locale '%locale' is not available.",
            array(
                'locale' => $locale,
            )
        );
    }
}

/**
 * Exception thrown for unknown date time formats
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitUnknownDateFormatException extends arbitException
{
    /**
     * Construct exception from format
     *
     * @param string $format
     * @return void
     */
    public function __construct( $format )
    {
        parent::__construct(
            "Date time format '%format' is not available.",
            array(
                'format' => $format,
            )
        );
    }
}

/**
 * Exception thrown for unknown date time format pattern tokens
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitUnknownPatternTokenException extends arbitException
{
    /**
     * Construct exception from token
     *
     * @param string $token
     * @return void
     */
    public function __construct( $token )
    {
        parent::__construct(
            "Unknown date time format pattern token '%token'.",
            array(
                'token' => $token,
            )
        );
    }
}

