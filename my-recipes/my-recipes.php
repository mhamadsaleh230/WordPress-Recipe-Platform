<?php
/**
 * Plugin Name:       My Recipes
 * Description:       Example block scaffolded with Create Block tool.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       my-recipes
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Registers the block using a `blocks-manifest.php` file, which improves the performance of block type registration.
 * Behind the scenes, it also registers all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
 */
function create_block_my_recipes_block_init() {
	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` and registers the block type(s)
	 * based on the registered block metadata.
	 * Added in WordPress 6.8 to simplify the block metadata registration process added in WordPress 6.7.
	 *
	 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
	 */
	if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
		wp_register_block_types_from_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
		return;
	}

	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` file.
	 * Added to WordPress 6.7 to improve the performance of block type registration.
	 *
	 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
	 */
	if ( function_exists( 'wp_register_block_metadata_collection' ) ) {
		wp_register_block_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
	}
	/**
	 * Registers the block type(s) in the `blocks-manifest.php` file.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	$manifest_data = require __DIR__ . '/build/blocks-manifest.php';
	foreach ( array_keys( $manifest_data ) as $block_type ) {
		register_block_type( __DIR__ . "/build/{$block_type}" );
	}
}
add_action( 'init', 'create_block_my_recipes_block_init' );

// Register Custom Post Type
function custom_post_type() {

	$labels = array(
		'name'                  => _x( 'recipes', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'recipe', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'recipes', 'text_domain' ),
		'name_admin_bar'        => __( 'recipe', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All Items', 'text_domain' ),
		'add_new_item'          => __( 'Add New Item', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Item', 'text_domain' ),
		'edit_item'             => __( 'Edit Item', 'text_domain' ),
		'update_item'           => __( 'Update Item', 'text_domain' ),
		'view_item'             => __( 'View Item', 'text_domain' ),
		'view_items'            => __( 'View Items', 'text_domain' ),
		'search_items'          => __( 'Search Item', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'recipe', 'text_domain' ),
		'description'           => __( 'creating a recipe', 'text_domain' ),
		'labels'                => $labels,
		'show_in_rest' => true,
        'supports' => array( 'title', 'editor', 'thumbnail' ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type' => 'post',
		'menu_icon'            =>'dashicons-food',
	);
	register_post_type( 'recipe', $args );

}
add_action( 'init', 'custom_post_type', 0 );


function my_recipes_set_block_template() {
    $post_type_object = get_post_type_object( 'recipe' );
    if ( $post_type_object ) {
        $post_type_object->template = array(
           array( 'create-block/my-recipes' ), // Adjust block name to your actual block name
        );
        $post_type_object->template_lock = false;
    }
}
add_action( 'init', 'my_recipes_set_block_template', 11 );

function fetch_all_recipes_from_spoonacular( $total ) {
    if ( ! defined( 'SPOONACULAR_API_KEY' ) ) {
        return new WP_Error( 'no_api_key', 'Spoonacular API key not defined.' );
    }

    $limit_per_request = 100; // Spoonacular allows up to 100 per request
    $offset = 0;

    while ( $offset < $total ) {
        $endpoint = "https://api.spoonacular.com/recipes/complexSearch?addRecipeInformation=true&number=$limit_per_request&offset=$offset&apiKey=" . SPOONACULAR_API_KEY;

        $response = wp_remote_get( $endpoint );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( empty( $data['results'] ) ) {
            break; // Stop if no more recipes returned
        }

        foreach ( $data['results'] as $recipe ) {
            insert_recipe_post_from_api( $recipe );
        }

        $offset += $limit_per_request;
        sleep(1); // Avoid hitting API rate limits
    }

    return true;
}


function insert_recipe_post_from_api( $recipe ) {
    $title = sanitize_text_field( $recipe['title'] );

    // Avoid duplicate titles
    $existing = get_page_by_title( $title, OBJECT, 'recipe' );
    if ( $existing ) return;

    // Insert the post
    $post_id = wp_insert_post([
        'post_title'  => $title,
        'post_type'   => 'recipe',
        'post_status' => 'publish',
    ]);

    if ( is_wp_error( $post_id ) || ! $post_id ) return;

    // Save meta fields
    update_post_meta( $post_id, 'prep_time', $recipe['preparationMinutes'] ?? '' );
    update_post_meta( $post_id, 'readyin', $recipe['readyInMinutes'] ?? '' );
    update_post_meta( $post_id, 'servings', $recipe['servings'] ?? '' );

    // Cuisine
    $cuisines = !empty($recipe['cuisines']) ? implode(', ', $recipe['cuisines']) : '';
    update_post_meta( $post_id, 'cuisine', $cuisines );

    // Dish types
    $dish_types = !empty($recipe['dishTypes']) ? implode(', ', $recipe['dishTypes']) : '';
    update_post_meta( $post_id, 'type', $dish_types );

    // Health score
    update_post_meta( $post_id, 'health_score', $recipe['healthScore'] ?? '' );

    // Instructions (cleaned)
    update_post_meta( $post_id, 'summary', wp_strip_all_tags( $recipe['summary'] ?? '' ) );

    // Ingredients
    $full_recipe = fetch_full_recipe_info_from_spoonacular( $recipe['id'] );

if ( ! is_wp_error( $full_recipe ) && ! empty( $full_recipe['extendedIngredients'] ) ) {
    $ingredients_json = json_encode( $full_recipe['extendedIngredients'] );
    update_post_meta( $post_id, 'ingredients', $ingredients_json );
}

// Save instructions (raw HTML from Spoonacular)
if (!empty($full_recipe['instructions'])) {
    $instructions_html = wp_kses_post($full_recipe['instructions']);
    update_post_meta($post_id, 'instructions', $instructions_html);
}

    // Set featured image
    if ( ! empty( $recipe['image'] ) ) {
        set_featured_image_from_url( $post_id, $recipe['image'] );
    }

}
function fetch_full_recipe_info_from_spoonacular( $recipe_id ) {
    if ( ! defined( 'SPOONACULAR_API_KEY' ) ) {
        return new WP_Error( 'no_api_key', 'Spoonacular API key not defined.' );
    }

    $endpoint = "https://api.spoonacular.com/recipes/{$recipe_id}/information?apiKey=" . SPOONACULAR_API_KEY;

    $response = wp_remote_get( $endpoint );

    if ( is_wp_error( $response ) ) {
        return $response;
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    if ( empty( $data ) ) {
        return new WP_Error( 'no_data', 'No data returned from Spoonacular.' );
    }

    return $data;
}



function set_featured_image_from_url( $post_id, $image_url ) {
	$tmp = download_url( $image_url );

	if ( is_wp_error( $tmp ) ) return;

	$file_array = [
		'name'     => basename( $image_url ),
		'tmp_name' => $tmp
	];

	$id = media_handle_sideload( $file_array, $post_id );

	if ( ! is_wp_error( $id ) ) {
		set_post_thumbnail( $post_id, $id );
	}else {
        // Use default Base64 image if API did not return an image
        set_default_featured_image_for_recipe($post_id);
    }
}



add_action( 'admin_post_fetch_spoonacular', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized' );
	}

	$result = fetch_all_recipes_from_spoonacular( 20); // Fetch recipes

	if ( is_wp_error( $result ) ) {
		wp_die( $result->get_error_message() );
	}

	wp_redirect( admin_url( 'edit.php?post_type=recipe' ) );
	exit;
} );


register_activation_hook( __FILE__, 'my_recipes_on_plugin_activation' );

function my_recipes_on_plugin_activation() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Fetch recipes automatically on plugin activation
	fetch_all_recipes_from_spoonacular( 20);
}

add_action( 'wp_enqueue_scripts', 'enqueue_my_recipe_ajax' );
function enqueue_my_recipe_ajax() {
	wp_enqueue_script(
		'my-recipe-search',
		plugins_url( 'search.js', __FILE__ ), // Adjust this path
		[ 'wp-element' ],
		null,
		true
	);

	wp_localize_script( 'my-recipe-search', 'my_recipes_ajax', [
		'ajax_url' => admin_url( 'admin-ajax.php' )
	] );
}

add_action('wp_ajax_search_recipes', 'handle_recipe_search');
add_action('wp_ajax_nopriv_search_recipes', 'handle_recipe_search');

function handle_recipe_search() {
    $search_query = sanitize_text_field($_GET['q'] ?? '');
    $cuisine = sanitize_text_field($_GET['cuisine'] ?? '');
    $ingredient = sanitize_text_field($_GET['ingredient'] ?? '');
    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;

    $args = [
        'post_type'      => 'recipe',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'paged'          => $paged,
        's'              => $search_query,
        'meta_query'     => [],
    ];

   $meta_query = [
    'relation' => 'AND', // always at the top
];

if (!empty($cuisine)) {
    $meta_query[] = [
        'key'     => 'cuisine',
        'value'   => $cuisine,
        'compare' => 'LIKE',
    ];
}

if (!empty($ingredient)) {
    $ingredient = strtolower($ingredient);
    $meta_query[] = [
        'key'     => 'ingredients',
        'value'   =>  $ingredient,
        'compare' => 'LIKE',
    ];
}

$args['meta_query'] = $meta_query;


    $recipes_query = new WP_Query($args);

    if ($recipes_query->have_posts()) {
        echo '<div class="recipe-container">';
while ($recipes_query->have_posts()) {
    $recipes_query->the_post();
    ?>
    <a class="recipe-card" href="<?php the_permalink(); ?>">
    <div class="recipe-image">
       <?php if ( has_post_thumbnail() ) : ?>
    <?php the_post_thumbnail( 'medium' ); else:?>
    <image src="https://humansecurity.world/wp-content/uploads/2023/04/food-security.png" alt="no image"/>
<?php endif; ?>
    </div>

    <h3><?php the_title(); ?></h3>

    <div class="attributes">
        <p><strong>Type :</strong> <?= esc_html(get_post_meta(get_the_ID(), 'type', true)); ?></p>
        <p><strong>Ready In :</strong> <?= esc_html(get_post_meta(get_the_ID(), 'readyin', true)); ?> minutes</p>
        <p><strong>Health Score :</strong> <?= esc_html(get_post_meta(get_the_ID(), 'health_score', true)); ?></p>
        <p><strong>Cuisine :</strong> <?= esc_html(get_post_meta(get_the_ID(), 'cuisine', true)); ?></p>
    </div>
</a>

    <?php
}
echo '</div>';

    } else {
        echo '<p>No recipes found.</p>';
    }

    wp_reset_postdata();
    wp_die();
}

