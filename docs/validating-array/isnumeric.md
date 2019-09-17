# isNumeric

#### Definition

```php
Arr::isNumeric(array $array): bool
```

#### Description

Check if array contain only numeric values

#### Examples

```php
Arr::isNumeric([1, '2', '3e10', 5.0002]) -> true

Arr::isNumeric([1, '2', '3e10', 5.0002, 'a']) -> false
```

