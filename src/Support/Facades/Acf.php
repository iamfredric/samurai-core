<?php

namespace Samurai\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Samurai\Acf\AcfConfigurator;
use Samurai\Acf\AcfConfiguratorOptionsPage;

/**
 * @method static AcfConfiguratorOptionsPage addOptionsPage(string $id, string $title, string $menuTitle = null, string $parentSlug = null, int $position = null, string $capability = 'edit_posts', string $iconUrl = '', bool $redirect = false, bool $autoload = false, string $updateButtonLabel = null, string $updateMessage = null, string $slug = null)
 * @method static AcfConfigurator addGroup(string $group)
 * @method static AcfConfigurator addGroups(array $groups)
 * @method static AcfConfigurator addGutenbergBlock(string $block)
 * @method static AcfConfigurator addGutenbergBlocks(array $blocks)
 */
class Acf extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Samurai\Acf\AcfConfigurator::class;
    }
}
