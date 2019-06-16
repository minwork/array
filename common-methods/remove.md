# remove

#### Definition

```php
Arr::remove(array $array, $keys): array
```

#### Description

Remove element inside array at path specified by keys

#### Examples

```php
$array = [
    'foo' => [
        1,
        'test' => [
            'abc' => 2,
            'def'
        ],
        [
            'bar' => true
        ],
    ],
];

Arr::remove($array, 'foo') -> []
Arr::remove($array, '') -> $array
Arr::remove($array, []) -> $array

Arr::remove($array, 'foo.test.abc') ->
[
    'foo' => [
        1,
        'test' => [
            // Removed
            //'abc' => 2,
            'def'
        ],
        [
            'bar' => true
        ],
    ],
]

Arr::remove($array, 'foo.test') ->
[
    'foo' => [
        1,
        // Removed
        /*'test' => [
            'abc' => 2,
            'def'
        ],*/
        [
            'bar' => true
        ],
    ],
]

Arr::remove($array, ['foo', 1, 'bar']) ->
[
    'foo' => [
        1,
        'test' => [
            'abc' => 2,
            'def'
        ],
        [
            // Removed
            //'bar' => true
        ],
    ],
]
```

