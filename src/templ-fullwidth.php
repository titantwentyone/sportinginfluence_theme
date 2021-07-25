<?php
/**
Template Name: Full Width
*/
?>
<!DOCTYPE html>
<html>
    <head>
        <?php wp_head(); ?>
        <?php get_template_part('parts/all', 'head'); ?>
    </head>

    <body <?php body_class(); ?>>
            <div class='container-fluid p-0'>
                <?php get_header(); ?>
                <div class='container-fluid p-0'>
                    <div class='row p-0 m-0'>
                    <?php if(have_posts()) : ?>
		                <?php while(have_posts()) : the_post();?>
                            <?php the_content(); ?>
                        <?php endwhile; ?>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
    </body>
    
    <?php wp_footer(); ?>
</html>