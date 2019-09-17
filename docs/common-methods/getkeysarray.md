# getKeysArray

#### Definition

```php
Arr::getKeysArray(mixed $keys): array
```

#### Description

Transform variable into standardised array of keys.

This method filters out any values that cannot be used as array key leaving only not empty strings and integers as seen in the example below.

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

