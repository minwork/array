<?php

namespace Minwork\Helper;

use ArrayAccess;
use ArrayIterator;
use BadMethodCallException;
use Countable;
use IteratorAggregate;

/**
 * Class ArrObj
 * @package Minwork\Helper
 * @method bool has(mixed $keys)
 * @method bool hasKeys(mixed $keys, bool $strict = false)
 * @method mixed get(mixed $keys, $default = null)
 * @method ArrObj set(mixed $keys, mixed $value)
 * @method ArrObj remove(mixed $keys)
 *
 * @method bool check(mixed|callable $condition, bool $strict = false)
 * @method bool isEmpty()
 * @method bool isAssoc(bool $strict = false)
 * @method bool isNumeric()
 * @method bool isUnique(bool $strict = false)
 * @method bool isNested()
 * @method bool isArrayOfArrays()
 *
 * @method ArrObj map(callable $callback, int $mode = Arr::MAP_ARRAY_KEY_VALUE)
 * @method ArrObj mapObjects(string $method, ...$args)
 *
 * @method ArrObj filterByKeys(mixed $keys, bool $exclude = false)
 * @method ArrObj filterObjects(string $method, ...$args)
 *
 * @method ArrObj group(string|int $key)
 * @method ArrObj groupObjects(string $method, ...$args)
 *
 * @method ArrObj orderByKeys(mixed $keys, bool $appendUnmatched = true)
 * @method ArrObj sortByKeys(mixed $keys = null, bool $assoc = true)
 * @method ArrObj sortObjects(string $method, ...$args)
 *
 * @method ArrObj sum(array ...$arrays)
 * @method ArrObj diffObjects(array $array, array ...$arrays)
 * @method ArrObj intersectObjects(array $array, array ...$arrays)
 *
 * @method ArrObj flatten(?int $depth = null, bool $assoc = false)
 * @method ArrObj flattenSingle()
 *
 * @method int getDepth()
 * @method ArrObj clone()
 * @method mixed random(int $count = 1)
 * @method ArrObj shuffle()
 * @method ArrObj nth(int $A = 1, int $B = 0)
 * @method ArrObj even()
 * @method ArrObj odd()
 *
 * @method string|int|null getFirstKey()
 * @method string|int|null getLastKey()
 * @method mixed getFirstValue()
 * @method mixed getLastValue()
 */
class ArrObj implements IteratorAggregate, ArrayAccess, Countable
{
    const METHODS = [
        'has',
        'hasKeys',
        'get',
        'set',
        'remove',
        'check',
        'isEmpty',
        'isAssoc',
        'isNumeric',
        'isUnique',
        'isNested',
        'isArrayOfArrays',
        'map',
        'mapObjects',
        'filterByKeys',
        'filterObjects',
        'group',
        'groupObjects',
        'orderByKeys',
        'sortByKeys',
        'sortObjects',
        'sum',
        'diffObjects',
        'intersectObjects',
        'flatten',
        'flattenSingle',
        'getDepth',
        'clone',
        'random',
        'shuffle',
        'nth',
        'even',
        'odd',
        'getFirstKey',
        'getLastKey',
        'getFirstValue',
        'getLastValue',
    ];

    const CHAINABLE_METHODS = [
        'set',
        'remove',
        'map',
        'mapObjects',
        'filterByKeys',
        'filterObjects',
        'group',
        'groupObjects',
        'orderByKeys',
        'sortByKeys',
        'sortObjects',
        'sum',
        'diffObjects',
        'intersectObjects',
        'flatten',
        'flattenSingle',
        'clone',
        'shuffle',
        'nth',
        'even',
        'odd',
    ];

    protected $array;

    /**
     * ArrObj constructor.
     * @param array|ArrayAccess $array
     */
    public function __construct($array = [])
    {
        $this->setArray($array);
    }

    public function __call($name, $arguments)
    {
        if (!in_array($name, self::METHODS)) {
            throw new BadMethodCallException("Method {$name} does not exists in Arr class or cannot be called on supplied array");
        }

        $result = Arr::$name($this->array, ...$arguments);

        if (in_array($name, self::CHAINABLE_METHODS)) {
            return $this->setArray($result);
        }
        
        return $result;
    }

    /**
     * @return array|ArrayAccess
     */
    public function getArray()
    {
        return $this->array;
    }

    /**
     * @param array|ArrayAccess $array
     * @return ArrObj
     */
    public function setArray($array): self
    {
        $this->array = $array;
        return $this;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->array);
    }

    public function offsetGet($offset)
    {
        return $this->array[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        return $this->array[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if (isset($this->array[$offset])) {
            unset($this->array[$offset]);
        }
    }

    public function getIterator()
    {
        return new ArrayIterator($this->array);
    }

    public function count()
    {
        return count($this->array);
    }
}