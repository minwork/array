# Computations

## sum

#### Definition

```php
Arr::sum(array ...$arrays): array
```

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

## diffObjects

#### Declaration

```php
Arr::diffObjects(array $array1, array $array2, array ...$arrays): array
```

#### Description

Compute difference between two or more arrays of objects

#### Examples

```php
$object1 = new \stdClass();
$object2 = new \stdClass();
$object3 = new \stdClass();

Arr::diffObjects(
    [$object3, $object1, $object2], 
    [$object3], [$object2]
) -> [1 => $object1]

Arr::diffObjects(
    [$object3, $object1, $object2], 
    [$object3], 
    [$object1, $object2]
) -> []

Arr::diffObjects(
    [$object1], 
    [$object3], 
    [$object2], 
    []
) -> [$object1]
```

## intersectObjects

#### Definition

```php
Arr::intersectObjects(array $array1, array $array2, array ...$arrays): array
```

#### Description

Compute intersection between two or more arrays of objects

#### Examples

```php
$object1 = new \stdClass();
$object2 = new \stdClass();
$object3 = new \stdClass();

Arr::intersectObjects(
    [$object3, $object1, $object2], 
    [$object3, $object2], 
    [$object2]
) -> [2 => $object2]

Arr::intersectObjects(
    [$object3, $object1, $object2], 
    [$object3], 
    [$object1, $object2]
) -> []

Arr::intersectObjects(
    [$object1, $object2, $object3, $object1], 
    [$object1, $object2]
) -> [$object1, $object2, 3 => $object1]
```

