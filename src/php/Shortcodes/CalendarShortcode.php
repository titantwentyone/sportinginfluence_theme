<?php

namespace Titan21\SportingInfluence\Shortcodes;

use Titan21\SportingInfluence\Calendar\Calendar;

class CalendarShortcode
{
    public function __construct()
    {
        //add_action('wp_enqueue_scripts', [$this, 'enqueue_style']);
        add_shortcode('sportinginfluence_calendar', [$this, 'display_calendar'], 10, 3);  
    }

    public function display_calendar($atts, $content, $tag)
    {

        $atts = shortcode_atts([
            'from_date' => strtotime('1 June 2020'),
            'to_date' => strtotime('31 July 2020')
        ], $atts, $tag);

        $calendar = new Calendar($atts['from_date'], $atts['to_date']);
        $calendar->display();
    }

    public function enqueue_style()
    {
        /*
        global $post;
        if(has_shortcode(get_post_meta($post->ID, 'ct_builder_shortcodes', true), 'sportinginfluence_calendar') || $post->ID == 28)
        {
            //wp_register_style('calendar', WP_PLUGIN_URL.'/sportinginfluence/dist/css/calendar.min.css');
            //wp_enqueue_style('calendar');
        }
        */
    }
}