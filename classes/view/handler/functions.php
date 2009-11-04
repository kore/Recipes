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
class arbitViewTemplateFunctions
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
     * Instance of arbit date formatter used to format given timestamps
     *
     * @var arbitDateTimeFormatter
     */
    protected static $dateFormatter;

    /**
     * Cached translation manager object
     *
     * @var arbitTranslationManager
     */
    protected static $translationManager;

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
            case 'arbit_get_debug_messages':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = 'arbitLogger';
                $def->method = 'getMessages';
                return $def;
            case 'arbit_get_template':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = __CLASS__;
                $def->method = 'getTemplatePath';
                return $def;
            case 'arbit_decorate':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = 'arbitViewModelDecorationDependencyInjectionManager';
                $def->method = 'decorate';
                return $def;
            case 'arbit_show':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = __CLASS__;
                $def->method = 'showTranslateable';
                return $def;
            case 'arbit_tr':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = __CLASS__;
                $def->method = 'translate';
                return $def;
            case 'arbit_url':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = __CLASS__;
                $def->method = 'getUrl';
                return $def;
            case 'arbit_env':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = 'arbitHttpTools';
                $def->method = 'serverVariable';
                return $def;
            case 'arbit_get_project_data':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = __CLASS__;
                $def->method = 'getProjectData';
                return $def;
            case 'arbit_may':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = 'arbitSession';
                $def->method = 'may';
                return $def;
            case 'arbit_recursive_iterator':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = __CLASS__;
                $def->method = 'getRecursiveIteratorIterator';
                return $def;
            case 'arbit_get_paths':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = __CLASS__;
                $def->method = 'getPathsFromArray';
                return $def;
            case 'arbit_diff':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = __CLASS__;
                $def->method = 'getDiff';
                return $def;
            case 'arbit_date_format':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = __CLASS__;
                $def->method = 'formatDate';
                return $def;
            case 'arbit_strip_nonprintable':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = __CLASS__;
                $def->method = 'stripNonPrintable';
                return $def;
            case 'arbit_get_form_values':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = __CLASS__;
                $def->method = 'getFormValues';
                return $def;
            case 'array_element':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = __CLASS__;
                $def->method = 'getArrayElement';
                return $def;
            case 'date_timezone_list':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = 'DateTimeZone';
                $def->method = 'listIdentifiers';
                return $def;
            case 'var_dump':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = __CLASS__;
                $def->method = 'varDump';
                return $def;
        }

        return false;
    }

    /**
     * Get correct template file from path
     *
     * @param string $file
     * @return string
     */
    public static function getTemplatePath( $file )
    {
        // If no override definitions exist, return default file
        if ( self::$overridePaths === false )
        {
            return $file;
        }

        // If not done yet, read override definitions from ini file
        if ( self::$overridePaths === null )
        {
            $conf = arbitBackendIniConfigurationManager::getMainConfiguration();
            self::$overridePaths = array_merge(
                $conf->get( 'layout', 'override' ),
                arbitModuleManager::getTemplatePaths()
            );
        }

        // Use first match
        foreach ( self::$overridePaths as $base )
        {
            // We don't care if an absolute or relative path has been provided
            // by the repective module or override definition.
            if ( is_file( $path = ARBIT_BASE . $base . $file ) ||
                 is_file( $path = $base . $file ) )
            {
                return $path;
            }
        }

        // Default to no override
        return $file;
    }

    /**
     * Show a translateable object
     *
     * Display a translateable object, which usually means it should be
     * translated accordingly to the currently set language, which is not yet
     * implemented.
     *
     * @param arbitTranslateable $object
     * @return string
     */
    public static function showTranslateable( arbitTranslateable $object )
    {
        return self::translate( $object->getText(), $object->getTextValues(), 'core/dynamic' );
    }

    /**
     * Translate a string
     *
     * Translates a given string with replaced values, just like the tr
     * template block. This template function should be used as seldom as
     * possible. Its results are not cached statically and it is not possible
     * to statically extract translation strings. Use the template block
     * whenever possible.
     *
     * @param string $text
     * @param array $params
     * @param string $context
     * @param string $comment
     * @return string
     */
    public static function translate( $text, array $params = array(), $context = null, $comment = null )
    {
        if ( self::$translationManager === null )
        {
            self::$translationManager = new arbitTranslationManager();
        }

        $context = self::$translationManager->getContext( null, $context );
        return $context->getTranslation( $text, $params );
    }

    /**
     * Get a absolute URL from request configuration
     *
     * Builds an absolute URL for inclusion in your tempaltes mapping the
     * given request configuration.
     *
     * @param string $controller
     * @param string $action
     * @param string $subaction
     * @param array $parameters
     * @return string
     */
    public static function getUrl( $controller, $action = 'index', $subaction = null, array $parameters = array() )
    {
        $request = new arbitHttpRequest();
        $request->controller = $controller;
        $request->action     = $action;
        $request->subaction  = $subaction;
        $request->variables  = $parameters;
        return $request->serialize( true );
    }

    /**
     * Get project data
     *
     * Return the project data for the currently selected project.
     *
     * @return arbitViewProjectModel
     */
    public static function getProjectData()
    {
        return new arbitViewProjectModel(
            new arbitModelProject()
        );
    }

    /**
     * Return RecursiveIteratorIterator for recursive array
     *
     * Build and return a RecursiveIteratorIterator to iterate over recursive
     * arrays in templates.
     *
     * @param array $array
     * @return arbitTemplateRecursiveIteratorIterator
     */
    public static function getRecursiveIteratorIterator( array $array )
    {
        $arrayIterator = new RecursiveArrayIterator( $array );
        return new arbitTemplateRecursiveIteratorIterator(
            $arrayIterator,
            RecursiveIteratorIterator::SELF_FIRST
        );
    }

    /**
     * getRecursiveIteratorIterator
     *
     * @param array $array
     * @return void
     */
    public static function getPathsFromArray( array $array )
    {
        if ( count( $array ) < 1 )
        {
            return array();
        }

        foreach ( $array as $name => $childs )
        {
            $paths[] = array( $name );
            $childPaths = self::getPathsFromArray( $childs );
            foreach ( $childPaths as $path )
            {
                array_unshift( $path, $name );
                $paths[] = $path;
            }
        }

        return $paths;
    }

    /**
     * Get formatted date
     *
     * Format given timestamp based on users timezone and a given date format
     * string. The string may either be "user" for the user configured format,
     * or one of the formats, which can be understand by the
     * arbitDateTimeFormatter, which currently are:
     *
     * - full, long, medium, short
     * - fullDate, longDate, mediumDate, shortDate
     * - fullTime, longTime, mediumTime, shortTime
     *
     * @param int $timestamp
     * @param string $format
     * @return string
     */
    public static function formatDate( $timestamp, $format = 'full' )
    {
        if ( self::$dateFormatter === null )
        {
            $templateConfig = ezcTemplateTranslationConfiguration::getInstance();
            self::$dateFormatter = new arbitDateTimeFormatter( $templateConfig->locale );
        }

        $date = new DateTime();
        $date->setTimestamp( $timestamp );

        $settings = arbitSession::get( 'settings' );
        $timezone = new DateTimeZone( $settings && isset( $settings['date_timezone'] ) ? $settings['date_timezone'] : 'UTC' );
        $date->setTimezone( $timezone );

        return self::$dateFormatter->format( $format, $date );
    }

    /**
     * Calculate and return diff
     *
     * Calculate and return the diff of the given type for the given input.
     * Type may be either 'line', 'array', or 'text', while the method defaults
     * to text diff.
     *
     * The returned array is documented at the repsective diff algorithm
     * implementation and may be slightly different depending on the used
     * algorithm.
     *
     * @param mixed $old
     * @param mixed $new
     * @param string $type
     * @return array
     */
    public static function getDiff( $old, $new, $type = 'text' )
    {
        switch ( $type )
        {
            case 'line':
                $differ = new arbitFrameworkLineDiff();
                return $differ->lineDiff( $old, $new );

            case 'array':
                $differ = new arbitFrameworkDiff();
                return $differ->diff( $old, $new );

            default:
            case 'text':
                $differ = new arbitFrameworkTextDiff();
                return $differ->textDiff( $old, $new );
        }
    }

    /**
     * Get element from array
     *
     * Get on element from array, specified by its index position. You may
     * either use positive number to get the nth element, or negitive numbers
     * to get the nth element from the end of the array, where -1 means the
     * last element in the array.
     *
     * @param array $array
     * @param int $element
     * @return mixed
     */
    public static function getArrayElement( array $array, $element )
    {
        $arrayElement = array_slice( $array, $element, 1 );
        return reset( $arrayElement );
    }

    /**
     * Strip non printable characters from string
     *
     * Returns a string with all non-printable characters stripped from the
     * string.
     *
     * @param string $string
     * @return string
     */
    public static function stripNonPrintable( $string )
    {
        return preg_replace( '([\x00-\x1f\x7f]+)S', '', $string );
    }

    /**
     * Get form values
     *
     * Receive all form values for the current form from the prior request. Can
     * be used to maintain form values in case of an error.
     *
     * Receives an array with the form value definition, which should look like:
     *
     * <code>
     *  array(
     *      'name' => 'string',
     *      'mail' => 'string',
     *      ...
     *  )
     * </code>
     *
     * Available types are "string", "number" and "array".
     *
     * @param array $definition
     * @return array
     */
    public static function getFormValues( array $definition )
    {
        $mapping = array(
            'string' => arbitHttpTools::TYPE_STRING,
            'number' => arbitHttpTools::TYPE_NUMERIC,
            'array'  => arbitHttpTools::TYPE_ARRAY,
        );

        $values = array();
        foreach ( $definition as $name => $type )
        {
            $values[$name] = arbitHttpTools::get( $name, $mapping[$type] );
        }
        return $values;
    }

    /**
     * Variable dumper
     *
     * Simple wrapper around PHPs var_dump(), which implements recursion
     * detection, as opposed to the default template debug function.
     *
     * @param mixed $var
     * @return array
     */
    public static function varDump( $var )
    {
        ob_start();
        var_dump( $var );
        return ob_get_clean();
    }
}

