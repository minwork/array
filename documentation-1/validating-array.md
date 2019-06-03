# Validating array

## check

#### Definition

`check(array $array, mixed|callable $condition, bool $strict = false): bool`

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

## isEmpty

#### Definition

`isEmpty(mixed $array): bool`

#### Description

Recursively check if all of array values match empty condition.

#### Examples

```php
Arr::isEmpty(null) -> true

Arr::isEmpty([]) -> true

Arr::isEmpty([0 => [0], [], null, [false]) -> true
Arr::isEmpty([0 => [0 => 'a'], [], null, [false]]) -> false
```

## isAssoc

#### Definition

`isAssoc(array $array, bool $strict = false): bool`

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

## isNumeric

#### Definition

`isNumeric(array $array): bool`

#### Description

Check if array contain only numeric values

#### Examples

```php
Arr::isNumeric([1, '2', '3e10', 5.0002]) -> true

Arr::isNumeric([1, '2', '3e10', 5.0002, 'a']) -> false
```

## isUnique

#### Definition

`isUnique(array $array, bool $strict = false): bool`

#### Description

Check if array values are unique

#### Examples

```php
// Without strict flag 1 is equal to '1' 
Arr::isUnique([1, '1', true]) -> false

Arr::isUnique([1, '1', true], true) -> true
```

## isNested

#### Definition

`isNested(array $array): bool`

#### Description

Check if any element of an array is also an array

#### Examples

```php
Arr::isNested([]) -> false

Arr::isNested([1, 2, 3]) -> false

Arr::isNested([1, 2 => [], 3]) -> true

Arr::isNested([1, 2 => [[[]]], 3 => []]) -> true
```

## isArrayOfArrays

#### Definition

`isArrayOfArrays(array $array): bool`

#### Description

Check if every array element is array

#### Examples

```php
Arr::isArrayOfArrays([]) -> false

Arr::isArrayOfArrays([[], []]) -> true

Arr::isArrayOfArrays([1, 2 => []]) -> false
```

