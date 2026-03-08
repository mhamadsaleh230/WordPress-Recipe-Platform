<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/download.png" type="image/png" />
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<header class="site-header">
    <div class="container">
        <div class="logo">
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/download.png" alt="<?php bloginfo('name'); ?>" />
            </a>
        </div>
        <nav class="site-nav">
           <?php
        wp_nav_menu([
            'theme_location' => 'primary',
            'menu_class'     => 'nav-menu',
            'container'      => false
        ]);
            ?>
        </nav>

    </div>
</header>
<main>
