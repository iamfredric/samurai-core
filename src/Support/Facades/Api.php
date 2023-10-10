<?php

namespace Samurai\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Samurai\Routing\Api\ApiRouter;

class Api extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ApiRouter::class;
    }
}
