<?php

namespace Minwork\Helper;

use ArrayAccess;
use BadMethodCallException;

/**
 * Class ArrObj
 * @package Minwork\Helper
 * @method bool has(mixed $keys)
 * @method bool hasKeys(mixed $keys, bool $strict = false)
 * @method mixed get(mixed $keys, $default = null)
 * @method self set(mixed $keys, mixed $value)
 * @method self remove(mixed $keys)
 *
 * @method bool check(mixed|callable $condition, bool $strict = false)
 * @method bool isEmpty()
 * @method bool isAssoc(bool $strict = false)
 * @method bool isNumeric()
 * @method bool isUnique(bool $strict = false)
 * @method bool isNested()
 * @method bool isArrayOfArrays()
 *
 * @method self map(callable $callback, int $mode = Arr::MAP_ARRAY_KEY_VALUE)
 * @method self mapObjects(string $method, ...$args)
 *
 * @method self filterByKeys(mixed $keys, bool $exclude = false)
 * @method self filterObjects(string $method, ...$args)
 *
 * @method self group(string|int $key)
 * @method self groupObjects(string $method, ...$args)
 *
 * @method self orderByKeys(mixed $keys, bool $appendUnmatched = true)
 * @method self sortByKeys(mixed $keys = null, bool $assoc = true)
 * @method self sortObjects(string $method, ...$args)
 *
 * @method self sum(array ...$arrays)
 * @method self diffObjects(array $array, array ...$arrays)
 * @method self intersectObjects(array $array, array ...$arrays)
 *
 * @method self flatten(?int $depth = null, bool $assoc = false)
 * @method self flattenSingle()
 *
 * @method int getDepth()
 * @method self clone()
 * @method mixed random(int $count = 1)
 * @method self shuffle()
 * @method self nth(int $A = 1, int $B = 0)
 * @method self even()
 * @method self odd()
 *
 * @method string|int|null getFirstKey()
 * @method string|int|null getLastKey()
 * @method mixed getFirstValue()
 * @method mixed getLastValue()
 */
class ArrObj
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
}