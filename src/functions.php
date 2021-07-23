<?php

require("vendor/autoload.php");

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
    if(is_front_page())
    {
        wp_enqueue_style('homepage-grid', get_template_directory_uri().'/styles/homepage.css');
    }
    else
    {
        wp_enqueue_style('generic', get_template_directory_uri().'/styles/generic.css');
        wp_enqueue_script('offcanvas', get_template_directory_uri().'/js/offcanvas.js', ['jquery'], false, true);
    }
});