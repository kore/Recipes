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
        '(^/(?:recipes?/overview)?$)' => array(
            'GET'  => array( $dic->controller, 'showOverview' ),
        ),
        '(^/recipes?/mine$)' => array(
            'GET'  => array( $dic->controller, 'mine' ),
        ),
        '(^/recipes?/tag/(?P<tag>.*)$)' => array(
            'GET'  => array( $dic->controller, 'tag' ),
        ),
        '(^/recipes?/tags$)' => array(
            'GET'  => array( $dic->controller, 'tags' ),
        ),
        '(^/recipes?/(?:recipe|view)/(?P<recipe>[^/]*)$)' => array(
            'GET'  => array( $dic->controller, 'view' ),
        ),
        '(^/recipes?/(?:recipe|view)/(?P<recipe>[^/]*)/(?P<file>.*)$)' => array(
            'GET'  => array( $dic->controller, 'attachment' ),
        ),
        '(^/recipes?/edit(?:/(?P<recipe>.*))?$)' => array(
            'GET'  => array( $dic->controller, 'edit' ),
            'POST' => array( $dic->controller, 'edit' ),
        ),
        '(^/recipes?/attach/(?P<recipe>.*)$)' => array(
            'POST' => array( $dic->controller, 'attach' ),
        ),
        '(^/recipes?/delete/(?P<recipe>.*)$)' => array(
            'GET'  => array( $dic->controller, 'delete' ),
        ),
        '(^/recipes?/ingredients/(?P<ingredient>.*)\.js$)' => array(
            'GET'  => array( $dic->controller, 'ingredients' ),
        ),
        '(^/recipes?/units/(?P<unit>.*)\.js$)' => array(
            'GET'  => array( $dic->controller, 'units' ),
        ),
        '(^/recipes?/listExports/(?P<recipe>.*)$)' => array(
            'GET'  => array( $dic->controller, 'listExports' ),
        ),
        '(^/recipes?/export/(?P<recipe>.*)\.(?P<format>[a-z]+)$)' => array(
            'GET'  => array( $dic->controller, 'export' ),
        ),
        '(^/recipes?/all$)' => array(
            'GET'  => array( $dic->controller, 'all' ),
        ),
        '(^/recipes?/ingredient/(?P<ingredient>.*)$)' => array(
            'GET'  => array( $dic->controller, 'ingredient' ),
        ),
    ) ),
    $dic->view
);

$request = new RMF\Request\HTTP();
$request->addHandler( 'body', new RMF\Request\PropertyHandler\PostBody() );
$request->addHandler( 'session', new RMF\Request\PropertyHandler\Session() );

$dispatcher->dispatch( $request );

