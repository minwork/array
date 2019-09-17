# set → setNestedElement

#### Definition

```php
Arr::setNestedElement(array $array, mixed $keys, mixed $value): array
```

#### Aliases

```php
set(array $array, $keys, $value) -> setNestedElement(array $array, $keys, $value)
```

#### Description

Set array element specified by keys to the desired value \(create missing keys if necessary\).

{% hint style="info" %}
`$keys` argument is parsed using [getKeysArray ](getkeysarray.md)method
{% endhint %}

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

