# getFirstValue

#### Definition

```php
Arr::getFirstValue(array $array): mixed|null
```

#### Description

Get the first value of the given array without affecting the internal array pointer.

Returns `null` if array is empty.

#### Examples

```php
Arr::getFirstValue(['a' => 1, 'b' => 2, 'c' => 3]) -> 1

Arr::getFirstValue([1, 2, 3, 4, 5]) -> 1

Arr::getFirstValue([]) -> null
```

