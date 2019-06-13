# Common methods

## getKeysArray

#### Definition

`getKeysArray(mixed $keys): array`

#### Description

Transform variable into standardised array of keys.

{% hint style="info" %}
All `$keys` arguments in other methods are normalized using this method
{% endhint %}

#### Examples

```php
Arr::getKeysArray(0) -> [0]

Arr::getKeysArray(null) -> []

Arr::getKeysArray('key') -> ['key']

Arr::getKeysArray('key1.0.key2.1') -> ['key1', '0', 'key2', '1']

Arr::getKeysArray([null, 'key1', '', 'key2', 3.1415, 0]) -> ['key1', 'key2', 0]
```

## hasKeys

#### Definition

`hasKeys(array $array, mixed $keys, bool $strict = false): bool`

#### Description

Check if array has specified keys \( all required, when `$strict` is `true`\).

#### Examples

```php
$array = ['key1' => 1, 'key2' => 2, 'key3' => 3];

Arr::hasKeys($array, ['key2', 'key3']) -> true

Arr::hasKeys($array, 'key1.key2') -> true

Arr::hasKeys($array, ['test', 'key1']) -> true

Arr::hasKeys($array, ['test', 'key1'], true) -> false

Arr::hasKeys($array, 'test') -> false
```

## getNestedElement

#### Definition

`getNestedElement(array|ArrayAccess $array, mixed $keys, mixed $default = null): mixed`

#### Description

Get nested array element using specified keys or return `$default` value if it does not exists.

#### Examples

```php
$array = ['key1' => ['key2' => ['key3' => ['test']]]];

Arr::getNestedElement($array, 'key1.key2.key3') -> ['test']

Arr::getNestedElement($array, 'key1.key2.key3.0') -> 'test'

Arr::getNestedElement($array, ['nonexistent', 'key'], 'default') -> 'default'

Arr::getNestedElement($array, 'nonexistent.key.without.default') -> null
```

## setNestedElement

#### Definition

`setNestedElement(array $array, mixed $keys, mixed $value): array`

#### Description

Set array element specified by keys to the desired value \(create missing keys if necessary\).

#### Examples

```php
$array = ['key1' => ['key2' => ['key3' => ['test']]]];

Arr::setNestedElement([], 'key1.key2.key3', ['test']) -> $array

$array = Arr::setNestedElement($array, 'key1.key2.key4', 'test2');
$array['key1']['key2']['key4'] -> 'test2'

// Create nested array element using automatic index
Arr::setNestedElement($array, 'foo.[].foo', 'bar') -> 
[
    'foo' => [
        [
            'foo' => 'bar',
        ],
    ],
]

Arr::setNestedElement([], '[].[].[]', 'test') -> [ [ [ 'test' ] ] ]
```

## unpack

#### Definition

`unpack(array $array, int $mode = Arr::UNPACK_ALL): array`

#### Description

Converts multidimensional array to map of keys concatenated by dot and corresponding values.

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
        'abc' => 'test3',
    ],
    'xyz' => 'test4',
    'test5'
];

Arr::unpack($array) ->
[
    'key1.key2.key3.foo' => 'test',
    'key1.key2.key3.bar' => 'test2',
    'key1.abc' => 'test3',
    'xyz' => 'test4',
    '0' => 'test5',
]
```

