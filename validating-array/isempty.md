# isEmpty

#### Definition

```php
Arr::isEmpty(mixed $array): bool
```

#### Description

Recursively check if all of array values match empty condition.

#### Examples

```php
Arr::isEmpty(null) -> true

Arr::isEmpty([]) -> true

Arr::isEmpty([0 => [0], [], null, [false]) -> true
Arr::isEmpty([0 => [0 => 'a'], [], null, [false]]) -> false
```

