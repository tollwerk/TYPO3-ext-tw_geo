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

namespace Tollwerk\TwGeo\Domain\Model;


class Position
{
    /**
     * @var string
     */
    protected $countryCode = null;

    /**
     * @var string
     */
    protected $countryName = null;

    /**
     * @var string
     */
    protected $region = null;

    /**
     * @var string
     */
    protected $locality = null;

    /**
     * @var string
     */
    protected $postalCode = null;

    /**
     * @var string
     */
    protected $street = null;

    /**
     * @var string
     */
    protected $houseNumber = null;


    /** @var float  */
    protected $latitude = null;


    /**
     * @var float
     */
    protected $longitude = null;


    /**
     * If true, this position is a debug position
     * @var bool
     */
    protected $debug = false;

    /**
     * If true, position was retrieved from feuser session
     * @var bool
     */
    protected $fromSession = false;

    public function __construct(float $latitude = null, float $longitude = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * @return string
     */
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode(string $countryCode = null)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return string
     */
    public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    /**
     * @param string $countryName
     */
    public function setCountryName(string $countryName = null)
    {
        $this->countryName = $countryName;
    }

    /**
     * @return string
     */
    public function getRegion(): ?string
    {
        return $this->region;
    }

    /**
     * @param string $region
     */
    public function setRegion(string $region = null)
    {
        $this->region = $region;
    }

    /**
     * @return string
     */
    public function getLocality(): ?string
    {
        return $this->locality;
    }

    /**
     * @param string $locality
     */
    public function setLocality(string $locality = null)
    {
        $this->locality = $locality;
    }

    /**
     * @return string
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode(string $postalCode = null)
    {
        $this->postalCode = $postalCode;
    }



    /**
     * @return float
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude(float $latitude = null)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude(float $longitude = null)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @param bool $debug
     */
    public function setDebug(bool $debug)
    {
        $this->debug = $debug;
    }

    /**
     * @return bool
     */
    public function isFromSession(): bool
    {
        return $this->fromSession;
    }

    /**
     * @param bool $fromSession
     */
    public function setFromSession(bool $fromSession)
    {
        $this->fromSession = $fromSession;
    }
}
