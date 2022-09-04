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

use Error;
use Tollwerk\TwGeo\Domain\Model\Position;

class PhpGeoIPService extends AbstractGeolocationService
{
    public function init()
    {
        return parent::init() && function_exists('geoip_record_by_name');
    }

    public function getGeolocation(string $ip = null): ?Position
    {
        try {
            $result = geoip_record_by_name($ip ?: $_SERVER['REMOTE_ADDR']);
            if ($result) {
                $position = new Position();
                $position->setServiceClass(self::class);
                $position->setCountryCode($result['country_code']);
                $position->setCountryName($result['country_name']);
                $position->setRegion($result['region']);
                $position->setLocality($result['city']);
                $position->setPostalCode($result['postal_code']);
                $position->setLatitude($result['latitude']);
                $position->setLongitude($result['longitude']);

                return ($position->getLatitude() && $position->getLongitude()) ? $position : null;
            }

            return null;
        } catch (Error $error) {
            return null;
        }
    }
}
