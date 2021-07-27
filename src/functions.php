<?php

require("vendor/autoload.php");

//add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

//@see https://wordpress.org/support/topic/call-to-undefined-function-get_current_screen-6/
require_once(ABSPATH . 'wp-admin/includes/screen.php');

new Titan21\SportingInfluence\Shortcodes\CalendarShortcode();
$bookingformshortcode = new Titan21\SportingInfluence\Shortcodes\BookingFormShortcode();
new Titan21\SportingInfluence\Ajax\CartAjax($bookingformshortcode);

new Titan21\SportingInfluence\Shortcodes\PopupShortcode();

new Titan21\SportingInfluence\WooCommerce\WCCart();
new Titan21\SportingInfluence\WooCommerce\WCAccountChildTab();
new Titan21\SportingInfluence\Layout\HomepagePanel();

new Titan21\SportingInfluence\WooCommerce\WCChildcareVoucherPaymentGatewayRegistration();


$wcproducteventregistration = new Titan21\SportingInfluence\WooCommerce\WCProductEventRegistration();
$wcproducteventregistration->setUp();

const WOOCOMMERCE_TEMPLATE_LOCATION = WP_PLUGIN_DIR."/sportinginfluence/woocommerce";

add_action('wp_enqueue_scripts', function()
{
    wp_enqueue_style('generic', get_template_directory_uri().'/styles/generic.css');

    wp_register_script('main', get_template_directory_uri().'/js/main.js', ['jquery'], false, true);
    wp_localize_script('main', 'ajax_object', ['ajax_url' => admin_url( 'admin-ajax.php' )]);
    wp_enqueue_script('main');
});

function new_excerpt_more($more) {
    global $post;
 return '<br/><br/><div class="d-flex justify-content-center"><a class="button" href="'. get_permalink($post->ID) . '">Read More</a></div>';
}
add_filter('excerpt_more', 'new_excerpt_more');


/*
add_action('pre_get_posts', function($query)
{
    if(is_page_template('templ-foundationblog.php') && $query->is_main_query())
    {
        //echo "hello";
        $query->set('post_type', 'post'); 
        $query->set('posts_per_page', 11);
        $query->set('tax_query', [
            [
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => 'foundation'
            ]
        ]);
        
        $query->set( 'category_name', 'foundation' );
        //print_r($query);
    }
}, 10, 1);
*/

/***********************************
 Modifying foundation permalinks
************************************/
/**
* Modify Permalinks for the Case Studies Category
*
* @author Nikki Stokes
* @link https://measurewhatworks.com/
*
* @param string $permalink
* @param array $post
* @param array $leavename
*/
// Modify the individual case study post permalinks
function nhs_custom_case_studies_permalink_post( $permalink, $post, $leavename ) {
    // Get the categories for the post
    $category = get_the_category($post->ID); 
    if (  !empty($category) && $category[0]->cat_name == "Foundation" ) {
        $permalink = trailingslashit( home_url('/foundation/news/'. $post->post_name . '/' ) );
    }
    return $permalink;
}
add_filter( 'post_link', 'nhs_custom_case_studies_permalink_post', 10, 3 );

// Modify the "case studies" category archive permalink
function nhs_custom_case_studies_permalink_archive( $permalink, $term, $taxonomy ){
	// Get the category ID 
	$category_id = $term->term_id;
 
	// Check for desired category 
	if( !empty( $category_id ) && $category_id == 38 ) {
        $permalink = trailingslashit( home_url('/foundation/news/' ) );		
	}

	return $permalink;
}
add_filter( 'term_link', 'nhs_custom_case_studies_permalink_archive', 10, 3 );

// Add rewrite rules so that WordPress delivers the correct content
function nhs_custom_rewrite_rules( $wp_rewrite ) {
    // This rule will will match the post name in /case-study/%postname%/ struture
	$new_rules['^foundation/news/([^/]+)/?$'] = 'index.php?name=$matches[1]';
	$new_rules['^foundation/news/?$'] = 'index.php?cat=38';
	$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    
	return $wp_rewrite;
}
add_action('generate_rewrite_rules', 'nhs_custom_rewrite_rules');
