<?php get_header(); ?>
<div class="container">
<h1 class="page-title"><?php the_title(); ?></h1>
<?php while (have_posts()) : the_post(); the_content(); endwhile; ?>
</div>
<?php get_footer(); ?>
