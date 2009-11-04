<?php
/**
 * arbit pie base chart
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
 * @version $Revision: 1457 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Base pie chart for arbit, defining custom rendering options, etc.
 *
 * @package Model
 * @subpackage Chart
 * @version $Revision: 1457 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitPieChart extends ezcGraphPieChart
{
    /**
     * Construct pie chart
     *
     * Construct pie chart and assign common setting to fit it to arbit layout
     * settings
     *
     * @return void
     */
    public function __construct()
    {
        // Parent constructor to initialise the chart
        parent::__construct();

        // Use specific arbit palette
        $this->palette = new arbitChartPalette();

        // Use 3D renderer by default, with some nicer options
        $this->renderer = new ezcGraphRenderer3d();

        // Add pie chart gleam
        $this->renderer->options->pieChartGleam          = .3;
        $this->renderer->options->pieChartGleamColor     = '#FFFFFF';
        $this->renderer->options->dataBorder             = 0;

        // Add shadow to pie chart bottom
        $this->renderer->options->pieChartShadowSize     = 3;
        $this->renderer->options->pieChartShadowColor    = '#000000';

        // More beautiful formatting for legend
        $this->renderer->options->legendSymbolGleam      = .3;
        $this->renderer->options->legendSymbolGleamSize  = .9;
        $this->renderer->options->legendSymbolGleamColor = '#FFFFFF';

        // Use semit transparent color for symbols
        $this->renderer->options->pieChartSymbolColor    = '#55575388';

        // Use different 3D simaltion effect settings
        $this->renderer->options->pieChartHeight         = 5;
        $this->renderer->options->pieChartRotation       = .8;

        // @TODO: Check for best possible output driver depending on the
        // installation environment and user settings.
        $this->options->font = __DIR__ . '/font.svg';
    }

    /**
     * Get chart image mime type
     *
     * Get chart image mime type
     *
     * @return string
     */
    public function getMimeType()
    {
        return 'image/svg+xml';
    }

    /**
     * Renders this chart
     *
     * Creates basic visual chart elements from the chart to be processed by
     * the renderer. If null is passed instead of a file name, the rendered
     * image will be returned as a string.
     *
     * @param int $width
     * @param int $height
     * @param mixed $file
     * @return mixed
     */
    public function render( $width, $height, $file = null )
    {
        if ( $file !== null )
        {
            return parent::render( $width, $height, $file );
        }
        else
        {
            // If no file name has been given, render to a temporary file, to
            // just return the render result later as a string.;
            $file = tempnam( ARBIT_CACHE_PATH, 'chart_' );
            parent::render( $width, $height, $file );
            $contents = file_get_contents( $file );
            unlink( $file );
            return $contents;
        }
    }
}

