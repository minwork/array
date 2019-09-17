# Mapping

## map

#### Definition

```php
Arr::map(array $array, callable $callback, int $mode = Arr::MAP_ARRAY_KEY_VALUE): array
```

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

## mapObjects

#### Definition

```php
Arr::mapObjects(array $objects, string $method, ...$args): array
```

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

