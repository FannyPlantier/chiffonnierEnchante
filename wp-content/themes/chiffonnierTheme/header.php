<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <title><?php wp_title(); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header id="mon-header">
         <button id="menu-toggle" aria-label="Toggle navigation">
            <span class="menu-icon"></span>
        </button>
        <a href="<?php echo home_url(); ?>">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-chiffonnier.png" alt="<?php bloginfo('name'); ?>" class="site-logo">
        </a>
        <nav id="primary-nav">
            <?php wp_nav_menu(['theme_location' => 'primary']); ?>
        </nav>
        <div id="menu-overlay"></div>
    </header>
 