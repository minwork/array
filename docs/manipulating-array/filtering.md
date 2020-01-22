# Filtering

## filter

#### Definition

```php
Arr::filter(array $array, ?callable $callback = null, int $flag = 0): array
```

#### Description

Wrapper around PHP built-in [array\_filter](https://www.php.net/manual/en/function.array-filter.php) method to allow [chaining](https://minwork.gitbook.io/array/object-oriented-methods/general-information#chaining) in `ArrObj`

#### Examples

See [array\_filter examples](https://www.php.net/manual/en/function.array-filter.php#refsect1-function.array-filter-examples)

## filterByKeys

#### Definition

```php
Arr::filterByKeys(array $array, mixed $keys, bool $exclude = false): array
```

#### Description

Filter array values by preserving only those which keys are present in array obtained from $keys variable

#### Examples

```php
$array = [
    'a' => 1, 
    'b' => 2, 
    3 => 'c', 
    4 => 5
];

Arr::filterByKeys($array, 'a.b.3') -> ['a' => 1, 'b' => 2, 3 => 'c']
Arr::filterByKeys($array, 'a.b.3', true) -> [4 => 5]

Arr::filterByKeys($array, [null, 0, '']) -> []
Arr::filterByKeys($array, [null, 0, ''], true) -> $array
```

## filterObjects

#### Definition

```php
Arr::filterObjects(array $objects, string $method, ...$args): array
```

#### Description

Filter objects array using return value of specified method

This method also filter values other than objects by standard boolean comparison

#### Examples

```php
$object = new class() { 
    function test($preserve = true) { 
        return $preserve;
    }
};

$array = [$object, 'foo', $object, false];

Arr::filterObjects($array, 'test') -> [$object, 'foo', $object]
Arr::filterObjects($array, 'test', false) -> ['foo']
```

