<?php
/**
 * arbit base messenger class
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
 * Arbit messenger implementation
 *
 * Sends any structures, which implement arbitDecorateable through one of the
 * configured messaging systems.
 *
 * Common messaging systems are mail or jabber, but also XMLRpc requests or
 * REST request are possible.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
final class arbitMessenger
{
    /**
     * Default registered messenger implementations.
     *
     * @var array
     */
    protected static $transports = array(
        'email'   => 'arbitMailMessenger',
//        'jabber' => 'arbitJabberMessenger',
    );

    /**
     * Register messenger
     *
     * Register additional custom messaging systems, or overwrite an existing
     * one with a custom implementation.
     *
     * @param string $name
     * @param string $class
     * @return void
     */
    public static function registerMessenger( $name, $class )
    {
        self::$transports[$name] = $class;
    }

    /**
     * Send message
     *
     * Send a decorateable object using the given messenger to the given
     * message receiver. By default the mail transport is used.
     *
     * @param arbitDecorateable $content
     * @param mixed $to
     * @param string $transport
     * @return void
     */
    public static function send( arbitDecorateable $content, $to = null, $transport = 'email' )
    {
        $class = self::$transports[$transport];

        $transport = new $class();
        $transport->deliver( $content, $to );
    }
}

