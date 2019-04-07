<?php
/*
 * This file is part of the Minwork package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Minwork\Helper;

use ArrayAccess;
use InvalidArgumentException;
use ReflectionFunction;
use ReflectionMethod;
use Throwable;

/**
 * Pack of advanced array functions - specifically for associative arrays and arrays of objects
 *
 * @author Krzysztof Kalkhoff
 *
 */
class Arr
{
    // Flags
    const FORCE_ARRAY_ALL = 1;
    const FORCE_ARRAY_PRESERVE_NULL = 2;
    const FORCE_ARRAY_PRESERVE_OBJECTS = 4;
    const FORCE_ARRAY_PRESERVE_ARRAY_OBJECTS = 8;

    private const AUTO_INDEX_KEY = '[]';

    /*--------------------------------------------------------------------------------------*\
     |                                        Common                                        |
     |    ******************************************************************************    |
     | Basic operations used by other methods                                               |
    \*--------------------------------------------------------------------------------------*/


    /**
     * Convert variable into normalized array of keys<br>
     * <br>
     * Transforms 'key1.key2.key3' strings into ['key1','key2','key3']<br>
     * <br>
     * When array is supplied, this function preserve only not empty strings and integers<br>
     * <pre>
     * ['', 'test', 5.5, null, 0] -> ['test', 0]
     * </pre>
     *
     * @param mixed $keys
     * @return array
     */
    public static function getKeysArray($keys): array
    {
        if (is_string($keys)) {
            return empty($keys) ? [] : explode('.', $keys);
        }
        return is_null($keys) ? [] : array_filter(array_values(self::forceArray($keys)), function ($value) {
            return $value !== null && $value !== '' && (is_string($value) || is_int($value));
        });
    }

    /**
     * Check if array has specified keys
     *
     * @param array $array
     * @param mixed $keys
     *            See getKeysArray method
     * @param bool $strict
     *            If array must have all of specified keys
     * @return bool
     * @see \Minwork\Helper\Arr::getKeysArray()
     */
    public static function hasKeys(array $array, $keys, bool $strict = false): bool
    {
        foreach (self::getKeysArray($keys) as $key) {
            if (array_key_exists($key, $array) && !$strict) {
                return true;
            } elseif (!array_key_exists($key, $array) && $strict) {
                return false;
            }
        }
        return $strict ? true : false;
    }

    /**
     * Get nested element of an array or object implementing array access
     *
     * @param array|ArrayAccess $array
     *            Array or object implementing array access to get element from
     * @param mixed $keys
     *            See getKeysArray method
     * @param mixed $default
     *            Default value if element was not found
     * @return null|mixed
     * @see \Minwork\Helper\Arr::getKeysArray()
     */
    public static function getNestedElement($array, $keys, $default = null)
    {
        $keys = self::getKeysArray($keys);
        foreach ($keys as $key) {
            if (!is_array($array) && !$array instanceof ArrayAccess) {
                return $default;
            }
            if (($array instanceof ArrayAccess && $array->offsetExists($key)) || array_key_exists($key, $array)) {
                $array = $array[$key];
            } else {
                return $default;
            }
        }
        return $array;
    }

    /**
     * Set array element specified by keys to the desired value (create missing keys if necessary)
     *
     * @see \Minwork\Helper\Arr::getKeysArray
     * @param array $array
     * @param mixed $keys Keys needed to access desired array element (for possible formats see getKeysArray method)
     * @param mixed $value Value to set
     * @return array Copy of an array with element set
     * @see \Minwork\Helper\Arr::getKeysArray()
     */
    public static function setNestedElement(array $array, $keys, $value): array
    {
        $result = self::clone($array);
        $keysArray = self::getKeysArray($keys);
        $tmp = &$result;

        while (count($keysArray) > 0) {
            $key = array_shift($keysArray);
            if (!is_array($tmp)) {
                $tmp = [];
            }
            if ($key === self::AUTO_INDEX_KEY) {
                $tmp[] = null;
                end($tmp);
                $key = key($tmp);
            }
            $tmp = &$tmp[$key];
        }
        $tmp = $value;

        return $result;
    }

    /*--------------------------------------------------------------------------------------*\
     |                                      Validation                                      |
     |    ******************************************************************************    |
     | Flexible check method and various specific checks                                    |
    \*--------------------------------------------------------------------------------------*/

    /**
     * Check if every element of an array meets specified condition
     *
     * @param array $array
     * @param mixed|callable $condition Can be either single value to compare every array value to or callable (which takes value as first argument and key as second) that performs check
     * @param bool $strict In case $condition is callable check if it result is exactly <code>true</code> otherwise if it is equal both by value and type to supplied $condition
     * @return bool
     */
    public static function check(array $array, $condition, bool $strict = false): bool
    {
        if (is_callable($condition)) {
            try {
                $reflection = is_array($condition) ?
                    new ReflectionMethod($condition[0], $condition[1]) :
                    new ReflectionMethod($condition);

                $paramsCount = $reflection->getNumberOfParameters();
            } catch (Throwable $e) {
                try {
                    $reflection = new ReflectionFunction($condition);
                    $paramsCount = $reflection->getNumberOfParameters();
                } catch (Throwable $exception) { // @codeCoverageIgnore
                    $paramsCount = 2; // @codeCoverageIgnore
                }
            }
        }

        foreach ($array as $key => $value) {
            if (is_callable($condition)) {
                /** @var int $paramsCount */
                $result = $paramsCount == 1 ? call_user_func($condition, $value) : call_user_func($condition, $value, $key);

                if ($strict ? $result !== true : !$result) {
                    return false;
                }
            } else {
                if ($strict ? $value !== $condition : $value != $condition) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Recursively check if all of array values match empty condition
     *
     * @param array $array
     * @return boolean
     */
    public static function isEmpty($array): bool
    {
        if (is_array($array)) {
            foreach ($array as $v) {
                if (!self::isEmpty($v)) {
                    return false;
                }
            }
        } elseif (!empty($array)) {
            return false;
        }

        return true;
    }

    /**
     * Check if array is associative
     *
     * @param array $array
     * @param bool $strict
     *            If false then this function will match any array that doesn't contain integer keys
     * @return boolean
     */
    public static function isAssoc(array $array, bool $strict = false): bool
    {
        if (empty($array)) {
            return false;
        }

        if ($strict) {
            return array_keys($array) !== range(0, count($array) - 1);
        } else {
            foreach (array_keys($array) as $key) {
                if (!is_int($key)) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * Check if array contain only numeric values
     *
     * @param array $array
     * @return bool
     */
    public static function isNumeric(array $array): bool
    {
        return self::check($array, 'is_numeric');
    }

    /**
     * Check if array values are unique
     *
     * @param array $array
     * @param bool $strict If it should also compare type
     * @return bool
     */
    public static function isUnique(array $array, bool $strict = false): bool
    {
        if ($strict) {
            foreach ($array as $key => $value) {
                $keys = array_keys($array, $value, true);
                if (count($keys) > 1 || $keys[0] !== $key) {
                    return false;
                }
            }
            return true;
        }
        return array_unique(array_values($array), SORT_REGULAR) === array_values($array);
    }

    /**
     * Check if every array element is array
     *
     * @param array $array
     * @return bool
     */
    public static function isArrayOfArrays(array $array): bool
    {
        // If empty array
        if (count($array) === 0) {
            return false;
        }
        foreach ($array as $element) {
            if (!is_array($element)) {
                return false;
            }
        }
        return true;
    }

    /*--------------------------------------------------------------------------------------*\
     |                                      Manipulation                                    |
     |    ******************************************************************************    |
     | Well known methods (like map, filter, group etc.) in 2 variants: regular and objects |
    \*--------------------------------------------------------------------------------------*/

    /**
     * Applies a callback to the elements of given array
     *
     * @param callable $callback Callback to run for each element of array (arguments: key, value)
     * @param array $array
     * @return array
     */
    public static function map(callable $callback, array $array): array
    {
        $return = [];

        foreach ($array as $key => $value) {
            $return[$key] = $callback($key, $value);
        }

        return $return;
    }

    /**
     * Map array of object to values returned from objects method
     *
     * This method leaves values other than objects intact
     *
     * @param array $objects Array of objects
     * @param string $method Object method name
     * @param mixed ...$args Method arguments
     * @return array
     */
    public static function mapObjects(array $objects, string $method, ...$args): array
    {
        $return = [];

        foreach ($objects as $key => $value) {
            if (is_object($value)) {
                $return[$key] = $value->$method(...$args);
            } else {
                $return[$key] = $value;
            }
        }

        return $return;
    }

    /**
     * Filter array values by preserving only those which keys are present in array obtained from $keys variable
     *
     * @param array $array
     * @param mixed $keys See getKeysArray function
     * @param bool $exclude If values matching $keys should be excluded from returned array
     * @return array
     * @see \Minwork\Helper\Arr::getKeysArray()
     */
    public static function filterByKeys(array $array, $keys, bool $exclude = false): array
    {
        if (is_null($keys)) {
            return $array;
        }
        $keysArray = self::getKeysArray($keys);
        if (empty($keysArray)) {
            return $exclude ? $array : [];
        }
        return $exclude ? array_diff_key($array, array_flip($keysArray)) : array_intersect_key($array, array_flip($keysArray));
    }

    /**
     * Filter objects array using return value of specified method.<br>
     * <br>
     * This method also filter values other than objects by standard boolean comparison
     *
     * @param array $objects Array of objects
     * @param string $method Object method name
     * @param mixed ...$args Method arguments
     * @return array
     */
    public static function filterObjects(array $objects, string $method, ...$args): array
    {
        $return = [];

        foreach ($objects as $key => $value) {
            if (is_object($value)) {
                if ($value->$method(...$args)) {
                    $return[$key] = $value;
                }
            } elseif ($value) {
                $return[$key] = $value;
            }
        }

        return $return;
    }

    /**
     * Group array of arrays by value of element with specified key<br>
     * <br>
     * <u>Example</u><br><br>
     * <pre>
     * Arr::group([
     *   'a' => [ 'key1' => 'test1', 'key2' => 1 ],
     *   'b' => [ 'key1' => 'test1', 'key2' => 2 ],
     *   2 => [ 'key1' => 'test2', 'key2' => 3 ]
     * ], 'key1')
     * </pre>
     * will produce
     * <pre>
     * [
     *   'test1' => [
     *     'a' => [ 'key1' => 'test1', 'key2' => 1 ],
     *     'b' => [ 'key1' => 'test1', 'key2' => 2 ]
     *   ],
     *   'test2' => [
     *     2 => [ 'key1' => 'test2', 'key2' => 3 ]
     *   ],
     * ]
     * </pre>
     * <br>
     * If key does not exists in one of the arrays, this array will be excluded from result
     * @param array $array Array of arrays
     * @param string|int $key Key on which to group arrays
     * @return array
     */
    public static function group(array $array, $key): array
    {
        $return = [];

        // If not array of arrays return untouched
        if (!self::isArrayOfArrays($array)) {
            return $array;
        }

        foreach ($array as $k => $v) {
            if (array_key_exists($key, $v)) {
                $return[$v[$key]][$k] = $v;
            }

        }

        return $return;
    }

    /**
     * Group array of objects by value returned from specified method<br>
     * <br>
     * <u>Example</u><br>
     * Let's say we have a list of Foo objects [Foo1, Foo2, Foo3] and all of them have method bar which return string.<br>
     * If method bar return duplicate strings then all keys will contain list of corresponding objects like this:<br>
     * <pre>
     * ['string1' => [Foo1], 'string2' => [Foo2, Foo3]]
     * </pre>
     *
     * @param array $objects Array of objects
     * @param string $method Object method name
     * @param mixed ...$args Method arguments
     * @return array
     */
    public static function groupObjects(array $objects, string $method, ...$args): array
    {
        $return = [];

        foreach ($objects as $key => $object) {
            if (is_object($object)) {
                $return[$object->$method(...$args)][$key] = $object;
            }
        }

        return $return;
    }

    /**
     * Order associative array according to supplied keys order
     * Keys that are not present in $keys param will be appended to the end of an array preserving supplied order.
     * @param array $array
     * @param mixed $keys See getKeysArray method
     * @param bool $appendUnmatched If values not matched by supplied keys should be appended to the end of an array
     * @see \Minwork\Helper\Arr::getKeysArray()
     * @return array
     */
    public static function orderByKeys(array $array, $keys, bool $appendUnmatched = true): array
    {
        $return = [];

        foreach (self::getKeysArray($keys) as $key) {
            if (array_key_exists($key, $array)) {
                $return[$key] = $array[$key];
            }
        }

        return $appendUnmatched ? $return + self::filterByKeys($array, $keys, true) : $return;
    }

    /**
     * Sort array of arrays using value specified by key(s)
     *
     * @param array $array Array of arrays
     * @param mixed $keys Keys in format specified by getKeysArray method or null to perform sort using 0-depth keys
     * @param bool $assoc If sorting should preserve main array keys (default: true)
     * @return array Sorted array
     * @see \Minwork\Helper\Arr::getKeysArray()
     */
    public static function sortByKeys(array $array, $keys = null, bool $assoc = true): array
    {
        $return = $array;
        $method = $assoc ? 'uasort' : 'usort';

        $method($return, function ($a, $b) use ($keys) {
            return self::getNestedElement($a, $keys) <=> self::getNestedElement($b, $keys);
        });

        return $return;
    }

    /**
     * Sum associative arrays by their keys into one array
     *
     * @param array ...$arrays Can be either list of an arrays or single array of arrays
     * @return array
     */
    public static function sum(array ...$arrays): array
    {
        $return = [];

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                $return[$key] = ($return[$key] ?? 0) + floatval($value);
            }
        }

        return $return;
    }

    /**
     * Differentiate two or more arrays of objects
     *
     * @param array $array1
     * @param array $array2
     * @param array[] $arrays
     * @return array
     */
    public static function diffObjects(array $array1, array $array2, array ...$arrays): array
    {
        $arguments = $arrays;
        array_unshift($arguments, $array1, $array2);
        array_push($arguments, function ($obj1, $obj2) {
            return strcmp(spl_object_hash($obj1), spl_object_hash($obj2));
        });

        return array_udiff(...$arguments);
    }

    /**
     * Flatten array of arrays to a n-depth array
     *
     * @param array $array
     * @param int|null $depth How many levels of nesting will be flatten. By default every nested array will be flatten.
     * @param bool $assoc If this param is set to true, this method will try to preserve as much string keys as possible.
     * In case of conflicting key name, value will be added with automatic numeric key.<br>
     * <br>
     * <i>Warning:</i> This method may produce unexpected results when array has numeric keys and $assoc param is set to true
     * @return array
     */
    public static function flatten(array $array, ?int $depth = null, bool $assoc = false): array
    {
        $return = [];

        $addElement = function ($key, $value) use (&$return, $assoc) {
            if (!$assoc || array_key_exists($key, $return)) {
                $return[] = $value;
            } else {
                $return[$key] = $value;
            }
        };

        foreach ($array as $key => $value) {
            if (is_array($value) && (is_null($depth) || $depth >= 1)) {
                foreach (self::flatten($value, is_null($depth) ? $depth : $depth - 1, $assoc) as $k => $v) {
                    $addElement($k, $v);
                }
            } else {
                $addElement($key, $value);
            }
        }

        return $return;
    }

    /**
     * Flatten single element arrays (also nested single element arrays)<br>
     * Let's say we have an array like this:<br>
     * <pre>
     * ['foo' => ['bar'], 'foo2' => ['bar2', 'bar3' => ['foo4']]
     * </pre>
     * then we have result:<br>
     * <pre>
     * ['foo' => 'bar', 'foo2' => ['bar2', 'bar3' => 'foo4']]
     * </pre>
     *
     * @param array $array
     * @return array
     */
    public static function flattenSingle(array $array): array
    {
        $return = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (count($value) === 1) {
                    $return[$key] = reset($value);
                } else {
                    $return[$key] = self::flattenSingle($value);
                }
            } else {
                $return[$key] = $value;
            }
        }

        return $return;
    }

    /*--------------------------------------------------------------------------------------*\
     |                                        Utility                                       |
     |    ******************************************************************************    |
     | Other useful methods                                                                 |
    \*--------------------------------------------------------------------------------------*/

    /**
     * Create multidimensional array using either first param as config of keys and values
     * or separate keys and values arrays
     *
     * @param array $keys If values are not specified, array will be created from this param keys (optionally dot formatted) and values. Otherwise it is used as array of keys (both dot and array notation possible)
     * @param array|null $values [optional] Values for new array
     * @return array
     */
    public static function createMulti(array $keys, ?array $values = null): array
    {
        if (is_null($values)) {
            $values = array_values($keys);
            $keys = array_keys($keys);
        }

        if (count($keys) !== count($values)) {
            throw new InvalidArgumentException('Keys and values arrays must have same amount of elements');
        }

        // Reset array indexes
        $keys = array_values($keys);
        $values = array_values($values);

        $array = [];

        foreach ($keys as $index => $key) {
            $array = self::setNestedElement($array, $key, $values[$index]);
        }

        return $array;
    }

    /**
     * Make variable an array (according to flag settings)
     *
     * @param mixed $var
     * @param int $flag Set flag(s) to preserve specific values from being converted to array
     * @return array
     */
    public static function forceArray($var, int $flag = self::FORCE_ARRAY_ALL)
    {
        if (!is_array($var)) {
            if ($flag & self::FORCE_ARRAY_ALL) {
                return [$var];
            }
            if (is_object($var)) {
                if ($flag & self::FORCE_ARRAY_PRESERVE_OBJECTS) {
                    return $var;
                }
                if (($flag & self::FORCE_ARRAY_PRESERVE_ARRAY_OBJECTS) && $var instanceof ArrayAccess) {
                    return $var;
                }
            }
            if (is_null($var) && ($flag & self::FORCE_ARRAY_PRESERVE_NULL)) {
                return $var;
            }

            return [$var];
        }
        return $var;
    }


    /**
     * Copy array and clone every object inside it
     *
     * @param array $array
     * @return array
     */
    public static function clone(array $array): array
    {
        $cloned = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $cloned[$key] = self::clone($value);
            } elseif (is_object($value)) {
                $cloned[$key] = clone $value;
            } else {
                $cloned[$key] = $value;
            }
        }
        return $cloned;
    }

    /**
     * Get random array value(s)
     *
     * @param array $array
     * @param int $count If equal to 1 than directly returns value or array of values otherwise
     * @throws InvalidArgumentException
     * @return mixed
     */
    public static function random(array $array, int $count = 1)
    {
        if (empty($array)) {
            return null;
        }

        if ($count > ($arrayCount = count($array)) || $count < 1) {
            throw new InvalidArgumentException("Count must be a number between 1 and $arrayCount");
        }

        return $count == 1 ? $array[array_rand($array)] : array_intersect_key($array, array_flip(array_rand($array, $count) ?? []));
    }

    /**
     * Shuffle array preserving keys and returning new shuffled array
     *
     * @param array $array
     * @return array
     */
    public static function shuffle(array $array): array
    {
        $return = [];
        $keys = array_keys($array);

        shuffle($keys);

        foreach ($keys as $key) {
            $return[$key] = $array[$key];
        }

        return $return;
    }

    /**
     * Gets array elements with index matching condition $An + $B (preserving original keys)
     *
     * @see \Minwork\Helper\Arr::even()
     * @see \Minwork\Helper\Arr::odd()
     * @param array $array
     * @param int $A
     * @param int $B
     * @return array
     */
    public static function nth(array $array, int $A = 1, int $B = 0): array
    {
        $keys = [];

        for ($i = $B; $i < count($array); $i += $A) {
            $keys[] = $i;
        }
        return self::filterByKeys($array, self::filterByKeys(array_keys($array), $keys));
    }

    /**
     * Get even array values - alias for <i>nth</i> method with $A = 2
     *
     * @param array $array
     * @return array
     */
    public static function even(array $array): array
    {
        return self::nth($array, 2);
    }

    /**
     * Get odd array values - alias for <i>nth</i> method with $A = 2 and $B = 1
     *
     * @param array $array
     * @return array
     */
    public static function odd(array $array): array
    {
        return self::nth($array, 2, 1);
    }


}