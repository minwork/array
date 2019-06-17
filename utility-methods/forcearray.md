# forceArray

#### Definition

```php
Arr::forceArray(mixed $var, int $flag = self::FORCE_ARRAY_ALL): mixed
```

#### Description

Make variable an array \(according to flag settings\)

#### Examples

```php
Arr::forceArray(0) -> [0]
Arr::forceArray('test') -> ['test']

Arr::forceArray(null) -> [null]
Arr::forceArray(null, Arr::FORCE_ARRAY_PRESERVE_NULL) -> null


$object = new stdClass();

Arr::forceArray($object) -> [$object]
// With this flag all objects remain intact
Arr::forceArray($object, Arr::FORCE_ARRAY_PRESERVE_OBJECTS) -> $object


$object = new ArrayObject();

Arr::forceArray($object) -> [$object]
// With this flag objects implementing ArrayAccess remain intact
Arr::forceArray($object, Arr::FORCE_ARRAY_PRESERVE_ARRAY_OBJECTS) -> $object
```

