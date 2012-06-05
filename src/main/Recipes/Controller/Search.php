<?php
/**
 * This file is part of recipes
 *
 * recipes is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * recipes is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with recipes; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @version $Revision: 1469 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

namespace Recipes\Controller;

use Recipes\Struct;
use Recipes\Model;
use Qafoo\RMF;

/**
 * Search controller
 *
 * @WARNING:
 * @HACK: This is a brutal hack. Do NOT reuse it. It works, but it will get
 * damn slow with more then a very few documents. (But it still works better
 * and faster then Zend_Search_Lucene).
 *
 * @version $Revision$
 */
class Search
{
    /**
     * Search path
     *
     * @var string
     */
    protected $searchPath;

    /**
     * Construct from search handler
     *
     * @param string $searchPath
     * @return void
     */
    public function __construct( $searchPath )
    {
        $this->searchPath = $searchPath;
    }

    /**
     * Index given recipe
     *
     * @param Model\Recipe $recipe
     * @return void
     */
    public function index( Model\Recipe $recipe )
    {
        $properties = $recipe->getState();
        file_put_contents(
            $this->searchPath . '/' . $properties['id'] . '.js',
            json_encode( $properties )
        );
    }

    /**
     * Search for provided search phrase
     *
     * @param string $phrase
     * @return void
     */
    public function search( $phrase, $count = 10, $offset = 0 )
    {
        $words = array_map( 'preg_quote', array_filter( preg_split( '(\\P{L})', $phrase ) ) );

        $found = array_flip( glob( $this->searchPath . '/*.js' ) );
        array_walk(
            $found,
            function ( &$value, $file, $words )
            {
                preg_match_all( '(' . implode( '|', $words ) . ')i', file_get_contents( $file ), $results );
                $value = count( $results[0] );
            },
            $words
        );

        $found = array_filter( $found );
        arsort( $found );
        $found = array_slice( $found, $offset, $count );

        $result = array();
        foreach ( $found as $file => $score )
        {
            $recipe = json_decode( file_get_contents( $file ), true );
            $recipe['score'] = $score;
            $result[] = $recipe;
        }

        return $result;
    }
}

