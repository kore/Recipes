#!/usr/bin/env php
<?php
/**
 * arbit autoload file
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
 * @package Scripts
 * @version $Revision: 1239 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

// Source and destination paths for conversion
$sourcePath  = __DIR__ . '/../../classes/framework/i18n/data/symfony/';
$destination = __DIR__ . '/../../classes/framework/i18n/data/';

/**
 * Synchronize arrays
 *
 * Ensure the given $array contains a structural superset of the $reference
 * array. This function recursively checks the $array for availability of the
 * keys in $reference and creates the keys, if they are not yet afailable in
 * $array.
 *
 * @param array $reference
 * @param array &$array
 * @access public
 * @return void
 */
function synchronizeArrays( array $reference, array &$array )
{
    foreach ( $reference as $key => $value )
    {
        if ( !isset( $array[$key] ) )
        {
            $array[$key] = $reference[$key];
            continue;
        }

        if ( is_array( $value ) )
        {
            synchronizeArrays( $reference[$key], $array[$key] );
        }
    }
}

// Root profile, containing dummy data for each setting
$reference = unserialize(  file_get_contents( $sourcePath . 'root.dat' ) );

// For now we only care for the root locale definitions without variations.
foreach ( glob( $sourcePath . '??.dat' ) as $file )
{
    $data = unserialize( file_get_contents( $file ) );

    // Do not care, if no date time data is provided for given locale
    if ( !isset( $data['calendar'] ) ||
         !isset( $data['calendar']['gregorian'] ) )
    {
        continue;
    }
    $data = $data['calendar']['gregorian'];

    // Check if all required data is available, skip otherwise
    synchronizeArrays( $reference['calendar']['gregorian'], $data );

    // Convert date time patterns array indexes to readable keys
    $dateTimePatterns = array(
        'fullTime'      => $data['DateTimePatterns'][0],
        'longTime'      => $data['DateTimePatterns'][1],
        'mediumTime'    => $data['DateTimePatterns'][2],
        'shortTime'     => $data['DateTimePatterns'][3],
        'fullDate'      => $data['DateTimePatterns'][4],
        'longDate'      => $data['DateTimePatterns'][5],
        'mediumDate'    => $data['DateTimePatterns'][6],
        'shortDate'     => $data['DateTimePatterns'][7],
        'DateTimeOrder' => $data['DateTimePatterns'][8],
    );

    $data['DateTimePatterns'] = $dateTimePatterns;

    // Create PHP parseable data file.
    file_put_contents(
        $destination . str_replace( '.dat', '.php', basename( $file ) ),
        sprintf( "<?php
/**
 * Date time data extracted from symfony.
 *
 * Original data can be found at:
 * http://svn.symfony-project.com/branches/1.2/lib/i18n/data
 *
 * Symfony license:
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the BSD License.
 *
 * @package Data
 * @version \$Revision\$
 * @license http://www.symfony-project.org/license BSD
 */
return %s;

",
        var_export( $data, true )
    ) );
}

