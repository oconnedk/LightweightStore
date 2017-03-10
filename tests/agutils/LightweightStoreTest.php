<?php
/**
 * Created by PhpStorm.
 * User: oconnedk
 * Date: 09/03/17
 * Time: 22:14
 */

namespace agutils\tests;

use agutils\LightweightStore;

class LightweightStoreTest extends \PHPUnit_Framework_TestCase
{

    public function testAdd()
    {
        $store = new LightweightStore();
        $addThese = [1, 2, 3];
        array_walk(
            $addThese,
            function ($add) use ($store) {
                $store->add($add, $add);
                $this->assertEquals($add, $store->get($add));
            }
        );
    }

    public function testGetStore()
    {
        $store = LightweightStore::getStore(__METHOD__);
        $this->assertNotEmpty($store);
    }

    public function testScope()
    {
        $this->assertNotSame(
            LightweightStore::getStore(__METHOD__),
            LightweightStore::getStore(__METHOD__."other")
        );
    }

    public function testStaticLikeScope()
    {
        foreach ([1,2,3,4,5] as $number) {
            $this->assertFalse(
                $this->alreadySeenThat($number),
                "Expected ::alreadySeenThat to say NO for the first call with $number"
            );
            $this->assertTrue(
                $this->alreadySeenThat($number),
                "Expected ::alreadySeenThat to say YES for the second call with $number"
            );
        }
    }

    /**
     * Tests the main reason for the library... static-like scoping
     * @param $key
     * @return boolean
     */
    public function alreadySeenThat($key)
    {
        $store = LightweightStore::getStore(__METHOD__);  // Give store local method scope
        $seenIt = $store->has($key);
        $store->add($key, $key);
        return $seenIt;
    }

}