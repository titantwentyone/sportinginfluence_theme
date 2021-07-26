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