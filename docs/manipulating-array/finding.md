# Finding

## find

#### Definition

```php
Arr::find(array|Iterator|IteratorAggregate $array, callable $condition, string $return = self::FIND_RETURN_VALUE): mixed|mixed[]
```

#### Description

Find array \(or iterable object\) element\(s\) that match specified condition.

#### Modes \(`$return` method argument\)

| Constant name | Description |
| :--- | :--- |
| FIND\_RETURN\_VALUE | Return value of array element matching find condition |
| FIND\_RETURN\_KEY | Return key of array element matching find condition |
| FIND\_RETURN\_ALL | Return array of all values \(preserving original keys\) of array elements matching find condition |

#### Examples

```php
$array = [
  'a' => 0, 
  'b' => 1, 
  3 => 'c', 
  4 => 5
];


Arr::find($array, 'boolval') -> 1
Arr::find($array, function ($element) {
  return is_string($element);
}) -> 'c'


Arr::find($array, 'boolval', Arr::FIND_RETURN_KEY) -> 'b'
Arr::find($array, function ($element) {
  return is_string($element);
}, Arr::FIND_RETURN_KEY) -> 3


Arr::find($array, 'boolval', Arr::FIND_RETURN_ALL) -> 
[
  'b' => 1, 
  3 => 'c', 
  4 => 5
]

Arr::find($array, function ($element) {
  return is_string($element);
}, Arr::FIND_RETURN_ALL) -> 
[
  3 => 'c',
]

Arr::find($array, function ($element) {
  return is_number($element);
}, Arr::FIND_RETURN_ALL) -> 
[
  'a' => 0,
  'b' => 1,
  4 => 5
]
```

