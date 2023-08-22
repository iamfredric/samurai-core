<?php

namespace Tests\Unit\Acf\Gutenberg\BlockTest;

use Boil\Acf\Gutenberg\Block;

it('has a render method', function () {
    $block = new ExampleBlock();

    // Todo: $block->render();
})->todo();

it('has a view method', function () {
    $block = new ExampleBlock();

    expect($block->view())->toBe('example');
});

it('has a data method', function () {
    $block = new ExampleBlock();

    expect($block->data('field'))->toBe('value');
    expect($block->data('nested.things'))->toBe('stuff');
});

it('has a toArray method', function () {
    $block = new ExampleBlock();

    expect($block->toArray())->toBe([
        'name' => 'my-example-block',
        'title' => 'My example block',
        'description' => null,
        'render_callback' => [$block, 'render'],
        'category' => null,
        'icon' => null,
        'keywords' => [],
        'example' => null,
    ]);
});

it('has a name method', function () {
    $block = new ExampleBlock();

    expect($block->name())->toBe('my-example-block');
});

it('has a title method', function () {
    $block = new ExampleBlock();

    expect($block->title())->toBe('My example block');
});

it('has a description method', function () {
    $block = new ExampleBlock();

    expect($block->description())->toBeNull();
});

it('has a category method', function () {
    $block = new ExampleBlock();

    expect($block->category())->toBeNull();
});

it('has a icon method', function () {
    $block = new ExampleBlock();

    expect($block->icon())->toBeNull();
});

it('has a keyWords method', function () {
    $block = new ExampleBlock();

    expect($block->keyWords())->toBe([]);
});

it('has a previewImageUrl method', function () {
    $block = new ExampleBlock();

    expect($block->previewImageUrl())->toBeNull();
});

class ExampleBlock extends Block
{
    protected array $data = [
        'field' => 'value',
        'other_field' => 'other_value',
        'nested' => [
            'things' => 'stuff'
        ]
    ];

    public function title(): string
    {
        return 'My example block';
    }
}
