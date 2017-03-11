LightweightStore
================

This simple micro-package defines a key-value store, with the main reason for existence being that it can serve as a run-time static method cache.

So, instead of repeatedly re-inventing a method static cache mechanism (see below), this can achieve the "is that thing in the cache?" "no? add it" "yes? use it" in one-or-two lines.

Why?
----

Because copy-pasting/ regurgitating "patterns" is error prone, with gotchas lying within `isset` instead of `array_key_exists` etc.

So, instead of:

    public function doSomethingWithAnId($id, $someOtherValue)
    {
        static $cache = [];
        $thatThing = null;
        if (array_key_exists($id, $cache)) {
            $thatThing = $cache[$id];
        } else {
            $thatThing = new ThingThatWillTakeAWhile($id);
        }
        $thatThing->hereIs($someOtherValue);
    }

You can do:

    public function doSomethingWithAnId($id, $someOtherValue)
    {
        $cache = LightweightStore::getStore(__METHOD__);
        $thatThing = $cache->get(
            $id,
            function ($key) {
                return new ThingThatWillTakeAWhile($key);
            }
        );
        $thatThing->hereIs($someOtherValue);
    }

Semantically, that should hopefully be easy to understand. The second argument to `::get` is a `callable` defining how to create that thing we want if it's not in our cache.

The use of `::getStore` is not mandatory, but has the benefit of defining a scope which you can use to lock something down to a method, class, module or application if needs be.

Have a look at the [unit tests](/tests/agutils/LightweightStoreTest.php) for example use.

Installation
-------------

###Composer Installation

See: [Composer Installation](https://getcomposer.org/download/)

    composer install

Unit Tests
----------

    ./vendor/phpunit/phpunit/phpunit - tests


Coding Standard
---------------

All coding should comply with the [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md), enforced by [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer).

Setting the standard to PSR-2:

    vendor/bin/phpcs --config-set default_standard PSR2

Checking for compliance:

    vendor/bin/phpcs src/ tests/


