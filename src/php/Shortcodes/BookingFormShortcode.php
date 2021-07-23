<?php

namespace Titan21\SportingInfluence\Shortcodes;

use Titan21\SportingInfluence\Data\ChildData;
use Titan21\SportingInfluence\Data\EventData;
use Titan21\SportingInfluence\Helpers\CartHelper;

class BookingFormShortcode
{
    public function __construct()
    {

        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);

        //swiper


        //shortcode
        add_shortcode('sportinginfluence_booking', [$this, 'display_booking'], 10, 3);


    }

    public function display_booking($atts, $content, $tag)
    {
        $children = ChildData::get_current_users_children();

        $this->displayChildrenTabs($children);
        $this->displayChildrenTabContent($children);
    }

    public function displayChildrenTabs($children)
    {
        echo "<div class='tablinks_container children'>";
            echo "<div class='tablinks'>";

            foreach($children as $index => $child)
            {
                echo "<a class='tab child button' data-index='{$index}' href='#{$child->ID}'>{$child->post_title}</a>";
            }

            echo "</div>";
        echo "</div>";

        $this->displaySeasonTabs();

    }

    public function displayChildrenTabContent($children)
    {
        echo "<div class='swiper-container-children'>";
            echo "<div class='swiper-wrapper'>";

            foreach($children as $child)
            {
                echo "<div class='tabcontent swiper-slide' data-hash='{$child->ID}' data-child-id='{$child->ID}'>";
                $this->displaySeasonTabContent($child);
                echo "</div>";
            }
            echo "</div>";
        echo "</div>";
    }

    public function displaySeasonTabs()
    {
        $active_seasons = rwmb_meta('active_seasons', ['object_type' => 'setting'], 'site-settings');
        
        echo "<div class='tablinks_container seasons'>";
        echo "<div class='tablinks'>";

        $tab_index = 0;
        foreach($active_seasons as $index => $active_season)
        {
            //dont display tab if no events
            

            if(EventData::get_count_of_posts_in_this_season($active_season) == 0)
            {
                continue;
            }

            echo "<a class='tab season button' data-index='{$tab_index}' href='#{$active_season->slug}'>{$active_season->name}</a>";

            $tab_index++;
        }

        echo "</div>";
        echo "</div>";
    }

    public function displaySeasonTabContent($child)
    {
        $active_seasons = rwmb_meta('active_seasons', ['object_type' => 'setting'], 'site-settings');

        echo "<div class='swiper-container-seasons'>";
        echo "<div class='swiper-wrapper'>";

        foreach($active_seasons as $active_season)
        {

            if(!ChildData::is_child_age_valid($child))
            {
                echo "<div class='tabcontent swiper-slide' data-hash='{$active_season->slug}' data-season='$active_season->slug'>";
                echo "<p>Sorry, your child is too young or too old to attend our sessions</p>";
                echo "</div>";
                continue;
            }

            if(EventData::get_count_of_posts_in_this_season($active_season) == 0)
            {
                continue;
            }

            echo "<div class='tabcontent swiper-slide' data-hash='{$active_season->slug}' data-season='$active_season->slug'>";

            add_filter('ec_day_classes', function($classes, $day, $month, $year) use ($active_season, $child)
            {
                $events_query = EventData::get_events_for_date($active_season, $day, $month, $year);

                if($events_query->found_posts)
                {
                    $classes[] = 'has_event';
                }

                return $classes;
            }, 10, 4);

            //inject content into calendar
            add_action("ec_day_content", function($day, $month, $year) use ($active_season, $child)
            {
                echo "<div class='ec_date'>";

                if($day == 1)
                {
                    $dateObj   = \DateTime::createFromFormat('!m', $month);
                    $monthName = $dateObj->format('F');
                    echo "<div class='ec_monthname'>{$monthName}</div>";
                }
                else
                {
                    echo "<div></div>";
                }

                echo "<div class='ec_day_number_bg'></div><span class='ec_day_number'>{$day}</span></div>";

                //get events for this date and season
                $events_query = EventData::get_events_for_date($active_season, $day, $month, $year);

                if($events_query->found_posts)
                {
                    $event_product = $events_query->get_posts()[0];
                    $this->display_options($event_product, $child);
                }
            }, 10, 3);

            //determine first date and last date
            $first_event_date = EventData::get_first_date_of_season($active_season);
            $last_event_date = EventData::get_last_date_of_season($active_season);

            echo do_shortcode("[sportinginfluence_calendar from_date='{$first_event_date}' to_date='{$last_event_date}']");
            //echo do_shortcode("[sportinginfluence_calendar]");

            remove_all_actions("ec_day_content");
            remove_all_actions("ec_day_classes");

            echo "</div>";


        }
        echo "</div>";
        echo "</div>";

    }

    public function display_options($event_product, $child)
    {
        $event_product = wc_get_product($event_product);

        $available_variations = $event_product->get_available_variations();

        echo "<div class='product_options'>";

        if(EventData::has_event_expired(($event_product->get_id())))
        {
            echo "<p>Sorry. You're too late to book this session.</p>";
        }
        else
        {
            foreach($available_variations as $variation)
            {
                $this->get_add_to_cart_variation_button($variation, $child);
            }

            //echo "<a class='book_session_group'>Book The Week</a>";
            $this->display_book_session_group_button($event_product->get_id(), $child->ID);
        }

        echo "</div>";
    }

    private function display_book_session_group_button($product_id, $child_id)
    {
        if(has_term(null, 'session_group', $product_id) && get_post_meta($product_id, 'default_variation', true) != "notselected")
        {
            $session_group = wp_get_post_terms($product_id, 'session_group')[0];
            echo "<a class='book_session_group' data-sessiongroup='{$session_group->term_id}' data-childid='{$child_id}'>Book The Week</a>";
        }
    }

    public function get_add_to_cart_variation_button($variation, $child)
    {
        $classes = [];

        $action = "add_to_cart";

        $product_id = wp_get_post_parent_id($variation['variation_id']);

        //if the variation and child is in the basket
        if(CartHelper::matched_cart_items( $variation['variation_id'], $child->ID ))
        {
            $classes[] = "in_cart";
            $action = "remove_from_cart";
        }
        else if(CartHelper::matched_cart_items( $product_id, $child->ID ))
        {
            $classes[] = "swap_in_cart";
            $action = "swap_in_cart";
        }

        //original order but amendment present
        else if(CartHelper::has_already_ordered($product_id, $child->ID, $variation['variation_id']) && CartHelper::has_amendment_in_cart($product_id, $child->ID))
        {
            $classes[] = "original_order";
            $action = "reset_swap";
        }
        //proposed change to order
        else if(CartHelper::has_already_ordered($product_id, $child->ID) && CartHelper::has_amendment_in_cart($product_id, $child->ID, $variation['variation_id']))
        {
            $classes[] = "swapped_in_order";
            $action = "reset_swap";
        }


        //already ordered
        else if(CartHelper::has_already_ordered($product_id, $child->ID, $variation['variation_id']))
        {
            $classes[] = "in_order";
            $action = "reset_swap";
        }
        //already ordered but not this variation
        else if(CartHelper::has_already_ordered($product_id, $child->ID))
        {
            $classes[] = "swap_in_order";
            $action = "swap_in_order";
        }

        $data_session_group = "";
        //flag default option if in session group
        if(has_term(null, 'session_group', $product_id))
        {
            //get_default option
            $default_variation = get_post_meta($product_id, "default_variation", true) ?: "";

            if($default_variation && $default_variation == $variation['variation_id'])
            {
                $session_group = wp_get_post_terms($product_id, 'session_group')[0];
                $data_session_group = "data-sessiongroup='{$session_group->term_id}'";
            }
        }

        $classes = implode(" ", $classes);


        echo "<a class='product_option {$classes}' data-childid='{$child->ID}' data-variationid='{$variation['variation_id']}' data-action='{$action}' {$data_session_group}>";
            echo "<div class='option_name'>";
            echo implode(" - ", $variation['attributes']);
            echo "</div>";

            echo "<div class='option_price'>";
            echo "&pound;".$variation['display_price'];
            echo "</div>";
        echo "</a>";
    }

    public function enqueue_scripts()
    {
        wp_register_script('booking_form',WP_PLUGIN_URL.'/sportinginfluence/dist/js/booking_form.min.js', ['jquery'], false, true);
        wp_localize_script( 'booking_form', 'ajax_object', ['ajax_url' => admin_url( 'admin-ajax.php' )]);
        wp_register_style('booking_form_css',WP_PLUGIN_URL.'/sportinginfluence/dist/css/booking_form_css.min.css');
        wp_register_script('swiper','https://cdnjs.cloudflare.com/ajax/libs/Swiper/6.7.1/swiper-bundle.min.js');
        wp_register_style('swiper', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/6.7.1/swiper-bundle.min.css');

        global $post;
        if(has_shortcode($post->post_content, 'sportinginfluence_booking'))
        {
            wp_enqueue_script('booking_form');
            wp_enqueue_style('booking_form_css');
            wp_enqueue_script('swiper');
            wp_enqueue_style('swiper');
        }
    }
}