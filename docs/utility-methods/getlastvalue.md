# getLastValue

#### Definition

```php
Arr::getLastValue(array $array): mixed|null
```

#### Description

Get the last value of the given array without affecting the internal array pointer.

Returns `null` if array is empty.

#### Examples

```php
Arr::getLastValue(['a' => 1, 'b' => 2, 'c' => 3]) -> 3

Arr::getLastValue([1, 2, 3, 4, 5]) -> 5

Arr::getLastValue([]) -> null
```

