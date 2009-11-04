<?php
/**
 * arbit admin controller
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
 * @version $Revision: 1263 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Main controller for the arbit project tracker, implementing all user and
 * group related functionality.
 *
 * @package Core
 * @version $Revision: 1263 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitAdminController extends arbitController
{
    /**
     * Clear cache
     *
     * Clear caches, as specified by the request parameters.
     *
     * @param arbitRequest $request
     * @return arbitViewModuleModel
     */
    public function clearCache( arbitRequest $request )
    {
        $cache = arbitCacheRegistry::getCache( $request->controller );

        $cacheTypes = $request->variables['types'] === false ?
            $cache->getCacheList() :
            array_intersect( $cache->getCacheList(), $request->variables['types'] );

        foreach ( $cacheTypes as $type )
        {
            ezcLog::getInstance()->log( "Clearing cache '$type'.", ezcLog::INFO );
            $cache->clearCache( $type );
        }

        return new arbitViewCoreCacheListModel(
            $request->controller,
            $cacheTypes
        );
    }

    /**
     * List available cache types
     *
     * Return a list of available cache types
     *
     * @param arbitRequest $request
     * @return arbitViewModuleModel
     */
    public function listCaches( arbitRequest $request )
    {
        $cache = arbitCacheRegistry::getCache( $request->controller );

        return new arbitViewCoreCacheListModel(
            $request->controller,
            $cache->getCacheList()
        );
    }

    /**
     * Verify integrity of selected database backend
     *
     * Call the integrity checking method in the selcted storage backend for
     * database maintanance.
     *
     * @param arbitRequest $request
     * @return arbitViewModuleModel
     */
    public function verifyBackend( arbitRequest $request )
    {
        // Select project
        arbitFacadeManager::selectProject( $request->controller );
        arbitFacadeManager::getSetupManager()->verifyIntegrity();

        // Ensure users group contains all users.
        $group = new arbitModelGroup( 'group-users' );
        $group->users = arbitModelUser::fetchAll();
        $group->storeChanges();

        return new arbitViewCoreDummyModel(
            $request->controller,
            array()
        );
    }

    /**
     * Validate the given user
     *
     * Validate the given user account, so he / she can login aftewards.
     *
     * @param arbitRequest $request
     * @return arbitViewModuleModel
     */
    public function validateUser( arbitRequest $request )
    {
        if ( $request->controller === 'core' )
        {
            return new arbitViewUserMessageModel(
                "No user validation for core module possible."
            );
        }

        if ( !$request->variables['user'] )
        {
            return new arbitViewUserMessageModel(
                "No user configured to validate.", array()
            );
        }

        $user = new arbitModelUser( $request->variables['user'] );
        $user->valid = '1';
        $user->storeChanges();

        return new arbitViewUserMessageModel(
            "Validated user account %user in project %project - you can login now.",
            array(
                'user'    => $request->variables['user'],
                'project' => $request->controller,
            )
        );
    }
}

