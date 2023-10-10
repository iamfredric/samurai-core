<?php

namespace Samurai\PostTypes;

use Samurai\Support\Concerns\ConfigPath;
use Samurai\Support\Facades\Image;
use Samurai\Support\Wordpress\WpHelper;

class PostTypeConfigurator
{
    /**
     * @param  PostType[]  $postTypes
     */
    public function __construct(
        protected ConfigPath $configPath,
        protected array $postTypes = [],
    ) {
    }

    public function register(PostType|string $type): PostType
    {
        return $this->postTypes[] = $type instanceof PostType ? $type : new PostType(id: $type);
    }

    public function taxonomy(string $id): Taxonomy
    {
        return new Taxonomy($id);
    }

    public function boot(): void
    {
        WpHelper::add_action('init', function () {
            $this->registerPostTypes();
        });
    }

    protected function registerPostTypes(): void
    {
        $this->configPath->include();

        foreach ($this->postTypes as $postType) {
            WpHelper::register_post_type($postType->id, $postType->toArray());

            foreach ($postType->taxonomies as $taxonomy) {
                WpHelper::register_taxonomy($taxonomy->id, $postType->id, $taxonomy->toArray());
            }

            if ($postType->isMediaSupported()) {
                Image::support($postType->id);
            }
        }
    }
}
