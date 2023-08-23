<?php

namespace Boil\Support\Facades;

use Boil\Support\Translations\Translator;
use Illuminate\Support\Facades\Facade;

class Lang extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Translator::class;
    }
}
