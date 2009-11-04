<?php
/**
 * arbit cache registry
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
 * Arbit simple cache registry
 *
 * Aggregates project dependant caches.
 *
 * The default cache is the of the project currently selected by the request.
 * Other caches can be explicitely requested, if necessary.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
final class arbitCacheRegistry
{
    /**
     * Array of contained caches
     *
     * @var array
     */
    protected static $caches = array();

    /**
     * Currently selected default cache
     *
     * @var string
     */
    protected static $defaultCache = null;

    /**
     * Set cache
     *
     * Initialize a cache entry with the given name. If no cache object is
     * given the default file based cache implementation will be chosen.
     *
     * @param string $name
     * @param arbitCache $cache
     * @return void
     */
    public static function setCache( $name, arbitCache $cache = null )
    {
        if ( $cache === null )
        {
            $cache = new arbitFilesystemCache( ARBIT_CACHE_PATH . $name . '/' );
        }

        self::$caches[$name] = $cache;
    }

    /**
     * Set default cache
     *
     * Set the name of the current default cache.
     *
     * @param string $name
     * @return void
     */
    public static function setDefaultCache( $name )
    {
        self::$defaultCache = $name;
    }

    /**
     * Get cache
     *
     * Get a cache object. If no name is given the current default cache is
     * returned.
     *
     * @param string $name
     * @return arbitCache
     */
    public static function getCache( $name = null )
    {
        if ( $name === null )
        {
            $name = self::$defaultCache;
        }

        if ( !isset( self::$caches[$name] ) )
        {
            throw new arbitPropertyException( $name );
        }

        return self::$caches[$name];
    }
}

