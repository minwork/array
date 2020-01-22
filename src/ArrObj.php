<?php

namespace Minwork\Helper;

use ArrayAccess;
use ArrayIterator;
use BadMethodCallException;
use Countable;
use Iterator;
use IteratorAggregate;

/**
 * Class ArrObj
 * @package Minwork\Helper
 *
 * --------------------------------------------------------------------------------
 *
 * @method bool has(mixed $keys)
 * @see Arr::has()
 *
 * @method bool hasKeys(mixed $keys, bool $strict = false)
 * @see Arr::hasKeys()
 *
 * @method mixed get(mixed $keys, $default = null)
 * @see Arr::get()
 *
 * @method ArrObj set(mixed $keys, mixed $value)
 * @see Arr::set()
 *
 * @method ArrObj remove(mixed $keys)
 * @see Arr::remove()
 *
 * ---------------------------------------------------------------------------------
 *
 * @method bool check(mixed|callable $condition, bool $strict = false)
 * @see Arr::check()
 *
 * @method bool isEmpty()
 * @see Arr::isEmpty()
 *
 * @method bool isAssoc(bool $strict = false)
 * @see Arr::isAssoc()
 *
 * @method bool isNumeric()
 * @see Arr::isNumeric()
 *
 * @method bool isUnique(bool $strict = false)
 * @see Arr::isUnique()
 *
 * @method bool isNested()
 * @see Arr::isNested()
 *
 * @method bool isArrayOfArrays()
 * @see Arr::isArrayOfArrays()
 *
 * ---------------------------------------------------------------------------------
 *
 * @method ArrObj map(callable $callback, int $mode = Arr::MAP_ARRAY_KEY_VALUE)
 * @see Arr::map()
 *
 * @method ArrObj mapObjects(string $method, ...$args)
 * @see Arr::mapObjects()
 *
 * ---------------------------------------------------------------------------------
 *
 * @method ArrObj each(callable $callback, int $mode = Arr::EACH_VALUE)
 * @see Arr::each()
 *
 * ---------------------------------------------------------------------------------
 *
 * @method ArrObj filter(?callable $callback = null, int $flag = 0)
 * @see Arr::filter()
 *
 * @method ArrObj filterByKeys(mixed $keys, bool $exclude = false)
 * @see Arr::filterByKeys()
 *
 * @method ArrObj filterObjects(string $method, ...$args)
 * @see Arr::filterObjects()
 *
 * ---------------------------------------------------------------------------------
 *
 * @method ArrObj group(string|int $key)
 * @see Arr::group()
 *
 * @method ArrObj groupObjects(string $method, ...$args)
 * @see Arr::groupObjects()
 *
 * ---------------------------------------------------------------------------------
 *
 * @method ArrObj find(array|IteratorAggregate|Iterator $array, callable $condition, string $return)
 * @see Arr::find()
 *
 * ---------------------------------------------------------------------------------
 *
 * @method ArrObj orderByKeys(mixed $keys, bool $appendUnmatched = true)
 * @see Arr::orderByKeys()
 *
 * @method ArrObj sortByKeys(mixed $keys = null, bool $assoc = true)
 * @see Arr::sortByKeys()
 *
 * @method ArrObj sortObjects(string $method, ...$args)
 * @see Arr::sortObjects()
 *
 * ---------------------------------------------------------------------------------
 *
 * @method ArrObj sum(array ...$arrays)
 * @see Arr::sum()
 *
 * @method ArrObj diffObjects(array $array, array ...$arrays)
 * @see Arr::diffObjects()
 *
 * @method ArrObj intersectObjects(array $array, array ...$arrays)
 * @see Arr::intersectObjects()
 *
 * ---------------------------------------------------------------------------------
 *
 * @method ArrObj flatten(?int $depth = null, bool $assoc = false)
 * @see Arr::flatten()
 *
 * @method ArrObj flattenSingle()
 * @see Arr::flattenSingle()
 *
 * ---------------------------------------------------------------------------------
 *
 * @method int getDepth()
 * @see Arr::getDepth()
 *
 * @method ArrObj clone()
 * @see Arr::clone()
 *
 * @method mixed random(int $count = 1)
 * @see Arr::random()
 *
 * @method ArrObj shuffle()
 * @see Arr::shuffle()
 *
 * @method ArrObj nth(int $A = 1, int $B = 0)
 * @see Arr::nth()
 *
 * @method ArrObj even()
 * @see Arr::even()
 *
 * @method ArrObj odd()
 * @see Arr::odd()
 *
 * ---------------------------------------------------------------------------------
 *
 * @method string|int|null getFirstKey()
 * @see Arr::getFirstKey()
 *
 * @method string|int|null getLastKey()
 * @see Arr::getLastKey()
 *
 * @method mixed getFirstValue()
 * @see Arr::getFirstValue()
 *
 * @method mixed getLastValue()
 * @see Arr::getLastValue()
 *
 * ---------------------------------------------------------------------------------
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
        'each',
        'filter',
        'filterByKeys',
        'filterObjects',
        'group',
        'groupObjects',
        'find',
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
        'each',
        'filter',
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