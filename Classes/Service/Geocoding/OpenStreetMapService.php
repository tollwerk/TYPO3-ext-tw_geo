<?php

/**
 * Tollwerk Geo Tools
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwGeo
 * @subpackage Tollwerk\TwGeo\Service\Geocoding
 * @author     Klaus Fiedler <klaus@tollwerk.de> / @jkphl
 * @copyright  Copyright © 2020 Klaus Fiedler <klaus@tollwerk.de>
 * @license    http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2020 Klaus Fiedler <klaus@tollwerk.de>
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
 * OpenStreetMap Service
 *
 * @package Tollwerk\TwGeo\Service\Geocoding
 */
class OpenStreetMapService extends AbstractGeocodingService
{
    /**
     * Nominatim Base URL for geocoding
     *
     * @var string
     */
    protected $geocodeUrl = 'https://nominatim.openstreetmap.org/search?format=json';
    /**
     * Nominatim Base URL for reverse geocoding
     *
     * @var string
     */
    protected $reverseGeocodeUrl = 'https://nominatim.openstreetmap.org/reverse?format=json';

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
            'q'               => $address,
            'addressdetails'  => 1,
            'accept-language' => ($language = $this->getCurrentFrontendLanguage()) ? $language->getTwoLetterIsoCode() : 'en',
        ];

        $requestUri = $this->geocodeUrl.'&'.http_build_query($parameters);
        $result     = CurlUtility::httpRequest($requestUri, $this->httpRequestHeader);
        $data       = json_decode($result);
        if (is_array($data) && count($data)) {
            $positions = new PositionList();
            /** @var \stdClass $result */
            foreach ($data as $result) {
                $address  = $result->address;
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

    /**
     * Reverse geocode a set of coordinates
     *
     * @param float $latitude             Latitude
     * @param float $longitude            Longitude
     * @param int $zoom                   Zoom level
     * @param array|string|null $language Language
     *
     * @return Position|null Position
     */
    public function reverseGeocode(
        float $latitude,
        float $longitude,
        int $zoom = 16,
        $language = null
    ): ?Position {
        $languages  = empty($languages) ? $this->detectLanguage($latitude, $longitude) : (array)$language;
        $parameters = [
            'lat'  => $latitude,
            'lon'  => $longitude,
            'zoom' => max(3, min(18, $zoom))
        ];
        if (!empty($languages)) {
            $parameters['accept-language'] = implode(',', $languages);
        }
        $requestUri = $this->reverseGeocodeUrl.'&'.http_build_query($parameters);
        $status     = null;
        $result     = CurlUtility::httpRequest(
            $requestUri,
            $this->httpRequestHeader,
            CurlUtility::GET,
            null,
            false,
            $status
        );
        // Stop on error
        if (($status != 200) && !strlen($result)) {
            return null;
        }

        $data = @json_decode($result);
        if (is_object($data)) {
            $position = new Position($data->lat ?? $latitude, $data->lon ?? $longitude);
            $position->setServiceClass(self::class);
            $position->setCountryCode($data->address->country_code ?? null);
            $position->setCountryName($data->address->country ?? null);
            $position->setRegion($data->address->state ?? null);
            $position->setLocality($data->address->city ?? $data->address->town ?? $data->address->county ?? null);
            $position->setPostalCode($data->address->postcode ?? null);
            $position->setStreet($data->address->road ?? null);
            $position->setDisplayName($data->display_name);

            return $position;
        }

        return null;
    }

    /**
     * Detect the language by running a low-level reverse geocoding
     *
     * @param float $latitude  Latitude
     * @param float $longitude Longitude
     *
     * @return array|null 2-letter language codes
     */
    protected function detectLanguage(float $latitude, float $longitude): ?array
    {
        $parameters = [
            'lat'  => $latitude,
            'lon'  => $longitude,
            'zoom' => 3
        ];
        $requestUri = $this->reverseGeocodeUrl.'&'.http_build_query($parameters);
        $status     = null;
        $result     = CurlUtility::httpRequest(
            $requestUri,
            $this->httpRequestHeader,
            CurlUtility::GET,
            null,
            false,
            $status
        );

        // Stop on error
        if (($status != 200) && !strlen($result)) {
            return null;
        }

        $data             = @json_decode($result);
        $countryIsoCodeA2 = strtoupper($data->address->country_code) ?? null;

        return empty($countryIsoCodeA2) ? null : (static::COUNTRY_TO_LANGUAGES[$countryIsoCodeA2] ?? null);
    }
}
