<?php

namespace Boil\Support\Facades;

use Boil\Routing\Api\ApiRouter;
use Illuminate\Support\Facades\Facade;

class Api extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ApiRouter::class;
    }
}
