<?php

/***************************************************************
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

use Generator;
use Tollwerk\TwGeo\Domain\Model\Position;
use Tollwerk\TwGeo\Domain\Model\PositionList;
use Tollwerk\TwGeo\Service\Geocoding\AbstractGeocodingService;
use Tollwerk\TwGeo\Service\Geolocation\AbstractGeolocationService;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Service\AbstractService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility;

/**
 * Geo Utilities
 *
 * @package Tollwerk\TwGeo\Utility
 */
class GeoUtility implements SingletonInterface
{
    /**
     * Earth radius
     *
     * @var int
     */
    const EARTH_RADIUS_METERS = 6371000;

    /**
     * True if debug mode is enabled for the current user IP
     *
     * @var bool
     */
    protected $debug = false;

    /**
     * True if user IP is part of excludeIps and geolocation sould be disabled
     *
     * @var bool
     */
    protected $excluded = false;

    /**
     * IP addresses for which the debug position should be returned instead of the real position
     *
     * @var array
     */
    protected $debugIps = [];

    /**
     * IP addresses to exclude from geolocation
     *
     * @var array
     */
    protected $excludeIps = [];

    /**
     * The debug position
     *
     * @var Position|null
     */
    protected $debugPosition = null;

    /**
     * Constructor
     *
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws Exception
     */
    public function __construct()
    {
        // Get extension configuration TODO: Implement better way to retrieve backend configuration depending on TYPO3 version
        if (class_exists('\TYPO3\CMS\Core\Configuration\ExtensionConfiguration')) {
            // For TYPO3 v9+
            $backendConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('tw_geo');
            $this->debugIps       = GeneralUtility::trimExplode(',', $backendConfiguration['debug']['ip']);
            $this->excludeIps     = GeneralUtility::trimExplode(',', $backendConfiguration['debug']['excludeIp']);

            // Check if geolocation should be disabled by excludeIPs
            $this->excluded = ($backendConfiguration['debug']['excludeIp'] === '*') ||
                              (strlen($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], $this->excludeIps));

            // Check if geolocation should return the debug position because of IP
            if (($backendConfiguration['debug']['ip'] === '*')
                || (strlen($backendConfiguration['debug']['ip']) && in_array($_SERVER['REMOTE_ADDR'], $this->debugIps))
            ) {
                // @extensionScannerIgnoreLine
                $this->debug         = true;
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
        } else {
            // For TYPO3 v8
            $backendConfiguration = GeneralUtility
                ::makeInstance(ObjectManager::class)
                ->get(ConfigurationUtility::class)
                ->getCurrentConfiguration('tw_geo');
            $this->debugIps       = GeneralUtility::trimExplode(',', $backendConfiguration['debug.ip']['value']);
            $this->excludeIps     = GeneralUtility::trimExplode(',', $backendConfiguration['debug.excludeIp']['value']);

            // Check if geolocation should be disabled by excludeIPs
            if (
                $backendConfiguration['debug.excludeIp']['value'] === '*'
                || (strlen($_SERVER['REMOTE_ADDR'])
                    && in_array($_SERVER['REMOTE_ADDR'], $this->excludeIps))
            ) {
                $this->excluded = true;
            }

            // Check if geolocation should return the debug position because of IP
            if (
                $backendConfiguration['debug.ip']['value'] === '*'
                || (strlen($backendConfiguration['debug.ip']['value'])
                    && in_array(
                        $_SERVER['REMOTE_ADDR'],
                        $this->debugIps
                    ))
            ) {
                // @extensionScannerIgnoreLine
                $this->debug         = true;
                $this->debugPosition = new Position();
                $this->debugPosition->setDebug(true);
                $this->debugPosition->setCountryCode($backendConfiguration['debug.countryCode']['value']);
                $this->debugPosition->setCountryName($backendConfiguration['debug.countryName']['value']);
                $this->debugPosition->setRegion($backendConfiguration['debug.region']['value']);
                $this->debugPosition->setLocality($backendConfiguration['debug.locality']['value']);
                $this->debugPosition->setPostalCode($backendConfiguration['debug.postalCode']['value']);
                $this->debugPosition->setLatitude($backendConfiguration['debug.latitude']['value']);
                $this->debugPosition->setLongitude($backendConfiguration['debug.longitude']['value']);
            }
        }
    }

    /**
     * Converts degrees to radians
     *
     * @param float $degrees
     *
     * @return float
     */
    public function degreesToRadians($degrees): ?float
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
    public function getDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo): ?float
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo   = deg2rad($latitudeTo);
        $lonTo   = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a        = pow(cos($latTo) * sin($lonDelta), 2) +
                    pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b        = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);

        return round($angle * self::EARTH_RADIUS_METERS);
    }

    /**
     * Determines and returns the current position
     *
     * @param string|null $ip
     *
     * @return null|Position
     */
    public function getGeoLocation(string $ip = null): ?Position
    {
        $sessionUtility = GeneralUtility::makeInstance(SessionUtility::class);

        /*
        * If an ip address is given, we always want to get the real geolocation for it.
        * So checking for excluded IPs, debug or cache must only be performend when
        * there is no given ip address.
        */
        if ($ip === null) {
            // If excluded ip, return null
            if ($this->excluded) {
                return null;
            }

            // If debug position, return it
            if ($this->debugPosition) {
                // Store posision in session
                return $this->debugPosition;
            }

            // If geolocation was already stored in session, return it
            if ($sessionUtility->get('geoLocation')) {
                /**
                 * @var Position $position
                 */
                $position = $sessionUtility->get('geoLocation');
                $position->setFromSession(true);

                return $position;
            }
        }

        // Try to get the real position
        /**
         * @var AbstractGeolocationService $geolocationService
         */
        foreach ($this->getServices('geolocation') as $geolocationService) {
            $position = $geolocationService->getGeolocation($ip);
            if ($position instanceof Position) {
                $sessionUtility->set('geoLocation', $position);

                return $position;
            }
        }

        return null;
    }

    /**
     * Iterate through all available services classes of $type and $subtype
     * and yield each one separately. Use this for chaining services
     * until one of them returns a result or no service is left.
     *
     * @param string $type
     * @param string $subtype
     *
     * @return Generator
     */
    protected function getServices(string $type, string $subtype = '')
    {
        $serviceChain = '';
        /**
         * @var AbstractService $serviceObject
         */
        while (is_object($serviceObject = GeneralUtility::makeInstanceService($type, $subtype, $serviceChain))) {
            $serviceChain .= ', ' . $serviceObject->getServiceKey();
            $serviceObject->init();
            yield $serviceObject;
        }
    }

    /**
     * Try to geocode an query string
     *
     * @param string $queryString
     * @param int    $limit If 0, return all
     *
     * @return null|Position|PositionList
     */
    public function geocode(string $queryString, int $limit = 1)
    {
        /**
         * @var AbstractGeocodingService $geocodingService
         */
        foreach ($this->getServices('geocoding') as $geocodingService) {
            $positions = $geocodingService->geocode($queryString, $limit);
            if ($positions instanceof PositionList && $positions->count()) {
                // Return complete PositionList if no limit was set
                if (!$limit) {
                    return $positions;
                }

                // Return first position
                if ($limit == 1) {
                    $positions->rewind();

                    return $positions->current();
                }

                // Return desired number of position
                $returnPositions = new PositionList();
                $positions->rewind();
                $count = 0;
                foreach ($positions as $position) {
                    if ($count >= $limit) {
                        break;
                    }
                    $returnPositions->add($position);
                }

                return $returnPositions;
            }
        }

        return null;
    }
}
