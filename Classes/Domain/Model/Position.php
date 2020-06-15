<?php

/**
 * Tollwerk Geo Tools
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwGeo
 * @subpackage Tollwerk\TwGeo\Domain\Model
 * @author     Klaus Fiedler <klaus@tollwerk.de> / @jkphl
 * @copyright  Copyright © 2020 Klaus Fiedler <klaus@tollwerk.de>
 * @license    http://opensource.org/licenses/MIT The MIT License (MIT)
 */

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

namespace Tollwerk\TwGeo\Domain\Model;

/**
 * Position
 *
 * @package Tollwerk\TwGeo\Domain\Model
 */
class Position
{
    /**
     * Country code
     *
     * @var string
     */
    protected $countryCode = null;

    /**
     * Country name
     *
     * @var string
     */
    protected $countryName = null;

    /**
     * Region
     *
     * @var string
     */
    protected $region = null;

    /**
     * Locality
     *
     * @var string
     */
    protected $locality = null;

    /**
     * Postal code
     *
     * @var string
     */
    protected $postalCode = null;

    /**
     * Street address
     *
     * @var string
     */
    protected $street = null;

    /**
     * Street number
     *
     * @var string
     */
    protected $streetNumber = null;

    /**
     * Latitude
     *
     * @var float|null
     */
    protected $latitude = null;

    /**
     * Longitude
     *
     * @var float
     */
    protected $longitude = null;

    /**
     * Display name
     *
     * @var string
     */
    protected $displayName = null;

    /**
     * If true, this position is a debug position
     *
     * @var bool
     */
    protected $debug = false;

    /**
     * Service class
     *
     * @var string
     */
    protected $serviceClass = null;

    /**
     * If true, position was retrieved from the frontend user session
     *
     * @var bool
     */
    protected $fromSession = false;
    /**
     * Country display name part
     *
     * @var int
     */
    const COUNTRY = 1;
    /**
     * Region display name part
     *
     * @var int
     */
    const REGION = 2;
    /**
     * Locality display name part
     *
     * @var int
     */
    const LOCALITY = 4;
    /**
     * Street display name part
     *
     * @var int
     */
    const STREET = 8;

    /**
     * Constructor
     *
     * @param float|null $latitude  Latitude
     * @param float|null $longitude Longitude
     */
    public function __construct(float $latitude = null, float $longitude = null)
    {
        $this->latitude  = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * Return the country code
     *
     * @return string Country code
     */
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * Set the country code
     *
     * @param string $countryCode Country code
     */
    public function setCountryCode(string $countryCode = null)
    {
        $this->countryCode = strtoupper($countryCode);
    }

    /**
     * Return the country name
     *
     * @return string Country name
     */
    public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    /**
     * Set the country name
     *
     * @param string $countryName Country name
     */
    public function setCountryName(string $countryName = null)
    {
        $this->countryName = $countryName;
    }

    /**
     * Return the region
     *
     * @return string Region
     */
    public function getRegion(): ?string
    {
        return $this->region;
    }

    /**
     * Set the region
     *
     * @param string $region Region
     */
    public function setRegion(string $region = null)
    {
        $this->region = $region;
    }

    /**
     * Return the Location
     *
     * @return string Location
     */
    public function getLocality(): ?string
    {
        return $this->locality;
    }

    /**
     * Set the location
     *
     * @param string $locality Location
     */
    public function setLocality(string $locality = null)
    {
        $this->locality = $locality;
    }

    /**
     * Return the postal code
     *
     * @return string Postal code
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * Set the postal code
     *
     * @param string $postalCode Postal code
     */
    public function setPostalCode(string $postalCode = null)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * Return the street address
     *
     * @return string Street address
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * Set the street address
     *
     * @param string $street Street address
     */
    public function setStreet(string $street = null)
    {
        $this->street = $street;
    }

    /**
     * Return the street number
     *
     * @return string Street number
     */
    public function getStreetNumber(): string
    {
        return $this->streetNumber;
    }

    /**
     * Set the street number
     *
     * @param string $streetNumber Street number
     */
    public function setStreetNumber(string $streetNumber = null)
    {
        $this->streetNumber = $streetNumber;
    }

    /**
     * Return the display name
     *
     * Control the display name parts to return by providing a part combination constant
     *
     * @return string Display name
     */
    public function getDisplayName(int $parts = self::COUNTRY | self::LOCALITY | self::REGION | self::STREET): string
    {
        if ($parts == (self::COUNTRY | self::LOCALITY | self::REGION | self::STREET)) {
            return $this->displayName;
        }

        return implode(', ', array_filter([
            ($parts & self::STREET) ? $this->getStreet() : null,
            ($parts & self::LOCALITY) ? $this->getLocality() : null,
            ($parts & self::REGION) ? $this->getRegion() : null,
            ($parts & self::COUNTRY) ? $this->getCountryName() : null,
        ]));
    }

    /**
     * Set the display name
     *
     * @param string $displayName Display name
     */
    public function setDisplayName(string $displayName = null)
    {
        $this->displayName = $displayName;
    }

    /**
     * Return the latitude
     *
     * @return float Latitude
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * Set the latitude
     *
     * @param float $latitude Latitude
     */
    public function setLatitude(float $latitude = null)
    {
        $this->latitude = $latitude;
    }

    /**
     * Return the longitude
     *
     * @return float Longitude
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * Set the longitude
     *
     * @param float $longitude Longitude
     */
    public function setLongitude(float $longitude = null)
    {
        $this->longitude = $longitude;
    }

    /**
     * Return whether this is a debug position
     *
     * @return bool Debug position
     */
    public function isDebug(): bool
    {
        // @extensionScannerIgnoreLine
        return $this->debug;
    }

    /**
     * Set whether this is a debug position
     *
     * @param bool $debug Debug position
     */
    public function setDebug(bool $debug)
    {
        // @extensionScannerIgnoreLine
        $this->debug = $debug;
    }

    /**
     * Return whether this position has been retrieved from the frontend user session
     *
     * @return bool From frontend user session
     */
    public function isFromSession(): bool
    {
        return $this->fromSession;
    }

    /**
     * Set whether this position has been retrieved from the frontend user session
     *
     * @param bool $fromSession From frontend user session
     */
    public function setFromSession(bool $fromSession)
    {
        $this->fromSession = $fromSession;
    }

    /**
     * Return the service class
     *
     * @return string Service class
     */
    public function getServiceClass(): string
    {
        return $this->serviceClass;
    }

    /**
     * Set the service class
     *
     * @param string $serviceClass Service class
     */
    public function setServiceClass(string $serviceClass)
    {
        $this->serviceClass = $serviceClass;
    }

    /**
     * Return serialization properties
     *
     * @return string[]
     */
    public function __sleep()
    {
        return [
            'countryCode',
            'countryName',
            'region',
            'locality',
            'postalCode',
            'street',
            'streetNumber',
            'latitude',
            'longitude',
            'displayName'
        ];
    }
}
