<?php get_header(); ?>

<div class="container home-container">

	<h1 class="home-title">
		<?php echo get_bloginfo('description'); ?>
	</h1>

	<div class="home-content">
		<?php
			while ( have_posts() ) {
				the_post();
				the_content(); // allows Gutenberg blocks
			}
		?>
	</div>

</div>

<?php get_footer(); ?>
