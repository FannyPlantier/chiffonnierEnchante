<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title(); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header id="mon-header">       
        <!-- Logo -->
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo-chiffonnier.png" 
                 alt="<?php bloginfo('name'); ?>" class="site-logo desktop-logo">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo-chiffonnier-text2.png" 
                 alt="<?php bloginfo('name'); ?>" class="site-logo mobile-logo">
        </a>

         <!-- Bouton burger -->
        <button id="menu-toggle" aria-label="Toggle navigation" aria-expanded="false">
             <span class="menu-icon"></span>
        </button>
        
        <!-- Menu principal -->
        <nav id="primary-nav" aria-hidden="true">
            <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => '', 
                ]);
            ?>
        </nav>
    </header>
