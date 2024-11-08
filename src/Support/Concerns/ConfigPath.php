<?php

namespace Samurai\Support\Concerns;

class ConfigPath
{
    /**
     * @param  string[]|string|null  $paths
     */
    public function __construct(protected array|string|null $paths = null) {}

    public function exists(): bool
    {
        foreach ((array) $this->paths as $path) {
            if (file_exists($path)) {
                return true;
            }
        }

        return false;
    }

    public function include(): void
    {
        foreach ((array) $this->paths as $path) {
            if (file_exists($path)) {
                include_once $path;
            }
        }
    }
}
