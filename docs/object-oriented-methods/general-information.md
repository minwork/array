# General information

### Object creating

You can create `ArrObj` by calling 

```php
new ArrObj(array|ArrayAccess $array = [])

// Or for easier chaining
Arr::obj(array|ArrayAccess $array = [])
```

### Chaining

For chaining just call standard `Arr` methods without first parameter \(array or ArrayAccess object\).

As a convenience `ArrObj` contains PHPDoc definitions for every available method, so you don't need to guess their parameters.

Also you can quickly jump to the corresponding `Arr` method by using `@see` tag.

To obtain array from object just call `getArray()` as the final method in chain.

### Examples

```php
// Chain setting nested array values
Arr::obj()->set('foo', 'bar')->set('test.[]', 'test')->getArray() ->
[
  'foo' => 'bar', 
  'test' => ['test']
]

// Quickly flatten array of objects grouped by id
Arr::obj([...])->groupObjects('getId')->flattenSingle()->getArray()
```



