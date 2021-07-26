<?php
/**
Template Name: Booking
*/
?>

<!DOCTYPE html>
<html>
    <head>
        <?php wp_head(); ?>
        <?php get_template_part('parts/all', 'head'); ?>
    </head>

    <body <?php body_class(); ?>>

        <div class='container-fluid p-0'>

            <?php $children = Titan21\SportingInfluence\Data\ChildData::get_current_users_children(); ?>
            <?php $seasons = rwmb_meta('active_seasons', ['object_type' => 'setting'], 'site-settings'); ?>



            <?php get_header('', [
                'children' => $children,
                'seasons' => $seasons]);
            ?>
            <div class='container-fluid p-0 overflow-hidden'>
                <div class='row p-0 position-relative'>

                    <div class='swiper-container-children'>
                        <div class='swiper-wrapper'>
                            <?php
                                foreach($children as $child)
                                {
                                    echo "<div class='tabcontent swiper-slide' data-hash='{$child->ID}' data-child-id='{$child->ID}'>";
                                    ?>
                                        <div class='swiper-container-seasons'>
                                        <div class='swiper-wrapper'>

                                        <?php

                                        foreach($seasons as $active_season)
                                        {

                                            if(Titan21\SportingInfluence\Data\EventData::get_count_of_posts_in_this_season($active_season) == 0)
                                            {
                                                continue;
                                            }

                                            if(!\Titan21\SportingInfluence\Data\ChildData::is_child_age_valid($child))
                                            {
                                                echo "<div class='tabcontent swiper-slide' data-hash='{$active_season->slug}' data-season='$active_season->slug'>";
                                                echo "<p>Sorry, your child is too young or too old to attend our sessions</p>";
                                                echo "</div>";
                                                continue;
                                            }

                                            echo "<div class='tabcontent swiper-slide' data-hash='{$active_season->slug}' data-season='$active_season->slug'>";

                                            add_filter('ec_day_classes', function($classes, $day, $month, $year) use ($active_season, $child)
                                            {
                                                $events_query = Titan21\SportingInfluence\Data\EventData::get_events_for_date($active_season, $day, $month, $year);

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
                                                $events_query = \Titan21\SportingInfluence\Data\EventData::get_events_for_date($active_season, $day, $month, $year);

                                                if($events_query->found_posts)
                                                {
                                                    $event_product = $events_query->get_posts()[0];
                                                    $bookingform = new Titan21\SportingInfluence\Shortcodes\BookingFormShortcode();
                                                    //Titan21\SportingInfluence\Shortcodes\BookingFormShortcode::display_options($event_product, $child);
                                                    $bookingform->display_options($event_product, $child);

                                                }
                                            }, 10, 3);

                                            //determine first date and last date
                                            $first_event_date = Titan21\SportingInfluence\Data\EventData::get_first_date_of_season($active_season);
                                            $last_event_date = Titan21\SportingInfluence\Data\EventData::get_last_date_of_season($active_season);

                                            echo do_shortcode("[sportinginfluence_calendar from_date='{$first_event_date}' to_date='{$last_event_date}']");
                                            //echo do_shortcode("[sportinginfluence_calendar]");

                                            remove_all_actions("ec_day_content");
                                            remove_all_actions("ec_day_classes");

                                            echo "</div>";


                                        }
                                        ?>
                                        </div>
                                        </div>
                                    <?php
                                    echo "</div>";
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        

    </body>
    <?php wp_footer(); ?>
</html>