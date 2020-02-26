<?php
/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2019 Klaus Fiedler <klaus@tollwerk.de>, tollwerk® GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace Tollwerk\TwGeo\Service\Geocoding;


use Tollwerk\TwGeo\Domain\Model\Position;
use Tollwerk\TwGeo\Domain\Model\PositionList;
use Tollwerk\TwGeo\Utility\CurlUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

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
            'accept-language' => $GLOBALS['TYPO3_REQUEST']->getAttribute('language') ? $GLOBALS['TYPO3_REQUEST']->getAttribute('language')->getTwoLetterIsoCode() : 'en',
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
