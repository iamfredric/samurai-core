<?php

namespace Boil\Support\Wordpress;

use Tests\Support\Wordpress\WpHelperFake;

class WpHelper
{
    protected static $instance;

    public static function fake(array $fakes = [])
    {
        return static::$instance = new WpHelperFake($fakes);
    }

    public static function callFunction($function, ...$args)
    {
        if (static::$instance) {
            return static::$instance->callFunction($function, ...$args);
        }

        return $function(...$args);

//        if (isset(static::$fakes[$function])) {
//            return static::$fakes[$function](...$args);
//        }
    }
}
