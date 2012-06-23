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

namespace Recipes\Cache;

use Recipes\Cache;

/**
 * Simple file based cache handler
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
 * @version $Revision: 1927 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class Filesystem extends Cache
{
    /**
     * Cache root directory
     *
     * @var string
     */
    protected $root;

    /**
     * Construct cache from cache path
     *
     * @param string $path
     * @return void
     */
    public function __construct( $path )
    {
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
            throw new \OutOfBoundsException( $cache );
        }

        // Check if the cache file exists at all...
        if ( !is_file( $path = $this->root . $name . '/' . $id . '.php' ) )
        {
            return false;
        }

        // Check if TTL has exceeded
        if ( ( $this->caches[$cache] !== self::INFINITE ) &&
             ( filemtime( $path ) + $this->caches[$cache] ) < time() )
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
     * Cacheable.
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
            throw new \OutOfBoundsException( $cache );
        }

        // Check if item is cacheable - all scalars are cacheable, so are
        // arrays and all objects, which implement Cacheable
        if ( !is_scalar( $item ) &&
             !is_array( $item ) &&
             !( is_object( $item ) && ( $item instanceof Cacheable ) ) )
        {
            throw new \InvalidArgumentException( $item . ' is not cacheable.' );
        }

        // Ensure cache directory exists
        $path = $this->root . $name . '/' . $id . '.php';
        if ( !is_dir( $directory = dirname( $path ) ) )
        {
            mkdir( $directory, 0777, true );
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
            throw new \OutOfBoundsException( $cache );
        }

        // Check if the cache file exists, and remove it in this case
        if ( is_file( $path = $this->root . $name . '/' . $id . '.php' ) )
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
        if ( is_dir( $path = $this->root . $name ) )
        {
            \ezcBaseFile::removeRecursive( $path );
        }
    }
}

