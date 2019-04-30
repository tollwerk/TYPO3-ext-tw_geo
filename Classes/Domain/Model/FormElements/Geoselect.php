<?php
/**
 * TwGeo
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

use TYPO3\CMS\Form\Domain\Model\FormElements\GenericFormElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;


class Geoselect extends \TYPO3\CMS\Form\Domain\Model\FormElements\Section
{
    /**
     * @var GenericFormElement
     */
    protected $latLon = null;


    /**
     * @return GenericFormElement
     */
    public function getLatLon(): GenericFormElement
    {
        return $this->latLon;
    }

    /**
     * @param GenericFormElement $latLon
     */
    public function setLatitude(GenericFormElement $latLon)
    {
        $this->latLon = $latLon;
    }

    /**
     * @throws \TYPO3\CMS\Form\Domain\Exception\TypeDefinitionNotFoundException
     */
    public function initializeFormElement()
    {
        $latLon = $this->createElement($this->identifier.'-lat-lon', 'Text');
        $this->latLon = $latLon;

        $update = $this->createElement($this->identifier.'-update', 'SubmitButton');
        $update->setLabel('update');
    }
}
