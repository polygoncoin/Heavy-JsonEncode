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
    private $_tempStream = null;

    /**
     * Characters that are escaped while creating JSON
     *
     * @var string[]
     */
    private $_escapers = array(
        "\\", "\"", "\n", "\r", "\t", "\x08", "\x0c", ' '
    );

    /**
     * Characters that are escaped with for $_escapers while creating JSON
     *
     * @var string[]
     */
    private $_replacements = array(
        "\\\\", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b", ' '
    );

    /**
     * Array of JsonEncodeObject _objects
     *
     * @var JsonEncodeObject[]
     */
    private $_objects = [];

    /**
     * Current JsonEncodeObject object
     *
     * @var null|JsonEncodeObject
     */
    private $_currentObject = null;

    /**
     * JsonEncode constructor
     */
    public function __construct()
    {
        ob_start();
        $this->_tempStream = fopen(filename: "php://temp", mode: "w+b");
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
        fwrite(stream: $this->_tempStream, data: $data);
    }

    /**
     * Escape the json string key or value
     *
     * @param string $str json key or value string.
     *
     * @return string
     */
    private function _escape($str): string
    {
        if (is_null(value: $str)) {
            return 'null';
        }

        $str = str_replace(
            search: $this->_escapers,
            replace: $this->_replacements,
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
            json_encode(value: $data) : $this->_escape(str: $data);
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
        if ($this->_currentObject) {
            $this->write(data: $this->_currentObject->comma);
            $this->write(data: $json);
            $this->_currentObject->comma = ',';
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
        if ($this->_currentObject && $this->_currentObject->mode === 'Object') {
            $this->write(data: $this->_currentObject->comma);
            $this->write(data: $this->_escape(str: $key) . ':' . $json);
            $this->_currentObject->comma = ',';
        }
    }

    /**
     * Add simple array/value as in the json format.
     *
     * @param mixed $value Add value/array in the current Array
     *
     * @return void
     */
    public function addValue($value): void
    {
        if ($this->_currentObject->mode !== 'Array') {
            throw new Exception('Mode should be Array');
        }
        if ($this->_currentObject) {
            $this->write(data: $this->_currentObject->comma);
        }
        $this->write(data: $this->encode(data: $value));
        if ($this->_currentObject) {
            $this->_currentObject->comma = ',';
        }
    }

    /**
     * Add simple array/value as in the json format.
     *
     * @param string $key   Key of associative array
     * @param mixed  $value Add value/array in the current Object
     *
     * @return void
     */
    public function addKeyValue($key, $value): void
    {
        if ($this->_currentObject->mode !== 'Object') {
            throw new Exception('Mode should be Object');
        }
        if ($this->_currentObject) {
            $this->write(data: $this->_currentObject->comma);
        }
        $this->write(
            data: $this->_escape(str: $key) . ':' . $this->encode(data: $value)
        );
        if ($this->_currentObject) {
            $this->_currentObject->comma = ',';
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
        if ($this->_currentObject) {
            $this->write(data: $this->_currentObject->comma);
            array_push($this->_objects, $this->_currentObject);
        }
        $this->_currentObject = new JsonEncodeObject(mode: 'Array');
        if (!is_null(value: $key)) {
            $this->write(data: $this->_escape(str: $key) . ':');
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
        $this->_currentObject = null;
        if (count(value: $this->_objects)>0) {
            $this->_currentObject = array_pop($this->_objects);
            $this->_currentObject->comma = ',';
        }
    }

    /**
     * Start simple array
     *
     * @param null|string $key Used for creating object inside an object
     *
     * @return void
     */
    public function startObject($key = null)
    {
        if ($this->_currentObject) {
            if ($this->_currentObject->mode === 'Object' && is_null(value: $key)) {
                throw new \Exception(
                    message: 'Object inside an Object should be supported with a Key'
                );
            }
            $this->write(data: $this->_currentObject->comma);
            array_push($this->_objects, $this->_currentObject);
        }
        $this->_currentObject = new JsonEncodeObject(mode: 'Object');
        if (!is_null(value: $key)) {
            $this->write(data: $this->_escape(str: $key) . ':');
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
        $this->_currentObject = null;
        if (count(value: $this->_objects)>0) {
            $this->_currentObject = array_pop($this->_objects);
            $this->_currentObject->comma = ',';
        }
    }

    /**
     * Stream Json String.
     *
     * @return void
     */
    private function _streamJson(): void
    {
        if ($this->_tempStream) {
            $this->end();

            //Clean (erase) the contents of the active output buffer and turn it off
            ob_end_clean();

            // rewind the temp stream.
            rewind(stream: $this->_tempStream);

            // stream the temp to output
            $outputStream = fopen(filename: "php://output", mode: "w+b");
            stream_copy_to_stream(from: $this->_tempStream, to: $outputStream);
            fclose(stream: $outputStream);
            fclose(stream: $this->_tempStream);
        }
    }

    /**
     * Checks json was properly closed.
     *
     * @return void
     */
    public function end(): void
    {
        while ($this->_currentObject && $this->_currentObject->mode) {
            switch ($this->_currentObject->mode) {
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
        $this->_streamJson();
    }
}
