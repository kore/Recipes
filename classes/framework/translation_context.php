<?php
/**
 * arbit translation context
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
 * Translation manager for arbit.
 *
 * Handles multiple backends for caching of the translations, and creates
 * translation file entries for missing translation items.
 *
 * @package Core
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitCachingTranslationContext
{
    /**
     * Translation object
     *
     * @var ezcTranslation
     */
    protected $translation;

    /**
     * Translation backend
     *
     * @var ezcTranslationBackend
     */
    protected $backend;

    /**
     * Current translation locale
     *
     * @var string
     */
    protected $locale;

    /**
     * Translation striing context
     *
     * @var string
     */
    protected $context;

    /**
     * Construct caching translation context
     *
     * Construct from translation backend and the actual
     * translation.
     *
     * @param ezcTranslation $translation
     * @param ezcTranslationBackend $backend
     * @param string $locale
     * @param string $context
     * @return void
     */
    public function __construct( ezcTranslation $translation, ezcTranslationBackend $backend, $locale, $context )
    {
        $this->translation = $translation;
        $this->backend     = $backend;
        $this->locale      = ( $locale ? $locale : 'en' );
        $this->context     = ( $context ? $context : 'core' );
    }

    /**
     * Returns the translated version of the original string $key.
     *
     * This method returns a translated string and substitutes the parameters $param
     * in the localized string.
     *
     * @throws ezcTranslationKeyNotAvailableException when the key is not available
     * @throws ezcTranslationParameterMissingException when not enough
     *         parameters are passed for a parameterized string
     * @param string $key
     * @param array(string=>string)  $params
     * @return string
     */
    public function getTranslation( $key, array $params = array() )
    {
        try
        {
            return $this->translation->getTranslation( $key, $params );
        }
        catch ( ezcTranslationKeyNotAvailableException $e )
        {
            // Clear translation cache for context, since
            // modifications are applied.
            arbitCacheRegistry::getCache()->purge( 'translation', $this->context );

            // If the translation file does not yet contain the
            // requested translation string, create it:
            $context = $this->backend->getContext( $this->locale, $this->context );
            $context[] = new ezcTranslationData( $key, $key, null, ezcTranslationData::UNFINISHED );

            // Write to file.
            $this->backend->initWriter( $this->locale );
            $this->backend->storeContext( $this->context, $context );
            $this->backend->deinitWriter();

            // Just use given key as translation
            return $this->replaceKeys( $key, $params );
        }
    }

    /**
     * Replace keys in translation strings
     *
     * Replace all keys in the translation strings. Keys start with a percent
     * sign and are followed by alsphanumeric characters. They are replaced by
     * the corresponding entries ind the $params array.
     *
     * @param mixed $key
     * @param array $params
     * @return void
     */
    protected function replaceKeys( $key, array $params )
    {
        // Little optimization to prevent preg if not needed, it bails out too
        // if there is just a percent sign in the string without a valid
        // parameter-identifier, but we can live with that.
        if ( strpos( $key, '%' ) === false )
        {
            return $key;
        }

        // So we do have a possibility of a parameterized string, replace those
        // with the parameters. The callback function can actually throw an
        // exception to tell that there was a missing parameter.
        return preg_replace_callback(
            '(%(([A-Za-z][a-z_]*[a-z])|[1-9]))',
            function( $replacement ) use ( $params )
            {
                $replacement = strtolower( $replacement[1] );
                if ( !isset( $params[$replacement] ) )
                {
                    throw new ezcTranslationParameterMissingException( $replacement );
                }

                return $params[$replacement];
            },
            $key
        );
    }

    /**
     * Returns the translated version of the original string $key.
     *
     * This method returns a translated string and substitutes the parameters $param
     * in the localized string with PHP code to place the variable data into
     * the string at a later moment. Instead of the values for each of the
     * parameters, an expression to get to the data should be sumbitted into
     * the $params array.
     *
     * <code>
     * echo $translation->compileTranslation( "Hello #%nr", array( "nr" => '$this->send->nr' ) );
     * </code>
     *
     * Will return something like:
     * <code>
     * 'Hallo #' . $this->send->nr . ''
     * </code>
     *
     * @param string $key
     * @param array(string=>string)  $params
     * @return string
     */
    public function compileTranslation( $key, array $params = array() )
    {
        // Just dispatch to compileTranslation method from wrapped
        // translation object.
        return $this->translation->compileTranslation( $key, $params );
    }
}

