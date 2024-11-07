<?php

namespace Samurai\Acf\Gutenberg;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Samurai\Support\Transformers\AttributeGetters;
use Samurai\Support\Transformers\AttributesWhenNull;
use Samurai\Support\Transformers\AutoCaster;
use Samurai\Support\Transformers\Caster;
use Samurai\Support\Transformers\MapKeysToCamel;
use Samurai\Support\Transformers\Transformations;
use Samurai\Support\Wordpress\WpHelper;

abstract class Block
{
    /** @var array<string, mixed> */
    protected array $data = [];

    /** @var array<string, mixed> */
    protected array $casts = [];

    /**
     * @param  array<string, mixed>  $data
     * @param  string  $content
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function render($data, $content = '', bool $preview = false): void
    {
        $previewImage = $data['data']['__preview_image'] ?? null;

        if ($preview && $previewImage) {
            echo "<img src=\"{$previewImage}\" >";

            return;
        }

        $this->data = WpHelper::get_fields($data['id']) ?: [];

        echo view(
            str_replace(
                '{name}',
                $this->view(),
                config('features.acf.gutenberg.views_dir', 'gutenberg.{$name}')
            ),
            array_merge($this->transform($this->data), [
                'preview' => $preview,
            ])
        );
    }

    public function view(): string
    {
        $items = explode('\\', get_class($this));

        $item = end($items);

        if (str_ends_with($item, 'Block')) {
            $item = substr($item, 0, strlen($item) - 5);
        }

        return Str::kebab($item);
    }

    /** @param  array<string, mixed>  $data */
    protected function transform(?array $data): mixed
    {
        if (empty($data)) {
            return $data;
        }

        $items = [];

        foreach ($data as $key => $value) {
            if (! str_starts_with($key, '_')) {
                $items[$key] = $value;
            }
        }

        return (new Transformations($items))
            ->through(Caster::class, $this->casts ?? [])
            ->through(AutoCaster::class)
            ->through(AttributeGetters::class, $this)
            ->through(AttributesWhenNull::class, $this)
            ->through(MapKeysToCamel::class)
            ->output();
    }

    public function data(?string $key = null, mixed $default = null): mixed
    {
        if ($key) {
            $data = Arr::dot($this->data);

            return $data[$key] ?? $default;
        }

        return $this->data;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name(),
            'title' => $this->title(),
            'description' => $this->description(),
            'render_callback' => [$this, 'render'],
            'category' => $this->category(),
            'icon' => $this->icon(),
            'keywords' => $this->keyWords(),
            'example' => $this->getExample(),
        ];
    }

    public function name(): string
    {
        return Str::slug($this->title());
    }

    abstract public function title(): string;

    public function description(): ?string
    {
        return null;
    }

    public function category(): ?string
    {
        return null;
    }

    public function icon(): ?string
    {
        return null;
    }

    /** @return string[] */
    public function keyWords(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>[]|null
     */
    protected function getExample(): ?array
    {
        if ($previewImage = $this->getPreviewImageUrl()) {
            return [
                'attributes' => [
                    'mode' => 'preview',
                    'data' => [
                        '__preview_image' => $previewImage,
                    ],
                ],
            ];
        }

        return null;
    }

    protected function getPreviewImageUrl(): ?string
    {
        if (empty(($dir = config('features.acf.gutenberg.preview_image_dir')))) {
            return null;
        }

        $path = implode('/', [theme_path($dir), $this->name().'.jpg']);

        return file_exists($path)
            ? implode('/', [theme_url($dir), $this->name().'.jpg'])
            : null;
    }

    public function previewImageUrl(): ?string
    {
        return null;
    }
}
