<?php get_header(); ?>

<main class="container">

<?php
if (have_posts()) :
    while (have_posts()) : the_post();

        // Load meta fields
        $prep_time    = get_post_meta(get_the_ID(), 'prep_time', true);
        $readyin      = get_post_meta(get_the_ID(), 'readyin', true);
        $servings     = get_post_meta(get_the_ID(), 'servings', true);
        $cuisine      = get_post_meta(get_the_ID(), 'cuisine', true);
        $type         = get_post_meta(get_the_ID(), 'type', true);
        $health_score = get_post_meta(get_the_ID(), 'health_score', true);
        $summary      = get_post_meta(get_the_ID(), 'summary', true);
        $instructions_html = get_post_meta(get_the_ID(), 'instructions', true);


        // Decode ingredients JSON
        $ingredients_raw = get_post_meta(get_the_ID(), 'ingredients', true);
        $ingredients_raw = wp_unslash($ingredients_raw);  // Remove slashes
        $ingredients = json_decode($ingredients_raw, true);

if (!is_array($ingredients)) {
    $ingredients = [];
}

?>

<article class="single-recipe" style="margin-bottom:40px;">

    <h1 class="page-title"><?php the_title(); ?></h1>

    <div class="recipe-image">
        <?php if (has_post_thumbnail()): ?>
            <?php the_post_thumbnail('full', ['class' => 'single-recipe-image']); ?>
        <?php else: ?>
            <img src="<?php echo get_template_directory_uri(); ?>/download.png" alt="Recipe image"/>
        <?php endif; ?>
    </div>

    <div class="recipe-content" style="margin-bottom:20px;">
        <?php the_content(); ?>
    </div>

    <div class="recipe-meta" style="padding:15px; border:1px solid #ddd; border-radius:8px; background:#f9f9f9;">

        <?php if ($prep_time): ?>
            <p><strong>Prep Time:</strong> <?= esc_html($prep_time) ?></p>
        <?php endif; ?>

        <?php if ($readyin): ?>
            <p><strong>Ready In:</strong> <?= esc_html($readyin) ?> minutes</p>
        <?php endif; ?>

        <?php if ($servings): ?>
            <p><strong>Servings:</strong> <?= esc_html($servings) ?></p>
        <?php endif; ?>

        <?php if ($cuisine): ?>
            <p><strong>Cuisine:</strong> <?= esc_html($cuisine) ?></p>
        <?php endif; ?>

        <?php if ($type): ?>
            <p><strong>Type:</strong> <?= esc_html($type) ?></p>
        <?php endif; ?>

        <?php if ($health_score): ?>
            <p><strong>Health Score:</strong> <?= esc_html($health_score) ?></p>
        <?php endif; ?>

        <?php if ($summary): ?>
            <p><strong>Summary:</strong> <?= esc_html($summary) ?></p>
        <?php endif; ?>

        <!-- Ingredients List -->
      <?php if (!empty($ingredients)): ?>
    <h3>Ingredients:</h3>
    <ul class="ingredients-list">
        <?php foreach ($ingredients as $item): 
            $name   = $item['name'] ?? '';
            $amount = $item['amount'] ?? '';
            $unit   = $item['unit'] ?? '';
        ?>
            <li>
                <span class="ingredient-amount"><?= esc_html($amount) ?></span>
                <span class="ingredient-unit"><?= esc_html($unit) ?></span>
                <span class="ingredient-name"><?= esc_html($name) ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (!empty($instructions_html)): ?>
    <h3>Instructions:</h3>
    <div class="recipe-instructions">
        <?= wp_kses_post($instructions_html); ?>
    </div>
<?php endif; ?>


    </div>

    <?php
    $terms = get_the_terms(get_the_ID(), 'recipe_category');
    if ($terms && !is_wp_error($terms)):
    ?>
        <div class="recipe-categories" style="margin-top:20px;">
            <strong>Categories:</strong>
            <?= implode(', ', array_map(fn($t) => '<a href="'.get_term_link($t).'">'.esc_html($t->name).'</a>', $terms)); ?>
        </div>
    <?php endif; ?>

</article>

<?php
    endwhile;
endif;
?>

</main>

<?php get_footer(); ?>
