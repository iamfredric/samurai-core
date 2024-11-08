<?php

namespace Samurai\Acf\Gutenberg;

use Samurai\Application;
use Samurai\Support\Concerns\ConfigPath;
use Samurai\Support\Wordpress\WpHelper;

class GutenbergConfigurator
{
    /** @var class-string[] */
    protected array $blocks = [];

    public function __construct(
        protected Application $app,
    ) {}

    /**
     * @param  class-string  $className
     * @return $this
     */
    public function block(string $className): static
    {
        $this->blocks[] = $className;

        return $this;
    }

    public function boot(): void
    {
        $config = new ConfigPath($this->app['config']->get('features.gutenberg.routes'));

        if (! $config->exists()) {
            return;
        }

        $config->include();

        foreach ($this->blocks as $block) {

            $concrete = $this->app->make($block);

            $array = array_merge($concrete->toArray(), [
                'render_callback' => [$concrete, 'render'],
            ]);

            WpHelper::acf_register_block_type($array);
        }
    }
}
