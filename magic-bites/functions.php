<?php
// ==============================
// Enqueue Styles & Scripts
// ==============================
function theme_assets() {
    wp_enqueue_style('theme-style', get_stylesheet_uri(), [], filemtime(get_template_directory() . '/style.css'));
    wp_enqueue_script('recipe-search', get_template_directory_uri() . '/recipe-search.js', ['jquery'], null, true);

    wp_localize_script('recipe-search', 'my_recipes_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
}
add_action('wp_enqueue_scripts', 'theme_assets');

// ==============================
// Theme Support
// ==============================
function tap_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');

    // Register Menu
    register_nav_menus([
        'primary' => __('Primary Menu', 'custom-recipe-theme'),
    ]);
}
add_action('after_setup_theme', 'tap_theme_setup');


// ==============================
// Recipe Category Rewrite Rules
// ==============================
function recipe_category_rewrite() {
    add_rewrite_rule('^recipe-category/([^/]+)/?$', 'index.php?recipe_category_title=$matches[1]', 'top');
}
add_action('init', 'recipe_category_rewrite');

function recipe_category_query_var($vars) {
    $vars[] = 'recipe_category_title';
    return $vars;
}
add_filter('query_vars', 'recipe_category_query_var');

add_filter('template_include', function($template) {
    $slug = get_query_var('recipe_category_title');
    if ($slug) {
        $new_template = locate_template('recipe-category-template.php');
        if ($new_template) return $new_template;
    }
    return $template;
});

// ==============================
// AJAX Recipe Search
// ==============================
function ajax_search_recipes() {
    $q = sanitize_text_field($_GET['q'] ?? '');
    $cuisine = sanitize_text_field($_GET['cuisine'] ?? '');
    $ingredient = sanitize_text_field($_GET['ingredient'] ?? '');

    $args = [
        'post_type'      => 'recipe',
        'posts_per_page' => 20,
        'post_status'    => 'publish',
        'meta_query'     => ['relation' => 'AND']
    ];

    if ($cuisine) $args['meta_query'][] = ['key' => 'cuisine', 'value' => $cuisine, 'compare' => 'LIKE'];
    if ($ingredient) {
        $ingredients = array_map('trim', explode(',', $ingredient));
        $ingredient_meta = ['relation' => 'AND'];
        foreach ($ingredients as $ing) $ingredient_meta[] = ['key' => 'ingredients', 'value' => $ing, 'compare' => 'LIKE'];
        $args['meta_query'][] = $ingredient_meta;
    }
    if ($q) $args['s'] = $q;

    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            echo '<a href="' . get_permalink() . '" class="recipe-card"><h3>' . get_the_title() . '</h3></a>';
        }
    } else echo '<p>No recipes found.</p>';
    wp_die();
}
add_action('wp_ajax_search_recipes', 'ajax_search_recipes');
add_action('wp_ajax_nopriv_search_recipes', 'ajax_search_recipes');

// ====================================
// Auto-Assign Primary Menu & Sync Pages Dynamically
// ====================================
function tap_set_primary_menu() {
    $menu_name = 'Main Menu';
    $menu_exists = wp_get_nav_menu_object($menu_name);

    if (!$menu_exists) {
        $menu_id = wp_create_nav_menu($menu_name);
    } else {
        $menu_id = $menu_exists->term_id;
    }

    // Assign menu to primary location
    $locations = get_theme_mod('nav_menu_locations');
    if (!is_array($locations)) $locations = [];
    $locations['primary'] = $menu_id;
    set_theme_mod('nav_menu_locations', $locations);

    // Sync all pages
    tap_sync_pages_to_menu($menu_id);
}

// Sync pages dynamically and remove default Home duplicates
function tap_sync_pages_to_menu($menu_id) {
    if (!$menu_id) return;

    $menu_items = wp_get_nav_menu_items($menu_id);
    $existing_pages = [];

    if ($menu_items) {
        foreach ($menu_items as $item) {
            if ($item->object === 'page') $existing_pages[] = intval($item->object_id);
            else wp_delete_post($item->ID, true); // remove non-page items
        }
    }

    $pages = get_pages(['sort_column' => 'menu_order', 'sort_order' => 'ASC']);
    $front_page_id = get_option('page_on_front');

    // Add Home page first
    if ($front_page_id && !in_array($front_page_id, $existing_pages)) {
        wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title' => get_the_title($front_page_id),
            'menu-item-object' => 'page',
            'menu-item-object-id' => $front_page_id,
            'menu-item-type' => 'post_type',
            'menu-item-status' => 'publish',
        ]);
        $existing_pages[] = $front_page_id;
    }

    // Add all other pages
    foreach ($pages as $page) {
        if (!in_array($page->ID, $existing_pages)) {
            wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title' => $page->post_title,
                'menu-item-object' => 'page',
                'menu-item-object-id' => $page->ID,
                'menu-item-type' => 'post_type',
                'menu-item-status' => 'publish',
            ]);
        }
    }
}

// Hooks
add_action('after_switch_theme', 'tap_set_primary_menu'); // initial theme activation
add_action('save_post_page', function($post_id) {
    tap_set_primary_menu(); // update menu whenever a page is added/edited
});
