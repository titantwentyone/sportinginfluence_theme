<?php

namespace Titan21\SportingInfluence\Data;

abstract class EventData
{
    public static function get_events_for_date(\WP_Term $season, $day, $month, $year)
    {
        //get events for this date and season
        $events_query = new \WP_Query([
            'cache_results' => false,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'post_type' => 'product',
            'posts_per_page' => 5,
            'tax_query' => [
                [
                    'taxonomy' => 'season',
                    'field' => 'term_id',
                    'terms' => $season->term_id,
                    'operator' => 'IN'
                ]
            ],
            'meta_query' => [
                [
                    'key' => 'event_date',
                    'value' => strtotime($day."-".$month."-".$year),
                    'compare' => "="
                ]
            ]
        ]);

        return $events_query;
    }

    public static function get_first_date_of_season(\WP_Term $season)
    {
        $events_first_date_query = new \WP_Query([
            'post_type' => 'product',
            'posts_per_page' => 100,
            'tax_query' => [
                [
                    'taxonomy' => 'season',
                    'field' => 'term_id',
                    'terms' => [$season->term_id],
                    'operator' => 'IN'
                ]
            ],
            'order' => 'ASC',
            'orderby' => 'meta_value_num',
            'meta_key' => 'event_date'                
        ]);

        $first_event = $events_first_date_query->get_posts()[0];

        $first_event_date = get_post_meta($first_event->ID, 'event_date', true);

        return $first_event_date;
    }

    public static function get_last_date_of_season(\WP_Term $season)
    {
        $events_last_date_query = new \WP_Query([
            'post_type' => 'product',
            'posts_per_page' => 100,
            'tax_query' => [
                [
                    'taxonomy' => 'season',
                    'field' => 'term_id',
                    'terms' => [$season->term_id],
                    'operator' => 'IN'
                ]
            ],
            'order' => 'DESC',
            'orderby' => 'meta_value_num',
            'meta_key' => 'event_date'                
        ]);

        $last_event = $events_last_date_query->get_posts()[0];

        $last_event_date = get_post_meta($last_event->ID, 'event_date', true);

        return $last_event_date;
    }

    public static function get_count_of_posts_in_this_season(\WP_Term $season)
    {
        $posts_of_this_season = new \WP_Query([
            'post_type' => 'product',
            'tax_query' => [
                [
                    'taxonomy' => 'season',
                    'field' => 'term_id',
                    'terms' => $season->term_id
                ]
            ]
        ]);

        return $posts_of_this_season->found_posts;
    }

    public static function has_event_expired($product_id)
    {
        //if today is after the event date, return
        $date = get_post_meta($product_id, 'event_date', true);
        $date   = \DateTime::createFromFormat('U', $date);
        $now = new \DateTime('now');

        if($now > $date)
        {
            return true;
        }

        return false;
    }
}