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
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Xml view handler generating Xml from the provided view model.
 *
 * @package Core
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitViewXmlHandler extends arbitViewHandler
{
    /**
     * Created DOM Document
     *
     * @var mixed
     */
    protected $doc;

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
        // Locales are irrelevant for XML
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
        $this->doc = new DOMDocument();
        $this->doc->formatOutput = true;
        arbitViewModelDecorationDependencyInjectionManager::setViewHandler( $this );
        $node  = arbitViewModelDecorationDependencyInjectionManager::decorate( $output );
        $this->doc->appendChild( $node );
        return $this->doc->saveXml();
    }

    /**
     * Return XML representation of some PHP value
     *
     * @param mixed $value
     * @return DOMNode
     */
    protected function transformValue( $value )
    {
        switch ( $type = strtolower( gettype( $value ) ) )
        {
            case 'boolean':
            case 'integer':
            case 'double':
            case 'string':
                $value = $this->doc->createElement( $type, htmlspecialchars( $value ) );
                return $value;

            case 'null':
                return $this->doc->createElement( 'null' );

            case 'object':
                if ( $value instanceof arbitDecorateable )
                {
                    return arbitViewModelDecorationDependencyInjectionManager::decorate(
                        $value
                    );
                }

                // If the object is not an instance of an arbitDecorateable, we
                // just ignore it, as it may contain information, which should
                // not be exposed.
                return $this->doc->createElement( 'object' );

            case 'array':
                return $this->transformArray( $value );

            default:
                throw new arbitRuntimeException( 'Not transformable value provided: ' . $type );
        }
    }

    /**
     * Return XML representation of PHP array
     *
     * @param array $array
     * @return DOMNode
     */
    protected function transformArray( array $array )
    {
        $arrayNode = $this->doc->createElement( 'array' );

        foreach ( $array as $key => $value )
        {
            $valueNode = $this->doc->createElement( 'element' );
            $valueNode->setAttribute( 'key', htmlspecialchars( $key ) );
            $arrayNode->appendChild( $valueNode );

            $propertyNode = $this->transformValue( $value );
            $valueNode->appendChild( $propertyNode );
        }

        return $arrayNode;
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
        if ( $this->doc === null )
        {
            throw new arbitRuntimeException(
                'Method should only be called after the display method.'
            );
        }

        $modelNode = $this->doc->createElement( 'model' );
        $modelNode->setAttribute( 'xmlns', 'http://arbitracker.org/xml' );
        $modelNode->setAttribute( 'class', get_class( $model ) );

        foreach ( $model->getProperties() as $property )
        {
            $valueNode = $this->doc->createElement( 'property' );
            $valueNode->setAttribute( 'name', htmlspecialchars( $property ) );
            $modelNode->appendChild( $valueNode );

            $propertyNode = $this->transformValue( $model->$property );
            $valueNode->appendChild( $propertyNode );
        }

        return $modelNode;
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
        if ( $this->doc === null )
        {
            throw new arbitRuntimeException(
                'Method should only be called after the display method.'
            );
        }

        $modelNode = $this->doc->createElement( 'error' );
        $modelNode->setAttribute( 'xmlns', 'http://arbitracker.org/xml' );
        $modelNode->setAttribute( 'class', get_class( $model ) );

        $message = $this->doc->createElement( 'message', htmlspecialchars( $model->exception->getMessage() ) );
        $modelNode->appendChild( $message );

        return $modelNode;
    }

    /**
     * Show user model
     *
     * Special display handler for user models, to not reveal to much data
     * about the user inside the XML.
     *
     * @param arbitViewUserModel $model
     * @return string
     */
    public function showUser( arbitViewUserModel $model )
    {
        if ( $this->doc === null )
        {
            throw new arbitRuntimeException(
                'Method should only be called after the display method.'
            );
        }

        $modelNode = $this->doc->createElement( 'user' );
        $modelNode->setAttribute( 'xmlns', 'http://arbitracker.org/xml' );
        $modelNode->setAttribute( 'id', htmlspecialchars( $model->id ) );

        $data = $this->doc->createElement( 'login', htmlspecialchars( $model->login ) );
        $modelNode->appendChild( $data );

        return $modelNode;
    }
}


