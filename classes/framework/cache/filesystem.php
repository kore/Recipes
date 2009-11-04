<?php
/**
 * arbit cache
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
 * Arbit simple file based cache handler
 *
 * Provides simple cache handling with support for TTL of cache items.
 *
 * Currently everything is just stored in the filesystem. Since we store PHP
 * files an opcode cache will keep the most recent items in the memory. We may
 * want to put some cache items into memcache or similar shared memory caches
 * at some point in the future. We may want to use the cache component from the
 * eZ Components at this point, which will pprovide such a mutilevel caching
 * since its 2008.1 release.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitFilesystemCache extends arbitCache
{
    /**
     * Cache root directory
     *
     * @var string
     */
    protected $root;

    /**
     * Construct cache from cache root
     *
     * If no explicit cache root is provided, the default location provided in
     * the arbit constant ARBIT_CACHE_PATH is used.;
     *
     * @param string $path
     * @return void
     */
    public function __construct( $path = null )
    {
        if ( $path === null )
        {
            $path = ARBIT_CACHE_PATH;
        }

        $this->root = $path;
    }

    /**
     * Get cache item from cache by its ID
     *
     * @param string $cache
     * @param string $id
     * @return mixed
     */
    public function get( $cache, $id )
    {
        if ( !isset( $this->caches[$cache] ) )
        {
            throw new arbitNoSuchCacheException( $cache );
        }

        // Check if the cache file exists at all...
        if ( !is_file( $path = $this->root . $this->caches[$cache]['path'] . $id . '.php' ) )
        {
            return false;
        }

        // Check if TTL has exceeded
        if ( ( $this->caches[$cache]['ttl'] !== arbitCache::INFINITE ) &&
             ( filemtime( $path ) + $this->caches[$cache]['ttl'] ) < time() )
        {
            unlink( $path );
            return false;
        }

        // Restore file, exported with var_export
        return include $path;
    }

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
    public function cache( $cache, $id, $item )
    {
        if ( !isset( $this->caches[$cache] ) )
        {
            throw new arbitNoSuchCacheException( $cache );
        }

        // Check if item is cacheable - all scalars are cacheable, so are
        // arrays and all objects, which implement arbitCacheable, which
        // especially means all objects extending arbitBaseStruct.
        if ( !is_scalar( $item ) &&
             !is_array( $item ) &&
             !( is_object( $item ) && ( $item instanceof arbitCacheable ) ) )
        {
            throw new arbitItemNotCacheableException( $id );
        }

        // Ensure cache directory exists
        $path = $this->root . $this->caches[$cache]['path'] . $id . '.php';
        if ( !is_dir( $directory = dirname( $path ) ) )
        {
            mkdir( $directory, 0770, true );
        }

        // Use var_export for caching, which also works for objects
        // implementing __set_state and causes the cache files to be delivered
        // by opcode caches.
        file_put_contents(
            $path,
            "<?php\n\nreturn " . var_export( $item, true ) . ";\n\n"
        );
    }

    /**
     * Remove item from cache
     *
     * Remove the item specified by its ID from the specified cache.
     *
     * @param string $cache
     * @param string $id
     * @return void
     */
    public function purge( $cache, $id )
    {
        if ( !isset( $this->caches[$cache] ) )
        {
            throw new arbitNoSuchCacheException( $cache );
        }

        // Check if the cache file exists, and remove it in this case
        if ( is_file( $path = $this->root . $this->caches[$cache]['path'] . $id . '.php' ) )
        {
            unlink( $path );
        }
    }

    /**
     * Clear cache
     *
     * Clear the cache specified by the cache ID completely. All contents will
     * be purged.
     *
     * @param string $cache
     * @return void
     */
    public function clearCache( $cache )
    {
        if ( is_dir( $path = $this->root . $this->caches[$cache]['path'] ) )
        {
            ezcBaseFile::removeRecursive( $path );
        }
    }
}

