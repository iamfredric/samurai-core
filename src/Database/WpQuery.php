<?php

namespace Samurai\Database;

class WpQuery
{
    protected static string $instance = \WP_Query::class;

    public static function make(mixed $arguments): mixed
    {
        if (is_callable(static::$instance)) {
            return call_user_func(self::$instance, $arguments); // @phpstan-ignore-line
        }

        return new static::$instance($arguments);
    }

    public static function setInstance(string $instance): void
    {
        static::$instance = $instance;
    }
}
