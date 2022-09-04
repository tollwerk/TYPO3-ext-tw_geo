<?php

/**
 * Tollwerk Geo Tools
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwGeo
 * @subpackage Tollwerk\TwGeo\Service\Geocoding
 * @author     Klaus Fiedler <klaus@tollwerk.de> / @jkphl
 * @copyright  Copyright © 2020 Klaus Fiedler <klaus@tollwerk.de>
 * @license    http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2020 Klaus Fiedler <klaus@tollwerk.de>
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

namespace Tollwerk\TwGeo\Service\Geocoding;

use TYPO3\CMS\Core\Service\AbstractService;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * Abstract Geocoding Service
 *
 * @package Tollwerk\TwGeo\Service\Geocoding
 */
abstract class AbstractGeocodingService extends AbstractService implements GeocodingInterface
{
    /**
     * Country to language mapping
     *
     * @var string[][]
     * @see https://wiki.openstreetmap.org/wiki/Nominatim/Country_Codes
     */
    const COUNTRY_TO_LANGUAGES = [
        'AD' => ['ca'],
        'AE' => ['ar'],
        'AF' => ['fa', 'ps'],
        'AG' => ['en'],
        'AI' => ['en'],
        'AL' => ['sq'],
        'AM' => ['hy'],
        'AO' => ['pt'],
        'AQ' => ['en', 'es', 'fr', 'ru'],
        'AR' => ['es'],
        'AS' => ['en', 'sm'],
        'AT' => ['de'],
        'AU' => ['en'],
        'AW' => ['nl', 'pap'],
        'AX' => ['sv'],
        'AZ' => ['az'],
        'BA' => ['bs', 'hr', 'sr'],
        'BB' => ['en'],
        'BD' => ['bn'],
        'BE' => ['nl', 'fr', 'de'],
        'BF' => ['fr'],
        'BG' => ['bg'],
        'BH' => ['ar'],
        'BI' => ['fr'],
        'BJ' => ['fr'],
        'BL' => ['fr'],
        'BM' => ['en'],
        'BN' => ['ms'],
        'BO' => ['es', 'qu', 'gn', 'ay'],
        'BQ' => ['nl'],
        'BR' => ['pt'],
        'BS' => ['en'],
        'BT' => ['dz'],
        'BV' => ['no'],
        'BW' => ['en', 'tn'],
        'BY' => ['be', 'ru'],
        'BZ' => ['en'],
        'CA' => ['en', 'fr'],
        'CC' => ['en'],
        'CD' => ['fr'],
        'CF' => ['fr', 'sg'],
        'CG' => ['fr'],
        'CH' => ['de', 'fr', 'it', 'rm'],
        'CI' => ['fr'],
        'CK' => ['en', 'rar'],
        'CL' => ['es'],
        'CM' => ['fr', 'en'],
        'CN' => ['zh-hans'],
        'CO' => ['es'],
        'CR' => ['es'],
        'CU' => ['es'],
        'CV' => ['pt'],
        'CW' => ['nl', 'en'],
        'CX' => ['en'],
        'CY' => ['el', 'tr'],
        'CZ' => ['cs'],
        'DE' => ['de'],
        'DJ' => ['fr', 'ar', 'so', 'aa'],
        'DK' => ['da'],
        'DM' => ['en'],
        'DO' => ['es'],
        'DZ' => ['ar'],
        'EC' => ['es'],
        'EE' => ['et'],
        'EG' => ['ar'],
        'EH' => ['ar', 'es', 'fr'],
        'ER' => ['ti', 'ar', 'en'],
        'ES' => ['ast', 'ca', 'es', 'eu', 'gl'],
        'ET' => ['am', 'om'],
        'FI' => ['fi', 'sv', 'se'],
        'FJ' => ['en'],
        'FK' => ['en'],
        'FM' => ['en'],
        'FO' => ['fo', 'da'],
        'FR' => ['fr'],
        'GA' => ['fr'],
        'GB' => ['en', 'ga', 'cy', 'gd', 'kw'],
        'GD' => ['en'],
        'GE' => ['ka'],
        'GF' => ['fr'],
        'GG' => ['en'],
        'GH' => ['en'],
        'GI' => ['en'],
        'GL' => ['kl', 'da'],
        'GM' => ['en'],
        'GN' => ['fr'],
        'GP' => ['fr'],
        'GQ' => ['es', 'fr', 'pt'],
        'GR' => ['el'],
        'GS' => ['en'],
        'GT' => ['es'],
        'GU' => ['en', 'ch'],
        'GW' => ['pt'],
        'GY' => ['en'],
        'HK' => ['zh-hant', 'en'],
        'HM' => ['en'],
        'HN' => ['es'],
        'HR' => ['hr'],
        'HT' => ['fr', 'ht'],
        'HU' => ['hu'],
        'ID' => ['id'],
        'IE' => ['en', 'ga'],
        'IL' => ['he'],
        'IM' => ['en'],
        'IN' => ['hi', 'en'],
        'IO' => ['en'],
        'IQ' => ['ar', 'ku'],
        'IR' => ['fa'],
        'IS' => ['is'],
        'IT' => ['it', 'de', 'fr'],
        'JE' => ['en'],
        'JM' => ['en'],
        'JO' => ['ar'],
        'JP' => ['ja'],
        'KE' => ['sw', 'en'],
        'KG' => ['ky', 'ru'],
        'KH' => ['km'],
        'KI' => ['en'],
        'KM' => ['ar', 'fr', 'sw'],
        'KN' => ['en'],
        'KP' => ['ko'],
        'KR' => ['ko', 'en'],
        'KW' => ['ar'],
        'KY' => ['en'],
        'KZ' => ['kk', 'ru'],
        'LA' => ['lo'],
        'LB' => ['ar', 'fr'],
        'LC' => ['en'],
        'LI' => ['de'],
        'LK' => ['si', 'ta'],
        'LR' => ['en'],
        'LS' => ['en', 'st'],
        'LT' => ['lt'],
        'LU' => ['lb', 'fr', 'de'],
        'LV' => ['lv'],
        'LY' => ['ar'],
        'MA' => ['fr', 'zgh', 'ar'],
        'MC' => ['fr'],
        'MD' => ['ro', 'ru', 'uk'],
        'ME' => ['srp', 'sr', 'hr', 'bs', 'sq'],
        'MF' => ['fr'],
        'MG' => ['mg', 'fr'],
        'MH' => ['en', 'mh'],
        'MK' => ['mk'],
        'ML' => ['fr'],
        'MM' => ['my'],
        'MN' => ['mn'],
        'MO' => ['zh-hant', 'pt'],
        'MP' => ['en', 'ch'],
        'MQ' => ['fr'],
        'MR' => ['ar', 'fr'],
        'MS' => ['en'],
        'MT' => ['mt', 'en'],
        'MU' => ['mfe', 'fr', 'en'],
        'MV' => ['dv'],
        'MW' => ['en', 'ny'],
        'MX' => ['es'],
        'MY' => ['ms'],
        'MZ' => ['pt'],
        'NA' => ['en', 'sf', 'de'],
        'NC' => ['fr'],
        'NE' => ['fr'],
        'NF' => ['en', 'pih'],
        'NG' => ['en'],
        'NI' => ['es'],
        'NL' => ['nl'],
        'NO' => ['nb', 'nn', 'no', 'se'],
        'NP' => ['ne'],
        'NR' => ['na', 'en'],
        'NU' => ['niu', 'en'],
        'NZ' => ['mi', 'en'],
        'OM' => ['ar'],
        'PA' => ['es'],
        'PE' => ['es'],
        'PF' => ['fr'],
        'PG' => ['en', 'tpi', 'ho'],
        'PH' => ['en', 'tl'],
        'PK' => ['en', 'ur'],
        'PL' => ['pl'],
        'PM' => ['fr'],
        'PN' => ['en', 'pih'],
        'PR' => ['es', 'en'],
        'PS' => ['ar', 'he'],
        'PT' => ['pt'],
        'PW' => ['en', 'pau', 'ja', 'sov', 'tox'],
        'PY' => ['es', 'gn'],
        'QA' => ['ar'],
        'RE' => ['fr'],
        'RO' => ['ro'],
        'RS' => ['sr', 'sr-Latn'],
        'RU' => ['ru'],
        'RW' => ['rw', 'fr', 'en'],
        'SA' => ['ar'],
        'SB' => ['en'],
        'SC' => ['fr', 'en', 'crs'],
        'SD' => ['ar', 'en'],
        'SE' => ['sv'],
        'SG' => ['zh-hans', 'en', 'ms', 'ta'],
        'SH' => ['en'],
        'SI' => ['sl'],
        'SJ' => ['no'],
        'SK' => ['sk'],
        'SL' => ['en'],
        'SM' => ['it'],
        'SN' => ['fr'],
        'SO' => ['so', 'ar'],
        'SR' => ['nl'],
        'ST' => ['pt'],
        'SS' => ['en'],
        'SV' => ['es'],
        'SX' => ['nl', 'en'],
        'SY' => ['ar'],
        'SZ' => ['en', 'ss'],
        'TC' => ['en'],
        'TD' => ['fr', 'ar'],
        'TF' => ['fr'],
        'TG' => ['fr'],
        'TH' => ['th'],
        'TJ' => ['tg', 'ru'],
        'TK' => ['tkl', 'en', 'sm'],
        'TL' => ['pt', 'tet'],
        'TM' => ['tk'],
        'TN' => ['ar', 'fr'],
        'TO' => ['en'],
        'TR' => ['tr'],
        'TT' => ['en'],
        'TV' => ['en'],
        'TW' => ['zh-hant'],
        'TZ' => ['sw', 'en'],
        'UA' => ['uk'],
        'UG' => ['en', 'sw'],
        'UM' => ['en'],
        'US' => ['en'],
        'UY' => ['es'],
        'UZ' => ['uz', 'kaa'],
        'VA' => ['it'],
        'VC' => ['en'],
        'VE' => ['es'],
        'VG' => ['en'],
        'VI' => ['en'],
        'VN' => ['vi'],
        'VU' => ['bi', 'en', 'fr'],
        'WF' => ['fr'],
        'WS' => ['sm', 'en'],
        'YE' => ['ar'],
        'YT' => ['fr'],
        'ZA' => ['en', 'af', 'st', 'tn', 'xh', 'zu'],
        'ZM' => ['en'],
        'ZW' => ['en', 'sn', 'nd']
    ];
    /**
     * HTTP Headers
     *
     * @var array
     */
    protected $httpRequestHeader = ['User-Agent: tollwerk/TYPO3-ext-tw_geo'];

    /**
     * Return the current frontend language
     *
     * @return SiteLanguage|null
     */
    public function getCurrentFrontendLanguage(): ?SiteLanguage
    {
        if ($GLOBALS['TYPO3_REQUEST']) {
            /** @var SiteLanguage $language */
            $language = $GLOBALS['TYPO3_REQUEST']->getAttribute('language');
            if ($language instanceof SiteLanguage) {
                return $language;
            }
        }

        return null;
    }
}
