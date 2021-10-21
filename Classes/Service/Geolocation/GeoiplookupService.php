<?php

/***************************************************************
 *
 *  Copyright notice
 *
 *  © 2020 Klaus Fiedler <klaus@tollwerk.de>, tollwerk® GmbH
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

namespace Tollwerk\TwGeo\Service\Geolocation;

use Tollwerk\TwGeo\Domain\Model\Position;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GeoiplookupService extends AbstractGeolocationService
{
    public function init()
    {
        $command = 'geoiplookup 127.0.0.1';
        $output  = [];
        $status  = null;
        exec($command, $output, $status);

        return $status == 0;
    }

    public function getGeolocation(string $ip = null): ?Position
    {
        $command = 'geoiplookup ' . ($ip ?: $_SERVER['REMOTE_ADDR']);
        $result  = [];
        $status  = 0;
        exec($command, $result, $status);

        try {
            // If shell command failed
            if (!count($result) || !array_key_exists(1, $result)) {
                return null;
            }

            // If no address found at all
            if (strpos($result[1], 'IP Address not found') > -1) {
                return null;
            }

            // If there are less than 8 items in the exploded result we can assume that vital information is missing
            $result = GeneralUtility::trimExplode(',', $result[1]);
            if (count($result) < 8) {
                return null;
            }

            $position = new Position();
            $position->setServiceClass(self::class);
            $countryCode = GeneralUtility::trimExplode(' ', $result[1]);
            $position->setCountryCode(is_array($countryCode) ? $countryCode[count($countryCode) - 1] : null);
            $position->setRegion($result[3]);
            $position->setLocality($result[4]);
            $position->setPostalCode($result[5]);
            $position->setLatitude($result[6]);
            $position->setLongitude($result[7]);

            return $position->getLatitude() && $position->getLongitude() ? $position : null;
        } catch (\Error $error) {
            return null;
        }
    }
}
