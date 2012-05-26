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

namespace Recipes;
use Qafoo\RMF;

require __DIR__ . '/../main/Recipes/bootstrap.php';
$dic = new DIC\Base();
$dic->environment = 'development';

$dispatcher = new RMF\Dispatcher\Simple(
    new RMF\Router\Regexp( array(
        '(^/user/login$)' => array(
            'GET'  => array( $dic->controller, 'login' ),
            'POST' => array( $dic->controller, 'login' ),
        ),
        '(^/user/logout$)' => array(
            'GET'  => array( $dic->controller, 'logout' ),
        ),
        '(^/(?:recipe/overview)?$)' => array(
            'GET'  => array( $dic->controller, 'showOverview' ),
        ),
        '(^/recipe/tag/(?P<tag>.*)$)' => array(
            'GET'  => array( $dic->controller, 'tag' ),
        ),
        '(^/recipe/tags$)' => array(
            'GET'  => array( $dic->controller, 'tags' ),
        ),
        '(^/recipe/recipe/(?P<recipe>.*)$)' => array(
            'GET'  => array( $dic->controller, 'view' ),
        ),
        '(^/recipe/edit(?:/(?P<recipe>.*))?$)' => array(
            'GET'  => array( $dic->controller, 'edit' ),
        ),
        '(^/recipe/delete/(?P<recipe>.*)$)' => array(
            'GET'  => array( $dic->controller, 'delete' ),
        ),
        '(^/recipe/listExports/(?P<recipe>.*)$)' => array(
            'GET'  => array( $dic->controller, 'listExports' ),
        ),
        '(^/recipe/export/(?P<recipe>.*)\.(?P<format>[a-z]+)$)' => array(
            'GET'  => array( $dic->controller, 'export' ),
        ),
        '(^/recipe/all$)' => array(
            'GET'  => array( $dic->controller, 'all' ),
        ),
        '(^/recipe/ingredient/(?P<ingredient>.*)$)' => array(
            'GET'  => array( $dic->controller, 'ingredient' ),
        ),
    ) ),
    $dic->view
);

$request = new RMF\Request\HTTP();
$request->addHandler( 'body', new RMF\Request\PropertyHandler\PostBody() );
$request->addHandler( 'session', new RMF\Request\PropertyHandler\Session() );

$dispatcher->dispatch( $request );

