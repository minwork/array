# Utility methods

## createMulti

#### Definition

`createMulti(array $keys, ?array $values = null): array`

#### Description

Create multidimensional array using either first param as config of keys and values or separate keys and values arrays

#### Examples

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

## forceArray

#### Definition

`forceArray(mixed $var, int $flag = self::FORCE_ARRAY_ALL): mixed`

#### Description

Make variable an array \(according to flag settings\)

#### Examples

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

## getDepth

#### Definition

`getDepth(array $array): int`

#### Description

Get nesting depth of an array

#### Examples

```php
Arr::getDepth([]) -> 1

Arr::getDepth([1, 2, 3]) -> 1

Arr::getDepth([1, 2 => [], 3]) -> 2

Arr::getDepth([
    1, 
    2 => [
        3 => [
            4 => []
        ]
    ], 
    5 => []
]) -> 4
```

## clone

#### Definition

`clone(array $array): array`

#### Description

Copy array and clone every object inside it

#### Examples

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

## random

#### Definition

`random(array $array, int $count = 1): mixed`

#### Description

Get random array value\(s\)

#### Examples

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

## shuffle

#### Definition

`shuffle(array $array): array`

#### Description

Shuffle array preserving keys and returning new shuffled array

#### Examples

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

## nth

#### Definition

`nth(array $array, int $A = 1, int $B = 0): array`

#### Description

Gets array elements with index matching condition $An + $B \(preserving original keys\)

#### Examples

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

