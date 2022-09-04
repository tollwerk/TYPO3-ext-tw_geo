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

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2020 Joschi Kuphal <joschi@tollwerk.de>
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

namespace Tollwerk\TwGeo\Domain\Model;

use ArrayIterator;
use IteratorIterator;

/**
 * Position List
 *
 * A type/class fixed list of Position objects for well defined retrieval of multiple Position objects
 * instead of using an untyped, simple array
 *
 * @package    Tollwerk\TwGeo
 * @subpackage Tollwerk\TwGeo\Domain\Model
 */
class PositionList extends IteratorIterator
{
    /**
     * Constructor
     *
     * @param Position ...$positions Positions
     */
    public function __construct(Position ...$positions)
    {
        parent::__construct(new ArrayIterator($positions));
    }

    /**
     * Return the current position
     *
     * @return Position Current position
     */
    public function current(): Position
    {
        return parent::current();
    }

    /**
     * Add a Position to the list
     *
     * @param Position $position Position
     */
    public function add(Position $position)
    {
        $this->getInnerIterator()->append($position);
    }

    /**
     * Count the positions in this list
     *
     * @return int Number of positions
     */
    public function count(): int
    {
        return $this->getInnerIterator()->count();
    }
}
