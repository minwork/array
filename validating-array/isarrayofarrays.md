# isArrayOfArrays

#### Definition

```php
Arr::isArrayOfArrays(array $array): bool
```

#### Description

Check if every array element is array

#### Examples

```php
Arr::isArrayOfArrays([]) -> false

Arr::isArrayOfArrays([[], []]) -> true

Arr::isArrayOfArrays([1, 2 => []]) -> false
```

