<?php
/**
 * arbit basic chart palette
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
 * Basic arbit palette, defining colors and borders for all arbit charts.
 *
 * @package Model
 * @subpackage Chart
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitChartPalette extends ezcGraphPalette
{
    /**
     * Axiscolor
     *
     * @var ezcGraphColor
     */
    protected $axisColor = '#2E3436';

    /**
     * Color of grid lines
     *
     * @var ezcGraphColor
     */
    protected $majorGridColor = '#2E3436B0';

    /**
     * Color of minor grid lines
     *
     * @var ezcGraphColor
     */
    protected $minorGridColor = '#2E3436E0';

    /**
     * Array with colors for datasets
     *
     * @var array
     */
    protected $dataSetColor = array(
        '#3465A4',
        '#4E9A06',
        '#CC0000',
        '#EDD400',
        '#75505B',
        '#F57900',
        '#204A87',
        '#C17D11',
    );

    /**
     * Array with symbols for datasets
     *
     * @var array
     */
    protected $dataSetSymbol = array(
        ezcGraph::BULLET,
    );

    /**
     * Name of font to use
     *
     * @var string
     */
    protected $fontName = 'sans-serif';

    /**
     * Fontcolor
     *
     * @var ezcGraphColor
     */
    protected $fontColor = '#2E3436';

    /**
     * Backgroundcolor for chart
     *
     * @var ezcGraphColor
     */
    protected $chartBackground = '#FFFFFFFF';

    /**
     * Padding in elements
     *
     * @var integer
     */
    protected $padding = 1;

    /**
     * Margin of elements
     *
     * @var integer
     */
    protected $margin = 1;

    /**
     * Backgroundcolor for elements
     *
     * @var ezcGraphColor
     */
    protected $elementBackground = '#FFFFFFB0';

    /**
     * Bordercolor for elements
     *
     * @var ezcGraphColor
     */
    protected $elementBorderColor = '#FFFFFF';

    /**
     * Borderwidth for elements
     *
     * @var integer
     * @access protected
     */
    protected $elementBorderWidth = 1;
}

