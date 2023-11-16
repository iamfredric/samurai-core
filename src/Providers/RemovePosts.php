<?php

namespace Samurai\Providers;

use Carbon\Laravel\ServiceProvider;
use Samurai\Hooks\HookConfigurator;
use Samurai\Support\Wordpress\WpHelper;

class RemovePosts extends ServiceProvider
{
    public function boot(): void
    {
        $hook = $this->app->make(HookConfigurator::class);

        $hook->action('admin_menu', fn () => WpHelper::remove_menu_page('edit.php'));

        $hook->action('admin_bar_menu', fn ($bar) => $bar->remove_node('new-post'), 999);

        $hook->action('wp_dashboard_setup', fn () => WpHelper::remove_meta_box('dashboard_quick_press', 'dashboard', 'side'), 999);
    }
}
