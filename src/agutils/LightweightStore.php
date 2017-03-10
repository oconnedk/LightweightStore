<?php
/**
 * Created by PhpStorm.
 * User: oconnedk
 * Date: 09/03/17
 * Time: 21:51
 */

namespace agutils;

class LightweightStore
{
    /** @var array */
    private $store = [];

    /**
     * Utility to remove the need for method statics
     * @param string $scope
     * @return LightweightStore
     */
    public static function getStore($scope)
    {
        /** @var LightweightStore $cache */
        static $cache = null;
        if ($cache === null) {
            $cache = new self();
        }
        return $cache->get(
            $scope,
            function () {
                return new self();
            }
        );
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return LightweightStore
     */
    public function add($key, $value)
    {
        $this->store[static::makeKey($key)] = $value;
        return $this;
    }

    /**
     * @param mixed $key
     * @param callable|null if a callable is passed, we can create the thing we're after using that
     * @return mixed|false
     */
    public function get($key, callable $createUsingThis = null)
    {
        $got = false;
        $existing = $this->has($key);
        if (!$existing && $createUsingThis !== null) {
            $got = $createUsingThis($key);  // Create it
            $this->add($key, $got);         // Store it
        } elseif ($existing) {
            $got = $this->store[static::makeKey($key)];
        }
        return $got;
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists(static::makeKey($key), $this->store);
    }

    /**
     * @param mixed $key Literally anything which can sensibly be used as a key
     * @return string
     */
    protected static function makeKey($key)
    {
        return print_r($key, true);
    }
}