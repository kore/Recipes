<?php
/**
 * arbit http string input conversions class
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
 * Arbit HTTP string input conversions
 *
 * Handle user input data as strings, and perform the necessary charset
 * conversions.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitHttpStringConverter extends arbitHttpInputConverter
{
    /**
     * Convert client input
     *
     * Convert and clean up client input.
     *
     * @param mixed $input
     * @return mixed
     */
    public static function convert( $input )
    {
        // Convert from client encoding to UTF-8, which should cover most
        // charsets, to ensure the same charset througout the application.
        //
        // This causes an exception for all clients which submit a form without
        // a session, eg. without requesting any site before. This should not
        // hurt, as those clients seems not to actually have requested the form
        // they try to submit.
        $clientEncoding = arbitSession::getGlobal( 'ClientEncoding' );

        if ( $clientEncoding === 'utf-8' )
        {
            // Ignore all invalid UTF-8 characters.
            //
            // This will cause a notice, when a non-UTF-8 character is found,
            // which should not happen in development mode, and is ignored
            // during production.
            return iconv( 'utf-8', 'utf-8//IGNORE', $input );
        }
        else
        {
            // Try to convert or replace all characters on import. The quality
            // of this conversions depends on the installed locales and the
            // glibc version.
            return iconv( $clientEncoding, 'utf-8//TRANSLIT', $input );
        }
    }
}

