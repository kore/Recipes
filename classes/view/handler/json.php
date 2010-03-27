<?php
/**
 * arbit view handler
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
 * @subpackage View
 * @version $Revision: 1650 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * JSON view handler generating JSON from the provided view model.
 *
 * @package Core
 * @subpackage View
 * @version $Revision: 1650 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitViewJsonHandler extends arbitViewHandler
{
    /**
     * Enriches view model with common context information
     *
     * @param arbitDecorateable $model
     * @return arbitDecorateable
     */
    public function addContextInformation( arbitDecorateable $model )
    {
        $model->request   = $this->request;
        $model->debugMode = ARBIT_DEBUG;
        $model->loggedIn  = arbitSession::get( "login" );

        return $model;
    }

    /**
     * Set view locale
     *
     * Set the view locale to the given locale string.
     *
     * @param string $locale
     * @return void
     */
    public function setLocale( $locale )
    {
        // Locales are irrelevant for JSON
    }

    /**
     * Display controller result
     *
     * Select the view, which should be used to display the controller results,
     * process and return the view results.
     *
     * @param arbitDecorateable $output
     * @return string
     */
    public function display( arbitDecorateable $output )
    {
        arbitViewModelDecorationDependencyInjectionManager::setViewHandler( $this );
        return arbitViewModelDecorationDependencyInjectionManager::decorate( $output );
    }

    /**
     * Return JSON representation of some PHP value
     *
     * @param mixed $value
     * @return DOMNode
     */
    protected function transformValue( $value )
    {
        switch ( $type = strtolower( gettype( $value ) ) )
        {
            case "boolean":
            case "integer":
            case "double":
            case "string":
            case "null":
                return json_encode( $value );

            case "object":
                if ( $value instanceof arbitDecorateable )
                {
                    return arbitViewModelDecorationDependencyInjectionManager::decorate(
                        $value
                    );
                }

                // If the object is not an instance of an arbitDecorateable, we
                // just ignore it, as it may contain information, which should
                // not be exposed.
                return json_encode( null );

            case "array":
                return $this->transformArray( $value );

            default:
                throw new arbitRuntimeException( "Not transformable value provided: " . $type );
        }
    }

    /**
     * Return JSON representation of PHP array
     *
     * @param array $array
     * @return DOMNode
     */
    protected function transformArray( array $array )
    {
        if ( array_reduce(
                array_map(
                    function ( $key )
                    {
                        return is_numeric( $key );
                    },
                    array_keys( $array )
                ),
                function ( $lastKey, $key )
                {
                    return $lastKey && $key;
                },
                true
             ) )
        {
            $string = '[ ';
            foreach ( $array as $key => $value )
            {
                $string .= $this->transformValue( $value ) . ",";
            }

            return substr( $string, 0, -1 ) . "]";
        }

        $string = "{";
        foreach ( $array as $key => $value )
        {
            $string .= $this->transformValue( (string) $key ) . ": " . $this->transformValue( $value ) . ",";
        }

        return substr( $string, 0, -1 ) . "}";
    }

    /**
     * Show default model
     *
     * Display an arbitrary model with some default view.
     *
     * @param arbitViewErrorModel $model
     * @return string
     */
    public function showDefaultModel( arbitDecorateable $model )
    {
        $string =  "{";
        $string .= "\"model\": \"" . get_class( $model ) . "\",";
        $string .= "\"properties\": {";

        foreach ( $model->getProperties() as $property )
        {
            $string .= "" . $this->transformValue( $property ) . ": " . $this->transformValue( $model->$property ) . ",";
        }

        return substr( $string, 0, -1 ) . "}}";
    }

    /**
     * Show default model
     *
     * Display an arbitrary model with some default view.
     *
     * @param arbitViewErrorModel $model
     * @return string
     */
    public function showError( arbitViewErrorContextModel $model )
    {
        return "{
            \"type\": " . $this->transformValue( get_class( $model->exception ) ) . ",
            \"error\": " . $this->transformValue( $model->exception->getMessage() ) . "
        }";
    }

    /**
     * Show user model
     *
     * Special display handler for user models, to not reveal to much data
     * about the user inside the JSON.
     *
     * @param arbitViewUserModel $model
     * @return string
     */
    public function showUser( arbitViewUserModel $model )
    {
        return "{\"user\": " . json_encode( $model->login ) . "}";
    }
}

