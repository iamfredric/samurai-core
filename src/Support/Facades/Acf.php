<?php

namespace Boil\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Acf extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Boil\Acf\AcfConfigurator::class;
    }
}
