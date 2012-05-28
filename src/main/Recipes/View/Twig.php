<?php
/**
 * This file is part of recipes
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

namespace Recipes\View;
use Recipes\Struct;
use Qafoo\RMF\Request;

/**
 * Base MySQLi connection class
 *
 * @version $Revision$
 */
class Twig extends \Qafoo\RMF\View
{
    /**
     * Twig envoronment
     *
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * Array of templates per class
     *
     * @var array
     */
    protected $templates = array();

    /**
     * Construct from twig environment
     *
     * @param \Twig_Environment $twig
     * @return void
     */
    public function __construct( \Twig_Environment $twig, array $templates = array() )
    {
        $this->twig      = $twig;
        $this->templates = $templates;
    }

    /**
     * Get template for passed object
     *
     * @param mixed $object
     * @return void
     */
    protected function getTemplate( $object )
    {
        foreach ( $this->templates as $class => $template )
        {
            if ( $object instanceof $class )
            {
                return $template;
            }
        }

        throw new \OutOfBoundsException( "Can't find a template for class " . ( $object ? get_class( $object ) : 'null' ) );
    }

    /**
     * Display the controller result
     *
     * @param Request $request
     * @param mixed $result
     * @return void
     */
    public function display( Request $request, $result )
    {
        // @TODO: This should not be here…
        if ( $result instanceof Struct\File )
        {
            header( 'Content-Type: ' . $result->mimeType );
            header( 'Content-Disposition: inline; filename="' . $result->name . '"' );

            echo $result->content;
            exit( 0 );
        }

        // @TODO: This should not be here…
        if ( $result instanceof Struct\Listing )
        {
            header( 'Content-Type: application/json' );
            echo json_encode( $result->listing );
            exit( 0 );
        }

        echo $this->twig->render(
            $this->getTemplate( $result ),
            array(
                'request' => $request,
                'result'  => $result,
            )
        );
    }
}

