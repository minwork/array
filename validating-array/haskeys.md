# hasKeys

#### Definition

```php
Arr::hasKeys(array $array, mixed $keys, bool $strict = false): bool
```

#### Description

Check if array has specified keys \( all required, when `$strict` is `true`\).

#### Examples

```php
$array = ['key1' => 1, 'key2' => 2, 'key3' => 3];

Arr::hasKeys($array, ['key2', 'key3']) -> true

Arr::hasKeys($array, 'key1.key2') -> true

Arr::hasKeys($array, ['test', 'key1']) -> true

Arr::hasKeys($array, ['test', 'key1'], true) -> false

Arr::hasKeys($array, 'test') -> false
```

