# random

#### Definition

```php
Arr::random(array $array, int $count = 1): mixed
```

#### Description

Get random array value\(s\)

#### Examples

```php
$array = [
    'a' => 1, 
    'b' => 2, 
    'c' => 3, 
    'd' => 4, 
    'e' => 5
];

Arr::random($array) -> 5
Arr::random($array) -> 1
Arr::random($array) -> 3
Arr::random($array) -> 2

Arr::random($array, 2) -> ['d' => 4, 'a' => 1]
Arr::random($array, 2) -> ['b' => 2, 'e' => 5]
Arr::random($array, 2) -> ['c' => 3, 'b' => 2]
```

