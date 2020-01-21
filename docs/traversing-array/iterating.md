# Iterating

## each

#### Definition

```php
Arr::each(array|Iterator|IteratorAggregate $iterable, callable $callback, int $mode = self::EACH_VALUE): array|Iterator|IteratorAggregate
```

#### Description

Traverse through array or iterable object and call callback for each element \(ignoring the result\).

#### Modes

<table>
  <thead>
    <tr>
      <th style="text-align:left">Constant name</th>
      <th style="text-align:left">Description</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td style="text-align:left">EACH_VALUE</td>
      <td style="text-align:left">Iterate using callback in form of <code>function($value)</code>
      </td>
    </tr>
    <tr>
      <td style="text-align:left">EACH_KEY_VALUE</td>
      <td style="text-align:left">Iterate using callback in form of <code>function($key, $value)</code>
      </td>
    </tr>
    <tr>
      <td style="text-align:left">EACH_VALUE_KEY</td>
      <td style="text-align:left">Iterate using callback in form of <code>function($value, $key)</code>
      </td>
    </tr>
    <tr>
      <td style="text-align:left">EACH_VALUE_KEYS_LIST</td>
      <td style="text-align:left">
        <p>Iterate using callback in form of <code>function($value, $key1, $key2, ...)</code>
          <br
          />
        </p>
        <p><b>Only for array</b>  <code>$iterable</code>
        </p>
      </td>
    </tr>
    <tr>
      <td style="text-align:left">EACH_KEYS_ARRAY_VALUE</td>
      <td style="text-align:left">
        <p>Iterate using callback in form of <code>function(array $keys, $value)</code>
        </p>
        <p><b><br />Only for array</b>  <code>$iterable</code>
        </p>
      </td>
    </tr>
  </tbody>
</table>#### Examples

```php
$array = [
    1 => [
        2 => 'a',
        3 => 'b',
        4 => [
            5 => 'c',
        ],
    ],
    'test' => 'd',
];

// Value only - using default EACH_VALUE mode
Arr::each($array, function ($value) {
  print_r($value);
  // [ 2 => 'a', ...]
  // 'd'
});

// Key, Value
Arr::each($array, function ($key, $value) {
  echo "{$key}: \t\t";
  print_r($value);
  // 1:      [2 => 'a', ...]
  // test:   'd'
}, Arr::EACH_KEY_VALUE);

// Value, Key
Arr::each($array, function ($value, $key) {
  echo "{$key}: \t\t";
  print_r($value);
  // 1:      [2 => 'a', ...]
  // test:   'd'
}, Arr::EACH_VALUE_KEY);

// Value, Keys list
Arr::each($array, function ($value, ...$keys) {
  echo implode('.', $keys) . ': \t\t';
  print_r($value);
  // 1.2:    'a'
  // 1.3:    'b'
  // 1.4.5:  'c'
  // test:   'd'
}, Arr::EACH_VALUE_KEYS_LIST);


// Keys array, value
Arr::each($array, function (array $keys, $value) {
  echo implode('.', $keys) . ': \t\t';
  print_r($value);
  // 1.2:    'a'
  // 1.3:    'b'
  // 1.4.5:  'c'
  // test:   'd'
}, Arr::EACH_KEYS_ARRAY_VALUE);
```

