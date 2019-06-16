# Flattening

## flatten

#### Definition

```php
Arr::flatten(array $array, ?int $depth = null, bool $assoc = false): array
```

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

## flattenSingle

#### Definition

```php
Arr::flattenSingle(array $array): array
```

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

