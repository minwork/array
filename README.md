# Minwork Array

[![Build Status](https://travis-ci.org/minwork/array.svg?branch=master)](https://travis-ci.org/minwork/array)
[![Coverage Status](https://coveralls.io/repos/github/minwork/array/badge.svg?branch=master)](https://coveralls.io/github/minwork/array?branch=master)
[![Latest Stable Version](https://poser.pugx.org/minwork/array/v/stable)](https://packagist.org/packages/minwork/array)
[![License](https://poser.pugx.org/minwork/array/license)](https://packagist.org/packages/minwork/array)

- Pack of advanced array functions best suited for:
  - **Multidimensional** arrays
  - Arrays of **objects**
  - **Associative** arrays
- Easily **access**, **validate**, **manipulate** and **transform** arrays
- Advanced implementation of well known operations
  - Map
  - Filter
  - Group
  - Order
  - Sort
  - Check
  - And many more...

# Table of Contents
   * [Installation](#installation)
   * [Advantages](#advantages)
   * [Documentation](#documentation)
   * [Change Log](https://github.com/minwork/array/releases)

# Installation
`composer require minwork/array`

# Advantages
- Thoroughly **tested**
- Well **documented**
- Leverages PHP 7 syntax and **speed**
- No external dependencies
- Large variety of usages

# Documentation
Detailed documentation is available through PHPDoc, so you can access it anytime in your editor

Here, you can quickly get started by becoming familiar with each and every method through detailed examples

### Common
* [getKeysArray](#getkeysarraymixed-keys-array)
* [hasKeys](#haskeysarray-array-mixed-keys-bool-strict--false-bool)
* [getNestedElement](#getnestedelementarrayarrayaccess-array-mixed-keys-mixed-default--null-mixed)
* [setNestedElement](#setnestedelementarray-array-mixed-keys-mixed-value-array)
* [unpack](#unpackarray-array-array-keys---array)
### Validation
* [check](#checkarray-array-mixedcallable-condition-bool-strict--false-bool)
* [isEmpty](#isemptymixed-array-bool)
* [isAssoc](#isassocarray-array-bool-strict--false-bool)
* [isNumeric](#isnumericarray-array-bool)
* [isUnique](#isuniquearray-array-bool-strict--false-bool)
* [isArrayOfArrays](#isarrayofarraysarray-array-bool)
### Manipulation
* Mapping
    * [map](#maparray-array-callable-callback-int-mode--arrmap_array_key_value-array)
    * [mapObjects](#mapobjectsarray-objects-string-method-args-array)
* Filtering
    * [filterByKeys](#filterbykeysarray-array-mixed-keys-bool-exclude--false-array)
    * [filterObjects](#filterobjectsarray-objects-string-method-args-array)
* Grouping
    * [group](#grouparray-array-stringint-key-array)
    * [groupObjects](#groupobjectsarray-objects-string-method-args-array)
* Ordering
    * [orderByKeys](#orderbykeysarray-array-mixed-keys-bool-appendunmatched--true-array)
    * [sortByKeys](#sortbykeysarray-array-mixed-keys--null-bool-assoc--true-array)
    * [sortObjects](#sortobjectsarray-objects-string-method-args-array)
* Computations
    * [sum](#sumarray-arrays-array)
    * [diffObjects](#diffobjectsarray-array1-array-array2-array-arrays-array)
    * [intersectObjects](#intersectobjectsarray-array1-array-array2-array-arrays-array)
* Flattening
    * [flatten](#flattenarray-array-int-depth--null-bool-assoc--false-array)
    * [flattenSingle](#flattensinglearray-array-array)
### Utilities
* [createMulti](#createmultiarray-keys-array-values--null-array)
* [forceArray](#forcearraymixed-var-int-flag--selfforce_array_all-mixed)
* [clone](#clonearray-array-array)
* [random](#randomarray-array-int-count--1-mixed)
* [shuffle](#shufflearray-array-array)
* [nth](#ntharray-array-int-a--1-int-b--0-array)

# Common

### `getKeysArray(mixed $keys): array`
Transform variable into standarised array of keys

**All `$keys` parameters are normalized using this method**

```php
Arr::getKeysArray(0) -> [0]

Arr::getKeysArray(null) -> []

Arr::getKeysArray('key') -> ['key']

Arr::getKeysArray('key1.0.key2.1') -> ['key1', '0', 'key2', '1']

Arr::getKeysArray([null, 'key1', '', 'key2', 3.1415, 0]) -> ['key1', 'key2', 0]
```

### `hasKeys(array $array, mixed $keys, bool $strict = false): bool`
Check if array has specified keys ( all required, when `$strict` is `true`)

```php
$array = ['key1' => 1, 'key2' => 2, 'key3' => 3];

Arr::hasKeys($array, ['key2', 'key3']) -> true

Arr::hasKeys($array, 'key1.key2') -> true

Arr::hasKeys($array, ['test', 'key1']) -> true

Arr::hasKeys($array, ['test', 'key1'], true) -> false

Arr::hasKeys($array, 'test') -> false
```

### `getNestedElement(array|ArrayAccess $array, mixed $keys, mixed $default = null): mixed`
Get nested array element using specified keys or return `$default` value if it does not exists

```php
$array = ['key1' => ['key2' => ['key3' => ['test']]]];

Arr::getNestedElement($array, 'key1.key2.key3') -> ['test']

Arr::getNestedElement($array, 'key1.key2.key3.0') -> 'test'

Arr::getNestedElement($array, ['nonexistent', 'key'], 'default') -> 'default'

Arr::getNestedElement($array, 'nonexistent.key.without.default') -> null
```

### `setNestedElement(array $array, mixed $keys, mixed $value): array`
Set array element specified by keys to the desired value (create missing keys if necessary)

```php
$array = ['key1' => ['key2' => ['key3' => ['test']]]];

Arr::setNestedElement([], 'key1.key2.key3', ['test']) -> $array

$array = Arr::setNestedElement($array, 'key1.key2.key4', 'test2');
$array['key1']['key2']['key4'] -> 'test2'

// Create nested array element using automatic index
Arr::setNestedElement($array, 'foo.[].foo', 'bar') -> 
[
    'foo' => [
        [
            'foo' => 'bar',
        ],
    ],
]

Arr::setNestedElement([], '[].[].[]', 'test') -> [ [ [ 'test' ] ] ]
```

### `unpack(array $array, array $keys = []): array`
Converts multidimensional array to map of keys concatenated by dot and corresponding values

```php
$array = [
    'key1' => [
        'key2' => [
            'key3' => [
                'foo' => 'test',
                'bar' => 'test2',
            ]
        ]
        'abc' => 'test3',
    ],
    'xyz' => 'test4',
    'test5'
];

Arr::unpack($array) ->
[
    'key1.key2.key3.foo' => 'test',
    'key1.key2.key3.bar' => 'test2',
    'key1.abc' => 'test3',
    'xyz' => 'test4',
    '0' => 'test5',
]
```

# Validation

### `check(array $array, mixed|callable $condition, bool $strict = false): bool`
Check if every element of an array meets specified condition

```php
$array = [1, 1, 1];

Arr::check($array, '1') -> true

Arr::check($array, '1', true) -> false

Arr::check($array, 'is_int') -> true

Arr::check($array, 'is_string') -> false

Arr::check($array, function ($value) { return $value; }) -> false

// In case of callback supplied as condition, strict flag checks if return value is exactly true
Arr::check($array, function ($value) { return $value; }, true) -> false

Arr::check($array, function ($value) { return $value === 1; }, true) -> true
```

### `isEmpty(mixed $array): bool`
Recursively check if all of array values match empty condition

```php
Arr::isEmpty(null) -> true

Arr::isEmpty([]) -> true

Arr::isEmpty([0 => [0], [], null, [false]) -> true
Arr::isEmpty([0 => [0 => 'a'], [], null, [false]]) -> false
```

### `isAssoc(array $array, bool $strict = false): bool`
Check if array is associative

```php
$array = ['a' => 1, 'b' => 3, 1 => 'd', 'c'];

Arr::isAssoc($array) -> true
Arr::isAssoc($array, true) -> true


$array = [1 => 1, 2 => 2, 3 => 3];

// There are no string keys
Arr::isAssoc($array) -> false

// However indexes are not automatically generated (starting from 0 up) 
Arr::isAssoc($array, true) -> true

// In this case keys are automatically generated
Arr::isAssoc([1, 2, 3], true) -> false

// Which is equal to this
Arr::isAssoc([0 => 1, 1 => 2, 2 => 3], true) -> false
```

### `isNumeric(array $array): bool`
Check if array contain only numeric values

```php
Arr::isNumeric([1, '2', '3e10', 5.0002]) -> true

Arr::isNumeric([1, '2', '3e10', 5.0002, 'a']) -> false
```

### `isUnique(array $array, bool $strict = false): bool`
Check if array values are unique

```php
// Without strict flag 1 is equal to '1' 
Arr::isUnique([1, '1', true]) -> false

Arr::isUnique([1, '1', true], true) -> true
```

### `isArrayOfArrays(array $array): bool`
Check if every array element is array

```php
Arr::isArrayOfArrays([]) -> false

Arr::isArrayOfArrays([[], []]) -> true

Arr::isArrayOfArrays([1, 2 => []]) -> false
```

# Manipulation

## Map

### `map(array $array, callable $callback, int $mode = Arr::MAP_ARRAY_KEY_VALUE): array`
Applies a callback to the elements of given array. Arguments supplied to callback differs depending on selected `$mode`

*For backward compatibility using map(callable, array) is still possible but is deprecated and will issue appropriate warning*

```php
$array1 = ['a', 'b', 'c'];
$array2 = [
    1 => [
        2 => 'a',
        3 => 'b',
        4 => [
            5 => 'c',
        ],
    ],
    'test' => 'd',
];

$mapKeyValue = function ($key, $value) {
    return "{$key} -> {$value}";
};
$mapKeysValue = function ($keys, $value) {
    return implode('.', $keys) . " -> {$value}";
};
$mapValueKeysList = function ($value, $key1, $key2) {
    return "$key1.$key2 -> {$value}";
};

// Equivalent to using MAP_ARRAY_KEY_VALUE as mode (3rd) argument
Arr::map($array1, $mapKeyValue) -> ['0 -> a', '1 -> b', '2 -> c']

// Map multidimensional array using keys array
Arr::map($array2, $mapKeysValue, Arr::MAP_ARRAY_KEYS_ARRAY_VALUE) ->
[
    1 => [
        2 => '1.2 -> a',
        3 => '1.3 -> b',
        4 => [
            5 => '1.4.5 -> c',
        ],
    ],
    'test' => 'test -> d',
]

// Map multidimensional array using keys list (mind that all keys above 2nd are ignored due to callback function syntax)
Arr::map($array2, $mapValueKeysList, Arr::MAP_ARRAY_VALUE_KEYS_LIST) ->
[
    1 => [
        2 => '1.2 -> a',
        3 => '1.3 -> b',
        4 => [
            5 => '1.4 -> c',
        ],
    ],
    'test' => 'test -> d',
]
```

### `mapObjects(array $objects, string $method, ...$args): array`
Map array of object to values returned from objects method

```php
$object = new class() { 
    function test($arg = 0) { 
        return 1 + $arg; 
    }
};
$array = [$object, $object, $object];

Arr::mapObjects($array, 'test') -> [1, 1, 1]
Arr::mapObjects($array, 'test', 2) -> [3, 3, 3]
```

## Filter

### `filterByKeys(array $array, mixed $keys, bool $exclude = false): array`
Filter array values by preserving only those which keys are present in array obtained from $keys variable

```php
$array = [
    'a' => 1, 
    'b' => 2, 
    3 => 'c', 
    4 => 5
];

Arr::filterByKeys($array, 'a.b.3') -> ['a' => 1, 'b' => 2, 3 => 'c']
Arr::filterByKeys($array, 'a.b.3', true) -> [4 => 5]

Arr::filterByKeys($array, [null, 0, '']) -> []
Arr::filterByKeys($array, [null, 0, ''], true) -> $array
```

### `filterObjects(array $objects, string $method, ...$args): array`
Filter objects array using return value of specified method

This method also filter values other than objects by standard boolean comparison

```php
$object = new class() { 
    function test($preserve = true) { 
        return $preserve;
    }
};

$array = [$object, 'foo', $object, false];

Arr::filterObjects($array, 'test') -> [$object, 'foo', $object]
Arr::filterObjects($array, 'test', false) -> ['foo']
```

## Group

### `group(array $array, string|int $key): array`
Group array of arrays by value of element with specified key

```php
$array = [
    'a' => ['key1' => 'test1', 'key2' => 1, 'key3' => 'a'],
    'b' => ['key1' => 'test1', 'key2' => 2],
    2 => ['key1' => 'test2', 'key2' => 3, 'key3' => 'b']
];

Arr::group($array, 'key1') -> 
[
    'test1' => [
        'a' => ['key1' => 'test1', 'key2' => 1,  'key3' => 'a'],
        'b' => ['key1' => 'test1', 'key2' => 2]
    ],
    'test2' => [
        2 => ['key1' => 'test2', 'key2' => 3, 'key3' => 'b']
    ],
]

Arr::group($array, 'key2') ->
[
    1 => [
        'a' => ['key1' => 'test1', 'key2' => 1, 'key3' => 'a'],
    ],
    2 => [
        'b' => ['key1' => 'test1', 'key2' => 2]
    ],
    3 => [
        2 => ['key1' => 'test2', 'key2' => 3, 'key3' => 'b']
    ],
]

Arr::group($array, 'key3') ->
[
    'a' => [
        'a' => ['key1' => 'test1', 'key2' => 1,  'key3' => 'a']
    ],
    'b' => [
        2 => ['key1' => 'test2', 'key2' => 3, 'key3' => 'b']
    ],
]

Arr::group($array, 'key4') -> []
```

### `groupObjects(array $objects, string $method, ...$args): array`
Group array of objects by value returned from specified method

```php
$object1 = new class() { 
    function test() { 
        return 'test1';
    }
};
$object2 = new class() { 
    function test() { 
        return 'test2';
    }
};

Arr::groupObjects([$object1, $object2, $object1], 'test') -> 
[
    'test1' => [$object1, $object1],
    'test2' => [$object2],
]

// This method is also very useful in conjunction with Arr::flattenSingle to assign unique key for each object
Arr::flattenSingle(Arr::groupObjects([$object1, $object2], 'test')) -> 
[
    'test1' => $object1,
    'test2' => $object2,
]
```

## Order

### `orderByKeys(array $array, mixed $keys, bool $appendUnmatched = true): array`
Order associative array according to supplied keys order

```php
$array = [
    'foo',
    'a' => 'bar',
    'b' => 'test',
    1,
    'c' => ['test' => 2]
];

Arr::orderByKeys($array, 'a.0.c.1.b') ->
[
    'a' => 'bar',
    0 => 'foo',
    'c' => ['test' => 2],
    1 => 1,
    'b' => 'test',
]

Arr::orderByKeys($array, 'a.0.c') ->
[
    'a' => 'bar',
    0 => 'foo',
    'c' => ['test' => 2],
    
    'b' => 'test',
    1 => 1,
]

Arr::orderByKeys($array, 'a.0.c', false) ->
[
    'a' => 'bar',
    0 => 'foo',
    'c' => ['test' => 2],
]
```

### `sortByKeys(array $array, mixed $keys = null, bool $assoc = true): array`
Sort array of arrays using value specified by key(s)

```php
$array = [
    'a' => ['b' => ['c' => 3]],
    'b' => ['b' => ['c' => -1]],
    'c' => ['b' => ['c' => 0]]
];

Arr::sortByKeys($array, 'b.c') ->
[
    'c' => ['b' => ['c' => -1]],
    'd' => ['b' => ['c' => 0]],
    'a' => ['b' => ['c' => 3]],
]

Arr::sortByKeys($array, 'b.c', false) ->
[
    ['b' => ['c' => -1]],
    ['b' => ['c' => 0]],
    ['b' => ['c' => 3]],
]

Arr::sortByKeys(['a' => 3, 'b' => 1, 'c' => 6]) -> ['b' => 1, 'a' => 3, 'c' => 6]
Arr::sortByKeys(['a' => 3, 'b' => 1, 'c' => 6], null, false) -> [1, 3, 6]
```

### `sortObjects(array $objects, string $method, ...$args): array`
Sort array of objects by comparing result of supplied method name 

`$object1->$method(...$args) <=> $object2->$method(...$args)`

```php
$object1 = new class() {
    function getValue() {
        return 1; 
    }
};
$object2 = new class() {
    function getValue(bool $reverse = false) {
        return $reverse ? 1/2 : 2; 
    }
};
$object3 = new class() {
    function getValue(bool $reverse = false) {
        return $reverse ? 1/3 : 3; 
    }
};

$array = [$object2, $object3, $object1];

Arr::sortObjects($array, 'getValue') -> [$object1, $object2, $object3]
Arr::sortObjects($array, 'getValue', true) -> [$object3, $object2, $object1]
```  

## Computations

### `sum(array ...$arrays): array`
Sum associative arrays by their keys into one array

```php
$arrays = [
    [
        'a' => 1,
        'b' => -3.5,
        'c' => 0,
        3
    ],
    [
        2,
        'a' => 0,
        'c' => -5,
        'd' => PHP_INT_MAX,
    ],
    [
        -5,
        'b' => 3.5,
        'a' => -1,
        'c' => 5,
    ],
    [
        'd' => PHP_INT_MAX,
    ],
    [
        'd' => 2 * -PHP_INT_MAX,
    ]
];

Arr::sum(...$arrays) ->
[
    0,
    'a' => 0,
    'b' => 0,
    'c' => 0,
    'd' => 0,
]

Arr::sum([null, '', false], ['1', true, 'test']) -> [1, 1, 0]
```

### `diffObjects(array $array1, array $array2, array ...$arrays): array`
Compute difference between two or more arrays of objects

```php
$object1 = new \stdClass();
$object2 = new \stdClass();
$object3 = new \stdClass();

Arr::diffObjects([$object3, $object1, $object2], [$object3], [$object2]) -> [1 => $object1]

Arr::diffObjects([$object3, $object1, $object2], [$object3], [$object1, $object2]) -> []

Arr::diffObjects([$object1], [$object3], [$object2], []) -> [$object1]
```

### `intersectObjects(array $array1, array $array2, array ...$arrays): array`
Compute intersection between two or more arrays of objects

```php
$object1 = new \stdClass();
$object2 = new \stdClass();
$object3 = new \stdClass();

Arr::intersectObjects([$object3, $object1, $object2], [$object3, $object2], [$object2]) -> [2 => $object2]

Arr::intersectObjects([$object3, $object1, $object2], [$object3], [$object1, $object2]) -> []

Arr::intersectObjects([$object1, $object2, $object3, $object1], [$object1, $object2]) -> [$object1, $object2, 3 => $object1]
```

## Flattening

### `flatten(array $array, ?int $depth = null, bool $assoc = false): array`
Flatten array of arrays to a n-depth array

```php
$array = [
    'a' => [
        'b' => [
            'c' => 'test'
        ],
        'd' => 1
    ],
    'b' => [
        'e' => 2
    ]
];

Arr::flatten($array) -> 
[
    'test', 
    1, 
    2
]
Arr::flatten($array, 1) -> 
[
    ['c' => 'test'], 
    1, 
    2
]
Arr::flatten($array, 0) -> 
[
    [
        'b' => [
            'c' => 'test'
        ],
        'd' => 1
    ],
    [
        'e' => 2
    ]
]

Arr::flatten([[[[]]]]) -> []

// When $assoc is set to true this method will try to preserve as much string keys as possible using automatically generated numeric indexes as fallback
Arr::flatten($array, null, true) -> 
[
    'c' => 'test', 
    'd' => 1, 
    'e' => 2
]

$array = [
    'a' => [
        'b' => [
            'c' => 1,
        ],
    ],
    [
        'c' => 2
    ]
];

// Here key 'c' is duplicated so it will fallback to numeric index
Arr::flatten($array, null, true) ->
[
    'c' => 1,
    2,
]
```

### `flattenSingle(array $array): array`
Flatten single element arrays (also nested single element arrays)

```php
$array = [
    'a' => ['test'],
    'b' => [
        'test2',
        'c' => ['test3']
    ]
];

Arr::flattenSingle($array) ->
[
    'a' => 'test',
    'b' => [
        'test2',
        'c' => 'test3'
    ],
]

$array = [
    'a' => [
        'b' => 1
    ],
    'b' => 2,
];

Arr::flattenSingle($array) -> ['a' => 1, 'b' => 2]

Arr::flattenSingle([['a']]) -> ['a']

Arr::flattenSingle([]) -> []
```

# Utilities

### `createMulti(array $keys, ?array $values = null): array`
 Create multidimensional array using either first param as config of keys and values or separate keys and values arrays

```php
Arr::createMulti([
    'test.[]' => '123',
    'test.test2.test3' => 'abc',
    'test.test2.[]' => 567,
    'test.[].1' => 'def',
]) ->
[
    'test' => [
        '123',
        'test2' => [
            'test3' => 'abc',
            567
        ],
        [
            1 => 'def'
        ],
    ]
]

Arr::createMulti([
     ['test', '[]'],
    ['test', 'test2', 'test3'],
    ['test', 'test2', '[]'],
    ['test', '[]', 1],
], [
     '123',
    'abc',
    567,
    'def',
]) ->
[
    'test' => [
        '123',
        'test2' => [
            'test3' => 'abc',
            567
        ],
        [
            1 => 'def'
        ],
    ]
]

// In case of empty keys argument simply return new empty array
Arr::createMulti([]) -> []
```

### `forceArray(mixed $var, int $flag = self::FORCE_ARRAY_ALL): mixed`
Make variable an array (according to flag settings)

```php
Arr::forceArray(0) -> [0]
Arr::forceArray('test') -> ['test']

Arr::forceArray(null) -> [null]
Arr::forceArray(null, Arr::FORCE_ARRAY_PRESERVE_NULL) -> null


$object = new stdClass();

Arr::forceArray($object) -> [$object]
// With this flag all objects remain intact
Arr::forceArray($object, Arr::FORCE_ARRAY_PRESERVE_OBJECTS) -> $object


$object = new ArrayObject();

Arr::forceArray($object) -> [$object]
// With this flag objects implementing ArrayAccess remain intact
Arr::forceArray($object, Arr::FORCE_ARRAY_PRESERVE_ARRAY_OBJECTS) -> $object
```

### `clone(array $array): array`
Copy array and clone every object inside it

```php
$object = new class() {
    public $counter = 1;

    function __clone()
    {
        $this->counter = 2;
    }
};

$array = [
    'foo',
    'bar',
    $object,
    'test',
    'nested' => [
        'object' => $object
    ]
];

$cloned = Arr::clone($array);

$cloned[0] -> 'foo'
$cloned[2]->counter -> 2
$cloned['nested']['object']->counter -> 2
```

### `random(array $array, int $count = 1): mixed`
Get random array value(s)

```php
$array = [
    'a' => 1, 
    'b' => 2, 
    'c' => 3, 
    'd' => 4, 
    'e' => 5
];

Arr::random($array) -> 5
Arr::random($array) -> 1
Arr::random($array) -> 3
Arr::random($array) -> 2

Arr::random($array, 2) -> ['d' => 4, 'a' => 1]
Arr::random($array, 2) -> ['b' => 2, 'e' => 5]
Arr::random($array, 2) -> ['c' => 3, 'b' => 2]
```

### `shuffle(array $array): array`
Shuffle array preserving keys and returning new shuffled array

```php
$array = [
    'a' => 1, 
    'b' => 2, 
    'c' => 3, 
    'd' => 4, 
    'e' => 5,
];

Arr::shuffle($array) -> 
[
    'e' => 5,
    'a' => 1, 
    'c' => 3, 
    'b' => 2, 
    'd' => 4, 
]

Arr::shuffle($array) -> 
[
    'a' => 1, 
    'e' => 5,
    'b' => 2, 
    'd' => 4, 
    'c' => 3, 
]
```

### `nth(array $array, int $A = 1, int $B = 0): array`
Gets array elements with index matching condition $An + $B (preserving original keys)

```php
$array = [
    'a' => 0, 
    'b' => 1, 
    'c' => 2, 
    'd' => 3, 
    'e' => 4,
];

Arr::nth($array, 2, 3) ->
[
    'c' => 2,
    'e' => 4,
]

Arr::nth($array, 2) === Arr::even($array) ->
[
    'a' => 0, 
    'c' => 2, 
    'e' => 4,
]

Arr::nth($array, 2, 1) === Arr::odd($array) ->
[
    'b' => 1, 
    'd' => 3,
]
```
