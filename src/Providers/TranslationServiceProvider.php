<?php

namespace Boil\Providers;

use Boil\Support\Translations\Translator;
use Carbon\Laravel\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Translator::class, function ($app) {
            $translator = new Translator();

            $translator->loadFromPath(dirname(__DIR__, 2).'/resources/lang');
            //            $translator->loadFromPath($this->app->langPath());

            return $translator;
        });
    }
}
