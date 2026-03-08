<?php
/*
Template Name: Recipe Category Template
*/
get_header();

// Get the category slug from rewrite
$slug = get_query_var('recipe_category_title', 'all-recipes'); // fallback to 'all-recipes'
$headingtext = ucwords(str_replace('-', ' ', $slug));

// Filters from query string
$cuisine     = sanitize_text_field($_GET['cuisine'] ?? '');
$ingredients = sanitize_text_field($_GET['ingredients'] ?? '');
$type        = sanitize_text_field($_GET['type'] ?? '');
$recipedate  = sanitize_text_field($_GET['recipedate'] ?? '');

// Display heading
echo "<div style='text-align:center; margin:20px 0 30px 0;'>
        <h1 class='title' style='color:#0073aa; font-style:italic; text-shadow:2px 2px 4px rgba(0,0,0,0.2);'>" 
        . esc_html($headingtext) . 
     "</h1>
      </div>";

// Display search inputs only if no filters applied
if (empty($cuisine) && empty($ingredients) && empty($type) && empty($recipedate)) {
    ?>
    <div class="search-wrapper" style="text-align:center; margin:20px 0;">
        <div class="search" style="display:inline-flex; gap:10px;">
            <input type="text" id="recipe-search" placeholder="Search recipes by title..." style="padding:5px 10px;" />
            <input type="text" id="recipe-cuisine" placeholder="Filter by cuisine..." style="padding:5px 10px;" />
            <input type="text" id="recipe-ingredient" placeholder="Filter by ingredient..." style="padding:5px 10px;" />
        </div>
    </div>
    <?php
}

// Container for search results (AJAX can populate this)
echo '<div id="search-results" class="container" style="display:none;"></div>';

// Default recipes container
echo '<div id="default-recipes" class="container">';

// Pagination param unique per block
$paged_param = sanitize_title($headingtext) . '_page';
$currentpage = isset($_GET[$paged_param]) ? intval($_GET[$paged_param]) : 1;

// Build WP_Query args
$args = [
    'post_type'      => 'recipe',
    'posts_per_page' => 40,
    'paged'          => $currentpage,
    'post_status'    => 'publish',
    'meta_query'     => ['relation' => 'AND'],
];

// Date filter
if ($recipedate) {
    $args['date_query'] = [
        [
            'after'     => $recipedate,
            'inclusive' => true,
        ]
    ];
}

// Cuisine filter
if ($cuisine) {
    $args['meta_query'][] = [
        'key'     => 'cuisine',
        'value'   => $cuisine,
        'compare' => 'LIKE',
        'type'    => 'CHAR',
    ];
}

// Ingredients filter (AND)
if ($ingredients) {
    $ingredient_array = array_map('trim', explode(',', strtolower($ingredients)));
    $ingredient_meta  = ['relation' => 'AND'];

    foreach ($ingredient_array as $ingredient) {
        $ingredient_meta[] = [
            'key'     => 'ingredients',
            'value'   => $ingredient,
            'compare' => 'LIKE',
            'type'    => 'CHAR',
        ];
    }

    $args['meta_query'][] = $ingredient_meta;
}

// Type filter (OR)
if ($type) {
    $type_array = array_map('trim', explode(',', strtolower($type)));
    $type_meta  = ['relation' => 'OR'];

    foreach ($type_array as $t) {
        $type_meta[] = [
            'key'     => 'type',
            'value'   => $t,
            'compare' => 'LIKE',
            'type'    => 'CHAR',
        ];
    }

    $args['meta_query'][] = $type_meta;
}

$recipes_query = new WP_Query($args);

if ($recipes_query->have_posts()) :
    echo '<div class="recipe-container">';
    while ($recipes_query->have_posts()) : $recipes_query->the_post(); 
        $recipe_type    = get_post_meta(get_the_ID(), 'type', true);
        $ready_in       = get_post_meta(get_the_ID(), 'readyin', true);
        $recipe_cuisine = get_post_meta(get_the_ID(), 'cuisine', true);
        $health_score   = get_post_meta(get_the_ID(), 'health_score', true);
    ?>
        <a href="<?php the_permalink(); ?>" class="recipe-card">
            <div class="recipe-image">
                <?php 
                if (has_post_thumbnail()) {
                    the_post_thumbnail('medium');
                } else { ?>
                    <image src="https://humansecurity.world/wp-content/uploads/2023/04/food-security.png" alt="no image"/>
                <?php } ?>
            </div>
            <h3><?php the_title(); ?></h3>
            <div class="attributes">
                <?php 
                if ($recipe_type) echo '<p><strong>Type:</strong> ' . esc_html($recipe_type) . '</p>';
                if ($ready_in) echo '<p><strong>Ready In:</strong> ' . esc_html($ready_in) . ' minutes</p>';
                if ($recipe_cuisine) echo '<p><strong>Cuisine:</strong> ' . esc_html($recipe_cuisine) . '</p>';
                echo '<p><strong>Health Score:</strong> ' . ($health_score !== '' ? esc_html($health_score) : 'N/A') . '</p>';
                ?>
            </div>
        </a>
    <?php
    endwhile;
    echo '</div>'; // .recipe-container

    // Pagination
    echo '<div class="pagination" style="display:flex; justify-content:center; gap:10px; margin:30px 0;">';
    echo paginate_links([
        'base'      => add_query_arg($paged_param, '%#%'),
        'format'    => '',
        'current'   => $currentpage,
        'total'     => $recipes_query->max_num_pages,
        'add_args'  => array_diff_key($_GET, [$paged_param => true]), // preserve other filters
        'prev_text' => '« Prev',
        'next_text' => 'Next »',
    ]);
    echo '</div>';

else :
    echo '<p style="margin:20px;">No recipes found.</p>';
endif;

wp_reset_postdata();
echo '</div>'; // #default-recipes

get_footer();
