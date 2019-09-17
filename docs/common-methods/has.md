# has

#### Definition

```php
Arr::has(array $array, mixed $keys): bool
```

#### Description

Check if specified \(nested\) key\(s\) exists in array

{% hint style="info" %}
`$keys` argument is parsed using [getKeysArray ](getkeysarray.md)method
{% endhint %}

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

Arr::has($array, 'foo') -> true
Arr::has($array, 'foo.0') -> true
Arr::has($array, 'foo.test') -> true
Arr::has($array, 'foo.test.abc') -> true
Arr::has($array, ['foo', 1, 'bar']) -> true

Arr::has($array, 'test') -> false
Arr::has($array, []) -> false
Arr::has($array, 'not.existing.key') -> false
```

