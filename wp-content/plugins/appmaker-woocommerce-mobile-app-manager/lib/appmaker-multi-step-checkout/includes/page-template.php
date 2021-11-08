<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta name="viewport" content= "width=device-width, initial-scale=1.0"> 
        <title><?php wp_title(); ?></title>
        <?php wp_head(); ?>

    </head>
    <body>
        <div id="primary" class="content-area">
            <main id="main" class="site-main appmakerCheckout" role="main">
                <?php
                    the_content();
                ?>
            </main><!-- .site-main -->
        </div><!-- .content-area -->
        <?php wp_footer(); ?>    
    </body>
</html>