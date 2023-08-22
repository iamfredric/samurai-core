<?php

namespace Tests\Unit\PostTypes\TaxonomyTest;

use Boil\PostTypes\Taxonomy;
use Boil\Support\Wordpress\WpHelper;

it('can be a taxonomy', function () {
    WpHelper::fake([
        '__' => fn ($string) => $string,
    ]);

   $taxonomy = new Taxonomy('test-taxonomy');

    $taxonomy->plural('Test taxonomies')
        ->singular('Test taxonomy')
        ->description('Test description')
        ->queryVar('test-query-var')
        ->isPublic()
        ->isHierarchical()
        ->showUi()
        ->showTagCloud()
        ->slug('test-slug')
        ->useGutenberg()
        ->showUi()
        ->showTagCloud()
        ->slug('test-slug');

    expect($taxonomy->toArray())->toBe([
        'labels' => [
            'name' => 'test taxonomies',
            'singular_name' => 'test taxonomy',
            'search_items' => 'Search test taxonomies',
            'popular_items' => 'Popular test taxonomies',
            'all_items' => 'All test taxonomies',
            'parent_item' => 'Parent test taxonomy',
            'parent_item_colon' => 'Parent: test taxonomy',
            'edit_item' => 'Edit test taxonomy',
            'view_item' => 'Show test taxonomy',
            'update_item' => 'Update test taxonomy',
            'add_new_item' => 'Add test taxonomy',
            'new_item_name' => 'New test taxonomy name',
            'add_or_remove_items' => 'Add/Remove test taxonomy',
            'choose_from_most_used' => 'Choose from most used test taxonomies',
            'not_found' => 'No test taxonomy found',
            'no_terms' => 'No test taxonomies',
        ],
        'query_var' => 'test-query-var',
        'description' => 'Test description',
        'public' => true,
        'hierarchical' => true,
        'show_ui' => true,
        'show_tagcloud' => true,
        'show_in_rest' => true,
        'rewrite' => [
            'slug' => 'test-slug'
        ],
    ]);
});
