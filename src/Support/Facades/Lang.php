<?php

namespace Samurai\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Samurai\Support\Translations\Translator;

class Lang extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Translator::class;
    }
}
