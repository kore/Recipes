<?php
/**
 * This file is part of recipes.
 *
 * recipes is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * recipes is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with recipes; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @version $Revision: 1469 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

namespace Recipes\Struct;
use Recipes\Struct;
use Recipes\Model;

/**
 * Overview struct class
 *
 * @version $Revision$
 */
class File extends Struct
{
    /**
     * Content
     *
     * @var string
     */
    public $content;

    /**
     * Mime type
     *
     * @var string
     */
    public $mimeType;

    /**
     * File name
     *
     * @var string
     */
    public $name;

    /**
     * Construct
     *
     * @param string $content
     * @param string $mimeType
     * @param string $name
     * @return void
     */
    public function __construct( $content = null, $mimeType = null, $name = null )
    {
        $this->content  = $content;
        $this->mimeType = $mimeType;
        $this->name     = $name;
    }
}

