<?php get_template_part('parts/all', 'topbar', [
    'location' => 'body',
    'children' => isset($args['children']) ? $args['children']: [],
    'seasons' => isset($args['seasons']) ? $args['seasons']: []
]); ?>
<?php get_template_part('parts/all', 'offcanvas'); ?>