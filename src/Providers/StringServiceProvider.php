<?php

namespace Boil\Providers;

use Carbon\Laravel\ServiceProvider;
use Illuminate\Support\Str;

class StringServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Str::macro('tel', function ($number, $countryCode = '46') {
            $number = str_replace('(0)', '', $number);

            $number = preg_replace('/[^+0-9]+/', '', $number);

            if (substr($number, 0, 2) == '00') {
                $number = '+' . substr($number, 2);
            }

            if (substr($number, 0, 1) != '+') {
                $number = "+{$countryCode}" . substr($number, 1);
            }

            return $number;
        });
    }
}
