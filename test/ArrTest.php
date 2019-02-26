<?php

use Minwork\Helper\Arr;
use PHPUnit\Framework\TestCase;

class ArrTest extends TestCase
{
    private function createObjectWithMethod(callable $method)
    {
        return new class($method)
        {
            private $method;

            function __construct($method)
            {
                $this->method = $method;
            }

            function __call($func, $params)
            {
                return ($this->method)(...$params);
            }
        };
    }

    /********************************* Common *********************************/
    public function testGetKeysArray()
    {
        $this->assertSame([], Arr::getKeysArray(null));
        $this->assertSame([3], Arr::getKeysArray(3));
        $this->assertSame(['key'], Arr::getKeysArray('key'));
        $this->assertSame([3], Arr::getKeysArray(3));
        $this->assertSame([1, 'test'], Arr::getKeysArray([1, 'test']));
        $this->assertSame([], Arr::getKeysArray([]));
        $this->assertSame([], Arr::getKeysArray(''));
        $this->assertSame([], Arr::getKeysArray(['', null]));
        $this->assertSame([], Arr::getKeysArray(null));
    }

    public function testHasKeys()
    {
        $array = ['key1' => 1, 'key2' => 2, 'key3' => 3];

        $this->assertSame(true, Arr::hasKeys($array, 'key1'));
        $this->assertSame(true, Arr::hasKeys($array, ['key2', 'key3']));

        $this->assertSame(false, Arr::hasKeys($array, 'test'));
        $this->assertSame(false, Arr::hasKeys($array, ''));

        $this->assertSame(true, Arr::hasKeys($array, 'key1.key2'));
        $this->assertSame(true, Arr::hasKeys($array, ['test', 'key1']));

        $this->assertSame(false, Arr::hasKeys($array, ['test', 'key1'], true));
        $this->assertSame(true, Arr::hasKeys($array, ['key2', 'key1'], true));
    }

    public function testGetNestedElement()
    {
        $array = ['key1' => ['key2' => ['key3' => ['test']]]];

        $this->assertSame(['test'], Arr::getNestedElement($array, 'key1.key2.key3'));
        $this->assertSame('test', Arr::getNestedElement($array, 'key1.key2.key3.0'));
        $this->assertSame('default', Arr::getNestedElement($array, 'key1.key4.key2.key3', 'default'));
        $this->assertNull(Arr::getNestedElement($array, 'key1.key4.key2.key3'));

        $object = new ArrayObject();
        $object['key'] = 'test';
        $object['nested'] = ['key' => 'test2'];

        $this->assertSame('test', Arr::getNestedElement($object, 'key'));
        $this->assertNull(Arr::getNestedElement($object, 'key2'));
        $this->assertSame('test2', Arr::getNestedElement($object, 'nested.key'));
    }

    public function testSetNestedElement()
    {
        $array = ['key1' => ['key2' => ['key3' => ['test']]]];
        // Array creation
        $this->assertSame($array, Arr::setNestedElement([], 'key1.key2.key3', ['test']));

        $array = Arr::setNestedElement($array, 'key1.key2.key3', 'test');
        $this->assertSame('test', $array['key1']['key2']['key3']);

        $array = Arr::setNestedElement($array, 'key1.key2', ['key3' => 'test']);
        $this->assertSame('test', $array['key1']['key2']['key3']);

        $array = Arr::setNestedElement($array, 'key1.key2.key4', 'test2');
        $this->assertSame('test2', $array['key1']['key2']['key4']);
    }

    /********************************* Validation *********************************/

    public function testCheck()
    {
        $array1 = ['test', 'test', 'test'];
        $array2 = [1, 1, 1];

        $this->assertTrue(Arr::check($array1, function ($value, $key) {
            return is_int($key) && $value == 'test';
        }));

        $this->assertTrue(Arr::check($array1, function ($value) {
            return $value;
        }, false));
        $this->assertFalse(Arr::check($array1, function ($value) {
            return $value;
        }, true));

        $this->assertTrue(Arr::check($array1, 'test', false));
        $this->assertTrue(Arr::check($array1, 'test', true));

        $this->assertTrue(Arr::check($array2, '1', false));
        $this->assertFalse(Arr::check($array2, '1', true));

        $this->assertTrue(Arr::check($array2, 'is_int'));
        $this->assertFalse(Arr::check($array2, 'is_string'));

        $this->assertFalse(Arr::check($array1, ['test']));
        $this->assertFalse(Arr::check($array2, [1]));
    }

    public function testIsEmpty()
    {
        $this->assertTrue(Arr::isEmpty(null));
        $this->assertTrue(Arr::isEmpty([]));
        $this->assertTrue(Arr::isEmpty([0]));
        $this->assertTrue(Arr::isEmpty([null]));
        $this->assertTrue(Arr::isEmpty(['']));
        $this->assertTrue(Arr::isEmpty([false]));
        $this->assertTrue(Arr::isEmpty([1 => '']));
        $this->assertTrue(Arr::isEmpty([2 => [null]]));

        $this->assertFalse(Arr::isEmpty(['a']));
        $this->assertTrue(Arr::isEmpty([0 => [0], [], null, [false]]));
        $this->assertFalse(Arr::isEmpty([0 => [0 => 'a'], [], null, [false]]));
    }

    public function testIsAssoc()
    {
        $array = ['a' => 1, 'b' => 3, 1 => 'd', 'c'];
        $array2 = array_combine(range(1, 11), range(0, 10));
        $this->assertTrue(Arr::isAssoc($array));
        $this->assertTrue(Arr::isAssoc($array, true));
        $this->assertFalse(Arr::isAssoc($array2));
        $this->assertTrue(Arr::isAssoc($array2, true));
        $this->assertFalse(Arr::isAssoc(range(0, 10), true));
    }

    public function testIsNumeric()
    {
        $this->assertTrue(Arr::isNumeric([1, '2', '3e10', 5.0002]));
        $this->assertFalse(Arr::isNumeric([1, '2', '3e10', 5.0002, 'a']));
    }

    public function testIsUnique()
    {
        $this->assertFalse(Arr::isUnique([1, '1', true]));
        $this->assertTrue(Arr::isUnique([1, '1', true], true));
        $this->assertFalse(Arr::isUnique([1, '1', true, false, null, 0, 1]));
        $this->assertFalse(Arr::isUnique([1, '1', true, false, null, 0, 1], true));
        $this->assertFalse(Arr::isUnique([1, 1, 1]));
        $this->assertFalse(Arr::isUnique([1, 1, 1], true));
    }

    public function testIsArrayOfArrays()
    {
        $this->assertFalse(Arr::isArrayOfArrays([]));
        $this->assertTrue(Arr::isArrayOfArrays([[], []]));
        $this->assertTrue(Arr::isArrayOfArrays([[]]));
        $this->assertFalse(Arr::isArrayOfArrays([1, 2 => []]));
    }

    /********************************* Manipulation *********************************/

    public function testMap()
    {
        $array = ['a', 'b', 'c'];
        $func = function ($key, $value) {
            return "{$key}{$value}";
        };

        $this->assertSame(['0a', '1b', '2c'], Arr::map($func, $array));
        $this->assertSame([], Arr::map($func, []));
        $this->assertSame(range(0, 2), Arr::map(function ($key) {
            return $key;
        }, $array));
    }

    public function testMapObjects()
    {
        $method1 = function ($arg1, $arg2 = 0) {
            return $arg1 + $arg2;
        };
        $method2 = function ($arg1) {
            return $arg1 ** 2;
        };
        $method3 = function ($arg1, $arg2 = null, $arg3 = null) {
            return "{$arg1}{$arg2}{$arg3}";
        };
        $array = ['test', $this->createObjectWithMethod($method1), $this->createObjectWithMethod($method2), $this->createObjectWithMethod($method3)];

        $this->assertSame(['test', 3, 4, '213'], Arr::mapObjects($array, 'test', 2, 1, 3));
        $this->assertSame(['test', 3, 4, '21'], Arr::mapObjects($array, 'test', 2, 1));
        $this->assertSame(['test', 2, 4, '2'], Arr::mapObjects($array, 'test', 2));
        $this->assertSame([], Arr::mapObjects([], 'test'));

        $object = new class()
        {
            function test($arg = 0)
            {
                return 1 + $arg;
            }
        };
        $array = [$object, $object, $object];

        $this->assertSame([1, 1, 1], Arr::mapObjects($array, 'test'));
        $this->assertSame([3, 3, 3], Arr::mapObjects($array, 'test', 2));
    }

    public function testFilterByKeys()
    {
        $array = ['a' => 1, 'b' => 2, 3 => 'c', 4 => 5];

        $this->assertSame($array, Arr::filterByKeys($array, null));
        $this->assertSame($array, Arr::filterByKeys($array, null, true));
        $this->assertSame(['a' => 1, 'b' => 2, 3 => 'c'], Arr::filterByKeys($array, 'a.b.3'));
        $this->assertSame(['a' => 1, 'b' => 2, 3 => 'c'], Arr::filterByKeys($array, ['a', null, 'b', 3, null]));
        $this->assertSame([3 => 'c', 4 => 5], Arr::filterByKeys($array, [0, 4, 3]));
        $this->assertSame([], Arr::filterByKeys($array, []));
        $this->assertSame([], Arr::filterByKeys($array, [null, 0, '']));
        $this->assertSame($array, Arr::filterByKeys($array, [null, 0, ''], true));
        $this->assertSame([4 => 5], Arr::filterByKeys($array, 'a.b.3', true));
        $this->assertSame([4 => 5], Arr::filterByKeys($array, ['a', null, 'b', 3, null], true));
        $this->assertSame(['a' => 1, 'b' => 2], Arr::filterByKeys($array, [0, 4, 3], true));
        $this->assertSame($array, Arr::filterByKeys($array, [], true));
        $this->assertSame($array, Arr::filterByKeys($array, [null, 0], true));
    }

    public function testFilterObjects()
    {
        $object1 = $this->createObjectWithMethod(function ($arg) {
            return boolval($arg);
        });
        $object2 = $this->createObjectWithMethod(function ($arg) {
            return !$arg;
        });
        $object3 = $this->createObjectWithMethod(function ($arg1, $arg2) {
            return $arg1 && $arg2;
        });
        $object4 = $this->createObjectWithMethod(function ($arg1, $arg2) {
            return $arg1 || $arg2;
        });
        $array1 = [
            'test1',
            $object1,
            $object2,
        ];
        $array2 = [
            $object3,
            null,
            $object4,
        ];

        $this->assertSame(['test1', $object1], Arr::filterObjects($array1, 'test', 1));
        $this->assertSame(['test1', $object1], Arr::filterObjects($array1, 'test', 'abc'));
        $this->assertSame(['test1', 2 => $object2], Arr::filterObjects($array1, 'test', false));
        $this->assertSame([], Arr::filterObjects($array2, 'test', false, 0));
        $this->assertSame([2 => $object4], Arr::filterObjects($array2, 'test', false, 1));
        $this->assertSame([0 => $object3, 2 => $object4], Arr::filterObjects($array2, 'test', true, 1));
    }

    public function testGroup()
    {
        $array = [
            'a' => ['key1' => 'test1', 'key2' => 1, 'key3' => 'a'],
            'b' => ['key1' => 'test1', 'key2' => 2],
            2 => ['key1' => 'test2', 'key2' => 3, 'key3' => 'b']
        ];

        $this->assertSame([
            'test1' => [
                'a' => ['key1' => 'test1', 'key2' => 1, 'key3' => 'a'],
                'b' => ['key1' => 'test1', 'key2' => 2]
            ],
            'test2' => [
                2 => ['key1' => 'test2', 'key2' => 3, 'key3' => 'b']
            ],
        ], Arr::group($array, 'key1'));
        $this->assertSame([
            1 => [
                'a' => ['key1' => 'test1', 'key2' => 1, 'key3' => 'a'],
            ],
            2 => [
                'b' => ['key1' => 'test1', 'key2' => 2]
            ],
            3 => [
                2 => ['key1' => 'test2', 'key2' => 3, 'key3' => 'b']
            ],
        ], Arr::group($array, 'key2'));
        $this->assertSame([
            'a' => [
                'a' => ['key1' => 'test1', 'key2' => 1, 'key3' => 'a']
            ],
            'b' => [
                2 => ['key1' => 'test2', 'key2' => 3, 'key3' => 'b']
            ],
        ], Arr::group($array, 'key3'));
        $this->assertSame([], Arr::group([], 'key'));
        $this->assertSame([], Arr::group($array, 'key4'));

    }

    public function testGroupObjects()
    {
        $object1 = $this->createObjectWithMethod(function () {
            return 'test';
        });
        $object2 = $this->createObjectWithMethod(function () {
            return 'test1';
        });
        $object3 = $this->createObjectWithMethod(function () {
            return 'test';
        });

        $this->assertSame([
            'test' => [
                1 => $object1,
                3 => $object3
            ],
            'test1' => [
                'd' => $object2,
            ],
        ], Arr::groupObjects([
            'abc',
            $object1,
            'def',
            'd' => $object2,
            3 => $object3
        ], 'test'));
        $this->assertSame([], Arr::groupObjects(['a', 'b', 'c'], 'test'));
        $this->assertSame([
            'test' => [
                3 => $object3
            ]
        ], Arr::groupObjects(['a', 'b', 'c', $object3], 'test'));
    }

    public function testOrderByKeys()
    {
        $array = [
            'abc',
            'd' => 'test1',
            'e' => 'test3',
            'test2',
            'x' => ['t' => 3]
        ];
        $ordered = [
            'd' => 'test1',
            0 => 'abc',
            'x' => ['t' => 3],
            'e' => 'test3',
            1 => 'test2',
        ];

        $this->assertSame($ordered, Arr::orderByKeys($array, 'd.0.x.e.1'));
        $this->assertSame($ordered, Arr::orderByKeys($array, ['d', 0, 'x', 'e', 1]));
        $this->assertSame($ordered, Arr::orderByKeys($array, 'd.0.x'));
        $this->assertSame(array_slice($ordered, 0, 3, true), Arr::orderByKeys($array, 'd.0.x', false));
        $this->assertSame($array, Arr::orderByKeys($array, null));
        $this->assertSame($array, Arr::orderByKeys($array, []));
        $this->assertSame($array, Arr::orderByKeys($array, [null, '']));
    }

    public function testSortByKeys()
    {
        $array1 = [
            'a' => 3,
            1,
            'c' => 6,
            -3,
            'e' => 0,
            'f' => 1,
        ];

        $array2 = [
            'a' => ['b' => 3],
            'c' => ['b' => -1],
            'd' => ['b' => 0]
        ];

        $array3 = [
            'a' => ['b' => ['c' => 3]],
            'c' => ['b' => ['c' => -1]],
            'd' => ['b' => ['c' => 0]]
        ];

        $this->assertSame([
            1 => -3,
            'e' => 0,
            0 => 1,
            'f' => 1,
            'a' => 3,
            'c' => 6,
        ], Arr::sortByKeys($array1));

        $this->assertSame([-3, 0, 1, 1, 3, 6], Arr::sortByKeys($array1, null, false));
        $this->assertSame([-3, 0, 1, 1, 3, 6], Arr::sortByKeys($array1, [null, ''], false));
        $this->assertSame([], Arr::sortByKeys([]));

        $this->assertSame([
            'c' => ['b' => -1],
            'd' => ['b' => 0],
            'a' => ['b' => 3],
        ], Arr::sortByKeys($array2, 'b'));
        $this->assertSame([
            ['b' => -1],
            ['b' => 0],
            ['b' => 3],
        ], Arr::sortByKeys($array2, 'b', false));

        $this->assertSame([
            'c' => ['b' => ['c' => -1]],
            'd' => ['b' => ['c' => 0]],
            'a' => ['b' => ['c' => 3]],
        ], Arr::sortByKeys($array3, 'b.c'));
        $this->assertSame([
            ['b' => ['c' => -1]],
            ['b' => ['c' => 0]],
            ['b' => ['c' => 3]],
        ], Arr::sortByKeys($array3, 'b.c', false));
    }

    public function testSum()
    {
        $arrays = [
            [
                'a' => 1,
                'b' => -3.5,
                'c' => 0,
                3
            ],
            [
                2,
                'a' => 0,
                'c' => -5,
                'd' => PHP_INT_MAX,
            ],
            [
                -5,
                'b' => 3.5,
                'a' => -1,
                'c' => 5,
            ],
            [
                'd' => PHP_INT_MAX,
            ],
            [
                'd' => 2 * -PHP_INT_MAX,
            ]
        ];

        $this->assertEquals([
            0,
            'a' => 0,
            'b' => 0,
            'c' => 0,
            'd' => 0,
        ], Arr::sum(...$arrays));
        $this->assertSame([], Arr::sum([]));
        $this->assertEquals([1, 1, 0], Arr::sum([null, '', false], ['1', true, 'test']));
    }

    public function testDiffObjects()
    {
        $object1 = new \stdClass();
        $object2 = new \stdClass();
        $object3 = new \stdClass();

        $this->assertSame([1 => $object1], Arr::diffObjects([$object3, $object1, $object2], [$object3], [$object2]));
        $this->assertSame([], Arr::diffObjects([$object3, $object1, $object2], [$object3], [$object1, $object2]));
        $this->assertSame([$object1], Arr::diffObjects([$object1], [$object3], [$object2], []));
    }

    public function testFlatten()
    {
        $array = [
            'a' => [
                'b' => [
                    1 => [
                        2 => 3
                    ],
                    'c' => [
                        4
                    ],
                    5
                ],
                'd' => [
                    'e',
                    [
                        'f' => 6
                    ]
                ],
            ],
            'g' => [
                'h',
                'i',
                'j' => [
                    7,
                    2 => 8,
                ]
            ],
            [
                'k',
                'l' => [
                    9,
                    10,
                ],
            ]
        ];

        $array2 = [
            'a' => [
                'b' => [
                    'c' => 'test'
                ],
                'd' => 1
            ],
            'b' => [
                'e' => 2
            ]
        ];


        $this->assertSame([], Arr::flatten([[[[]]]]));
        $this->assertSame([], Arr::flatten([]));

        $this->assertSame(['test', 1, 2], Arr::flatten($array2));
        $this->assertSame([3, 4, 5, 'e', 6, 'h', 'i', 7, 8, 'k', 9, 10], Arr::flatten($array));

        // Test depth
        $this->assertSame(['a'], Arr::flatten([[[['a']]]]));
        $this->assertSame(array_values($array), Arr::flatten($array, 0));
        $this->assertSame($array, Arr::flatten($array, 0, true));
        $this->assertSame([['c' => 'test'], 1, 2], Arr::flatten($array2, 1));

        $this->assertSame([
            'c' => 'test',
            'd' => 1,
            'e' => 2
        ], Arr::flatten($array2, null, true));


        $this->assertSame([
            [
                1 => [
                    2 => 3
                ],
                'c' => [
                    4
                ],
                5
            ],
            [
                'e',
                [
                    'f' => 6
                ]
            ],
            'h',
            'i',
            [
                7,
                2 => 8,
            ],
            'k',
            [
                9,
                10,
            ],
        ], Arr::flatten($array, 1));

        $this->assertSame([
            'b' => [
                1 => [
                    2 => 3
                ],
                'c' => [
                    4
                ],
                5
            ],
            'd' => [
                'e',
                [
                    'f' => 6
                ]
            ],
            'h',
            'i',
            'j' => [
                7,
                2 => 8,
            ],
            'k',
            'l' => [
                9,
                10,
            ],
        ], Arr::flatten($array, 1, true));

        $this->assertSame([
            3,
            4,
            5,
            'e',
            6,
            'h',
            'i',
            7,
            8,
            'k',
            9,
            10,
        ], Arr::flatten($array, 3));

        $this->assertSame([
            'a' => 1,
            'b' => 2,
            'c' => 3,
            4,
            'd' => 5,
            6,
        ], Arr::flatten([
            'a' => [
                'a' => [
                    'a' => 1,
                    'b' => 2,
                ],
                'b' => [
                    'c' => 3,
                ],
            ],
            'b' => [
                'a' => [
                    'c' => 4
                ],
                [
                    'd' => 5,
                ]
            ],
            [
                [
                    'd' => 6,
                ]
            ],
        ], 2, true));

        // Test conflicting string key
        $this->assertSame([
            'c' => 1,
            2,
        ], Arr::flatten([
            'a' => [
                'b' => [
                    'c' => 1,
                ],
            ],
            [
                'c' => 2
            ]
        ], null, true));
    }

    public function testFlattenSingle()
    {
        $this->assertSame([], Arr::flattenSingle([]));
        $this->assertSame(['a'], Arr::flattenSingle([['a']]));
        $this->assertSame([
            'a' => 'test',
            'b' => [
                'test2',
                'c' => 'test3'
            ],
        ], Arr::flattenSingle([
            'a' => ['test'],
            'b' => [
                'test2',
                'c' => ['test3']
            ]
        ]));
        $this->assertSame([
            'a' => 1,
            'b' => 2,
        ], Arr::flattenSingle([
            'a' => [
                'b' => 1
            ],
            'b' => 2,
        ]));
    }

    public function testForceArray()
    {
        $object1 = new ArrayObject();
        $object2 = new stdClass();
        $function = function () {
        };

        $this->assertSame(['a' => 1], Arr::forceArray(['a' => 1]));
        $this->assertSame([], Arr::forceArray([]));
        $this->assertSame([null], Arr::forceArray(null));
        $this->assertSame(null, Arr::forceArray(null, Arr::FORCE_ARRAY_PRESERVE_NULL));
        $this->assertSame([$object1], Arr::forceArray($object1));
        $this->assertSame($object1, Arr::forceArray($object1, Arr::FORCE_ARRAY_PRESERVE_ARRAY_OBJECTS));
        $this->assertSame([$object2], Arr::forceArray($object2));
        $this->assertSame([$object2], Arr::forceArray($object2, Arr::FORCE_ARRAY_PRESERVE_ARRAY_OBJECTS));
        $this->assertSame($object2, Arr::forceArray($object2, Arr::FORCE_ARRAY_PRESERVE_OBJECTS));
        $this->assertSame($object2, Arr::forceArray($object2, Arr::FORCE_ARRAY_PRESERVE_NULL | Arr::FORCE_ARRAY_PRESERVE_OBJECTS | Arr::FORCE_ARRAY_PRESERVE_ARRAY_OBJECTS));
        $this->assertSame([1], Arr::forceArray(1));
        $this->assertSame([1.5], Arr::forceArray(1.5));
        $this->assertSame([0], Arr::forceArray(0));
        $this->assertSame(['test'], Arr::forceArray('test'));
        $this->assertSame(['1'], Arr::forceArray('1'));
        $this->assertSame([$function], Arr::forceArray($function));
    }

    public function testClone()
    {
        $object = new stdClass();
        $object2 = new ArrayObject();
        $object3 = new class()
        {
            public $counter = 1;

            function __clone()
            {
                $this->counter = 2;
            }
        };

        $this->assertSame([1, 2, 'a'], Arr::clone([1, 2, 'a']));
        $this->assertSame([], Arr::clone([]));
        $this->assertEquals([$object], Arr::clone([$object]));
        $this->assertEquals([$object2], Arr::clone([$object2]));
        $this->assertEquals(['a' => $object2, [[$object]]], Arr::clone(['a' => $object2, [[$object]]]));
        $this->assertNotEquals([$object], Arr::clone([$object2]));

        $array = ['test' => $object3];
        $newArray = Arr::clone($array);
        $this->assertSame(1, $array['test']->counter);
        $this->assertSame(2, $newArray['test']->counter);
    }

    public function testRandom()
    {
        $array = [1, 2, 3];
        $array2 = ['test', 'abc'];

        $this->assertContains(Arr::random($array), $array);
        $this->assertIsInt(Arr::random($array));
        $this->assertContains(Arr::random($array2), $array2);
        $this->assertIsString(Arr::random($array2));
        $this->assertIsNotArray(Arr::random($array));
        $this->assertIsNotArray(Arr::random($array2));

        $random = Arr::random($array, 2);
        $this->assertIsArray($random);
        $this->assertCount(2, $random);
        $this->assertSame($random, array_intersect($array, $random));
        $this->assertSame(null, Arr::random([]));

        $this->expectException(InvalidArgumentException::class);
        Arr::random($array2, 4);
    }

    public function testNth()
    {
        $array = range(0, 10);

        $this->assertEquals([0, 2, 4, 6, 8, 10], Arr::even($array), '', 0, 10, true);
        $this->assertEquals([1, 3, 5, 7, 9], Arr::odd($array), '', 0, 10, true);
        $this->assertEquals([2, 6, 10], Arr::nth($array, 4, 2), '', 0, 10, true);
        $this->assertSame($array, Arr::nth($array));
        $this->assertSame(['b' => 2, 'd' => 4], Arr::nth(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4], 2, 1));
        $this->assertSame([], Arr::nth([], 100, 100));
    }

    public function testShuffle()
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
        $this->assertTrue(Arr::hasKeys(Arr::shuffle($array), array_keys($array), true));
        $this->assertSame([], array_diff(Arr::shuffle($array), $array));
    }
}
