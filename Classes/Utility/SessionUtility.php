<?php
/**
 * RWS TYPO3 v9
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwGeo
 * @subpackage Tollwerk\TwGeo\Utility
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

namespace Tollwerk\TwGeo\Utility;

use TYPO3\CMS\Core\SingletonInterface;

/**
 * Session Utility
 *
 * @package Tollwerk\TwGeo\Utility
 */
class SessionUtility implements SingletonInterface
{
    /**
     * The session key
     *
     * @var string
     */
    const KEY = 'tw_geo';

    /**
     * @var array
     */
    protected $session = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        session_start();
        if (!array_key_exists(self::KEY, $_SESSION)) {
            $_SESSION[self::KEY] = [];
        }
    }

    /**
     * Set a session value
     *
     * @param string $key
     * @param mixed $value
     *
     * @return bool Returns true if value could be stored
     */
    public function set(string $key, $value): bool
    {
        if(!$_SESSION || !is_array($_SESSION[self::KEY])) {
            return false;
        }

        $_SESSION[self::KEY][$key] = serialize($value);
        return true;
    }

    /**
     * Get a session value
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function get(string $key)
    {
        if (!is_array($_SESSION) || !isset($_SESSION[self::KEY][$key])) {
            return null;
        }

        return unserialize($_SESSION[self::KEY][$key]);
    }
}
