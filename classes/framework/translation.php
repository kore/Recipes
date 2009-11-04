<?php
/**
 * arbit translation manager
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
class arbitTranslationManager extends ezcTranslationManager
{
    /**
     * The backend in use which is responsible for reading in
     * the context and returning it to the manager.
     *
     * @var ezcTranslationBackendInterface
     */
    protected $backend;

    /**
     * Default locale, if no locale is explicitely passed to fetch methods.
     *
     * @var string
     */
    protected $defaultLocale = 'en';

    /**
     * Constructs an ezcTranslationManager object
     *
     * This constructor constructs a new ezcTranslationManager object. The only
     * parameter is a class that implements the ezcTranslationBackendInterface.
     *
     * @param ezcTranslationBackend $backend An instance of a translation
     *                                       backend.
     */
    function __construct( ezcTranslationBackend $backend = null )
    {
        $this->backend = new ezcTranslationTsBackend( ARBIT_BASE . 'translations' );
        $this->backend->options->format = '[LOCALE].xml';

        parent::__construct( $this->backend );
    }

    /**
     * Set default locale
     *
     * @param string $locale
     * @return void
     */
    public function setDefaultLocale( $locale )
    {
        $this->defaultLocale = $locale;
    }

    /**
     * Returns the translations for the $context context and the locale $locale.
     *
     * This methods reads the context data from the backend and applies the
     * filters before returning the translation map as part of the
     * ezcTranslation object that it returns.
     *
     * @param string $locale
     * @param string $context
     * @return ezcTranslation
     */
    public function getContext( $locale, $context )
    {
        // Fallback to default locale, if none given explicitely
        if ( $locale === null )
        {
            $locale = $this->defaultLocale;
        }

        // Check cache for already cached translation context
        if ( ( $translation = arbitCacheRegistry::getCache()->get( 'translation', $context ) ) !== false )
        {
            return new arbitCachingTranslationContext(
                new ezcTranslation( $translation ), $this->backend, $locale, $context
            );
        }

        try
        {
            $translation = $this->backend->getContext( $locale, $context );

            // Cache translation context, if it has been received successfully
            arbitCacheRegistry::getCache()->cache( 'translation', $context, $translation );

            return new arbitCachingTranslationContext(
                new ezcTranslation( $translation ), $this->backend, $locale, $context
            );
        }
        catch ( ezcTranslationContextNotAvailableException $e )
        {
            // Create yet missing empty context in translation file
            $this->backend->initWriter( $locale );
            $this->backend->storeContext( $context, array() );
            $this->backend->deinitWriter();

            // Retry fetching the context
            return new arbitCachingTranslationContext(
                parent::getContext( $locale, $context ),
                $this->backend, $locale, $context
            );
        }
    }
}

