# getFirstKey

#### Definition

```php
Arr::getFirstKey(array $array): int|string|null
```

#### Description

Get the first key of the given array without affecting the internal array pointer.

Returns `null` if array is empty.

#### Examples

```php
Arr::getFirstKey(['a' => 1, 'b' => 2, 'c' => 3]) -> 'a'

Arr::getFirstKey([1, 2, 3]) -> 0

Arr::getFirstKey([]) -> null
```

