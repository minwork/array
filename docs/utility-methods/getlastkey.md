# getLastKey

#### Definition

```php
Arr::getLastKey(array $array): int|string|null
```

#### Description

Get the last key of the given array without affecting the internal array pointer.

Returns `null` if array is empty.

#### Examples

```php
Arr::getLastKey(['a' => 1, 'b' => 2, 'c' => 3]) -> 'c'

Arr::getLastKey([1, 2, 3]) -> 2

Arr::getLastKey([]) -> null
```

