<?php

namespace Boil\Support;

use Illuminate\Contracts\Foundation\MaintenanceMode as LaravelMaintenanceMode;

class MaintenanceMode implements LaravelMaintenanceMode
{
    public function __construct(
        protected ?string $path
    ) {
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function activate(array $payload): void
    {
        //        if (! $this->active()) {
        //            file_put_contents(rtrim($this->path, '/') . '/.maintenance', json_encode($payload));
        //        }
    }

    public function deactivate(): void
    {
        //        if ($this->active()) {
        //            unlink(rtrim($this->path, '/') . '/.maintenance');
        //        }
    }

    public function active(): bool
    {
        return file_exists(rtrim($this->path ?: '', '/').'/.maintenance');
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return [];
    }
}
