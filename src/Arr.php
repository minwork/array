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
use Iterator;
use IteratorAggregate;
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

    /**
     * Map array using callback in form of function($key, $value)
     */
    const MAP_ARRAY_KEY_VALUE = 1;
    /**
     * Map array using callback in form of function($value, $key1, $key2, ...)
     */
    const MAP_ARRAY_VALUE_KEYS_LIST = 2;
    /**
     * Map array using callback in form of function(array $keys, $value)
     */
    const MAP_ARRAY_KEYS_ARRAY_VALUE = 4;
    /**
     * Map array using callback in form of function($value, $key)
     */
    const MAP_ARRAY_VALUE_KEY = 8;

    /**
     * Iterate using callback in form of function($value)
     */
    const EACH_VALUE = 0;
    /**
     * Iterate using callback in form of function($key, $value)
     */
    const EACH_KEY_VALUE = 1;
    /**
     * Iterate using callback in form of function($value, $key1, $key2, ...)
     */
    const EACH_VALUE_KEYS_LIST = 2;
    /**
     * Iterate using callback in form of function(array $keys, $value)
     */
    const EACH_KEYS_ARRAY_VALUE = 3;
    /**
     * Iterate using callback in form of function($value, $key)
     */
    const EACH_VALUE_KEY = 4;

    const UNPACK_ALL = 1;
    /**
     * Preserve arrays with highest nesting level (if they are not assoc) as element values instead of unpacking them
     */
    const UNPACK_PRESERVE_LIST_ARRAY = 2;
    /**
     * Preserve arrays with highest nesting level (if they are assoc) as element values instead of unpacking them
     */
    const UNPACK_PRESERVE_ASSOC_ARRAY = 4;
    /**
     * Preserve all arrays with highest nesting level as element values instead of unpacking them
     */
    const UNPACK_PRESERVE_ARRAY = 8;

    /**
     * Return value of array element matching find condition
     */
    const FIND_RETURN_VALUE = 'value';
    /**
     * Return key of array element matching find condition
     */
    const FIND_RETURN_KEY = 'key';
    /**
     * Return array of all values (preserving original keys) of array elements matching find condition
     */
    const FIND_RETURN_ALL = 'all';

    private const AUTO_INDEX_KEY = '[]';
    private const KEY_SEPARATOR = '.';

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
            return empty($keys) ? [] : explode(self::KEY_SEPARATOR, $keys);
        }
        return is_null($keys) ? [] : array_filter(array_values(self::forceArray($keys)), function ($value) {
            return $value !== null && $value !== '' && (is_string($value) || is_int($value));
        });
    }

    /**
     * Check if specified (nested) key(s) exists in array
     *
     * @param array $array
     * @param mixed $keys Keys needed to access desired array element (for possible formats see getKeysArray method)
     * @return bool
     * @see Arr::getKeysArray()
     */
    public static function has(array $array, $keys): bool
    {
        $keysArray = self::getKeysArray($keys);

        if (empty($keysArray)) {
            return false;
        }

        $tmp = $array;

        foreach ($keysArray as $key) {
            if (!array_key_exists($key, $tmp)) {
                return false;
            }
            $tmp = $tmp[$key];
        }

        return true;
    }

    /**
     * Check if array has list of specified keys
     *
     * @param array $array
     * @param mixed $keys Keys needed to access desired array element (for possible formats see getKeysArray method)
     * @param bool $strict If array must have all of specified keys
     * @return bool
     * @see Arr::getKeysArray()
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
     * Alias of Arr::getNestedElement
     *
     * @param array|ArrayAccess $array Array or object implementing array access to get element from
     * @param mixed $keys Keys needed to access desired array element (for possible formats see getKeysArray method)
     * @param mixed $default Default value if element was not found
     * @return null|mixed
     * @see Arr::getNestedElement()
     */
    public static function get($array, $keys, $default = null)
    {
        return self::getNestedElement($array, $keys, $default);
    }

    /**
     * Get nested element of an array or object implementing array access
     *
     * @param array|ArrayAccess $array Array or object implementing array access to get element from
     * @param mixed $keys Keys needed to access desired array element (for possible formats see getKeysArray method)
     * @param mixed $default Default value if element was not found
     * @return null|mixed
     * @see Arr::getKeysArray()
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
     * Alias of Arr::setNestedElement
     *
     * @param array $array
     * @param mixed $keys Keys needed to access desired array element (for possible formats see getKeysArray method)
     * @param mixed $value Value to set
     * @return array Copy of an array with element set
     * @see Arr::setNestedElement()
     */
    public static function set(array $array, $keys, $value): array
    {
        return self::setNestedElement($array, $keys, $value);
    }

    /**
     * Set array element specified by keys to the desired value (create missing keys if necessary)
     *
     * @param array $array
     * @param mixed $keys Keys needed to access desired array element (for possible formats see getKeysArray method)
     * @param mixed $value Value to set
     * @return array Copy of an array with element set
     * @see Arr::getKeysArray()
     */
    public static function setNestedElement(array $array, $keys, $value): array
    {
        $result = $array;
        $keysArray = self::getKeysArray($keys);

        // If no keys specified then preserve array
        if (empty($keysArray)) {
            return $result;
        }

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

    /**
     * Remove element inside array at path specified by keys
     *
     * @param array $array
     * @param mixed $keys Keys needed to access desired array element (for possible formats see getKeysArray method)
     * @return array
     * @see Arr::getKeysArray()
     */
    public static function remove(array $array, $keys): array
    {
        $result = $array;
        $keysArray = self::getKeysArray($keys);

        $tmp = &$result;

        while (count($keysArray) > 1) {
            $key = array_shift($keysArray);
            if (!is_array($tmp) || !array_key_exists($key, $tmp)) {
                return $result;
            }

            $tmp = &$tmp[$key];
        }
        $key = array_shift($keysArray);
        unset($tmp[$key]);

        return $result;
    }

    /**
     * Converts map of keys concatenated by dot and corresponding values to multidimensional array
     *
     * @param array $array
     * @return array
     */
    public static function pack(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $result = self::setNestedElement($result, $key, $value);
        }

        return $result;
    }

    /**
     * Converts multidimensional array to map of keys concatenated by dot and corresponding values
     *
     * @param array $array
     * @param int $mode Modify behaviour of unpack (see description of Arr::UNPACK_ constants)
     * @return array
     */
    public static function unpack(array $array, int $mode = self::UNPACK_ALL): array
    {
        return self::_unpack($array, $mode);
    }

    private static function _unpack(array $array, int $mode = self::UNPACK_ALL, array $keys = []): array
    {
        $result = [];

        foreach ($array as $key => $value) {

            if (is_array($value) && !(
                    // Check if value IS NOT a subject for preserve mode
                    !self::isNested($value) && // Preserve mode only work for highest depth elements
                    (
                        ($mode === self::UNPACK_PRESERVE_LIST_ARRAY && !self::isAssoc($value, true)) ||
                        ($mode === self::UNPACK_PRESERVE_ASSOC_ARRAY && self::isAssoc($value, true)) ||
                        $mode === self::UNPACK_PRESERVE_ARRAY
                    )
                )) {
                $keys[] = $key;
                $result += self::_unpack($value, $mode, $keys);
                array_pop($keys);
            } else {
                $result[implode(self::KEY_SEPARATOR, array_merge($keys, [$key]))] = $value;
            }
        }

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
     * <p>If <i>false</i> then this function will match any array that doesn't contain integer keys.</p>
     * <p>If <i>true</i> then this function match only arrays with sequence of integers starting from zero (range from 0 to elements_number - 1) as keys.</p>
     *
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
     * Check if any element of an array is also an array
     *
     * @param array $array
     * @return bool
     */
    public static function isNested(array $array): bool
    {
        foreach ($array as $element) {
            if (is_array($element)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if every element of an array is array
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
     * @param array $array
     * @param callable $callback Callback to run for each element of array
     * @param int $mode Determines callback arguments order and format<br>
     *   <br>
     *   MAP_ARRAY_KEY_VALUE -> callback($key, $value)<br>
     *   MAP_ARRAY_VALUE_KEYS_LIST -> callback($value, $key1, $key2, ...)<br>
     *   MAP_ARRAY_KEYS_ARRAY_VALUE -> callback(array $keys, $value)
     *   MAP_ARRAY_VALUE_KEY -> callback($value, $key)
     * @return array
     */
    public static function map($array, $callback, int $mode = self::MAP_ARRAY_KEY_VALUE): array
    {
        // If has old arguments order then swap and issue warning
        if (is_callable($array) && is_array($callback)) {
            $tmp = $array;
            $array = $callback;
            $callback = $tmp;
            trigger_error('Supplying callback as first argument to Arr::map method is deprecated and will trigger error in next major release. Please use new syntax -> Arr::map(array $array, callback $callback, int $mode)', E_USER_DEPRECATED);
        }
        $result = [];

        switch ($mode) {
            case self::MAP_ARRAY_KEY_VALUE:
                foreach ($array as $key => $value) {
                    $result[$key] = $callback($key, $value);
                }
                break;
            case self::MAP_ARRAY_VALUE_KEY:
                foreach ($array as $key => $value) {
                    $result[$key] = $callback($value, $key);
                }
                break;
            case self::MAP_ARRAY_VALUE_KEYS_LIST:
                foreach (self::unpack($array) as $dotKeys => $value) {
                    $keys = self::getKeysArray($dotKeys);
                    $result = self::setNestedElement($result, $keys, $callback($value, ...$keys));
                }
                break;
            case self::MAP_ARRAY_KEYS_ARRAY_VALUE:
                foreach (self::unpack($array) as $dotKeys => $value) {
                    $keys = self::getKeysArray($dotKeys);
                    $result = self::setNestedElement($result, $keys, $callback($keys, $value));
                }
                break;
        }

        return $result;
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
     * Traverse through array or iterable object and call callback for each element (ignoring the result).<br/>
     * <br/>
     * <b>Warning:</b> For <tt>EACH_VALUE_KEYS_LIST</tt> and <tt>EACH_KEYS_ARRAY_VALUE</tt> modes <tt>$iterable</tt> MUST be an array.
     *
     * @param array|Iterator|IteratorAggregate $iterable Usually array, but can be an iterable object.
     * @param callable $callback Callback function for each element of an iterable
     * @param int $mode What parameters and in which order should <tt>$callback</tt> receive
     * @return array|Iterator|IteratorAggregate Return unchanged input for chaining
     */
    public static function each($iterable, callable $callback, int $mode = self::EACH_VALUE)
    {
        switch ($mode) {
            case self::EACH_KEY_VALUE:
                foreach ($iterable as $key => $value) {
                    $callback($key, $value);
                }
                break;
            case self::EACH_VALUE_KEY:
                foreach ($iterable as $key => $value) {
                    $callback($value, $key);
                }
                break;
            case self::EACH_VALUE_KEYS_LIST:
                foreach (self::unpack($iterable) as $dotKeys => $value) {
                    $keys = self::getKeysArray($dotKeys);
                    $callback($value, ...$keys);
                }
                break;
            case self::EACH_KEYS_ARRAY_VALUE:
                foreach (self::unpack($iterable) as $dotKeys => $value) {
                    $keys = self::getKeysArray($dotKeys);
                    $callback($keys, $value);
                }
                break;
            case self::EACH_VALUE:
            default:
                foreach ($iterable as $value) {
                    $callback($value);
                }
                break;
        }

        return $iterable;
    }

    /**
     * Filter array values by preserving only those which keys are present in array obtained from $keys variable
     *
     * @param array $array
     * @param mixed $keys Keys needed to access desired array element (for possible formats see getKeysArray method)
     * @param bool $exclude If values matching $keys should be excluded from returned array
     * @return array
     * @see Arr::getKeysArray()
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
     * Wrapper around PHP built-in <b>array_filter</b> method.<br/>
     * <br/>
     * Iterates over each value in the array passing them to the <b>callback</b> function. If the <b>callback</b> function returns true, the current value from <b>array</b> is returned into the result array. Array keys are preserved.
     *
     * @param array $array The array to iterate over
     * @param callable|null $callback <p>[optional]</p>
     * <p>The callback function to use</p>
     * <p>If no callback is supplied, all entries of input equal to false (see converting to boolean) will be removed.</p>
     * @param int $flag <p>[optional]</p>
     * <p>Flag determining what arguments are sent to callback:</p>
     * <ul>
     * <li><b>ARRAY_FILTER_USE_KEY</b> - pass key as the only argument to callback instead of the value</li>
     * <li><b>ARRAY_FILTER_USE_BOTH</b> - pass both value and key as arguments to callback instead of the value</li>
     * @return array
     * @see array_filter()
     */
    public static function filter(array $array, ?callable $callback = null, int $flag = 0): array
    {
        return is_null($callback) ? array_filter($array) : array_filter($array, $callback, $flag);
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
     * Find array (or iterable object) element(s) that match specified condition
     *
     * @param array|Iterator|IteratorAggregate $array
     * @param callable $condition Callable accepting one argument (current array element value) and returning truthy or falsy value
     * @param string $return What type of result should be returned after finding desired element(s)
     * @return mixed|mixed[] Either key, value or assoc array containing keys and values for elements matching specified condition. Returns null if element was not found, or empty array if FIND_RETURN_ALL mode was used.
     */
    public static function find($array, callable $condition, string $return = self::FIND_RETURN_VALUE)
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (call_user_func($condition, $value)) {
                switch ($return) {
                    case self::FIND_RETURN_KEY:
                        return $key;
                    case self::FIND_RETURN_VALUE:
                        return $value;
                    case self::FIND_RETURN_ALL:
                        $result[$key] = $value;
                        break;
                }
            }
        }

        return $return === self::FIND_RETURN_ALL ? $result : null;
    }

    /**
     * Order associative array according to supplied keys order
     * Keys that are not present in $keys param will be appended to the end of an array preserving supplied order.
     * @param array $array
     * @param mixed $keys Keys needed to access desired array element (for possible formats see getKeysArray method)
     * @param bool $appendUnmatched If values not matched by supplied keys should be appended to the end of an array
     * @return array
     * @see Arr::getKeysArray()
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
     * @return array New sorted array
     * @see Arr::getKeysArray()
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
     * Sort array of objects using result of calling supplied method name on object as value to compare
     *
     * @param array $objects Array of objects
     * @param string $method Name of a method called for every array element (object) in order to obtain value to compare
     * @param mixed ...$args Arguments for method
     * @return array New sorted array
     */
    public static function sortObjects(array $objects, string $method, ...$args): array
    {
        $result = $objects;

        uasort($result, function ($a, $b) use ($method, $args) {
            return $a->$method(...$args) <=> $b->$method(...$args);
        });

        return $result;
    }

    /**
     * Sum associative arrays by their keys into one array
     *
     * @param array $first Require first argument to ensure method is not called without arguments
     * @param array[] ...$arrays
     * @return array
     */
    public static function sum(array $first, array ...$arrays): array
    {
        $return = [];

        array_unshift($arrays, $first);

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                $return[$key] = ($return[$key] ?? 0) + floatval($value);
            }
        }

        return $return;
    }

    /**
     * Compute difference between two or more arrays of objects
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
     * Compute intersection between two or more arrays of objects
     *
     * @param array $array1
     * @param array $array2
     * @param array[] $arrays
     * @return array
     */
    public static function intersectObjects(array $array1, array $array2, array ...$arrays): array
    {
        $arguments = $arrays;
        array_unshift($arguments, $array1, $array2);
        array_push($arguments, function ($obj1, $obj2) {
            return strcmp(spl_object_hash($obj1), spl_object_hash($obj2));
        });

        return array_uintersect(...$arguments);
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
     * @param int $flag Set flag(s) to preserve specific values from being converted to array (see Arr::FORCE_ARRAY_ constants)
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
     * Get nesting depth of an array.<br>
     * <br>
     * Depth is calculated by counting amount of nested arrays - each nested array increase depth by one.
     * Nominal depth of an array is 1.
     *
     * @param array $array
     * @return int
     */
    public static function getDepth(array $array): int
    {
        $depth = 0;
        $queue = [$array];

        do {
            ++$depth;
            $current = $queue;
            $queue = [];
            foreach ($current as $element) {
                foreach ($element as $value) {
                    if (is_array($value)) {
                        $queue[] = $value;
                    }
                }
            }
        } while (!empty($queue));

        return $depth;
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
     * @return mixed
     * @throws InvalidArgumentException
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
     * @param array $array
     * @param int $A
     * @param int $B
     * @return array
     * @see Arr::even()
     * @see Arr::odd()
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

    /**
     * Get the first key of the given array without affecting the internal array pointer.
     *
     * @param array $array
     * @return string|int|null Null if array is empty
     */
    public static function getFirstKey(array $array)
    {
        return empty($array) ? null : array_keys($array)[0];
    }

    /**
     * Get the last key of the given array without affecting the internal array pointer.
     *
     * @param array $array
     * @return string|int|null Null if array is empty
     */
    public static function getLastKey(array $array)
    {
        if (empty($array)) {
            return null;
        } else {
            $keys = array_keys($array);
            return $keys[count($keys) - 1];
        }
    }

    /**
     * Get the first value of the given array without affecting the internal array pointer.
     *
     * @param array $array
     * @return mixed|null Null if array is empty
     */
    public static function getFirstValue(array $array)
    {
        if (empty($array)) {
            return null;
        }

        return array_values($array)[0];
    }

    /**
     * Get the last value of the given array without affecting the internal array pointer.
     *
     * @param array $array
     * @return mixed|null Null if array is empty
     */
    public static function getLastValue(array $array)
    {
        if (empty($array)) {
            return null;
        }

        $values = array_values($array);

        return $values[count($values) - 1];
    }

    /**
     * Convenience method for creating new ArrObj instance
     *
     * @param array|ArrayAccess $array
     * @return ArrObj
     */
    public static function obj($array = []): ArrObj
    {
        return new ArrObj($array);
    }
}