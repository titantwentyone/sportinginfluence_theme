<!DOCTYPE html>
<html>
    <head>
        <?php wp_head(); ?>
        <?php get_template_part('parts/all', 'head'); ?>
    </head>
home
    <body <?php body_class(); ?>>
        <div class='container-fluid p-0'>
            <?php get_header(); ?>
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