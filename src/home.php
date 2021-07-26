<!DOCTYPE html>
<html>
    <head>
        <?php wp_head(); ?>
        <?php get_template_part('parts/all', 'head'); ?>
    </head>

    <body <?php body_class(); ?>>
        <div class='container-fluid p-0'>
            <?php get_header(); ?>
            <div class='container'>
                <div class='p-5 posts d-grid'>
                    <div class='d-flex justify-content-center align-items-center bg-primary text-white'>
                    <h1>News</h1>
                    </div>
                    <?php if(have_posts()) : ?>
                        <?php while(have_posts()) : the_post();?>
                            <div class='post d-flex flex-column p-0 text-break'>
                                <?php the_post_thumbnail('large', [
                                    'class' => 'mb-3'
                                ]); ?>
                                <h3 class='mb-3'><?php the_title(); ?></h3>
                                <?php the_excerpt('Read More'); ?>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>

                    <div class='text-center p-3 d-flex flex-row'>
                    <?php
                    global $wp_query;

                    $big = 999999999; // need an unlikely integer

                    echo paginate_links( array(
                    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                    'format' => '?paged=%#%',
                    'current' => max( 1, get_query_var('paged') ),
                    'total' => $wp_query->max_num_pages,
                    'next_text' => '<i class="fas fa-arrow-right text-white"></i>',
                    'prev_text' => '<i class="fas fa-arrow-left text-white"></i>',
                    'mid_size' => 1
                    ) );
                    ?>
                    </div>
                </div>
            </div>
        </div>

    </body>
    <?php wp_footer(); ?>
</html>