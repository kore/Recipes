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
class arbitViewTextTemplateFunctions
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
            case 'arbit_wrap':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = __CLASS__;
                $def->method = 'indentedWrap';
                return $def;
        }

        return false;
    }

    /**
     * Wrap text
     *
     * Wrap text with an optional specified indentation string and a default
     * wrapping width of 78.
     *
     * @param string $text
     * @param string $indetation
     * @param int $width
     * @return string
     */
    public static function indentedWrap( $text, $indetation = '', $width = 78 )
    {
        // Normalize line breaks
        $text = preg_replace( '(\r\n|\r|\n)', "\n", trim( $text ) );

        // Wrap text using the default word wrap function, with a width
        // reduced by the later indetation.
        $text = wordwrap( $text, $width - strlen( $indetation ) );

        // Add indentation to text
        return $indetation . str_replace( "\n", "\n" . $indetation, $text );
    }
}

