<?php
/**
 * This file is part of recipes.
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

namespace Recipes;

/**
 * Dependency Injection Container base class
 *
 * @version $Revision$
 */
class DIC
{
    /**
     * List of properties of this DIC instance.
     *
     * Actually this is a list of closures instantiating the objects, which
     * will be retrieved from the DIC.
     *
     * @var array(Closure)
     */
    protected $objects = array();

    /**
     * Array with names of objects, which are always shared inside of this DIC
     * instance.
     *
     * @var array(string)
     */
    protected $alwaysShared = array();

    /**
     * Shared object storage.
     *
     * Stores references to shared objects in this DIC instance.
     *
     * @var array(mixed)
     */
    protected $shared = array();

    /**
     * Create DIC and initialize default objects.
     *
     * @return void
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Set closure to create a given object, or set given object directly.
     *
     * @param string $name
     * @param mixed $closure
     * @return void
     */
    public function __set( $name, $closure )
    {
        $this->objects[$name] = $closure;

        // If object is  already shared, remove maybe existing shared instance
        if ( isset( $this->shared[$name] ) )
        {
            unset( $this->shared[$name] );
        }
    }

    /**
     * Retrieve object of given name from DIC
     *
     * @param string $name
     * @return mixed
     */
    public function __get( $name )
    {
        if ( !isset( $this->objects[$name] ) )
        {
            throw new DIC\PropertyException( $name );
        }

        if ( isset( $this->alwaysShared[$name] ) &&
             $this->alwaysShared[$name] )
        {
            return $this->getShared( $name );
        }

        return is_callable( $this->objects[$name] ) ?
            $this->objects[$name]( $this ) :
            $this->objects[$name];
    }

    /**
     * Create a new object with parameters from the DIC
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call( $name, array $arguments )
    {
        if ( !isset( $this->objects[$name] ) )
        {
            throw new DIC\PropertyException( $name );
        }

        if ( isset( $this->alwaysShared[$name] ) &&
             $this->alwaysShared[$name] )
        {
            throw new DIC\RuntimeException( 'Cannot construct shared object with parameters.' );
        }

        if ( !is_callable( $this->objects[$name] ) )
        {
            throw new DIC\RuntimeException( 'Cannot instantiate non-closure object with arguments.' );
        }

        return $this->objects[$name]( $this, $arguments );
    }

    /**
     * Get shared object of given name
     *
     * @param string $name
     * @return mixed
     */
    public function getShared( $name )
    {
        if ( !isset( $this->shared[$name] ) )
        {
            $this->shared[$name] = is_callable( $this->objects[$name] ) ?
                $this->objects[$name]( $this ) :
                $this->objects[$name];
        }

        return $this->shared[$name];
    }

    /**
     * Set object as shared
     *
     * @param string $name
     * @return void
     */
    public function setShared( $name )
    {
        $this->alwaysShared[$name] = true;
    }

    /**
     * Initialize DIC values
     *
     * @return void
     */
    public function initialize()
    {
        // Nothing by default.
    }
}

