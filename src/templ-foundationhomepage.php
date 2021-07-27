<?php
/**
 * Template Name: Foundation Homepage
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

            <div class='container-fluid m-0 p-0'>
                <div class='swiper-container-foundation'>
                    <div class='swiper-wrapper'>
                        <div class='swiper-slide' style='height:400px;background-image:url(https://picsum.photos/1920/400?random=1);background-size:cover'></div>
                        <div class='swiper-slide' style='height:400px;background-image:url(https://picsum.photos/1920/400?random=2);background-size:cover'></div>
                        <div class='swiper-slide' style='height:400px;background-image:url(https://picsum.photos/1920/400?random=3);background-size:cover'></div>
                    </div>
                </div>
            </div>

            <div class='container'>
                <div class='row p-5'>
                <?php if(have_posts()) : ?>
                    <?php while(have_posts()) : the_post();?>
                        <h1 class='mb-5'><?php the_title(); ?></h1>
                        <?php the_content(); ?>
                    <?php endwhile; ?>
                <?php endif; ?>
                </div>
            </div>
        </div>

    </body>
    <?php wp_footer(); ?>
</html>