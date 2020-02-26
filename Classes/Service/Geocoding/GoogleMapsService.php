<?php
/**
 * TwGeo
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwGeo
 * @subpackage Tollwerk\TwGeo\Service\Geocoding
 * @author     Klaus Fiedler <klaus@tollwerk.de> / @jkphl
 * @copyright  Copyright © 2019 Klaus Fiedler <klaus@tollwerk.de>
 * @license    http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2019 Klaus Fiedler <klaus@tollwerk.de>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy of
 *  this software and associated documentation files (the "Software"), to deal in
 *  the Software without restriction, including without limitation the rights to
 *  use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 *  the Software, and to permit persons to whom the Software is furnished to do so,
 *  subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 *  FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 *  COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 *  IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 *  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 ***********************************************************************************/

namespace Tollwerk\TwGeo\Service\Geocoding;

use Tollwerk\TwGeo\Domain\Model\Position;
use Tollwerk\TwGeo\Domain\Model\PositionList;
use Tollwerk\TwGeo\Utility\CurlUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class GoogleMapsService extends AbstractGeocodingService
{
    const STATUS_OK = 'OK';

    /**
     * @var string
     */
    protected $baseUrl = 'https://maps.googleapis.com/maps/api/geocode/json?';

    /**
     * @var array
     */
    protected $httpRequestHeader = [
        'User-Agent: tollwerk/TYPO3-ext-tw_geo',
    ];

    /**
     * Get geocoding result for address string
     *
     * @param string|null $address
     *
     * @return null|Position
     */
    public function geocode(string $address = null): ?PositionList
    {
        // Check if all necessary typoscript settings are set and return null if not
        $typoscript = GeneralUtility::makeInstance(ObjectManager::class)->get(ConfigurationManager::class)->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $googleMapsApiKey = $typoscript['plugin.']['tx_twgeo.']['settings.']['googleMaps.']['apiKey'] ? : null;

        if (!$googleMapsApiKey) {
            return null;
        }  

        // Call web API
        $parameters = [
            'address' => $address,
            'key' => $googleMapsApiKey,
            'language' => $GLOBALS['TSFE']->sys_language_isocode
        ];
        $requestUri = $this->baseUrl.'&'.http_build_query($parameters);
        $result = CurlUtility::httpRequest($requestUri, $this->httpRequestHeader);
        $data = json_decode($result);

        // Return results
        if ($data->status == self::STATUS_OK && count($data->results)) {
            $positions = new PositionList();
            /** @var \stdClass $result */
            foreach($data->results as $result){
                $position = new Position($result->geometry->location->lat, $result->geometry->location->lng);
                $position->setServiceClass(self::class);
                $position->setDisplayName($result->formatted_address);
                foreach ($result->address_components as $addressComponent) {
                    $addressComponentType = $addressComponent->types[0];
                    $addressComponentValue = $addressComponent->long_name;

                    switch ($addressComponentType) {
                        case 'street_number':
                            $position->setStreetNumber($addressComponentValue);
                            break;
                        case 'route':
                            $position->setStreet($addressComponentValue);
                            break;
                        case 'locality':
                            $position->setLocality($addressComponentValue);
                            break;
                        case 'administrative_area_level_1':
                            $position->setRegion($addressComponentValue);
                            break;
                        case 'country':
                            $position->setCountryName($addressComponentValue);
                            $position->setCountryCode($addressComponent->short_name);
                            break;
                        case 'postal_code':
                            $position->setPostalCode($addressComponentValue);
                            break;
                    }
                }
                $positions->append($position);
            }
            return $positions;
        }
        return null;
    }
}
