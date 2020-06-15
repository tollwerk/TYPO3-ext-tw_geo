<?php
/**
 * Tollwerk Geo Tools
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwGeo
 * @subpackage Tollwerk\TwGeo\Domain\Model\FormElements
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

namespace Tollwerk\TwGeo\Domain\Model\FormElements;

use Tollwerk\TwGeo\Domain\Model\PositionList;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Form\Domain\Exception\TypeDefinitionNotFoundException;
use TYPO3\CMS\Form\Domain\Exception\TypeDefinitionNotValidException;
use TYPO3\CMS\Form\Domain\Model\FormElements\GenericFormElement;
use TYPO3\CMS\Form\Domain\Model\FormElements\Section;

/**
 * Geoselect form element
 *
 * @package Tollwerk\TwGeo\Domain\Model\FormElements
 */
class Geoselect extends Section
{
    /**
     * Text input for geolocation search string
     * @var GenericFormElement
     */
    protected $searchField = null;

    /**
     * Text input for combined latitude and longitude
     * @var GenericFormElement
     */
    protected $latLonField = null;

    /**
     * Radiobuttons for selectable places based on geolocation results
     * @var GenericFormElement
     */
    protected $positionField = null;

    /**
     * Position list
     *
     * @var PositionList
     */
    protected $positions = null;

    /**
     * Latitude / Longitude
     *
     * @var int
     */
    protected $latLon = null;

    /**
     * @return GenericFormElement
     */
    public function getSearchField(): GenericFormElement
    {
        return $this->searchField;
    }

    /**
     * @return GenericFormElement
     */
    public function getLatLonField(): GenericFormElement
    {
        return $this->latLonField;
    }

    /**
     * @return GenericFormElement
     */
    public function getPositionField(): GenericFormElement
    {
        return $this->positionField;
    }

    /**
     * @return PositionList
     */
    public function getPositions(): ?PositionList
    {
        return $this->positions;
    }

    /**
     * @param PositionList|null $positions
     */
    public function setPositions(PositionList $positions = null)
    {
        $this->positions = $positions;
    }

    /**
     * @return string
     */
    public function getLatLon(): ?string
    {
        return $this->latLon;
    }

    /**
     * @param int $latLon
     */
    public function setLatLon(string $latLon = null)
    {
        $this->latLon = $latLon;
    }

    /**
     * Initialize the form element
     *
     * @throws TypeDefinitionNotFoundException
     * @throws InvalidConfigurationTypeException
     * @throws TypeDefinitionNotValidException
     */
    public function initializeFormElement()
    {
        $this->setRenderingOption('map', true);

        // Get typoscript settings for tw_geo
        $settings = GeneralUtility::makeInstance(ObjectManager::class)
                                  ->get(ConfigurationManager::class)
                                  ->getConfiguration(
                                      ConfigurationManager::CONFIGURATION_TYPE_SETTINGS,
                                      'TwGeo'
                                  );

        // Include google maps javascript if enabled.
        if (!empty($settings['googleMaps']['includeJs']) && !empty($settings['googleMaps']['apiKey'])) {
            $googleMapsParameters                                             = [
                'key'      => $settings['googleMaps']['apiKey'],
                'language' => $GLOBALS['TYPO3_REQUEST']->getAttribute('language')->getTwoLetterIsoCode(),
            ];
            $GLOBALS['TSFE']->additionalFooterData['tx_twgeo_google_maps_js'] = '<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&'.http_build_query($googleMapsParameters).'"></script>';
            $GLOBALS['TSFE']->additionalFooterData['tx_twgeo_google_geoselect_js'] = '<script src="/typo3conf/ext/tw_geo/Resources/Public/tw_geo-default.min.js"></script>';
        }
        $this->setProperty(
            'mapMarker',
            GeneralUtility::getIndpEnv('TYPO3_SITE_URL').$settings['googleMaps']['mapMarker']
        );
        $this->setProperty(
            'mapCenter',
            [
                'latitude'  => $settings['googleMaps']['latitude'],
                'longitude' => $settings['googleMaps']['longitude']
            ]
        );
        $this->setProperty('mapRestrictions', [
            'countries' => $settings['googleMaps']['restrictions']['countries']
        ]);




        // Add search field
        $this->searchField = $this->createElement($this->identifier.'-search', 'Text');
//        $this->searchField->setLabel(
//            LocalizationUtility::translate(
//                'LLL:EXT:tw_geo/Resources/Private/Language/locallang_forms.xlf:geoselect.search.label',
//                'TwGeo'
//            )
//        );

        // Add hidden latitude;longitude field
        $this->latLonField = $this->createElement($this->identifier.'-lat-lon', 'Hidden');

        // Add position field for selection found positions based on the search result. Only used in non-js version.
        $this->positionField = $this->createElement($this->identifier.'-position', 'SingleSelect');
        $this->positionField->setLabel(
            LocalizationUtility::translate(
                'LLL:EXT:tw_geo/Resources/Private/Language/locallang_forms.xlf:geoselect.positions.label',
                'TwGeo'
            )
        );
    }
}
