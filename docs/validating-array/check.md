# check

#### Definition

{% code title="" %}
```php
Arr::check(array $array, mixed|callable $condition, int $flag = 0): bool
```
{% endcode %}

#### Description

Check if some or every array element meets specified condition.

If `CHECK_SOME` flag is NOT present then every array element must meet specified condition in order to pass check.

#### Condition

<table>
  <thead>
    <tr>
      <th style="text-align:left"><code>$condition</code> type</th>
      <th style="text-align:left">Description</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td style="text-align:left"><code>callable</code>
      </td>
      <td style="text-align:left">
        <p><em>Callable </em>should return truthy or falsy value (while using <code>CHECK_STRICT</code> flag,
          return values other than <code>true</code> are treated as <code>false</code>).</p>
        <p></p>
        <p><em>Callable</em> is supplied with either only element value (<code>function($value)</code>)
          or pair of element value and key (<code>function($value, $key)</code>)
          as arguments.
          <br />Arguments amount depend on <em>callable </em>definition and is dynamically
          resolved using <a href="https://www.php.net/manual/en/reflectionfunctionabstract.getnumberofparameters.php">reflection</a> (defaults
          to 2 - value and key)</p>
      </td>
    </tr>
    <tr>
      <td style="text-align:left"><code>mixed</code>
      </td>
      <td style="text-align:left">
        <p>If <em>condition</em> type is different than <code>callable</code> then every
          array element is compared against it value.</p>
        <p></p>
        <p><code>$value == $condition</code> by default</p>
        <p><code>$value === $condition</code>if <code>CHECK_STRICT</code> flag is enabled</p>
      </td>
    </tr>
  </tbody>
</table>#### Flags

Can be used as stand alone \(i.e.`Arr::CHECK_STRICT`\) as well as in conjunction \(i.e. `Arr::CHECK_STRICT | Arr::CHECK_SOME`\)

{% hint style="info" %}
`$flag` argument used to be a boolean parameter called `$strict`

But do not worry, it it is fully backward compatible due to in-flight type conversion from `bool` to `int` 
{% endhint %}

<table>
  <thead>
    <tr>
      <th style="text-align:left">Constant name</th>
      <th style="text-align:left">Description</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td style="text-align:left"><code>CHECK_STRICT</code>
      </td>
      <td style="text-align:left">
        <p>In case <em>condition</em> is <code>callable</code> check if it result is
          exactly <code>true</code>
        </p>
        <p>
          <br />If <em>condition</em> is not <code>callable</code>, then check if array element
          is equal to it both by value and type</p>
        <p></p>
        <p>See <b>Condition</b> section for more info</p>
      </td>
    </tr>
    <tr>
      <td style="text-align:left"><code>CHECK_SOME</code>
      </td>
      <td style="text-align:left">
        <p>Check will return <code>true</code> on first array element that match specified <em>condition</em> or
          false if none of them matches it.</p>
        <p></p>
        <p>By default <code>check</code> method will return <code>true</code> only if
          ALL of array elements meet specified <em>condition</em>
        </p>
      </td>
    </tr>
  </tbody>
</table>#### Examples

```php
$array = [1, '1', true];

// Every array element is EQUAL to 1 ($value == '1')
Arr::check($array, '1') -> true

// Only one array element is the SAME as 1 ($value === '1') which is not sufficient
Arr::check($array, '1', Arr::CHECK_STRICT) -> false

// When CHECK_SOME flag is present, one element is sufficient to pass check
Arr::check($array, '1', Arr::CHECK_STRICT | Arr::CHECK_SOME) -> true

// You can also use built-in functions to validate whole array
Arr::check($array, 'is_int') -> false
Arr::check($array, 'is_string') -> false

// When CHECK_SOME flag is present only one element need to meet specified condition
Arr::check($array, 'is_int', Arr::CHECK_SOME) -> true
Arr::check($array, 'is_string', Arr::CHECK_SOME) -> true

// Every value of array is truthy
Arr::check($array, function ($value) { return $value; }) -> true
// Above check can be simplified to
Arr::check($array, true) -> true
// Or even shorter
Arr::check($array, 1) -> true

// When CHECK_STRICT flag is present, check will fail for callback return values other true
Arr::check($array, function ($value) { return $value; }, Arr::CHECK_STRICT) -> false
// But when callback is written as follows check will pass
Arr::check($array, function ($value) { return boolval($value); }, Arr::CHECK_STRICT) -> true
// Above check will pass if we add CHECK_SOME flag cause of of the elements is exactly true
Arr::check($array, function ($value) { return $value; }, Arr::CHECK_STRICT | Arr::CHECK_SOME) -> true


// Callback function arguments count is automatically detected
Arr::check($array, function ($value, $key) { return $value > $key; }) -> false
// First array element 1 is greater than its key 0
Arr::check($array, function ($value, $key) { return $value > $key; }, Arr::CHECK_SOME) -> true
```

