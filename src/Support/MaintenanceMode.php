<?php

namespace Boil\Support;

use Illuminate\Contracts\Foundation\MaintenanceMode as LaravelMaintenanceMode;

class MaintenanceMode implements LaravelMaintenanceMode
{
    /**
     * @param array<string, mixed> $payload
     * @return void
     */
    public function activate(array $payload): void
    {
    }

    public function deactivate(): void
    {
    }

    public function active(): bool
    {
        return false;
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return [];
    }
}
