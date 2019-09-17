# nth

#### Definition

```php
Arr::nth(array $array, int $A = 1, int $B = 0): array
```

#### Aliases

```php
even(array $array) -> nth($array, 2)
odd(array $array)  -> nth($array, 2, 1)
```

#### Description

Gets array elements with index matching condition $An + $B \(preserving original keys\)

#### Examples

```php
$array = [
    'a' => 0, 
    'b' => 1, 
    'c' => 2, 
    'd' => 3, 
    'e' => 4,
];

Arr::nth($array, 2, 3) ->
[
    'c' => 2,
    'e' => 4,
]

Arr::nth($array, 2) === Arr::even($array) ->
[
    'a' => 0, 
    'c' => 2, 
    'e' => 4,
]

Arr::nth($array, 2, 1) === Arr::odd($array) ->
[
    'b' => 1, 
    'd' => 3,
]
```

