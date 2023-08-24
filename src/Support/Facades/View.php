<?php

namespace Boil\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Illuminate\View\Factory
 *
 * @method static \Illuminate\Contracts\View\View make(string $view, array $data = [], array $mergeData = [])
 * @method static string renderWhen(bool $condition, string $view, array $data = [], array $mergeData = [])
 * @method static string renderUnless(bool $condition, string $view, array $data = [], array $mergeData = [])
 * @method static bool exists(string $view)
 * @method static mixed share(array|string $key, mixed $value = null)
 */
class View extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'view';
    }
}
