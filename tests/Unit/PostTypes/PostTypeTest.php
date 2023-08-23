<?php

namespace Tests\Unit\PostTypes\PostTypeTest;

use Boil\PostTypes\PostType;

it('can be a post type', function () {
   $postType = new PostType('test-post-type');

   $postType->supports('title', 'editor', 'thumbnail')
       ->singular('Me singular name')
       ->plural('Me plural name')
       ->isPrivate()
       ->position(123)
       ->icon('dashicons-admin-post')
       ->isExportable()
       ->deleteWithUser()
       ->hierarchical()
       ->capability('edit_posts')
       ->hasArchives()
       ->hasIndexPage()
       ->slug('test-post-type')
       ->useGutenberg();

    expect($postType->isMediaSupported())->toBeTrue();

    expect($postType->toArray())->toBe([
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 123,
        'menu_icon' => 'dashicons-admin-post',
        'can_export' => true,
        'delete_with_user' => true,
        'hierarchical' => true,
        'has_archive' => true,
        'query_var' => false,
        'capability_type' => 'edit_posts',
        'show_in_rest' => true,
        'supports' => [
            'title',
            'editor',
            'thumbnail',
        ],
        'rewrite' => [
            'slug' => 'test-post-type'
        ],
        'labels' => [
            'name' => 'Me plural name',
            'singular_name' => 'me singular name',
            'menu_name' => 'Me plural name',
            'name_admin_bar' => 'Me plural name',
            'add_new' => 'Add me singular name',
            'add_new_item' => 'Add new me singular name',
            'edit_item' => 'Edit me singular name',
            'new_item' => 'New me singular name',
            'view_item' => 'View me singular name',
            'search_items' => 'Search Me plural name',
            'not_found' => 'No me singular name found',
            'not_found_in_trash' => 'No Me plural name found in trash',
            'all_items' => 'All Me plural name',
            'parent_item' => 'Parent me singular name',
            'parent_item_colon' => 'Parent: me singular name',
            'archive_title' => 'Archive: Me plural name',
        ]
    ]);
});
