<?php

namespace Boil\Acf\Gutenberg;

use Boil\Support\Transformers\AttributeGetters;
use Boil\Support\Transformers\AttributesWhenNull;
use Boil\Support\Transformers\AutoCaster;
use Boil\Support\Transformers\Caster;
use Boil\Support\Transformers\MapKeysToCamel;
use Boil\Support\Transformers\Transformations;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class Block
{
    protected array $data = [];

    public function render($data, $content = '', $preview = false)
    {
        $previewImage = $data['data']['__preview_image'] ?? null;

        if ($preview && $previewImage) {
            echo "<img src=\"{$previewImage}\" >";

            return;
        }

        $this->data = get_fields($data['id']) ?: [];

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

    protected function transform(?array $data)
    {
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

    public function data(string $key = null, $default = null)
    {

        if ($key) {
            $data = Arr::dot($this->data);

            return $data[$key] ?? $default;
        }

        return $this->data;
    }

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

    public function keyWords(): array
    {
        return [];
    }

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
