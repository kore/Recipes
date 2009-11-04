<?php
/**
 * arbit mail MTA transport
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
 * Arbit mail MTA transport
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitMailMessengerMtaTransport extends arbitMailMessengerTransportBase
{
    /**
     * Option array
     *
     * @var array
     */
    protected $options = array(
        'from'    => 'no-reply@example.org',
        'subject' => '[Arbit] ',
    );

    /**
     * ezc MTA transport, which is used to actually deliver the mails.
     *
     * @var ezcMailMtaTransport
     */
    protected $transport;

    /**
     * Configure transport
     *
     * Configure transport with options given in the configuration file.
     *
     * @param array $options
     * @return void
     */
    public function configure( array $options )
    {
        foreach ( array_keys( $this->options ) as $option )
        {
            switch ( $option )
            {
                case 'from':
                case 'subject':
                    if ( isset( $options[$option] ) )
                    {
                        // Just accept from address. Will throw an exception if
                        // it is invalid later.
                        $this->$options[$option] = $options[$option];
                    }
                    break;

                default:
                    // Just stay silently with default value.
            }
        }

        // Instantiate transport, no options to set.
        $this->transport = new ezcMailMtaTransport();
    }

    /**
     * Deliver mail
     *
     * Just try to deliver mail, using the transport mechanims.
     *
     * @param ezcMailComposer $mail
     * @return void
     */
    public function deliver( ezcMailComposer $mail )
    {
        $mail->from = ezcMailTools::parseEmailAddress( $this->options['from'] );
        $mail->subject = $this->options['subject'] . $mail->subject;

        $this->transport->send( $mail );
    }
}

