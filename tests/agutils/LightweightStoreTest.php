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
    private static $NUMBERS = [1, 2, 3, 4, 5];

    public function testAdd()
    {
        $store = new LightweightStore();
        array_walk(
            self::$NUMBERS,
            function ($add) use ($store) {
                $store->add($add, $add);
                $this->assertEquals($add, $store->get($add));
            }
        );
    }

    public function testGetRepeatable()
    {
        $store = new LightweightStore();
        $key = 'key';
        $value = 'value';
        $store->add($key, $value);
        $this->assertEquals($store->get($key), $store->get($key));
    }

    public function testGetNotSet()
    {
        $keys = [
            null,
            '',
            0,
            1,
            1.5,
            'a'
        ];
        $keys[] = (object) ['value' => 1];  // because.. why not?
        $store = new LightweightStore();
        foreach ($keys as $key) {
            $this->assertFalse(
                $store->get($key),
                'Expected a FALSE value for key: '.print_r($key, true)
            );
        }
    }

    public function testCallable()
    {
        $store = new LightweightStore();
        foreach (self::$NUMBERS as $number) {
            $this->assertEquals(
                $number,
                $store->get(
                    $number,
                    function ($key) {
                        return $key;
                    }
                )
            );
            // So, we auto-created $number as the key and value ... ensure we still know about it
            $this->assertEquals(
                $number,
                $store->get($number),
                "Expected $number to have been stored after being set in callable"
            );
        }
    }

    public function testNullKey()
    {
        // PHP likes to coalesce NULL as '', so let's ensure we can store '' differently to NULL
        // We can use our alreadySeenThat utility to check
        foreach ([null, ''] as $key) {
            $nullOrBlank = $key === null ? "NULL" : "''";
            $this->assertFalse(
                $this->alreadySeenThat($key),
                "Expected ::alreadySeenThat to say NO for the first call with $nullOrBlank"
            );
            $this->assertTrue(
                $this->alreadySeenThat($key),
                "Expected ::alreadySeenThat to say YES for the second call with $nullOrBlank"
            );
        }
    }

    public function testNullStored()
    {
        // Ensure that NULL value is stored and retrieved from cache
        $store = new LightweightStore();
        $store->add('key', null);
        $this->assertTrue(
            $store->has('key'),
            'Expected NULL value to be stored'
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
        foreach (self::$NUMBERS as $number) {
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
