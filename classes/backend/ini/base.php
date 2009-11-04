<?php
/**
 * arbit ini configuration backend
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
 * @subpackage IniBackend
 * @version $Revision: 1290 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Base class for configuration wrappers
 *
 * @package Core
 * @subpackage IniBackend
 * @version $Revision: 1290 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class arbitBackendIniConfigurationBase
{
    /**
     * Array with default values for all required configuration directives. The
     * array has the followin structure:
     *
     * <code>
     *  array(
     *      'group' => array(
     *          'key' => $value,
     *          ...
     *      ),
     *      ...
     *  )
     * </code>
     *
     * @var array
     */
    protected $defaultValues = array();

    /**
     * Shortcuts for configuration value access.
     *
     * These shortcuts may be used for property access to the configuration
     * values. The shortcut array has the following structure:
     *
     * <code>
     *  array(
     *      'shortcut' => array( 'group', 'value' ),
     *      ...
     *  )
     * </code>
     *
     * @var array
     */
    protected $shortcuts = array();

    /**
     * Configuration manager instance
     *
     * @var ezcConfigurationManager
     */
    protected $config;

    /**
     * Used ini file.
     *
     * This should be set in the extending class.
     *
     * @var string
     */
    protected $iniFile = null;

    /**
     * All values, which were fetched once are stored in the value cache and
     * returned from there in any later request to this value.
     *
     * @var array
     */
    protected $valueCache = array();

    /**
     * Construct from ezc configuration manager
     *
     * @param ezcConfigurationManager $config
     * @return void
     */
    public function __construct( ezcConfigurationManager $config )
    {
        $this->config = $config;
    }

    /**
     * Get a configuration value
     *
     * @param mixed $group
     * @param mixed $value
     * @return void
     */
    public function get( $group, $value )
    {
        // Check if value has already been fetched earlier during this request,
        // then return it from the value cache.
        //
        // This should help to prevent from parsing ini files again and again,
        // and spare the checks for default values, etc. Depending on the used
        // ini backend this may be an expensive operation.
        if ( isset( $this->valueCache[$group] ) &&
             isset( $this->valueCache[$group][$value] ) )
        {
            return $this->valueCache[$group][$value];
        }

        // Check if ini file had correctly been set by the extending class.
        if ( $this->iniFile === null )
        {
            throw new arbitRuntimeException(
                'Broken configuration object: No ini file defined.'
            );
        }

        try
        {
            // Check if setting exists
            if ( $this->config->hasSetting( $this->iniFile, $group, $value ) )
            {
                // Setting exists, so store it in the value cache and return
                // it.
                $this->valueCache[$group][$value] = $returnValue = $this->config->getSetting( $this->iniFile, $group, $value );
                return $returnValue;
            }
        }
        catch ( ezcConfigurationUnknownConfigException $e )
        {
            // This also means, that the configuration value is not defined.
            // Simply continue as nothing happens. See
            // http://issues.ez.no/12523 for details.
        }

        // If the setting does not exist in the configuration file, there
        // may be a default value, which can be used.
        if ( array_key_exists( $group, $this->defaultValues ) &&
             array_key_exists( $value, $this->defaultValues[$group] ) )
        {
            return $this->defaultValues[$group][$value];
        }

        // Otherwise the value really does not exist, we throw our own
        // exception in this case
        throw new arbitBackendUnknownConfigurationException(
            $this->iniFile, $group, $value
        );
    }

    /**
     * Property access implementations for configuration shortcuts
     *
     * @ignore
     * @param string $shortcut
     * @return mixed
     */
    public function __get( $shortcut )
    {
        // Check if the requested shortcut exists
        if ( !isset( $this->shortcuts[$shortcut] ) )
        {
            throw new arbitPropertyException( $shortcut );
        }

        // just dispatch with the shortcut definition values.
        list( $group, $value ) = $this->shortcuts[$shortcut];
        return $this->get( $group, $value );
    }

    /**
     * Property setting stub, always throwing an exception.
     *
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public function __set( $property, $value )
    {
        // Do not allow to set values, always throw an exception for this.
        throw new arbitPropertyException( $property );
    }
}

