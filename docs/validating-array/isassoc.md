# isAssoc

#### Definition

```php
Arr::isAssoc(array $array, bool $strict = false): bool
```

#### Description

Check if array is associative

#### Examples

```php
$array = ['a' => 1, 'b' => 3, 1 => 'd', 'c'];

Arr::isAssoc($array) -> true
Arr::isAssoc($array, true) -> true


$array = [1 => 1, 2 => 2, 3 => 3];

// There are no string keys
Arr::isAssoc($array) -> false

// However indexes are not automatically generated (starting from 0 up) 
Arr::isAssoc($array, true) -> true

// In this case keys are automatically generated
Arr::isAssoc([1, 2, 3], true) -> false

// Which is equal to this
Arr::isAssoc([0 => 1, 1 => 2, 2 => 3], true) -> false
```

