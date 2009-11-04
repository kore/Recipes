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
 * Filter class for OpenID user authentication mechanism
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitCoreModuleUserOpenIDAuthentificationFilter extends ezcAuthenticationOpenidFilter
{
    /**
     * Holds the mandatory attributes which will be requested during the
     * authentication process.
     *
     * The array should look like this:
     * array( 'fullname', 'gender', 'country', 'language' );
     *
     * @var array(string)
     */
    protected $requestedMandatoryData = array();
    /**
     * Registers which mandatory extra data to fetch during the auth process.
     *
     * If you want to know, which data can be fetched, see ezcAuthenticationOpenidFilter.
     *
     * @param array(string) $data
     */
    public function registerMandatoryFetchData( array $data = array() )
    {
        $this->requestedMandatoryData = $data;
    }
    /**
     * Returns an array of parameters for use in an OpenID check_id request.
     *
     * This method calls the ezcAuthenticationOpenidFilter::createCheckidRequest
     * and adds fields that are required for mandatory data to the returning array.
     *
     * @param string $id The OpenID identifier from the user
     * @param array(string) $providers OpenID providers retrieved during discovery
     * @return array(string=>array)
     */
    public function createCheckidRequest( $id, array $providers )
    {
        $params = parent::createCheckidRequest( $id, $providers );
        if ( count( $this->requestedMandatoryData ) > 0 )
        {
            $params['openid.sreg.required'] = implode( ',', $this->requestedMandatoryData );
            $params['openid.ns.sreg'] = urlencode( 'http://openid.net/sreg/1.0' );
        }
        return $params;
    }
}

