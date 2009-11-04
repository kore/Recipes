<?php
/**
 * arbit messenger base class
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
 * Arbit messenger base class.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitMailMessenger extends arbitMessengerBase
{
    /**
     * Mapping of internal transport names as configuration shortcuts to their
     * class names.
     *
     * @var array
     */
    protected $transportNameMapping = array(
        'mta'  => 'arbitMailMessengerMtaTransport',
        'smtp' => 'arbitMailMessengerSmtpTransport',
    );

    /**
     * Used transport class instance
     *
     * @var arbitMailMessengerTransport
     */
    protected static $transport = null;

    /**
     * Mail messenger constructor
     *
     * Intilizes the transport, so it can be reused for later messages without
     * reaccessing the configuration.
     *
     * @return void
     */
    public function __construct()
    {
        $this->initializeTransport();
    }

    /**
     * Initilize transport
     *
     * @return void
     */
    protected function initializeTransport()
    {
        if ( self::$transport !== null )
        {
            // If transport already has been initlized, exit immediately.
            return;
        }

        // Read from configuration, which messenger should be used.
        $conf = arbitBackendIniConfigurationManager::getMainConfiguration();
        $transportClass = $conf->get( 'mail', 'transport' );

        // Check for registered name mapping of transport name shortcut
        if ( isset( $this->transportNameMapping[$transportClass] ) )
        {
            $transportClass = $this->transportNameMapping[$transportClass];
        }

        // Instantiate messenger and set options
        self::$transport = new $transportClass();
        self::$transport->configure( $conf->get( 'mail', 'options' ) );
    }

    /**
     * Deliver messager to receiver
     *
     * @param arbitDecorateable $content
     * @param mixed $to
     * @return void
     */
    public function deliver( arbitDecorateable $content, $to )
    {
        arbitViewModelDecorationDependencyInjectionManager::setViewHandler(
            $view = new arbitViewEmailHandler( arbitSession::getCurrentRequest() )
        );

        // Fixes mail sending for some MTA, which got the RFC wrong, see:
        // http://ezcomponents.org/docs/tutorials/Mail#mta-qmail
        ezcMailTools::setLineBreak( "\n" );

        // Use normal decoration framework to build mail.
        $mail = new ezcMailComposer();
        $mail->addTo( new ezcMailAddress( $to ) );
        $mail->plainText = arbitViewModelDecorationDependencyInjectionManager::decorate(
            $content
        );
        $subject = $view->getReturnValue( 'subject' );
        $mail->subject = ( $subject === null ) ? 'Arbit tracker notification' : $subject;
        $mail->build();

        // Deliver mail through configured transport
        try
        {
            self::$transport->deliver( $mail );
        }
        catch ( ezcMailTransportException $e )
        {
            // @TODO: Do something about this, like logging, storing for
            // resending, or similar.
        }
    }
}

