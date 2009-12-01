<?php
/**
 * arbit core controller
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
 * Main controller for the arbit project tracker, implementing all user and
 * group related functionality.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitReceiptController extends arbitController
{
    /**
     * Index action
     *
     * Dispatches to the default action
     *
     * @param arbitRequest $request
     * @return arbitViewModel
     */
    public function index( arbitRequest $request )
    {
        return $this->overview( $request );
    }

    /**
     * Overview action
     *
     * Gives an overview on the currently available receipts
     *
     * @param arbitRequest $request
     * @return arbitViewModel
     */
    public function overview( arbitRequest $request )
    {
        return new arbitViewUserMessageModel( 'Hello Receipts!' );
    }

    /**
     * Overview action
     *
     * Gives an overview on the currently available receipts
     *
     * @param arbitRequest $request
     * @return arbitViewModel
     */
    public function add( arbitRequest $request )
    {
        $model = new arbitReceiptCreateModel();

        if ( arbitHttpTools::get( 'create' ) !== null )
        {
            try
            {
                $issue = new arbitReceiptModel();
                $issue->title      = arbitHttpTools::get( 'title' );
                $issue->issueType  = arbitHttpTools::get( 'type' );
                $issue->text       = arbitHttpTools::get( 'text' );
                $issue->state      = 'new';
                $issue->priority   = arbitHttpTools::get( 'priority' );
                $issue->resolution = 'none';
                $issue->versions   = arbitHttpTools::get( 'versions', arbitHttpTools::TYPE_ARRAY );
                $issue->components = arbitHttpTools::get( 'components', arbitHttpTools::TYPE_ARRAY );
                $issue->create();
                $issue->storeChanges();

                $model->success = array( new arbitViewUserMessageModel( 'Your issue has been successfully added.' ) );

                // Assigne issue to model to keep already validated data.
                $model->issue = new arbitTrackerIssueViewModel( $issue );

                // Update issue in search index
                $search = $this->getSearchSession( $request );
                $search->index( $issue );
                arbitCacheRegistry::getCache()->clearCache( 'tracker_reports' );
            }
            catch ( arbitException $e )
            {
                $model->errors = array( $e );
            }
        }

        return $model;
    }
}

