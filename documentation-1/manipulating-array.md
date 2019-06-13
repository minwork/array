# Manipulating array

## Mapping

### map

#### Definition

`map(array $array, callable $callback, int $mode = Arr::MAP_ARRAY_KEY_VALUE): array`

#### Description

Applies a callback to the elements of given array. Arguments supplied to callback differs depending on selected `$mode`.

{% hint style="warning" %}
For backward compatibility using `map(callable, array)` is still possible but is deprecated and will issue appropriate warning
{% endhint %}

#### Examples

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

### mapObjects

#### Definition

`mapObjects(array $objects, string $method, ...$args): array`

#### Description

Map array of object to values returned from objects method

#### Examples

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

## Filtering

### filterByKeys

#### Definition

`filterByKeys(array $array, mixed $keys, bool $exclude = false): array`

#### Description

Filter array values by preserving only those which keys are present in array obtained from $keys variable

#### Examples

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

### filterObjects

#### Definition

`filterObjects(array $objects, string $method, ...$args): array`

#### Description

Filter objects array using return value of specified method

This method also filter values other than objects by standard boolean comparison

#### Examples

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

## Grouping

### group

#### Definition

`group(array $array, string|int $key): array`

#### Description

Group array of arrays by value of element with specified key

#### Examples

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

### groupObjects

#### Definition

`groupObjects(array $objects, string $method, ...$args): array`

#### Description

Group array of objects by value returned from specified method

#### Examples

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

## Sorting

### orderByKeys

#### Definition

`orderByKeys(array $array, mixed $keys, bool $appendUnmatched = true): array`

#### Description

Order associative array according to supplied keys order

#### Examples

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

### sortByKeys

#### Definition

`sortByKeys(array $array, mixed $keys = null, bool $assoc = true): array`

#### Description

Sort array of arrays using value specified by key\(s\)

#### Examples

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

### sortObjects

#### Definition

`sortObjects(array $objects, string $method, ...$args): array`

#### Description

Sort array of objects by comparing result of supplied method name

`$object1->$method(...$args) <=> $object2->$method(...$args)`

#### Examples

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

### sum

#### Definition

`sum(array ...$arrays): array`

#### Description

Sum associative arrays by their keys into one array

#### Examples

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

### diffObjects

#### Declaration

`diffObjects(array $array1, array $array2, array ...$arrays): array`

#### Description

Compute difference between two or more arrays of objects

#### Examples

```php
$object1 = new \stdClass();
$object2 = new \stdClass();
$object3 = new \stdClass();

Arr::diffObjects([$object3, $object1, $object2], [$object3], [$object2]) -> [1 => $object1]

Arr::diffObjects([$object3, $object1, $object2], [$object3], [$object1, $object2]) -> []

Arr::diffObjects([$object1], [$object3], [$object2], []) -> [$object1]
```

### intersectObjects

#### Definition

`intersectObjects(array $array1, array $array2, array ...$arrays): array`

#### Description

Compute intersection between two or more arrays of objects

#### Examples

```php
$object1 = new \stdClass();
$object2 = new \stdClass();
$object3 = new \stdClass();

Arr::intersectObjects([$object3, $object1, $object2], [$object3, $object2], [$object2]) -> [2 => $object2]

Arr::intersectObjects([$object3, $object1, $object2], [$object3], [$object1, $object2]) -> []

Arr::intersectObjects([$object1, $object2, $object3, $object1], [$object1, $object2]) -> [$object1, $object2, 3 => $object1]
```

## Flattening

### flatten

#### Definition

`flatten(array $array, ?int $depth = null, bool $assoc = false): array`

#### Description

Flatten array of arrays to a n-depth array

#### Examples

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

### flattenSingle

#### Definition

`flattenSingle(array $array): array`

#### Description

Flatten single element arrays \(also nested single element arrays\)

#### Examples

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

