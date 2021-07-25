<header class='position-sticky d-flex w-100 flex-column top-0 bg-white <?php echo $args['location'] ?>'>
    <div class='position-relative'>
        <div class='d-flex flex-row justify-content-between p-3'>
            
            <?php get_template_part('parts/header', 'social', ['location' => $args['location']]); ?>

            <div class='phone'>
                <i class='fas fa-phone me-2 fa-flip-horizontal'></i>01423 522999
            </div>
        </div>
        <div class='d-flex flex-row justify-content-between align-items-center p-3'>
            <div>
                <?php get_template_part('parts/all', 'brand'); ?>
            </div>

            <div class='menu d-flex flex-row'>
                <a class='ms-1' href='/booking'>
                    <span class='fa-stack fa-2x'>
                        <i class='fas fa-circle fa-stack-2x text-primary'></i>
                        <i class='fas fa-calendar fa-stack-1x fa-inverse'></i>
                    </span>
                </a>
                <a class='ms-1' href='/basket'>
                    <span class='fa-stack fa-2x'>
                        <i class='fas fa-circle fa-stack-2x text-primary'></i>
                        <i class='fas fa-shopping-cart fa-stack-1x fa-inverse'></i>
                    </span>
                </a>
                <a id='toggle' class='ms-1 toggle' href='#'>
                    <span class='fa-stack fa-2x d-flex'>
                        <i class='fas fa-circle fa-stack-2x text-primary'></i>
                        <!--<i class='fas fa-bars fa-stack-1x fa-inverse'></i>-->
                        <div class='w-100 h-100 d-flex justify-content-center align-items-center'>
                            <div class='d-flex w-100 p-3 h-100 p-1 flex-column justify-content-between align-items-center'>
                                <span class="top bg-white d-block" style='height:7px;z-index:2;width:70%;border-radius:3px'></span>
                                <span class="top bg-white d-block" style='height:7px;z-index:2;width:70%;border-radius:3px'></span>
                                <span class="top bg-white d-block" style='height:7px;z-index:2;width:70%;border-radius:3px'></span>
                            </div>
                        </div>
                    </span>
                </a>
            </div>
        </div>
        <div class='flash position-absolute top-0 end-0 h-100'>
            <div class='flash_image w-100 h-100 position-absolute top-0 end-0' style='background-image: url(https://picsum.photos/560/280?random=1);background-size:cover'></div>
            <div style='background: linear-gradient(135deg,white 50%,rgba(255,50,29,0.5) 50%);' class='w-100 h-100'></div>
        </div>
    </div>

    <?php if(is_page_template('templ-booking.php') && $args['location'] != 'oc'): ?>
        <div class='position-sticky w-100' style='top:0'>

            <?php Titan21\SportingInfluence\Shortcodes\BookingFormShortcode::displayChildrenTabs($args['children']); ?>

            <?php Titan21\SportingInfluence\Shortcodes\BookingFormShortcode::displaySeasonTabs($args['seasons']); ?>


        </div>
    <?php endif; ?>
</header>