# Grouping

## group

#### Definition

```php
Arr::group(array $array, string|int $key): array
```

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

## groupObjects

#### Definition

```php
Arr::groupObjects(array $objects, string $method, ...$args): array
```

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

