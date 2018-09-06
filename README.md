

# Installation
`composer require minwork/array`
# What do I need this library for?
Wide range of advanced associative array manipulation methods, smoother transforming array of objects and usefull shortcuts for most common array operations.

Here are some example cases that this library is usefull for:
- Group, filter, map or diff array of objects using their specific method called with supplied arguments  
- Flatten associative or single element array (including nested elements)
- Get or set multidimensional array element using its keys
- Filter or order array by its keys
- Check if array is truly associative, numeric, unique or array of arrays
- Get even or odd values

# How it works?
All of the methods are more or less self-explanatory and well documented making them easy to use with every IDE capable of viewing PHPDoc.
Just start with typing `Arr::` and you should figure out the rest.
You can find some of detailed documentation below. More coming soon.

## What do I need to use it?
PHP 7.1+ - that's all! No unnecessary dependencies, just one simple static class.

## Is it tested?
Yes, it is currently used in live project and works like a charm. Automated tests coming soon.

# Detailed documentation
## Get keys array
Transform variable into standarised array of keys

`Arr::getKeysArray($keys): array`

Possible input formats:

`0` - integer

`'key'` - string with single key

`'key1.key2.key3'` - string with multiple keys separated by dot

`['key1', 'key2', 'key3']` - array with values representing key names

```
object(stdClass) {
     ['prop1'] => 'key1',
     ['prop2'] => 'key2',
     ['prop3'] => 'key3',
}
```
Or object with public properties

## Get multidimensional array element
Get nested element of an array or object implementing array access

`Arr::getNestedElement($array, $keys, $default = null)`

`array|\ArrayAccess $array` - Array or object implementing array access to get element from
`mixed $keys` - Keys indicator (for details on possible keys format check `getKeysArray` method)
`mixed $default` - Default value if element does not exists

## Set multidimensional array element
Set nested element of an array

`Arr::setNestedElement(array &$array, $keys, $value)`

`array $array` - Reference to an array where element be created or updated
`mixed $keys` - Keys indicator (for details on possible keys format check `getKeysArray` method)
`mixed $value` - Value to set for element indicated by keys
