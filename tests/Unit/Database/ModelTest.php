<?php

namespace Tests\Feature\Unit\Database;

use Boil\Database\Model;
use Boil\Support\Wordpress\WpHelper;

it('can be constructed', function () {
    $model = new Model(postArray());

    expect($model->title)->toBe('Example post')
        ->and($model->id)->toBe(8)
        ->and($model->getKey())->toBe(8);
});

it('can fetch the current model', function () {
    WpHelper::fake([
        'get_post' => fn () => ['post_id' => 8],
    ]);

    $model = Model::current();

    $this->assertEquals(8, $model->getKey());
});

it('can be fetched via id', function () {
    WpHelper::fake([
        'get_post' => fn ($id) => ['post_id' => $id],
    ]);

    $model = Model::find(10);

    expect($model->id)->toBe(10);
});

it('can be created', function () {
    WpHelper::fake([
        'wp_insert_post' => fn ($data) => 12,
        'get_post' => fn ($id) => ['post_id' => $id],
    ]);

    $created = Model::create([
        'title' => 'Example post',
    ]);

    expect($created->id)->toBe(12);
});

it('can be updated', function () {
    WpHelper::fake([
        'create_post' => fn ($data) => 123,
        'get_post' => fn ($data) => [
            'id' => 123,
            'title' => 'Example post updated',
        ],
        'wp_insert_post' => fn () => 123,
    ]);
    $model = new Model(['id' => 123]);

    $model->update(['title' => 'Example post updated']);

    expect($model->title)->toBe('Example post updated');
});

it('can be saved', function () {
    $helperFake = WpHelper::fake([
        'create_post' => fn ($data) => 222,
        'wp_update_post' => fn () => 222,
        'get_post' => fn ($data) => [
            'id' => 222,
            'title' => 'Example post updated',
        ],
    ]);
    $model = new Model(['id' => 222]);

    $model->title = 'Update that title';
    $model->save();

    expect($model->title)->toBe('Update that title');

    $helperFake->assertCalled(
        'wp_update_post',
        fn ($args) => $args['post_title'] === 'Update that title'
            && $args['ID'] === 222
    );
});

it('can be treated as an array', function () {
    $model = new Model([
        'id' => 123,
        'title' => 'Example post',
    ]);

    expect($model['id'])->toBe(123)
        ->and($model['title'])->toBe('Example post');
});

it('can be casted to an array', function () {
    $model = new Model([
        'id' => 123,
        'title' => 'Example post',
    ]);

    expect($model->toArray())->toBe([
        'id' => 123,
        'title' => 'Example post',
    ]);
});

it('can be casted to a wordpress array', function () {
    $model = new Model([
        'id' => 123,
        'title' => 'Example post',
    ]);

    expect($model->toWordpressArray())->toBe([
        'ID' => 123,
        'post_title' => 'Example post',
    ]);
});

it('can be casted to json', function () {
    $model = new Model([
        'id' => 123,
        'title' => 'Example post',
    ]);

    expect($model->toJson())->toBe(json_encode([
        'id' => 123,
        'title' => 'Example post',
    ], true));
});

it('can have accessors', function () {
    $model = new class(['id' => 111]) extends Model
    {
        public function getFooAttribute()
        {
            return 'bar';
        }
    };

    expect($model->foo)->toBe('bar');
});

function postArray(array $merge = []): array
{
    $data = array_merge([
        'ID' => 8,
        'post_author' => '1',
        'post_date' => '2021-03-23 13:42:07',
        //            'post_date_gmt' => "2021-03-23 12:42:07",
        'post_content' => '',
        'post_title' => 'Example post',
        'post_excerpt' => '',
        'post_status' => 'publish',
        'comment_status' => 'closed',
        'ping_status' => 'closed',
        'post_password' => '',
        'post_name' => 'example-post',
        'to_ping' => '',
        'pinged' => '',
        'post_modified' => '2021-04-19 08:52:56',
        //            'post_modified_gmt' => "2021-04-19 06:52:56",
        'post_content_filtered' => '',
        'post_parent' => 0,
        'guid' => 'http://example.test/?page_id=6',
        'menu_order' => 0,
        'post_type' => 'model',
        'post_mime_type' => '',
        'comment_count' => '0',
        'filter' => 'raw',
    ], $merge);

    ksort($data);

    return $data;
}
