<?php

namespace Samurai\Providers;

use Carbon\Laravel\ServiceProvider;
use Illuminate\Support\Str;

class StringServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Str::macro('tel', function (string $number, $countryCode = '46') {
            $number = str_replace('(0)', '', $number);

            $number = preg_replace('/[^+0-9]+/', '', $number);

            if (empty($number)) {
                return $number;
            }

            if (str_starts_with($number, '00')) {
                $number = '+'.substr($number, 2);
            }

            if (! str_starts_with($number, '+')) {
                $number = "+{$countryCode}".substr($number, 1);
            }

            return $number;
        });
    }
}
