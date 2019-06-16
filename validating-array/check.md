# check

#### Definition

```php
Arr::check(array $array, mixed|callable $condition, bool $strict = false): bool
```

#### Description

Check if every element of an array meets specified condition.

#### Examples

```php
$array = [1, 1, 1];

Arr::check($array, '1') -> true

Arr::check($array, '1', true) -> false

Arr::check($array, 'is_int') -> true

Arr::check($array, 'is_string') -> false

Arr::check($array, function ($value) { return $value; }) -> false

// In case of callback supplied as condition, strict flag checks if return value is exactly true
Arr::check($array, function ($value) { return $value; }, true) -> false

Arr::check($array, function ($value) { return $value === 1; }, true) -> true
```

