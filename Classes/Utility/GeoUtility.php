<?php
/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2018 Klaus Fiedler <klaus@tollwerk.de>, tollwerk® GmbH
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

namespace Tollwerk\TwGeo\Utility;

use Doctrine\Common\Util\Debug;
use Tollwerk\TwGeo\Service\Geolocation\GeolocationInterface;
use Tollwerk\TwGeo\Domain\Model\Position;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class GeoUtility implements SingletonInterface
{
    const EARTH_RADIUS_METERS = 6371000;

    /**
     * True if debug mode is enabled for the current user IP
     * @var bool
     */
    protected $debug = false;

    /**
     * IP addresses for which the debug position should be returned instead of the real position
     * @var array
     */
    protected $debugIps = [];

    /**
     * The debug position
     * @var Position|null
     */
    protected $debugPosition = null;

    public function __construct()
    {
        // Get extension configuration
        $backendConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('tw_geo');

        // If user IP is one of the debug IP addresses, return debug position1
        $this->debugIps = GeneralUtility::trimExplode(',', $backendConfiguration['debug']['ip']);
        if (in_array($_SERVER['REMOTE_ADDR'], $this->debugIps)) {
            $this->debug = true;
            $this->debugPosition = new Position();
            $this->debugPosition->setDebug(true);
            $this->debugPosition->setCountryCode($backendConfiguration['debug']['countryCode']);
            $this->debugPosition->setCountryName($backendConfiguration['debug']['countryName']);
            $this->debugPosition->setRegion($backendConfiguration['debug']['region']);
            $this->debugPosition->setLocality($backendConfiguration['debug']['locality']);
            $this->debugPosition->setPostalCode($backendConfiguration['debug']['postalCode']);
            $this->debugPosition->setLatitude($backendConfiguration['debug']['latitude']);
            $this->debugPosition->setLongitude($backendConfiguration['debug']['longitude']);
        }
    }

    /**
     * Converts degrees to radians
     *
     * @param float $degrees
     */
    public function degreesToRadians($degrees)
    {
        return $degrees * pi() / 180;
    }

    /**
     * Calculates the great-circle distance between two points, with
     * the Vincenty formula.
     *
     * @param float $latitudeFrom  Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo    Latitude of target point in [deg decimal]
     * @param float $longitudeTo   Longitude of target point in [deg decimal]
     * @param float $earthRadius   Mean earth radius in [m]
     *
     * @return float Distance between points in [m] (same as earthRadius)
     */
    public function getDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
             pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);
        return round($angle * self::EARTH_RADIUS_METERS);
    }

    /**
     * @return null|Position
     */
    public function getGeoLocation(): ?Position
    {
        if($this->debugPosition){
            return $this->debugPosition;
        }

        // Try to return the geolocation
        /** @var GeolocationInterface $geoService */
        if (is_object($geoService = GeneralUtility::makeInstanceService('geolocation'))) {
            if ($position = $geoService->getGeolocation()) {
                /** @var Position $position */
                return $position;
            }
        }

        return null;
    }
}