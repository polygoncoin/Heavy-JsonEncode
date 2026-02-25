<?php

/**
 * Custom Json Encode
 * php version 7
 *
 * @category  JsonEncode
 * @package   CustomJsonEncode
 * @author    Ramesh N. Jangid (Sharma) <polygon.co.in@gmail.com>
 * @copyright © 2026 Ramesh N. Jangid (Sharma)
 * @license   MIT https://opensource.org/license/mit
 * @link      https://github.com/polygoncoin/Microservices
 * @since     Class available since Release 1.0.0
 */

namespace CustomJsonEncode;

/**
 * Custom Json Encode Object
 * php version 7
 *
 * @category  JsonEncode
 * @package   CustomJsonEncode
 * @author    Ramesh N. Jangid (Sharma) <polygon.co.in@gmail.com>
 * @copyright © 2026 Ramesh N. Jangid (Sharma)
 * @license   MIT https://opensource.org/license/mit
 * @link      https://github.com/polygoncoin/Microservices
 * @since     Class available since Release 1.0.0
 */
class JsonEncodeObject
{
    /**
     * Mode value can be any one among Array/Object string value
     *
     * @var string $mode
     */
    public $mode = '';

    /**
     * Contains a comma(,) or empty string
     *
     * @var string $comma
     */
    public $comma = '';

    /**
     * Constructor
     *
     * @param string $mode Value can be any one among Array/Object string value
     */
    public function __construct($mode)
    {
        $this->mode = $mode;
    }
}
