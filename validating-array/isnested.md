# isNested

#### Definition

```php
Arr::isNested(array $array): bool
```

#### Description

Check if any element of an array is also an array

#### Examples

```php
Arr::isNested([]) -> false

Arr::isNested([1, 2, 3]) -> false

Arr::isNested([1, 2 => [], 3]) -> true

Arr::isNested([1, 2 => [[[]]], 3 => []]) -> true
```

