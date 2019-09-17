# createMulti

#### Definition

```php
Arr::createMulti(array $keys, ?array $values = null): array
```

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

