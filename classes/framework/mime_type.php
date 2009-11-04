<?php
/**
 * arbit file mime type detection
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
 * This class provides the basics for file mime type guessing and validation.
 *
 * The mime types are basically guessed on the base of the file extension,
 * which is rather inaccurate, because it is purely user provided value.
 *
 * For certain file types we offer callbacks, which may perform an additional
 * validation step to ensure the file is of the guessed type, like for images,
 * where we may use the PHP function getimagesize(), or to ensure proper
 * contents in text files.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitFrameworkMimeTypeGuesser
{
    /**
     * Mapping of file extensions to mime types.
     *
     * A list may be found at, and can be extended further from:
     * http://www.w3schools.com/media/media_mimeref.asp
     *
     * @var array
     */
    protected static $extensionMapping = array(
        // Application specific extensions
        '.pdf'     => 'application/pdf',
        '.sig'     => 'application/pgp-signature',
        '.ps'      => 'application/postscript',
        '.rtf'     => 'application/rtf',
        '.swf'     => 'application/x-shockwave-flash',

        // Archives
        '.gz'      => 'application/x-gzip',
        '.tar.gz'  => 'application/x-tgz',
        '.tgz'     => 'application/x-tgz',
        '.tar'     => 'application/x-tar',
        '.zip'     => 'application/zip',
        '.bz2'     => 'application/x-bzip',
        '.tbz'     => 'application/x-bzip-compressed-tar',
        '.tar.bz2' => 'application/x-bzip-compressed-tar',

        // Audio formats
        '.mp3'     => 'audio/mpeg',
        '.m3u'     => 'audio/x-mpegurl',
        '.wma'     => 'audio/x-ms-wma',
        '.wax'     => 'audio/x-ms-wax',
        '.ogg'     => 'application/ogg',
        '.wav'     => 'audio/x-wav',

        // Video formats
        '.mpeg'    => 'video/mpeg',
        '.mpg'     => 'video/mpeg',
        '.mov'     => 'video/quicktime',
        '.qt'      => 'video/quicktime',
        '.avi'     => 'video/x-msvideo',
        '.asf'     => 'video/x-ms-asf',
        '.asx'     => 'video/x-ms-asf',
        '.wmv'     => 'video/x-ms-wmv',

        // Image formats
        '.gif'     => 'image/gif',
        '.jpg'     => 'image/jpeg',
        '.jpeg'    => 'image/jpeg',
        '.png'     => 'image/png',
        '.xbm'     => 'image/x-xbitmap',
        '.xpm'     => 'image/x-xpixmap',
        '.xwd'     => 'image/x-xwindowdump',
        '.bmp'     => 'image/bmp',
        '.tif'     => 'image/tiff',
        '.tiff'    => 'image/tiff',
        '.ico'     => 'image/x-icon',
        '.svgz'    => 'image/svg+xml',
        '.svg'     => 'image/svg+xml',

        // Plain text and code
        '.css'     => 'text/css',
        '.html'    => 'text/html',
        '.htm'     => 'text/html',
        '.js'      => 'text/javascript',
        '.asc'     => 'text/plain',
        '.c'       => 'text/plain',
        '.h'       => 'text/plain',
        '.cc'      => 'text/plain',
        '.cpp'     => 'text/plain',
        '.hh'      => 'text/plain',
        '.hpp'     => 'text/plain',
        '.conf'    => 'text/plain',
        '.log'     => 'text/plain',
        '.text'    => 'text/plain',
        '.txt'     => 'text/plain',
        '.diff'    => 'text/plain',
        '.patch'   => 'text/plain',
        '.php'     => 'text/plain',
        '.ini'     => 'text/plain',
        '.dtd'     => 'text/xml',
        '.xml'     => 'text/xml',
    );

    /**
     * Additional validation mechanisms for mime types matching the given
     * regular expressions.
     *
     * The key of this array is a PCRE regular expression, which defines the
     * mime types for which the given callback should be called to verify the
     * guessed mime type. The callback method may modifiy the mimetype, in case
     * of a validation error this should be modified to
     * 'application/octet-stream'.
     *
     * You may add general validation mechanisms here, which may add a general
     * intrusion scan or use some common mime type detection mechanism.
     *
     * @var array
     */
    protected static $validationCallbackMap = array(
        '(^image/(?!svg))' => 'checkImageMimeType',
        '(^text/)'         => 'checkText',
        // @TODO: Maybe add some trivial XML/HTML validity check?
    );

    /**
     * Guess mime type
     *
     * Guess the mime type of the given file, basically based on the file
     * extension and optional additional validation mechanisms. The returned
     * mime type may be completely wrong, and just is a fair guess. Do never
     * really rely on user provided data.
     *
     * If the user also provided a mime type, it is checked if the user
     * provided information is consitent with the guessed type. Otherwise we
     * fall back to the default mimetype value: 'application/octet-stream'.
     *
     * @param string $file
     * @param string $userMimeType
     * @return string
     */
    public static function guess( $file, $userMimeType = false )
    {
        // Try to detect an extension from the given file name
        if ( !preg_match( '((?:\\.[a-zA-Z0-9]+)+$)', $file, $match ) )
        {
            // The file does not have any extension.
            return 'application/octet-stream';
        }

        $extension = strtolower( $match[0] );
        if ( !isset( self::$extensionMapping[$extension] ) )
        {
            // We do not know about this extension yet.
            return 'application/octet-stream';
        }

        // Use mime type given by file extension as a first guess.
        $mimeType = self::$extensionMapping[$extension];

        // Check if this matches the user provided miem type
        if ( ( $userMimeType !== false ) &&
             ( trim( strtolower( $userMimeType ) ) !== $mimeType ) )
        {
            // The user provided a mime type, which is different from the
            // guessed mime type.
            return 'application/octet-stream';
        }

        // Check provided callbacks for additional file validations
        foreach ( self::$validationCallbackMap as $expression => $callback )
        {
            if ( preg_match( $expression, $mimeType ) )
            {
                $mimeType = self::$callback( $file, $mimeType );
            }
        }

        // Return guessed and maybe verified mime type.
        return $mimeType;
    }

    /**
     * Check and validate guessed image mime type
     *
     * Tries to verify the guessed mime typ of image files using PHPs function
     * getimagesize. If the mimetypes do not match, or active contents are
     * detected the method will always fall back to 'application/octet-stream'.
     *
     * @param string $file
     * @param string $mimeType
     * @return string
     */
    protected static function checkImageMimeType( $file, $mimeType )
    {
        $info = getimagesize( $file );
        if ( $info['mime'] !== $mimeType )
        {
            // Fall back, if guessed mimetype does not match the mime type
            // detected by the image function handler.
            return 'application/octet-stream';
        }

        // Everything seems OK
        return $mimeType;
    }

    /**
     * Check text contents.
     *
     * There are some non-printable characters we do not want in text files. If
     * we detect them in a text file, we fall back to
     * 'application/octet-stream'.
     *
     * @param string $file
     * @param string $mimeType
     * @return string
     */
    protected static function checkText( $file, $mimeType )
    {
        if ( preg_match( '([\x00-\x08\x0e-\x1f])', file_get_contents( $file ) ) )
        {
            // We found some of the unwanted charactes in the given file
            return 'application/octet-stream';
        }

        // Everything seems OK
        return $mimeType;
    }
}

