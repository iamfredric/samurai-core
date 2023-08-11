<?php

namespace Boil\Routing;

class Routes
{
    protected array $templates = [];

    public function register(string $name, string|array|callable $callback)
    {
        $this->templates[$name] = $callback;
    }

    public function template()
    {
        // Todo...
    }

    public function isRegistered(string $template)
    {
        if (isset($this->templates[$template])) {
            return $template;
        }

        $template = str_replace('.php', '', $template);

        if (isset($this->templates[$template])) {
            return $template;
        }

        return false;
    }

    public function getCurrentRoute()
    {
        return 'A current route has emerged!';
    }

    public function resolve(string $route)
    {
        return $this->templates[$route];
    }
}
