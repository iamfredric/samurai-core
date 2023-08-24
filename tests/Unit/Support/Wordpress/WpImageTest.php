<?php

namespace Tests\Unit\Support\Wordpress\WpImageTest;

use Boil\Support\Wordpress\WpHelper;
use Boil\Support\Wordpress\WpImage;

it('can be a wordpress image', function () {
    WpHelper::fake([
        'get_post_thumbnail_id' => fn ($id) => $id,
        'get_the_title' => fn () => 'Me image title',
        'wp_get_attachment_image_url' => fn ($id) => "http://example.com/wp-content/uploads/{$id}.jpg",
        'has_post_thumbnail' => fn () => true,
        //        'acf_get_attachment' => fn () => [
        //            'ID'          => 999,
        //            'id'          => 999,
        //            'title'       => 'Me image title',
        //            'filename'    => "{$id}.jpg",
        //            'filesize'    => 200,
        //            'url'         => "http://example.com/wp-content/uploads/{$id}.jpg",
        //            'link'        => "http://example.com/wp-content/uploads/{$id}.jpg",
        //            'alt'         => 'Alt for me image',
        //            'author'      => 'John Doe',
        //            'description' => 'Coolers',
        //            'caption'     => 'Look at this cool image',
        //            'name'        => 'Cool image',
        //            'status'      => 'published'
        //            'uploaded_to' => $attachment->post_parent,
        //            'date'        => $attachment->post_date_gmt,
        //            'modified'    => $attachment->post_modified_gmt,
        //            'menu_order'  => $attachment->menu_order,
        //            'mime_type'   => $attachment->post_mime_type,
        //            'type'        => $type,
        //            'subtype'     => $subtype,
        //            'icon'        => wp_mime_type_icon( $attachment->ID ),
        //        ]
    ]);

    $image = new WpImage(123);

    expect($image->id())->toBe(123);
    expect($image->identifier())->toBe('thumbnail-123');
    expect($image->title())->toBe('Me image title');
    expect($image->url())->toBe('http://example.com/wp-content/uploads/123.jpg');
    //    expect($image->render($size = null, $attr = []))->toBe();
    //    expect($image->style($size = null))->toBe();
    //    expect($image->styles($size = null))->toBe();
    expect($image->exists())->toBeTrue();
    // Todo: acf_get_attachment expect($image->toArray())->toBe([]);
    //    expect($image->toJson($options = 0): string)->toBe();
})->todo();
