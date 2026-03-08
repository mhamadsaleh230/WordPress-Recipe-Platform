<?php if(empty($attributes['ingredients'])&&empty($attributes['cuisine'])&&empty($attributes['type'])&&empty($attributes['recipedate']))
    {?><div class="search">
  <input type="text" id="recipe-search" placeholder="Search recipes by title..." />
  <input type="text" id="recipe-cuisine" placeholder="Filter by cuisine..." />
  <input type="text" id="recipe-ingredient" placeholder="Filter by ingredient..." />
</div><?php } ?>

<!-- Search results -->
<div id="search-results" class="container" style="display:none;"></div>

<!-- Default recipes (paged list) -->
<div id="default-recipes" class="container">
   <?php
// Sanitize and generate slug for URL
$heading_slug = sanitize_title($attributes['headingtext'] ?? 'all-recipes');

// Base URL using rewrite rule
$page_url = site_url("/recipe-category/{$heading_slug}/");

// Append query params if needed
$query_params = [];
if (!empty($attributes['cuisine'])) $query_params['cuisine'] = $attributes['cuisine'];
if (!empty($attributes['ingredients'])) $query_params['ingredients'] = $attributes['ingredients'];
if (!empty($attributes['type'])) $query_params['type'] = $attributes['type'];

if (!empty($query_params)) {
    $page_url = add_query_arg($query_params, $page_url);
}

?>

<h2 style="margin-left: 20px;">
    <a href="<?php echo esc_url($page_url); ?>">
        <?php echo esc_html($attributes['headingtext']); ?>
    </a>
</h2>


    <div class="recipe-container">
  <?php
  $block_id = sanitize_title($attributes['headingtext']);
$paged_param = $block_id . '_page';
$currentpage = isset($_GET[$paged_param]) ? intval($_GET[$paged_param]) : 1;

// 🔥 Force WP NOT to override this block's paging
set_query_var('paged', 1);
set_query_var('page', 1);
  $args = [
      'post_type'      => 'recipe',
      'posts_per_page' => 20,
      'paged'          => $currentpage,
      'post_status'    => 'publish',
  ];
  if ( $attributes['recipedate'] ) {
				$args['date_query'] = [
					[
						'after'     => $attributes['recipedate'] ,
						'inclusive' => true,
					]
				];
			}
   $args['meta_query'] = [];

    if ( ! empty( $attributes['cuisine'] ) ) {
    $args['meta_query'][] = [
        'key'     => 'cuisine',
        'value'   => $attributes['cuisine'],
        'compare' => 'LIKE',
        'type' => 'CHAR',
         ];
      }

   if ( ! empty( $attributes['ingredients'] ) ) {
    $ingredients = array_map( 'trim', explode( ',', strtolower( $attributes['ingredients'] ) ) );
    $ingredient_meta = [ 'relation' => 'AND' ];

    foreach ( $ingredients as $ingredient ) {
        $ingredient_meta[] = [
            'key'     => 'ingredients',
            'value'   => $ingredient,
            'compare' => 'LIKE',
            'type'    => 'CHAR',
        ];
    }

    $args['meta_query'][] = $ingredient_meta;
}
if ( ! empty( $attributes['type'] ) ) {
    $types = array_map( 'trim', explode( ',', strtolower( $attributes['type'] ) ) );

    $type_meta = [ 'relation' => 'OR' ];
    foreach ( $types as $type ) {
        $type_meta[] = [
            'key'     => 'type',
            'value'   => $type,
            'compare' => 'LIKE',
            'type'    => 'CHAR',
        ];
    }

    $args['meta_query'][] = $type_meta;
}
  $recipes_query = new WP_Query( $args );

  if ( $recipes_query->have_posts() ) :
      while ( $recipes_query->have_posts() ) : $recipes_query->the_post(); ?>
         <a href="<?php the_permalink(); ?>" class="recipe-card">
   <div class="recipe-image">
    <?php if ( has_post_thumbnail() ) : ?>
    <?php the_post_thumbnail( 'medium' ); else:?>
<image src="https://humansecurity.world/wp-content/uploads/2023/04/food-security.png" alt="no image"/>
<?php endif; ?>

</div>



    <h3><?php the_title(); ?></h3>
    <div class="attributes">
        <?php
        $type = get_post_meta( get_the_ID(), 'type', true );
        if ( $type ) {
            echo '<p><strong>Type :</strong> ' . esc_html( $type ) . '</p>';
        }

        $time = get_post_meta( get_the_ID(), 'readyin', true );
        if ( $time ) {
            echo '<p><strong>Ready In :</strong> ' . esc_html( $time ) . ' minutes</p>';
        }
         $cuisine = get_post_meta( get_the_ID(), 'cuisine', true );
        if ( $cuisine ) {
            echo '<p><strong>Cuisine :</strong> ' . esc_html( $cuisine) . '</p>';
        }

        $health_score = get_post_meta( get_the_ID(), 'health_score', true );
        if($health_score){
        echo '<p><strong>Health Score :</strong> ' . ( $health_score !== '' ? esc_html( $health_score ) : 'N/A' ) . '</p>';
        }
      $ingredients_json = get_post_meta( get_the_ID(), 'ingredients', true );
        ?>
    </div>
</a>

      <?php endwhile; ?> </div>
      <div style="display: flex; flex-direction: row;" class="pagination">
    <?php if(empty($attributes['ingredients'])&&empty($attributes['cuisine'])&&empty($attributes['type'])&&empty($attributes['recipedate'])){
   echo paginate_links([
    'base'      => add_query_arg($paged_param, '%#%'),
    'format'    => '',
    'current'   => $currentpage,
    'total'     => $recipes_query->max_num_pages,
    'add_args'  => array_diff_key($_GET, [$paged_param => true]), // keep other filters
]);}

    ?>
</div>

</div>


<?php
    wp_reset_postdata();
else :
    echo '<p>No recipes found.</p>';
endif;
?>
