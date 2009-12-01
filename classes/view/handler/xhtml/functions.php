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
 * Custom template helper functions used by the xhtml view handler.
 *
 * @package Core
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitViewXHtmlTemplateFunctions
    implements
        ezcTemplateCustomFunction
{
    /**
     * Template override paths
     *
     * @var array
     */
    protected static $overridePaths;

    /**
     * Return function definition for function name
     *
     * @param string $name
     * @return ezcTemplateCustomFunctionDefinition
     */
    public static function getCustomFunctionDefinition( $name )
    {
        switch ( $name )
        {
            case 'arbit_dump':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = __CLASS__;
                $def->method = 'dumpModel';
                return $def;
            case 'arbit_simple_markup':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = __CLASS__;
                $def->method = 'simpleMarkup';
                return $def;
            case 'arbit_form_token':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = 'arbitSession';
                $def->method = 'getFormToken';
                return $def;
            case 'json_encode':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = null;
                $def->method = 'json_encode';
                return $def;
        }

        return false;
    }

    /**
     * Dump any object
     *
     * Dump any object for debuggin purpose. Especially usefull for the view
     * models. Does not work with recursive structures yet.
     *
     * Returns a formatted HTML block.
     *
     * @param mixed $content
     * @param int $maxDepth
     * @param int $depth
     * @return string
     */
    public static function dumpModel( $content, $maxDepth = 5, $depth = 0 )
    {
        if ( $maxDepth <= 0 )
        {
            return "Max nesting depth reached.";
        }

        switch ( $type = gettype( $content ) )
        {
            case 'integer':
            case 'double':
                $type = 'number';
            case 'string':
                $string = '<span class="' . $type . '">' . htmlspecialchars( $content, ENT_QUOTES ) . "</span>\n";
                break;

            case 'boolean':
                $string = '<span class="bool">' . ( $content ? 'true' : 'false' ) . "</span>\n";
                break;

            case 'null':
            case 'resource':
                $string = "<span class=\"$type\">$type</span>\n";
                break;

            case 'object':
                // Transform object to array for display.
                //
                // Handle arbitDecorateable special to show only the real
                // properties.
                if ( $content instanceof arbitDecorateable )
                {
                    $array = array();
                    foreach ( $content->getProperties() as $key )
                    {
                        $array[$key] = $content->$key;
                    }
                    $content = $array;
                    $type    = 'model';
                }
                else
                {
                    $content = (array) $content;
                }
            case 'array':
                $string = "<ul class=\"$type\">\n";
                $childs = false;

                // Display all array children
                foreach ( $content as $key => $value )
                {
                    $string .= "<li>" . htmlspecialchars( str_replace( "\0", '', $key ), ENT_QUOTES ) . ' => ' .
                        self::dumpModel( $value, $maxDepth - 1, $depth + 1 ) . "</li>\n";
                    $childs = true;
                }

                // Explicitely show, when an array has no elements
                if ( $childs === false )
                {
                    $string .= "<li>No children</li>\n";
                }

                $string .= "</ul>\n";
                break;

            default:
                $string = 'Unknown.';
        }

        return ( $depth === 0 ? "<div class=\"dump\">\n$string\n</div>" : $string );
    }

    /**
     * Simple markup
     *
     * Simple markup used for user constributed stuff. Should ensure not to
     * introduce any XSS, as it returns raw HTML.
     *
     * @param string $text
     * @return string
     */
    public static function simpleMarkup( $text )
    {
        // Ensure XSS free
        $text = htmlspecialchars( $text, ENT_QUOTES );

        // Make URLs clickable
        $text = preg_replace( '((?:https?|ftp|irc|svn)://(?:[^\s\)\]&]|&amp;)+)', '<a href="\\0">\\0</a>', $text );

        // Wrap on newlines
        $text = nl2br( $text );

        return $text;
    }
}

