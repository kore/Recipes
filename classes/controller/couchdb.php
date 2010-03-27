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
 * @version $Revision: 1434 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * CouchDB backend management controller.
 *
 * @package Core
 * @version $Revision: 1434 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitAdminCouchdbController extends arbitController
{
    /**
     * Counter indicating the already used file names.
     *
     * @var int
     */
    static protected $file = 0;

    /**
     * Get file name for current request
     * 
     * @param arbitRequest $request 
     * @return string
     */
    protected function getFileName( arbitRequest $request )
    {
        if ( isset( $request->variables['file'] ) &&
             is_array( $request->variables['file'] ) &&
             isset( $request->variables['file'][self::$file] ) )
        {
            return $request->variables['file'][self::$file++];
        }

        return $filename = $request->controller . '.dump';
    }

    /**
     * Export backups
     *
     * Export backups for all listed projects.
     *
     * @param arbitRequest $request
     * @return arbitViewModel
     */
    public function exportBackup( arbitRequest $request )
    {
        $filename = $this->getFileName( $request );
        $dsn      = arbitBackendIniConfigurationManager::getMainConfiguration()->backendUrl;
        $tool     = new phpillowTool( $dsn . $request->controller );
        $tool->setOutputStreams( fopen( $filename, 'w' ) );
        $tool->dump();

        return new arbitViewUserMessageModel(
            "Backup written to file \"%file\".",
            array(
                'file' => $filename,
            )
        );
    }

    /**
     * Import backups
     *
     * Import backups for all listed projects.
     *
     * @param arbitRequest $request
     * @return arbitViewModel
     */
    public function importBackup( arbitRequest $request )
    {
        if ( $request->controller === 'core' )
        {
            return new arbitViewUserMessageModel(
                "No backup for core module possible."
            );
        }

        $filename = $this->getFileName( $request );
        $dsn      = arbitBackendIniConfigurationManager::getMainConfiguration()->backendUrl;
        $options  = array(
            'ignore-errors' => true,
            'input'         => $filename,
        );
        $tool     = new phpillowTool( $dsn . $request->controller, $options );
        $tool->setOutputStreams(
            $stdout = fopen( 'string://', 'w' ),
            $stderr = fopen( 'string://', 'w' )
        );
        $tool->load();

        // Log occured errors
        fseek( $stderr, 0 );
        $errors = explode( "\n", trim( stream_get_contents( $stderr ) ) );
        foreach ( $errors as $error )
        {
            ezcLog::getInstance()->log( $error, ezcLog::WARNING );
        }

        // Log occured infos
        fseek( $stdout, 0 );
        $msgs = explode( "\n", trim( stream_get_contents( $stdout ) ) );
        foreach ( $msgs as $msg )
        {
            ezcLog::getInstance()->log( $msg, ezcLog::INFO );
        }

        return new arbitViewUserMessageModel(
            "Imported file \"%file\" into database.",
            array(
                'file' => $filename,
            )
        );
    }

    /**
     * Prime all view caches
     *
     * Since view cache generation can take some time this action ensures all
     * view caches are request once. Especially useful after importing a
     * backup.
     * 
     * @param arbitRequest $request 
     * @return arbitViewModel
     */
    public function primeViews( arbitRequest $request )
    {
        if ( $request->controller === 'core' )
        {
            return new arbitViewUserMessageModel(
                "No backup for core module possible."
            );
        }

        $dsn  = arbitBackendIniConfigurationManager::getMainConfiguration()->backendUrl;
        $tool = new phpillowTool( $dsn . $request->controller );
        $tool->primeCaches();

        return new arbitViewUserMessageModel(
            "Primed caches for %project.",
            array(
                'project' => $request->controller,
            )
        );
    }
}

