# Sorting

## orderByKeys

#### Definition

```php
Arr::orderByKeys(array $array, mixed $keys, bool $appendUnmatched = true): array
```

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

## sortByKeys

#### Definition

```php
Arr::sortByKeys(array $array, mixed $keys = null, bool $assoc = true): array
```

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

## sortObjects

#### Definition

```php
Arr::sortObjects(array $objects, string $method, ...$args): array
```

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

