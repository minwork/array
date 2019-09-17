# get → getNestedElement

#### Definition

```php
Arr::getNestedElement(array|ArrayAccess $array, mixed $keys, mixed $default = null): mixed
```

#### Aliases

```php
get($array, $keys, $default = null) -> getNestedElement($array, $keys, $default)
```

#### Description

Get nested array element using specified keys or return `$default` value if it does not exists.

{% hint style="info" %}
`$keys` argument is parsed using [getKeysArray ](getkeysarray.md)method
{% endhint %}

#### Examples

```php
$array = ['key1' => ['key2' => ['key3' => ['test']]]];

Arr::getNestedElement($array, 'key1.key2.key3') -> ['test']

Arr::getNestedElement($array, 'key1.key2.key3.0') -> 'test'

Arr::getNestedElement($array, ['nonexistent', 'key'], 'default') -> 'default'

Arr::getNestedElement($array, 'nonexistent.key.without.default') -> null
```

