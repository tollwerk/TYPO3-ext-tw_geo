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

namespace Tollwerk\TwGeo\Service\Geocoding;

use Tollwerk\TwGeo\Domain\Model\Position;
use Tollwerk\TwGeo\Domain\Model\PositionList;

interface GeocodingInterface
{
    /**
     * Get geocoding result for address string
     *
     * @param string $queryString
     *
     * @return \Traversable
     */
    public function geocode(string $queryString = null): ?PositionList;

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
    ): ?Position;
}
