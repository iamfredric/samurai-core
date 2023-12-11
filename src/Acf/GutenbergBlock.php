<?php

namespace Samurai\Acf;

use Extended\ACF\Fields\Field;
use Extended\ACF\Location;
use Illuminate\Support\Str;
use Samurai\Support\Transformers\AttributeGetters;
use Samurai\Support\Transformers\AttributesWhenNull;
use Samurai\Support\Transformers\AutoCaster;
use Samurai\Support\Transformers\Caster;
use Samurai\Support\Transformers\MapKeysToCamel;
use Samurai\Support\Transformers\Transformations;

abstract class GutenbergBlock
{
    protected array $casts = [];

    abstract public function title(): string;

    public function name(): string
    {
        return Str::of(class_basename($this))->remove('Block')->kebab()->toString();
    }

    protected function description(): ?string
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

    /** @return list<Field> */
    abstract public function fields(): array;

    public function render($data, $content = '', $preview = false)
    {
        $blockData = get_fields($data['id']) ?: [];

        $blockData = (new Transformations($blockData))
            ->through(Caster::class, $this->casts)
            ->through(AutoCaster::class)
            ->through(AttributesWhenNull::class, $this)
            ->through(AttributeGetters::class, $this)
            ->through(MapKeysToCamel::class)
            ->output();

        echo view($this->getViewName(), $blockData)->render();
    }

    public function getFieldGroupRegistrationAttributes(): array
    {
        return [
            'title' => $this->title(),
            'key' => $this->name().'-block',
            'fields' => $this->fields(),
            'location' => [
                Location::where('block', '=', 'acf/'.$this->name()),
            ],
            'style' => 'seamless',
            'menu_order' => 20,
        ];
    }

    public function getBlockRegistrationsAttributes(): array
    {
        return [
            'name' => $this->name(),
            'title' => $this->title(),
            'description' => $this->description(),
            'render_callback' => [$this, 'render'],
            'category' => $this->category(),
            'icon' => $this->icon(),
            'keywords' => $this->keyWords(),
            'mode' => 'edit', // Todo: auto, preview, edit
            'align' => 'full', // Todo: “left”, “center”, “right”, “wide” and “full”.
            // https://www.advancedcustomfields.com/resources/acf_register_block_type/

        ];
    }

    protected function getViewName(): string
    {
        return Str::of(class_basename($this))->remove('Block')->kebab()->prepend('gutenberg.')->__toString();
    }
}
