<?php

namespace Samurai\Providers;

use Carbon\Laravel\ServiceProvider;
use Samurai\Support\Translations\Translator;

class TranslationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Translator::class, function ($app) {
            $translator = new Translator();

            $translator->setLocale($app->getLocale());

            $translator->loadFromPath(dirname(__DIR__, 2).'/resources/lang');
            //            $translator->loadFromPath($this->app->langPath());

            return $translator;
        });
    }
}
