<?php
/**
 * arbit view handler
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
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Text view handler generating emails from the provided view model.
 *
 * @package Core
 * @subpackage View
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitViewTextHandler extends arbitTemplateViewHandler
{
    /**
     * Method template association for default calls.
     *
     * In most cases we just assign some template to a view more, for which a
     * method on this class is called. The normal plain procedure may just be
     * mapped using this array and is handled inside the __call method of this
     * class.
     *
     * @var array
     */
    protected $templates = array(
        // View models
        'showCoreCacheListModel' => 'text/admin/caches.tpl',
        'showCoreDummyModel'     => 'text/admin/dummy.tpl',
        'showCliViewModelList'   => 'text/list.tpl',
        'showModuleModel'        => 'text/module.tpl',
        'showMessageModel'       => 'text/message.tpl',
    );

    /**
     * Template extensions used by the view handler.
     *
     * @var array
     */
    protected $extensions = array(
        'arbitViewTemplateFunctions',
        'arbitViewTextTemplateFunctions',
    );
}


