<?php
/**
 * TwGeo
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwGeo
 * @subpackage Tollwerk\TwGeo\Hook
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


namespace Tollwerk\TwGeo\Hook;


use Tollwerk\TwGeo\Domain\Model\FormElements\Geoselect;
use Tollwerk\TwGeo\Domain\Model\PositionList;
use Tollwerk\TwGeo\Utility\GeoUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Domain\Runtime\FormRuntime;

/**
 * Class FormHook
 * @package Tollwerk\TwGeo\Hook
 */
class FormHook
{
    /**
     * Check the geo select field
     *
     * @param FormRuntime $formRuntime
     * @param Geoselect $renderable
     *
     * @return bool
     */
    protected function checkGeoselectField(FormRuntime $formRuntime, Geoselect $renderable)
    {
        // If no search string is given, we can abort all further processing
        $searchString = $formRuntime->getElementValue($renderable->getIdentifier().'-search');
        if (!$searchString) {
            return false;
        }

        // Try to geocode the search string, update the Geoselect form field
        /** @var GeoUtility $geoUtilty */
        $geoUtilty = GeneralUtility::makeInstance(GeoUtility::class);
        $positions = $geoUtilty->geocode($searchString, 0);

        /** @var PositionList $positions */
        if ($positions instanceof PositionList) {
            $renderable->setPositions($positions);
            if ($positions->count()) {
                $positions->rewind();
                $firstPosition = $positions->current();
                $renderable->setLatLon($firstPosition->getLatitude().','.$firstPosition->getLongitude());
            }
        }

        $latLon = $formRuntime->getElementValue($renderable->getIdentifier().'-position');
        if ($latLon) {
            $renderable->setLatLon($latLon);
        }

    }

    /**
     * @param \TYPO3\CMS\Form\Domain\Runtime\FormRuntime $formRuntime
     * @param \TYPO3\CMS\Form\Domain\Model\Renderable\RootRenderableInterface $renderable
     *
     * @return void
     */
    public function beforeRendering(
        \TYPO3\CMS\Form\Domain\Runtime\FormRuntime $formRuntime,
        \TYPO3\CMS\Form\Domain\Model\Renderable\RootRenderableInterface $renderable
    ) {
        if ($renderable->getType() == 'Geoselect') {
            $this->checkGeoselectField($formRuntime, $renderable);
        }
    }
}
