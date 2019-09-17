# unpack

#### Definition

```php
Arr::unpack(array $array, int $mode = Arr::UNPACK_ALL): array
```

#### Description

Converts multidimensional array to map of keys concatenated by dot and corresponding values.

Inverse of [`pack`]().

#### Modes

| Mode | Description |
| :--- | :--- |
| `Arr::UNPACK_ALL` | Every array will be unpacked |
| `Arr::UNPACK_PRESERVE_LIST_ARRAY` | Preserve arrays with highest nesting level \(if they are not associative\) as element values instead of unpacking them |
| `Arr::UNPACK_PRESERVE_ASSOC_ARRAY` | Preserve arrays with highest nesting level \(if they are associative\) as element values instead of unpacking them |
| `Arr::UNPACK_PRESERVE_ARRAY` | Preserve all arrays with highest nesting level as element values instead of unpacking them |

#### Examples

```php
$array = [
    'key1' => [
        'key2' => [
            'key3' => [
                'foo' => 'test',
                'bar' => 'test2',
            ]
        ]
        'abc' => ['test3'],
    ],
    'xyz' => 'test4',
    'test5'
];

// Equal to Arr::unpack($array, Arr::UNPACK_ALL)
Arr::unpack($array) ->
[
    'key1.key2.key3.foo' => 'test',
    'key1.key2.key3.bar' => 'test2',
    'key1.abc.0' => 'test3',
    'xyz' => 'test4',
    '0' => 'test5',
]

Arr::unpack($array, Arr::UNPACK_PRESERVE_LIST_ARRAY) ->
[
    'key1.key2.key3.foo' => 'test',
    'key1.key2.key3.bar' => 'test2',
    // Preserve list array as value
    'key1.abc' => ['test3'],
    'xyz' => 'test4',
    '0' => 'test5',
]

Arr::unpack($array, Arr::UNPACK_PRESERVE_ASSOC_ARRAY) ->
[
    // Preserve assoc array as value
    'key1.key2.key3' => [
        'foo' => 'test',
        'bar' => 'test2',
    ],
    'key1.abc.0' => 'test3',
    'xyz' => 'test4',
    '0' => 'test5',
]

Arr::unpack($array, Arr::UNPACK_PRESERVE_ARRAY) ->
[
    // Preserve assoc array as value
    'key1.key2.key3' => [
        'foo' => 'test',
        'bar' => 'test2',
    ],
    // Preserve list array as value
    'key1.abc' => ['test3'],
    'xyz' => 'test4',
    '0' => 'test5',
]
```

