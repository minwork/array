# shuffle

#### Definition

```php
Arr::shuffle(array $array): array
```

#### Description

Shuffle array preserving keys and returning new shuffled array

#### Examples

```php
$array = [
    'a' => 1, 
    'b' => 2, 
    'c' => 3, 
    'd' => 4, 
    'e' => 5,
];

Arr::shuffle($array) -> 
[
    'e' => 5,
    'a' => 1, 
    'c' => 3, 
    'b' => 2, 
    'd' => 4, 
]

Arr::shuffle($array) -> 
[
    'a' => 1, 
    'e' => 5,
    'b' => 2, 
    'd' => 4, 
    'c' => 3, 
]
```

