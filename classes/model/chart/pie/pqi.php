<?php
/**
 * arbit PQI pie chart
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
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Pie chart class for the project quality index charts.
 *
 * @package Model
 * @subpackage Chart
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitPqiPieChart extends arbitPieChart
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

        // Disable legend
        $this->legend = false;

        // Set title
        $this->title = 'PQI*';
        $this->title->maxHeight = .15;
        $this->title->borderWidth = 0;
        $this->title->background = '#FFFFFFFF';
        $this->title->padding = 0;
        $this->title->margin = 0;

        // Set sum of pie this to 1
        $this->options->sum                         = 1;

        // Let pie start at 180°
        $this->renderer->options->pieChartOffset    = 180;

        // Disable labels and lable symbols
        $this->options->label                       = '';
        $this->renderer->options->showSymbol        = false;

        // Let pie this span the complete chart
        $this->renderer->options->pieHorizontalSize = .5;
        $this->renderer->options->pieVerticalSize   = .5;
    }

    /**
     * Get quality color
     *
     * Get a color value for the passed quality. The quality should be a value
     * between 0 and 1 indicating the project quality in percent.
     *
     * @param float $quality
     * @return mixed
     */
    protected function getQualityColor( $quality )
    {
        // Calculate a color value, which follows a linear curve, from (0,
        // #A40000) to (.5, #fce94f) to (1, #8ae234).
        return array(
            ( $quality < .5 ) ? .65 + $quality * .68 : .99 - ( $quality - .5 ) * .9,
            ( $quality < .5 ) ? $quality * 1.82 : .91 - ( $quality - .5 ) * .05,
            ( $quality < .5 ) ? $quality * .62 : .31 - ( $quality - .5 ) * .22,
            .3,
        );
    }

    /**
     * Set quality value
     *
     * Set the quality value for the chart and automatically assign the correct
     * color.
     *
     * @param string $propertyName
     * @param mixed $propertyValue
     * @return void
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'quality':
                $this->data['quality'] = new ezcGraphArrayDataSet( array( $propertyValue ) );
                $this->data['quality']->color[0] = $this->getQualityColor( $propertyValue );
                break;
            default:
                return parent::__set( $propertyName, $propertyValue );
        }
    }
}

