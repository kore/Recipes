<?php
/**
 * recipe storage backend gateway
 *
 * This file is part of recipe.
 *
 * recipe is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * recipe is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with recipe; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @subpackage Gateway
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

namespace Recipes\View;
use Recipes\DIC;

/**
 * Twig extension
 *
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class TwigExtension extends \Twig_Extension
{
    /**
     * DIC, to coupld this with the whole environment for pure insanity.
     *
     * @var DIC\Base
     */
    protected $dic;

    /**
     * Construct from DIC
     *
     * @param DIC\Base $dic
     * @return void
     */
    public function __construct( DIC\Base $dic )
    {
        $this->dic = $dic;
    }

    /**
     * Get extension name
     *
     * @return string
     */
    public function getName()
    {
        return __CLASS__;
    }

    /**
     * Load user model from user ID
     *
     * @param string $id
     * @return Model\User
     */
    public function user( $id )
    {
        return $this->dic->userModel->load( $id );
    }
}

