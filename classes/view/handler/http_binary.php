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
 * Handler to display binary data over HTTP.
 *
 * @package Core
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitViewHttpBinaryHandler extends arbitViewHandler
{
    /**
     * Show binary data
     *
     * Send the proper HTTP content type header for the passed binary data and
     * display it.
     *
     * @param arbitViewDataModel $model
     * @return string
     */
    public static function showBinaryData( arbitViewDataModel $model )
    {
        arbitHttpTools::header( 'Content-Type: ' . $model->mimetype );
        return $model->content;
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
        // Locales are irrelevant for binaries
    }

    /**
     * Display controller result
     *
     * Select the view, which should be used to display the controller results,
     * process and echo the view results.
     *
     * @param arbitDecorateable $model
     * @return void
     */
    public function display( arbitDecorateable $model )
    {
        return self::showBinaryData( $model );
    }
}

