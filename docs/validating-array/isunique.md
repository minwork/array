# isUnique

#### Definition

```php
Arr::isUnique(array $array, bool $strict = false): bool
```

#### Description

Check if array values are unique

#### Examples

```php
// Without strict flag 1 is equal to '1' 
Arr::isUnique([1, '1', true]) -> false

Arr::isUnique([1, '1', true], true) -> true
```

