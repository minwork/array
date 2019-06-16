# pack

#### Definition

```php
Arr::pack(array $array): array
```

#### Description

Converts map of keys concatenated by dot and corresponding values to multidimensional array.

Inverse of [`unpack`]().

#### Examples

Let's use result array from [example below]().

```php
$array = [
    'key1.key2.key3.foo' => 'test',
    'key1.key2.key3.bar' => 'test2',
    'key1.abc.0' => 'test3',
    'xyz' => 'test4',
    '0' => 'test5',
];

Arr::pack($array) -> 
[
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
]

// Unpack is inverse operation to pack
$array2 = [
    'test',
    [
        'foo' => ['bar'],
        'a' => [
            'b' => 1
        ]
    ]
];

Arr::unpack(Arr::pack($array2)) === $array2 -> true
```

