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

namespace Recipes\DIC;
use Recipes\DIC;
use Recipes;

/**
 * Base DIC
 *
 * @version $Revision$
 */
class Base extends DIC
{
    /**
     * Array with names of objects, which are always shared inside of this DIC
     * instance.
     *
     * @var array(string)
     */
    protected $alwaysShared = array(
        'srcDir'            => true,
        'view'              => true,
        'twig'              => true,
        'couchdbConnection' => true,
        'userGateway'       => true,
        'userModel'         => true,
        'recipeGateway'     => true,
        'recipeModel'       => true,
        'controller'        => true,
    );

    /**
     * Initialize DIC values
     *
     * @return void
     */
    public function initialize()
    {
        $this->srcDir = function ( $dic )
        {
            return substr( __DIR__, 0, strpos( __DIR__, '/src/' ) + 4 );
        };

        $this->twig = function ( $dic )
        {
            $twig = new \Twig_Environment(
                new \Twig_Loader_Filesystem( $dic->srcDir . '/templates' ),
                array(
//                    'cache' => $dic->srcDir . '/cache'
                )
            );

            $twig->addFunction( 'max', new \Twig_Function_Function( 'max' ) );
            $twig->addFunction( 'floor', new \Twig_Function_Function( 'floor' ) );

            return $twig;
        };

        $this->view = function( $dic )
        {
            return new Recipes\View\Twig(
                $dic->twig,
                array(
                    '\\Exception'                 => 'error.twig',
                    '\\Recipes\\Struct\\Login'    => 'login.twig',
                    '\\Recipes\\Struct\\Overview' => 'overview.twig',
                    '\\Recipes\\Struct\\Tags'     => 'tags.twig',
                    '\\Recipes\\Struct\\Recipes'  => 'all.twig',
                    '\\Recipes\\Struct\\Recipe'   => 'view.twig',
                )
            );
        };

        $this->couchdbConnection = function( $dic )
        {
            \phpillowConnection::createInstance(
                'localhost',
                5984
            );
            \phpillowConnection::setDatabase( 'recipe_core' );
            return \phpillowConnection::getInstance();
        };

        $this->userGateway = function( $dic )
        {
            return new Recipes\Gateway\CouchDB\User(
                $dic->couchdbConnection,
                new Recipes\Gateway\CouchDB\User\View()
            );
        };

        $this->userModel = function( $dic )
        {
            return new Recipes\Model\User(
                $this->userGateway
            );
        };

        $this->recipeGateway = function( $dic )
        {
            return new Recipes\Gateway\CouchDB\Recipe(
                $dic->couchdbConnection,
                new Recipes\Gateway\CouchDB\Recipe\View()
            );
        };

        $this->recipeModel = function( $dic )
        {
            return new Recipes\Model\Recipe(
                $this->recipeGateway
            );
        };

        $this->controller = function ( $dic )
        {
            return new Recipes\Controller\Auth(
                $dic->userModel,
                new Recipes\Controller\Recipe(
                    $dic->recipeModel
                )
            );
        };
    }
}

