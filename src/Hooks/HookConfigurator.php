<?php

namespace Boil\Hooks;

use Boil\Application;
use Boil\Support\Concerns\ConfigPath;
use Boil\Support\Wordpress\WpHelper;

class HookConfigurator
{
    public function __construct(
        protected Application $app
    ) {
    }

    /**
     * @param string $name
     * @param callable|string|string[] $callable
     * @param int $priority
     * @param int $acceptedArgs
     * @return void
     */
    public function action(string $name, callable|string|array $callable, int $priority = 10, int $acceptedArgs = 1): void
    {
        WpHelper::add_action(
            $name, fn (...$args) => $this->resolveCallable($callable, $args),
            $priority,
            $acceptedArgs
        );
    }

    /**
     * @param string $name
     * @param callable|string|string[] $callable
     * @param int $priority
     * @param int $acceptedArgs
     * @return void
     */
    public function filter(string $name, callable|string|array $callable, int $priority = 10, int $acceptedArgs = 1): void
    {
        WpHelper::add_filter(
            $name, fn (...$args) => $this->resolveCallable($callable, $args),
            $priority,
            $acceptedArgs
        );
    }

    public function boot(): void
    {
        $config = new ConfigPath($this->app['config']->get('features.hooks.routes'));

        $config->include();
    }

    /**
     * @param callable|string[]|string $callable
     * @param mixed[] $args
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function resolveCallable(callable|array|string $callable, array $args = [])
    {
        if (is_callable($callable)) {
            return $callable(...$args);
        }

        if (! is_array($callable)) {
            $callable = [$callable, '__invoke'];
        }

        return $this->app->make($callable[0])->{$callable[1]}(...$args);
    }
}
