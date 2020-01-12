<?php
namespace Test;

use InvalidArgumentException;
use Minwork\Helper\ArrObj;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

class ArrTestCase extends TestCase
{
    protected $typesMock = [
        'string' => '',
        'array' => [],
        'int' => 1,
        'bool' => true,
        'float' => 1.0,
        'null' => null,
        'callable' => 'boolval',
    ];

    /**
     * @param string $method
     * @param bool $includeFirst
     * @return array
     * @throws ReflectionException
     */
    protected function getMockedParams(string $method, bool $includeFirst = false)
    {
        $reflection = new ReflectionClass('\Minwork\Helper\Arr');
        $reflectionMethod = $reflection->getMethod($method);
        $reflectionParams = $reflectionMethod->getParameters();

        if (!$includeFirst) {
            // Skip first param cause it's array supplied from object
            array_shift($reflectionParams);
        }

        $params = [];

        foreach ($reflectionParams as $reflectionParam) {
            $reflectionType = $reflectionParam->getType();
            $params[] = $this->typesMock[$reflectionType ? $reflectionType->getName() : 'null'];
        }

        return $params;
    }

    protected function createObjectWithMethod(callable $method)
    {
        return new class($method)
        {
            protected $method;

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

    public function arrayProvider(): array
    {
        return [
            [[3]],
            [['key']],
            [[1, 'test']],
            [['key1' => 1, 'key2' => 2, 'key3' => 3]],
            [[[[]]]],
            [[0, '', null, false]],
            [[1, true, 'true', 'false', '0', ' 1', PHP_INT_MIN, PHP_INT_MAX]],
            [[
                'test' => [
                    'test1',
                    'test2' => [
                        'test3' => 'abc',
                        'test4'
                    ],
                    [
                        'test6' => 'def'
                    ],
                ],
            ]],
            ['a' => [
                'b' => [
                    1 => [
                        PHP_INT_MIN,
                        2 => 3
                    ],
                    'c' => [
                        4,
                        true,
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
                    PHP_INT_MAX,
                    'j' => [
                        7,
                        2 => 8,
                        null,
                    ]
                ],
                [
                    'k',
                    'l' => [
                        9,
                        10,
                        false,
                    ],
                ]]
        ];
    }

    protected function callMethod(callable $callable, $array, ...$args)
    {
        if (!is_array($callable)) {
            throw new InvalidArgumentException('Callable must be array');
        }

        $class = $callable[0];
        $method = $callable[1];

        // If calling ArrObj
        if (is_object($class)) {
            /** @var ArrObj $class */

            $class->setArray($array);
            // Call method
            $result = $class->$method(...$args);

            // If method is chainable then get current array value for proper assertion
            if (in_array($method, ArrObj::CHAINABLE_METHODS)) {
                return $class->getArray();
            } else {
                return $result;
            }
        }

        return call_user_func("{$class}::{$method}", $array, ...$args);
    }

    public function arrayClassProvider(): array
    {
        return [
            ['\Minwork\Helper\Arr'],
            [new ArrObj()],
        ];
    }
}