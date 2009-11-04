<?php
/**
 * arbit base controller
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
 * @version $Revision: 1272 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Core controller which will be used for all project independant actions.
 *
 * @package Core
 * @version $Revision: 1272 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitMainController extends arbitController
{
    /**
     * Default action
     *
     * The default action of the core controller always dispatches to the
     * dashboard action.
     *
     * @param arbitRequest $request
     * @return arbitViewModel
     */
    public function index( arbitRequest $request )
    {
        return $this->dashboard( $request );
    }

    /**
     * Initialize basic project data structure
     *
     * Initialize basic project data with its configured settings, from the
     * project name.
     *
     * @param string $project
     * @return arbitViewDashboardProjectModel
     */
    protected static function initializeProject( $project )
    {
        $conf = arbitBackendIniConfigurationManager::getProjectConfiguration( $project );
        $data = new arbitViewDashboardProjectModel(
            $project,
            $conf->name,
            $conf->description
        );

        return $data;
    }

    /**
     * Dashboard quality subaction
     *
     * Subaction to display an image of the project quality index on the
     * dashboard.
     *
     * @param arbitRequest $request
     * @return arbitViewDataModel
     */
    public function quality( arbitRequest $request )
    {
        if ( ( $model = arbitCacheRegistry::getCache()->get( 'dashboard', 'overview' ) ) === false )
        {
            // The dashboard cache should always exist, when requesting the
            // image - otherwise just fail.
            throw new arbitRuntimeException( 'Unexpectedly missing dashboard cache.' );
        }

        $project = substr( $request->path, 1 );
        if ( !isset( $model->projects[$project] ) )
        {
            // @TODO: This shold some different exception.
            throw new arbitRuntimeException( "No such project: $project." );
        }

        if ( ( $chartData = arbitCacheRegistry::getCache()->get( 'dashboard', 'chart/' . $project ) ) === false )
        {
            // Regenerate PQI chart
            $projectQuality = 0;
            foreach ( $model->projects[$project]->quality as $quality )
            {
                $projectQuality += $quality;
            }

            if ( $count = count( $model->projects[$project]->quality ) )
            {
                $projectQuality /= $count;
            }

            $chart = new arbitPqiPieChart();
            $chart->quality = $projectQuality;

            // Cache image
            arbitCacheRegistry::getCache()->cache(
                'dashboard',
                'chart/' . $project,
                $chartData = $chart->render( 80, 80 )
            );
        }

        return new arbitViewDataModel( $chartData, 'image/svg+xml' );
    }

    /**
     * Dashboard action
     *
     * The initial default view on arbit for each user. Provides a general
     * project overview with some optional details on the projects, if provided
     * by its modules.
     *
     * @param arbitRequest $request
     * @return arbitViewDashboardModel
     */
    public function dashboard( arbitRequest $request )
    {
        // If a dashboard subaction is requested, jsut forward
        if ( $request->subaction !== 'index' )
        {
            $subaction = $request->subaction;
            return $this->$subaction( $request );
        }

        if ( ( $model = arbitCacheRegistry::getCache()->get( 'dashboard', 'overview' ) ) === false )
        {
            // Dashboard cache does not exist or is outdated
            $model = new arbitViewDashboardContextModel();
        }

        // Include all projects in report
        $mainConf = arbitBackendIniConfigurationManager::getMainConfiguration();
        $modified = false;
        foreach ( $mainConf->projects as $project )
        {
            if ( !isset( $model->projects[$project] ) )
            {
                // Merge project to main model
                $model->projects = array_merge(
                    $model->projects,
                    array(
                        $project => self::initializeProject( $project ),
                    )
                );

                // Cache, because we modified the data
                $modified = true;
            }
        }

        // Cache updated dashboard model
        if ( $modified === true )
        {
            arbitCacheRegistry::getCache()->cache( 'dashboard', 'overview', $model );
        }

        // Set dashboard model language
        $model->language = $mainConf->language;

        return $model;
    }

    /**
     * Callback method for the dashboard info update slot
     *
     * @param arbitDashboardInfoStruct $data
     * @return void
     */
    public static function dashboardUpdate( arbitCoreDashboardInfoStruct $data )
    {
        if ( ( $model = arbitCacheRegistry::getCache( 'core' )->get( 'dashboard', 'overview' ) ) === false )
        {
            // Dashboard cache does not exist or is outdated
            $model = new arbitViewDashboardContextModel();
        }

        $projectData = $model->projects;

        // If no projectdata has been fetched yet initialize project struct
        if ( !isset( $projectData[$data->project] ) )
        {
            $projectData[$data->project] = self::initializeProject( $data->project );
        }

        // Set data received from the module
        $projectData[$data->project]->state = array_merge(
            $projectData[$data->project]->state,
            array( $data->module => $data->state )
        );
        $projectData[$data->project]->messages = array_merge(
            $projectData[$data->project]->messages,
            array( $data->module => $data->message )
        );
        $projectData[$data->project]->quality = array_merge(
            $projectData[$data->project]->quality,
            array( $data->module => $data->quality )
        );
        $model->projects = $projectData;

        // Cache updated dashboard model
        arbitCacheRegistry::getCache( 'core' )->cache( 'dashboard', 'overview', $model );

        // Remove PQI image from cache to regenerate it next time it is requested
        arbitCacheRegistry::getCache( 'core' )->purge( 'dashboard', 'chart/' . $data->project );
    }

    /**
     * Styles action
     *
     * If the styles directory is not handled by the webserver, either because
     * of misconfiguration, or because we are inside a PHAR archive, still
     * deliver the files.
     *
     * @param arbitRequest $request
     * @return arbitViewDashboardModel
     */
    public function styles( arbitRequest $request )
    {
        return $this->staticFile( $request );
    }

    /**
     * Images action
     *
     * If the images directory is not handled by the webserver, either because
     * of misconfiguration, or because we are inside a PHAR archive, still
     * deliver the files.
     *
     * @param arbitRequest $request
     * @return arbitViewDashboardModel
     */
    public function images( arbitRequest $request )
    {
        return $this->staticFile( $request );
    }

    /**
     * Static files action handler
     *
     * If the static files are not handled by the webserver, either because
     * of misconfiguration, or because we are inside a PHAR archive, still
     * deliver the files.
     *
     * @param arbitRequest $request
     * @return arbitViewDashboardModel
     */
    protected function staticFile( arbitRequest $request )
    {
        $file  = $request->action . '/' . $request->subaction;
        $file .= !empty( $request->path ) ? $request->path : '.' . $request->extension;

        if ( !is_file( $path = ARBIT_HTDOCS . $file ) )
        {
            throw new Exception( "File not found: $file." );
        }

        return new arbitViewDataModel(
            file_get_contents( $path ),
            arbitFrameworkMimeTypeGuesser::guess( $path )
        );
    }
}

