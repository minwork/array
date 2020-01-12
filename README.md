# Minwork Array

[![Build Status](https://camo.githubusercontent.com/e98c32cb27c2f579cc8a8472235668692d3ef75f/68747470733a2f2f7472617669732d63692e6f72672f6d696e776f726b2f61727261792e7376673f6272616e63683d6d6173746572)](https://travis-ci.org/minwork/array) [![Coverage Status](https://camo.githubusercontent.com/5597efd400c8dc6e11b7e0246ad03de2c5437b2a/68747470733a2f2f636f766572616c6c732e696f2f7265706f732f6769746875622f6d696e776f726b2f61727261792f62616467652e7376673f6272616e63683d6d6173746572)](https://coveralls.io/github/minwork/array?branch=master) [![Latest Stable Version](https://camo.githubusercontent.com/8d4c9f33e111bea52ddeb53c915e8d4f32e143b9/68747470733a2f2f706f7365722e707567782e6f72672f6d696e776f726b2f61727261792f762f737461626c65)](https://packagist.org/packages/minwork/array) [![License](https://camo.githubusercontent.com/3dfeab76bf8b4c567a0b23fb7e381dff9f1b2ba9/68747470733a2f2f706f7365722e707567782e6f72672f6d696e776f726b2f61727261792f6c6963656e7365)](https://packagist.org/packages/minwork/array)

## Pack of array convenience methods for handling:
  * **Nested** arrays
  * Arrays of **objects**
  * **Associative** arrays
  * **Chaining** array transformations
### Easily **create**, **access**, **validate**, **manipulate** and **transform** arrays
Advanced implementation of well known operations:
  * [Get](https://minwork.gitbook.io/array/common-methods/get-getnestedelement)
  * [Set](https://minwork.gitbook.io/array/common-methods/set-setnestedelement)
  * [Has](https://minwork.gitbook.io/array/common-methods/has)
  * [Map](https://minwork.gitbook.io/array/manipulating-array/mapping)
  * [Filter](https://minwork.gitbook.io/array/manipulating-array/filtering)
  * [Find](https://minwork.gitbook.io/array/manipulating-array/finding)
  * [Group](https://minwork.gitbook.io/array/manipulating-array/grouping)
  * [Sort](https://minwork.gitbook.io/array/manipulating-array/sorting)
  * [Check](https://minwork.gitbook.io/array/validating-array/check)
  * [And many more...](https://minwork.gitbook.io/array/)

## Installation

`composer require minwork/array`

## Advantages

* Thoroughly **tested**
* Well **documented**
* Leverages PHP 7 syntax and **speed**
* No external dependencies
* Large variety of usages

## Example of usage
```php
// Set nested array value
$array = Arr::set([], 'key1.key2.key3', 'my_value'); 
// Which is equivalent to
[
  'key1' => [
    'key2' => [
      'key3' => 'my_value'
    ]
  ]
]

// Get nested array value
Arr::get($array, 'key1.key2') -> ['key3' => 'my_value']

// Check if array has nested element
Arr::has($array, 'key1.key2.key3') -> true 

// Map array while accessing it's key
Arr::map($array, function ($key, $value) {
   // Your code here
});

// Find array element
Arr::find($array, function ($element) {
  return Arr::get($element, 'key2.key3') === 'my_value';
}) -> [ 'key2' => [ 'key3' => 'my_value'] ]

// Chain few methods
Arr::obj(['test' => 1, 'foo' => 'bar'])
    ->set('abc', 123)
    ->set('[]', 'auto_index')
    ->remove('foo')
    ->getArray() 
->
[
  'test' => 1,
  'abc' => 123,
  'auto_index'
]

// Group objects by the result of calling method 'getSize' on each object
Arr::groupObjects([$cat, $dog, $fish, ...], 'getSize') ->
[
  'medium' => [$cat, $dog, ...],
  'small' => [$fish, ...],
  ...
]
```

## Documentation

[https://minwork.gitbook.io/array/](https://minwork.gitbook.io/array/)

