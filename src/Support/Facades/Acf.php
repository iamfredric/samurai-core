<?php

namespace Boil\Support\Facades;

use Boil\Acf\AcfConfigurator;
use Boil\Acf\AcfConfiguratorOptionsPage;
use Illuminate\Support\Facades\Facade;

/**
 * @method static AcfConfiguratorOptionsPage addOptionsPage(string $id, string $title, string $menuTitle = null, string $parentSlug = null, int $position = null, string $capability = 'edit_posts', string $iconUrl = '', bool $redirect = false, bool $autoload = false, string $updateButtonLabel = null, string $updateMessage = null, string $slug = null)
 * @method static AcfConfigurator addGroup(string $group)
 * @method static AcfConfigurator addGroups(array $groups)
 */
class Acf extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Boil\Acf\AcfConfigurator::class;
    }
}
