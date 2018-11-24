<?php
/*
 * This file is part of the Minwork package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Minwork\Helper;

/**
 * Pack of advanced array functions - specifically for associative arrays and arrays of objects
 *
 * @author Krzysztof Kalkhoff
 *        
 */
class Arr
{

    /**
     * Convert any var matching exmaples showed below into array of keys
     *
     * @param mixed $keys
     *            <pre>
     *            0
     *            'key'
     *            'key1.key2.key3'
     *            ['key1', 'key2', 'key3']
     *            object(stdClass) {
     *            ['prop1']=> 'key1',
     *            ['prop2']=> 'key2',
     *            }
     *            </pre>
     * @return array
     */
    public static function getKeysArray($keys): array
    {
        if (is_string($keys)) {
            return empty($keys) ? [] : explode('.', $keys);
        }
        return is_null($keys) ? [] : array_values(self::forceArray($keys));
    }

    /**
     * Get nested element of an array or object implementing array access
     *
     * @param array|\ArrayAccess $array
     *            Array or object implementing array access to get element from
     * @param mixed $keys
     *            Keys indicator
     * @param mixed $default
     *            Default value if element was not found
     * @see Arr::getKeysArray
     * @return null|mixed
     */
    public static function getNestedElement($array, $keys, $default = null)
    {
        $keys = self::getKeysArray($keys);
        foreach ($keys as $key) {
            if (! is_array($array) && ! $array instanceof \ArrayAccess) {
                return $default;
            }
            if (($array instanceof \ArrayAccess && $array->offsetExists($key)) || array_key_exists($key, $array)) {
                $array = $array[$key];
            } else {
                return $default;
            }
        }
        return $array;
    }

    /**
     * Handle multidimensional array access using array of keys (get or set depending on $value argument)
     *
     * @see \Minwork\Helper\Arr::getKeysArray
     * @param array $array
     * @param mixed $keys
     *            Keys needed to access desired array element (for possible formats look at getKeysArray method)
     * @param mixed $value
     *            Value to set (if null this function will work as get)
     */
    public static function handleNestedElement(array &$array, $keys, $value = null)
    {
        $tmp = &$array;
        $keys = self::getKeysArray($keys);
        while (count($keys) > 0) {
            $key = array_shift($keys);
            if (! is_array($tmp)) {
                if (is_null($value)) {
                    return null;
                } else {
                    $tmp = [];
                }
            }
            if (! isset($tmp[$key]) && is_null($value)) {
                return null;
            }
            $tmp = &$tmp[$key];
        }
        if (is_null($value)) {
            return $tmp;
        } else {
            $tmp = $value;
            return true;
        }
    }
    
    /**
     * Alias to handleNestedElement method, used to set element value in multidimensional array
     * 
     * @see \Minwork\Helper\Arr::handleNestedElement
     * @see \Minwork\Helper\Arr::getKeysArray
     * @param array $array
     * @param mixed $keys Keys needed to access desired array element (for possible formats look at getKeysArray method)
     * @param mixed $value Value to set
     * @return NULL|boolean|mixed
     */
    public static function setNestedElement(array &$array, $keys, $value)
    {
        return self::handleNestedElement($array, $keys, $value);
    }

    /**
     * Make variable an array
     *
     * @param mixed $var
     * @return array
     */
    public static function forceArray($var): array
    {
        if (! is_array($var)) {
            if (is_object($var)) {
                return $var instanceof \ArrayAccess ? $var : [
                    $var
                ];
            } else {
                return [
                    $var
                ];
            }
        }
        return $var;
    }

    /**
     * Clone array with every object inside it
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
     * Get random array value
     *
     * @param array $array
     * @return mixed
     */
    public static function random(array $array, int $count = 1)
    {
        if (empty($array)) {
            return null;
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
     * Recursively check if all of array values match empty condition
     *
     * @param array $array
     * @return boolean
     */
    public static function isEmpty($array): bool
    {
        if (is_array($array)) {
            foreach ($array as $v) {
                if (! self::isEmpty($v)) {
                    return false;
                }
            }
        } elseif (! empty($array)) {
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
                if (! is_int($key)) {
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
        return ctype_digit(implode('', $array));
    }

    /**
     * Check if array values are unique
     *
     * @param array $array
     * @return bool
     */
    public static function isUnique(array $array): bool
    {
        return array_unique(array_values($array)) === array_values($array);
    }

    /**
     * Check if every array element is array
     *
     * @param array $array
     * @return bool
     */
    public static function isArrayOfArrays(array $array): bool
    {
        foreach ($array as $element) {
            if (! is_array($element)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Filter array by preserving only those which keys are present in $keys expression
     *
     * @param array $array
     * @param mixed $keys
     *            Look at getKeysArray function
     * @see \Minwork\Helper\Arr::getKeysArray()
     * @return array
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
     * Order associative array by supplied keys order.
     * Keys that are not present in $keys param will be appended to the end of an array preserving supplied order.
     * @param array $array
     * @param mixed $keys
     *            Look at getKeysArray function
     * @see \Minwork\Helper\Arr::getKeysArray()
     * @return array
     */
    public static function orderByKeys(array $array, $keys): array
    {
        $return = [];
        
        foreach (self::getKeysArray($keys) as $key) {
            if (array_key_exists($key, $array)) {
                $return[$key] = $array[$key];
            }
        }
        
        return array_merge($return, self::filterByKeys($array, $keys, true));
    }

    /**
     * Sort array of arrays using value specified by key(s) (can be nested)
     *
     * @param array $array Array of arrays
     * @param $keys Keys in format specified by getKeysArray method
     * @param bool $assoc If sorting should preserve main array keys (default: true)
     * @return array Sorted array
     * @see \Minwork\Helper\Arr::getKeysArray()
     */
    public static function sortByKeys(array $array, $keys, bool $assoc = true): array
    {
        $return = $array;
        $method = $assoc ? 'uasort' : 'usort';

        $method($return, function ($a, $b) use ($keys) {
            return self::getNestedElement($a, $keys) <=> self::getNestedElement($b, $keys);
        });

        return $return;
    }

    /**
     * Check if array has specified keys
     *
     * @param array $array
     * @param mixed $keys
     *            Look at getKeysArray function
     * @see \Minwork\Helper\Arr::getKeysArray()
     * @param bool $strict
     *            If array must have every key
     * @return bool
     */
    public static function hasKeys(array $array, $keys, bool $strict = false): bool
    {
        foreach (self::getKeysArray($keys) as $key) {
            if (array_key_exists($key, $array) && ! $strict) {
                return true;
            } elseif (! array_key_exists($key, $array) && $strict) {
                return false;
            }
        }
        return $strict ? true : false;
    }

    /**
     * Get even array values
     *
     * @param array $array
     * @return array
     */
    public static function evenValues(array $array): array
    {
        $actualValues = array_values($array);
        $values = array();
        for ($i = 0; $i <= count($array) - 1; $i += 2) {
            $values[] = $actualValues[$i];
        }
        return $values;
    }

    /**
     * Get odd array values
     *
     * @param array $array
     * @return array
     */
    public static function oddValues(array $array): array
    {
        $actualValues = array_values($array);
        $values = array();
        if (count($actualValues) > 1) {
            for ($i = 1; $i <= count($array) - 1; $i += 2) {
                $values[] = $actualValues[$i];
            }
        }
        return $values;
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
     * Group array of arrays by value of one key.<br><br>
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
        if (! self::isArrayOfArrays($array)) {
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
     * Group list of objects by value returned from supplied method.<br><br>
     * <u>Example</u><br>
     * Let's say we have a list of Foo objects [Foo1, Foo2, Foo3] and all of them have method bar which return string.<br>
     * If method bar return duplicate strings then all keys will contain list of corresponding objects like this:<br>
     * <pre>
     * ['string1' => [Foo1], 'string2' => [Foo2, Foo3]]
     * </pre>
     * If flat param is equal to <i>true</i> then every object returning duplicate key will replace previous one, like:<br>
     * <pre>
     * ['string1' => Foo1, 'string2' => Foo3]
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
        
        foreach ($objects as $object) {
            if (is_object($object)) {
                $key = $object->$method(...$args);
                if (! array_key_exists($key, $return)) {
                    $return[$key] = [
                        $object
                    ];
                } else {
                    $return[$key][] = $object;
                }
            }
        }
        
        return $return;
    }

    /**
     * Filter objects array using supplied method name.<br>
     * Discard any object which method return value convertable to false
     * 
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
     * Applies a callback to the elements of given array
     * 
     * @param callable $function Callback to run for each element of array (arguments: key, value) 
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
     * Overwrite value of every object in $objects array with return value from object method
     * 
     * This method preserve values other than objects leaving them intact
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
     * Differentiate two or more arrays of objects
     * 
     * @param array ...$objects
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
     * Flatten single element arrays<br>
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
    
    /**
     * Flatten array of arrays to single level array
     * 
     * @param array $array
     * @param int|null $depth How many levels of nesting will be flatten. By default every nested array will be flatten.
     * @param bool $assoc If this param is set to true, this method will try to preserve as much string keys as possible. 
     * In case of conflicting key name, value will be merged with automatic numeric key.
     * @return array
     */
    public static function flatten(array $array, ?int $depth = null, bool $assoc = false): array
    {
        $return = [];
        
        $addElement = function ($key, $value) use (&$return, $assoc) {
            if (! $assoc || array_key_exists($key, $return)) {
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
}