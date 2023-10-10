<?php

namespace Samurai\Exceptions;

class BuilderCallNotFoundException extends \Exception
{
    public static function methodNotFound(string $method): BuilderCallNotFoundException
    {
        return new BuilderCallNotFoundException("The query builder call to {$method} was not found");
    }
}
