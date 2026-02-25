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

use CustomJsonEncode\JsonEncode;

/**
 * Custom Json Encoder
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
class JsonEncoder
{
    /**
     * JSON generator object
     *
     * @var null|JsonEncode
     */
    public static $jsonEncodeObj = null;

    /**
     * Initialize
     *
     * @return void
     */
    public static function init(): void
    {
        self::$jsonEncodeObj = new JsonEncode();
    }

    /**
     * JSON generator object
     *
     * @return object
     */
    public static function getObject(): JsonEncode
    {
        if (is_null(value: self::$jsonEncodeObj)) {
            self::init();
        }
        return self::$jsonEncodeObj;
    }
}
