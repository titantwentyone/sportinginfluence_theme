<?php
/**
Template Name: Homepage
*/
?>
<!DOCTYPE html>
<html>
    <head>
        <?php wp_head(); ?>
        <?php get_template_part('parts/all', 'head'); ?>
    </head>
    
    <body <?php body_class(); ?>>
        <div id='homepage_grid'>
            <div id='video'>
                <video autoplay="" loop="" playsinline="" muted="">
                    <source src="/wp-content/themes/sportinginfluence/media/Site-Vid.mp4">
                </video>

                <?php get_template_part('parts/all', 'brand'); ?>
            </div>
            
            <div id='activity_camps'>
                <div>
                    <h2 class='title'>Activity Camps</h2>
                    <!--
                    <a href='' class='panel_button'>
                        <div class='corner bottom_left_corner'></div>
                        <span>Read More</span>
                        <div class='corner top_right_corner'></div>
                    </a>
                    -->
                    <div class='popout'>
                        <span class='text'>
                            Lorem ipsum dolor site amet
                        </span>
                        <div class='popout_buttons'>
                            <a class='popout_button button' href='/booking'>
                                <span>Book Now <i class='fas fa-arrow-circle-right'></i></span>
                            </a>

                            <a class='popout_button button' href='/activity-camps'>
                                <span>Learn More <i class='fas fa-arrow-circle-right'></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id='after_school_clubs'>
                <div>
                    <h2 class='title'>After School Clubs</h2>
                    <!--
                    <a href='' class='panel_button'>
                        <div class='corner bottom_left_corner'></div>
                        <span>Read More</span>
                        <div class='corner top_right_corner'></div>
                    </a>
                    -->
                    <div class='popout'>
                        <span class='text'>
                            Lorem ipsum dolor site amet
                        </span>
                        <div class='popout_buttons'>
                            <a class='popout_button button'>
                                <span>Learn More <i class='fas fa-arrow-circle-right'></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id='your_account'>
                <div>
                    <h2 class='title'>Your Account</h2>
                    <!--
                    <a href='' class='panel_button'>
                        <div class='corner bottom_left_corner'></div>
                        <span>Read More</span>
                        <div class='corner top_right_corner'></div>
                    </a>
                    -->
                    <div class='popout'>
                        <span class='text'>
                            Lorem ipsum dolor site amet
                        </span>
                        <div class='popout_buttons'>

                            <a class='popout_button button' href='my-account'>
                                <span>Login <i class='fas fa-arrow-circle-right'></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id='about_us'>
                <div>
                    <h2 class='title'>About Us</h2>
                    <!--
                    <a href='' class='panel_button'>
                        <div class='corner bottom_left_corner'></div>
                        <span>Read More</span>
                        <div class='corner top_right_corner'></div>
                    </a>
                    -->
                    <div class='popout'>
                        <span class='text'>
                            Lorem ipsum dolor site amet
                        </span>
                        <div class='popout_buttons'>
                            <a class='popout_button button' href='/about-us'>
                                <span>Read More <i class='fas fa-arrow-circle-right'></i></span>
                            </a>
                        </div>
                    </div>
                </div>        
            </div>
            
            <div id='foundation'>
                <div>
                    <h2 class='title'>Foundation</h2>
                    <!--
                    <a href='' class='panel_button'>
                        <div class='corner bottom_left_corner'></div>
                        <span>Read More</span>
                        <div class='corner top_right_corner'></div>
                    </a>
                    -->
                    <div class='popout'>
                        <span class='text'>
                            Lorem ipsum dolor site amet
                        </span>
                        <div class='popout_buttons'>
                            <a class='popout_button button' href='/foundation'>
                                <span>Read More <i class='fas fa-arrow-circle-right'></i></span>
                            </a>
                        </div>
                    </div>
                </div>       
            </div>
        </div>
    </body>
    <?php wp_footer(); ?>
</html>
