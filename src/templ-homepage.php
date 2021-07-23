<?php
/**
Template Name: Homepage
*/
?>
<!DOCTYPE html>
<html>
    <head>
        <?php wp_head(); ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/fontawesome.min.css" integrity="sha512-OdEXQYCOldjqUEsuMKsZRj93Ht23QRlhIb8E/X0sbwZhme8eUw6g8q7AdxGJKakcBbv7+/PX0Gc2btf7Ru8cZA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/solid.min.css" integrity="sha512-jQqzj2vHVxA/yCojT8pVZjKGOe9UmoYvnOuM/2sQ110vxiajBU+4WkyRs1ODMmd4AfntwUEV4J+VfM6DkfjLRg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
                    <span class='title'>Activity Camps</span>
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

                            <a class='popout_button button'>
                                <span>Learn More <i class='fas fa-arrow-circle-right'></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id='after_school_clubs'>
                <div>
                    <span class='title'>After School Clubs</span>
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
                    <span class='title'>Your Account</span>
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
                    <span class='title'>About Us</span>
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
                    <span class='title'>Foundation</span>
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
