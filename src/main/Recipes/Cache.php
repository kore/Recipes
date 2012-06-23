<?php
/**
 * This file is part of recipe.
 *
 * recipe is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * recipe is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with recipe; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @subpackage Model
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

namespace Recipes;

/**
 * Basic model class, provifing the default methods for getters and setters for
 * model classes.
 *
 * @package Core
 * @subpackage Model
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class Cache
{
    /**
     * Array with caches and their configurations.
     *
     * Each cache has two directives, the directory it stores its contents in,
     * and the time to live (TTL) in seconds.
     *
     * @var array
     */
    protected $caches = array();

    /**
     * Infinite TTL, causes that the cache will never timeout itself.
     */
    const INFINITE = -1;

    /**
     * Add a new cache
     *
     * Add a new cache specified by its name. Each cache requires a basepath
     * and you may optionally specify a TTL for cache items. The TTL defaults
     * to 3600 seconds (which is one hour).
     *
     * You may specify arbitCache::INFINITE as a TTL, so that the cache
     * will never tie out.
     *
     * @param string $name
     * @param int $ttl
     * @return void
     */
    public function addCache( $name, $ttl = 3600 )
    {
        $this->caches[$name] = $ttl;
    }

    /**
     * Get cache list
     *
     * Get list of registered caches
     *
     * @return array
     */
    public function getCacheList()
    {
        return array_keys( $this->caches );
    }

    /**
     * Get cache item from cache by its ID.
     *
     * If the cache item with $id cannot be found, false is returned.
     *
     * @param string $cache
     * @param string $id
     * @return mixed
     */
    abstract public function get( $cache, $id );

    /**
     * Cache item
     *
     * Cache item specified with a (hopefully unique) identifier in specified
     * cache.
     *
     * Cacheable are all PHP scalar values, arrays and all object implementing
     * arbitCacheable, which especially means, that all arbitBaseStruct
     * extending objects are cacheable.
     *
     * @param string $cache
     * @param string $id
     * @param mixed $item
     * @return void
     */
    abstract public function cache( $cache, $id, $item );

    /**
     * Remove item from cache
     *
     * Remove the item specified by its ID from the specified cache.
     *
     * @param string $cache
     * @param string $id
     * @return void
     */
    abstract public function purge( $cache, $id );

    /**
     * Clear cache
     *
     * Clear the cache specified by the cache ID completely. All contents will
     * be purged.
     *
     * @param string $cache
     * @return void
     */
    abstract public function clearCache( $cache );
}

