# clone

#### Definition

```php
Arr::clone(array $array): array
```

#### Description

Copy array and clone every object inside it

#### Examples

```php
$object = new class() {
    public $counter = 1;

    function __clone()
    {
        $this->counter = 2;
    }
};

$array = [
    'foo',
    'bar',
    $object,
    'test',
    'nested' => [
        'object' => $object
    ]
];

$cloned = Arr::clone($array);

$cloned[0] -> 'foo'
$cloned[2]->counter -> 2
$cloned['nested']['object']->counter -> 2
```

