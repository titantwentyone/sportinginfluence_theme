<!DOCTYPE html>
<html>
    <head>
        <?php wp_head(); ?>
        <?php get_template_part('parts/all', 'head'); ?>
    </head>

    <?php $body_class = has_term('foundation', 'category') == 'foundation' ? 'green' : ''; ?>

    <body <?php body_class($body_class); ?>>
        <div class='container-fluid p-0'>
            <?php get_header(); ?>
            <div class='container'>
                <div class='row p-5'>
                <?php if(have_posts()) : ?>
                    <?php while(have_posts()) : the_post();?>
                        <h1 class='mb-5'><?php the_title(); ?></h1>
                        
                        <?php if(is_single()): ?>
                            <div class='meta d-flex flex-column p-4 rounded bg-light bg-gradient mb-4'>
                                <span class="date"><?php the_time('j F Y'); ?></span>
                                <span class="categories">Categories:&nbsp;<?php the_category(' '); ?></span>
                                <?php if(has_tag()): ?>
                                <span class="tags">Tagged with:<?php the_tags(null, ' '); ?></span>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php the_content(); ?>
                    <?php endwhile; ?>
                <?php endif; ?>
                </div>
            </div>
        </div>

    </body>
    <?php wp_footer(); ?>
</html>