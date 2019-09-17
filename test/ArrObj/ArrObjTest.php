<?php

use Minwork\Helper\Arr;
use Minwork\Helper\ArrObj;
use Test\ArrTestCase;

class ArrObjTest extends ArrTestCase
{
    /** @var ArrObj */
    private $obj;

    public function setUp()
    {
        $this->obj = new ArrObj();
    }

    public function notChainableMethodsProvider(): array
    {
        return Arr::map(array_diff(ArrObj::METHODS, ArrObj::CHAINABLE_METHODS), function ($m) {
            return [$m];
        }, Arr::MAP_ARRAY_VALUE_KEY);
    }

    public function chainableMethodsProvider(): array
    {
        return Arr::map(ArrObj::CHAINABLE_METHODS, function ($m) {
            return [$m];
        }, Arr::MAP_ARRAY_VALUE_KEY);
    }

    public function testIsProperlyInitializing()
    {
        $this->assertSame([], (new ArrObj())->getArray());
        $this->assertSame([], (new ArrObj([]))->getArray());
        $this->assertSame([1, 2, 3], (new ArrObj([1, 2, 3]))->getArray());
        $this->assertSame([1, 2, 3], Arr::obj([1, 2, 3])->getArray());

        $arrayObject = new ArrayObject();
        $this->assertSame($arrayObject, Arr::obj($arrayObject)->getArray());
    }

    /**
     * @param string $method
     * @throws ReflectionException
     * @dataProvider chainableMethodsProvider
     */
    public function testReturnSelf(string $method)
    {
        $this->assertSame($this->obj, $this->obj->$method(...$this->getMockedParams($method)));
    }

    /**
     * @param string $method
     * @throws ReflectionException
     * @dataProvider notChainableMethodsProvider
     */
    public function testNotReturnSelf(string $method)
    {
        $this->assertNotSame($this->obj, $this->obj->$method(...$this->getMockedParams($method)));
    }

    public function testIsChainable()
    {
        $arr = new ArrObj();

        $this->assertSame([
            'test' => 2,
            'test2' => [
                3
            ]
        ], $arr
            ->set('test', 1)
            ->set('test2.[]', 2)
            ->map(function ($val) {
                return $val + 1;
            }, Arr::MAP_ARRAY_VALUE_KEYS_LIST)
            ->set('test3', null)
            ->filterByKeys(['test3'], true)
            ->getArray()
        );
    }

    public function testThrowsExceptionOnInvalidMethod()
    {
        $this->expectException(BadMethodCallException::class);
        /** @noinspection PhpUndefinedMethodInspection */
        (new ArrObj())->invalidMethodTest();
    }
}