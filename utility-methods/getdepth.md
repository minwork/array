# getDepth

#### Definition

```php
Arr::getDepth(array $array): int
```

#### Description

Get nesting depth of an array

#### Examples

```php
Arr::getDepth([]) -> 1

Arr::getDepth([1, 2, 3]) -> 1

Arr::getDepth([1, 2 => [], 3]) -> 2

Arr::getDepth([
    1, 
    2 => [
        3 => [
            4 => []
        ]
    ], 
    5 => []
]) -> 4
```

