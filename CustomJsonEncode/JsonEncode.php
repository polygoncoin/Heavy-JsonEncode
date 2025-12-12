<?php

/**
 * Custom Json Encode
 * php version 7
 *
 * @category  JsonEncode
 * @package   CustomJsonEncode
 * @author    Ramesh N Jangid <polygon.co.in@gmail.com>
 * @copyright 2025 Ramesh N Jangid
 * @license   MIT https://opensource.org/license/mit
 * @link      https://github.com/polygoncoin/Microservices
 * @since     Class available since Release 1.0.0
 */

namespace CustomJsonEncode;

use CustomJsonEncode\JsonEncodeObject;

/**
 * Custom Json Encode
 * php version 7
 *
 * @category  JsonEncode
 * @package   CustomJsonEncode
 * @author    Ramesh N Jangid <polygon.co.in@gmail.com>
 * @copyright 2025 Ramesh N Jangid
 * @license   MIT https://opensource.org/license/mit
 * @link      https://github.com/polygoncoin/Microservices
 * @since     Class available since Release 1.0.0
 */
class JsonEncode
{
    /**
     * Temporary Stream
     *
     * @var null|resource
     */
    private $tempStream = null;

    /**
     * Characters that are escaped while creating JSON
     *
     * @var string[]
     */
    private $escapers = array(
        "\\", "\"", "\n", "\r", "\t", "\x08", "\x0c", ' '
    );

    /**
     * Characters that are escaped with for $escapers while creating JSON
     *
     * @var string[]
     */
    private $replacements = array(
        "\\\\", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b", ' '
    );

    /**
     * Array of JsonEncodeObject _objects
     *
     * @var JsonEncodeObject[]
     */
    private $objects = [];

    /**
     * Current JsonEncodeObject object
     *
     * @var null|JsonEncodeObject
     */
    private $currentObject = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        ob_start();
        $this->tempStream = fopen(filename: "php://temp", mode: "w+b");
    }

    /**
     * Write data to temporary stream
     *
     * @param string $data String Data
     *
     * @return void
     */
    public function write($data): void
    {
        fwrite(stream: $this->tempStream, data: $data);
    }

    /**
     * Escape the json string key or value
     *
     * @param string $str json key or value string.
     *
     * @return string
     */
    private function escape($str): string
    {
        if (is_null(value: $str)) {
            return 'null';
        }

        $str = str_replace(
            search: $this->escapers,
            replace: $this->replacements,
            subject: $str
        );

        return '"' . $str . '"';
    }

    /**
     * Encodes both simple and associative array to json
     *
     * @param mixed $data string/array value escaped
     *
     * @return void
     */
    public function encode($data): bool|string
    {
        return (is_array(value: $data)) ?
            json_encode(value: $data) : $this->escape(str: $data);
    }

    /**
     * Append raw json string
     *
     * @param string $json JSON
     *
     * @return void
     */
    public function appendJson(&$json): void
    {
        if ($this->currentObject) {
            $this->write(data: $this->currentObject->comma);
            $this->write(data: $json);
            $this->currentObject->comma = ',';
        }
    }

    /**
     * Append raw json string
     *
     * @param string $key  key of associative array
     * @param string $json JSON
     *
     * @return void
     */
    public function appendKeyJson($key, &$json): void
    {
        if ($this->currentObject && $this->currentObject->mode === 'Object') {
            $this->write(data: $this->currentObject->comma);
            $this->write(data: $this->escape(str: $key) . ':' . $json);
            $this->currentObject->comma = ',';
        }
    }

    /**
     * Add simple array/value as in the json format.
     *
     * @param mixed $value Add value/array in the current Array
     *
     * @return void
     * @throws \Exception
     */
    public function addValue($value): void
    {
        if ($this->currentObject->mode !== 'Array') {
            throw new \Exception('Mode should be Array');
        }
        if ($this->currentObject) {
            $this->write(data: $this->currentObject->comma);
        }
        $this->write(data: $this->encode(data: $value));
        if ($this->currentObject) {
            $this->currentObject->comma = ',';
        }
    }

    /**
     * Add simple array/value as in the json format.
     *
     * @param string $key   Key of associative array
     * @param mixed  $value Add value/array in the current Object
     *
     * @return void
     * @throws \Exception
     */
    public function addKeyValue($key, $value): void
    {
        if ($this->currentObject->mode !== 'Object') {
            throw new \Exception('Mode should be Object');
        }
        if ($this->currentObject) {
            $this->write(data: $this->currentObject->comma);
        }
        $this->write(
            data: $this->escape(str: $key) . ':' . $this->encode(data: $value)
        );
        if ($this->currentObject) {
            $this->currentObject->comma = ',';
        }
    }

    /**
     * Start simple array
     *
     * @param null|string $key Used for creating simple array inside an object
     *
     * @return void
     */
    public function startArray($key = null): void
    {
        if ($this->currentObject) {
            $this->write(data: $this->currentObject->comma);
            array_push($this->objects, $this->currentObject);
        }
        $this->currentObject = new JsonEncodeObject(mode: 'Array');
        if (!is_null(value: $key)) {
            $this->write(data: $this->escape(str: $key) . ':');
        }
        $this->write(data: '[');
    }

    /**
     * End simple array
     *
     * @return void
     */
    public function endArray(): void
    {
        $this->write(data: ']');
        $this->currentObject = null;
        if (count(value: $this->objects) > 0) {
            $this->currentObject = array_pop($this->objects);
            $this->currentObject->comma = ',';
        }
    }

    /**
     * Start simple array
     *
     * @param null|string $key Used for creating object inside an object
     *
     * @return void
     * @throws \Exception
     */
    public function startObject($key = null)
    {
        if ($this->currentObject) {
            if ($this->currentObject->mode === 'Object' && is_null(value: $key)) {
                throw new \Exception(
                    message: 'Object inside an Object should be supported with a Key'
                );
            }
            $this->write(data: $this->currentObject->comma);
            array_push($this->objects, $this->currentObject);
        }
        $this->currentObject = new JsonEncodeObject(mode: 'Object');
        if (!is_null(value: $key)) {
            $this->write(data: $this->escape(str: $key) . ':');
        }
        $this->write(data: '{');
    }

    /**
     * End associative array
     *
     * @return void
     */
    public function endObject(): void
    {
        $this->write(data: '}');
        $this->currentObject = null;
        if (count(value: $this->objects) > 0) {
            $this->currentObject = array_pop($this->objects);
            $this->currentObject->comma = ',';
        }
    }

    /**
     * Stream Json String.
     *
     * @return void
     */
    private function streamJson(): void
    {
        if ($this->tempStream) {
            $this->end();

            //Clean (erase) the contents of the active output buffer and turn it off
            ob_end_clean();

            // rewind the temp stream.
            rewind(stream: $this->tempStream);

            // stream the temp to output
            $outputStream = fopen(filename: "php://output", mode: "w+b");
            stream_copy_to_stream(from: $this->tempStream, to: $outputStream);
            fclose(stream: $outputStream);
            fclose(stream: $this->tempStream);
        }
    }

    /**
     * Checks json was properly closed.
     *
     * @return void
     */
    public function end(): void
    {
        while ($this->currentObject && $this->currentObject->mode) {
            switch ($this->currentObject->mode) {
                case 'Array':
                    $this->endArray();
                    break;
                case 'Object':
                    $this->endObject();
                    break;
            }
        }
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->streamJson();
    }
}
