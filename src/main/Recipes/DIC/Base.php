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
        'imageConverter'    => true,
        'cachePath'         => true,
        'cache'             => true,
        'searchPath'        => true,
        'search'            => true,
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

        $this->twigExtension = function ( $dic )
        {
            return new Recipes\View\TwigExtension( $dic );
        };

        $this->twig = function ( $dic )
        {
            $twig = new \Twig_Environment(
                new \Twig_Loader_Filesystem( $dic->srcDir . '/templates' ),
                array(
//                    'cache' => $dic->srcDir . '/cache'
                )
            );

            $twig->addExtension( $dic->twigExtension );

            $twig->addFunction( 'max', new \Twig_Function_Function( 'max' ) );
            $twig->addFunction( 'floor', new \Twig_Function_Function( 'floor' ) );
            $twig->addFunction( 'user', new \Twig_Function_Method( $dic->twigExtension, 'user' ) );

            return $twig;
        };

        $this->view = function( $dic )
        {
            return new Recipes\View\Twig(
                $dic->twig,
                array(
                    '\\Exception'                   => 'error.twig',
                    '\\Recipes\\Struct\\Login'      => 'login.twig',
                    '\\Recipes\\Struct\\Message'    => 'message.twig',
                    '\\Recipes\\Struct\\Overview'   => 'overview.twig',
                    '\\Recipes\\Struct\\Mine'       => 'mine.twig',
                    '\\Recipes\\Struct\\Tag'        => 'tag.twig',
                    '\\Recipes\\Struct\\Tags'       => 'tags.twig',
                    '\\Recipes\\Struct\\Recipes'    => 'all.twig',
                    '\\Recipes\\Struct\\Recipe'     => 'view.twig',
                    '\\Recipes\\Struct\\Edit'       => 'edit.twig',
                    '\\Recipes\\Struct\\Export'     => 'export.twig',
                    '\\Recipes\\Struct\\Ingredient' => 'ingredient.twig',
                    '\\Recipes\\Struct\\Search'     => 'search.twig',
                )
            );
        };

        $this->configuration = function( $dic )
        {
            $defaults = array(
                'couchdb.host' => 'localhost',
                'couchdb.port' => 5984,
                'couchdb.user' => null,
                'couchdb.pass' => null,
                'couchdb.name' => 'recipe_core',
            );

            $configuration = $defaults;
            $configurationFiles = array(
                __DIR__ . '/../../../../build.properties',
                __DIR__ . '/../../../../build.properties.local',
            );
            foreach ( $configurationFiles as $file )
            {
                if ( file_exists( $file ) )
                {
                    self::$configuration = array_merge( $configuration, parse_ini_file( $file ) );
                }
            }

            return $configuration;
        };

        $this->couchdbConnection = function( $dic )
        {
            \phpillowConnection::createInstance(
                $dic->configuration['couchdb.host'],
                $dic->configuration['couchdb.port'],
                $dic->configuration['couchdb.user'],
                $dic->configuration['couchdb.pass']
            );
            \phpillowConnection::setDatabase( $dic->configuration['couchdb.name'] );
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
                $dic->userGateway
            );
        };

        $this->recipeGateway = function( $dic )
        {
            return new Recipes\Gateway\Cached\Recipe(
                $dic->cache,
                new Recipes\Gateway\CouchDB\Recipe(
                    $dic->couchdbConnection,
                    new Recipes\Gateway\CouchDB\Recipe\View()
                )
            );
        };

        $this->recipeModel = function( $dic )
        {
            return new Recipes\Model\Recipe(
                $dic->recipeGateway
            );
        };

        $this->imageConverter = function( $dic )
        {
            $converter = new \ezcImageConverter(
                new \ezcImageConverterSettings(
                    array(
                        new \ezcImageHandlerSettings(  'GD',          'ezcImageGdHandler' ),
                        new \ezcImageHandlerSettings(  'ImageMagick', 'ezcImageImagemagickHandler' ),
                    )
                )
            );

            $filter = array(
                new \ezcImageFilter(
                    'scale',
                    array(
                        'width'     => 270,
                        'height'    => 180,
                        'direction' => \ezcImageGeometryFilters::SCALE_DOWN,
                    )
                )
            );

            $converter->createTransformation( 'thumbnail', $filter, array( 'image/jpeg' ) );

            return $converter;
        };

        $this->cachePath = function ( $dic )
        {
            return $dic->srcDir . '/var/cache/';
        };

        $this->cache = function ( $dic )
        {
            $cache = new Recipes\Cache\Filesystem(
                $dic->cachePath
            );
            $cache->addCache( 'recipes', 86400 * 30 );

            return $cache;
        };

        $this->searchPath = function ( $dic )
        {
            return $dic->srcDir . '/var/search/';
        };

        $this->search = function ( $dic )
        {
            return new Recipes\Search\Hack(
                $dic->searchPath
            );
        };

        $this->controller = function ( $dic )
        {
            return new Recipes\Controller\Auth(
                $dic->userModel,
                new Recipes\Controller\Recipe(
                    $dic->recipeModel,
                    $dic->userModel,
                    $dic->twig,
                    $dic->imageConverter,
                    $dic->search
                ),
                array(
                    '(^/recipes?/export)',
                )
            );
        };
    }
}

