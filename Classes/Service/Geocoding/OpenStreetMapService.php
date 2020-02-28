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

/**
 * Class OpenStreetMapService
 * @package Tollwerk\TwGeo\Service\Geocoding
 */
class OpenStreetMapService extends AbstractGeocodingService
{
    /**
     * @var string
     */
    protected $baseUrl = 'https://nominatim.openstreetmap.org/search?format=json';

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
        $parameters = [
            'q' => $address,
            'addressdetails' => 1,
            'accept-language' => ($language = $this->getCurrentFrontendLanguage()) ? $language->getTwoLetterIsoCode() : 'en',
        ];

        $requestUri = $this->baseUrl.'&'.http_build_query($parameters);
        $result = CurlUtility::httpRequest($requestUri, $this->httpRequestHeader);
        $data = json_decode($result);
        if (is_array($data) && count($data)) {
            $positions = new PositionList();
            /** @var \stdClass $result */
            foreach ($data as $result) {
                $address = $result->address;
                $position = new Position($result->lat, $result->lon);
                $position->setServiceClass(self::class);
                $position->setCountryCode($address->country_code);
                $position->setCountryName($address->country);
                $position->setRegion($address->state);
                $position->setLocality($address->city ?? $address->town ?? $address->county ?? null);
                $position->setPostalCode($address->postcode);
                $position->setDisplayName($result->display_name);
                $positions->add($position);
            }
            return $positions;
        }
        return null;
    }
}
