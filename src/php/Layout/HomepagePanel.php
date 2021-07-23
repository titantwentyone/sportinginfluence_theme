<?php

namespace Titan21\SportingInfluence\Layout;

class HomepagePanel
{
    public function __construct()
    {
        add_shortcode('homepage_panel', [$this, 'display']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function display($atts, $content, $tag)
    {
        $panel_settings = rwmb_meta('homepage_panels', ['object_type' => 'setting'], 'site-settings');
        $panel_settings = array_filter($panel_settings, function($item) use($atts)
        {
            if($item['id'] == $atts['id'])
            {
                return $item;
            }
        });

        $panel_settings = array_pop($panel_settings);

        //var_dump($panel_settings);

        $background_image_id = $panel_settings['image'];
        $background_image = wp_get_attachment_image_src($background_image_id, 'medium');
        $background_image_url = $background_image[0];

        $id = $panel_settings['id'];
        $title = $panel_settings['title'];
        $button_text = $panel_settings['button_text'];
        
        $accent_colour = $panel_settings['accent_colour'];
        $accent_colour_rgb = $this->hexToRgb($accent_colour);
        $accent_colour_rgb_faded = "rgba({$accent_colour_rgb['r']}, {$accent_colour_rgb['g']}, {$accent_colour_rgb['b']}, 0.7)";

        $overlay_html = "";

        $icon = '<svg class="icon"><use xlink:href="#FontAwesomeicon-arrow-circle-right"></use></svg>';

        $panel_link = "";
        if(!$panel_settings['provide_overlay'])
        {
            $panel_link = get_permalink($panel_settings['link']);
        }

        if($panel_settings['provide_overlay'])
        {
            $cta_text = $panel_settings['text'];

            $buttons = $panel_settings['buttons'];
            $button_html = "";

            foreach($buttons as $button)
            {
                $link = get_permalink($button['link']);
                $button_html .= "<a href='{$link}' style='color:{$accent_colour} !important'><span>{$button['text']}</span>{$icon}</a>";
            }

            $overlay_html = <<<EOF
                <div class='overlay'>
                    <div class='bg' style='background-image:url({$background_image_url});'>
                        <div>
                            <p class='cta_text'>{$cta_text}</p>
                            <div class='buttons'>
                                {$button_html}
                            </div>
                        </div>
                    </div>
                </div>
EOF;
        }



        /**options are
         * from_top_and_right
         * from_right
         * from_bottom_and_right
         * from_bottom
         * from_bottom_and_left
         */
        $animation_style = $panel_settings['animation'];

        $elem = $panel_link ? 'a' : 'div';

        $output = <<<EOF
        <style>
        #{$id}::before
        {
            background-image:url({$background_image_url});
        }

        #{$id} .bg::before
        {
            background:linear-gradient(45deg, {$accent_colour} 50%, transparent 50%);

        }

        #{$id} a:after
        {
            background-color: {$accent_colour};
        }

        #{$id} .buttons .icon
        {
            fill: {$accent_colour} !important;
        }

        #{$id} .buttons a:hover .icon
        {
            fill: #fff !important;
        }
        </style>
        <{$elem} href='{$panel_link}' id='{$id}' class='homepage_panel {$animation_style}'>
            <div class='colour_overlay' style='background-color:{$accent_colour_rgb_faded};background-blend-mode:multiply;'></div>
            <span class='title'>{$title}</span>
            <div class='cta'>
                <div class='cta_border_left'></div>
                <div class='cta_border_right'></div>
                <span>{$button_text}</span>{$icon}
            </div>

            {$overlay_html}
        </{$elem}>
EOF;

        return $output;
    }

    public function enqueue_scripts()
    {
        if(is_front_page())
        {
            wp_register_style('homepage_css',WP_PLUGIN_URL.'/sportinginfluence/dist/css/homepage.min.css');
            wp_enqueue_style('homepage_css');
        }
    }

    private function hexToRgb($hex, $alpha = false)
    {
        $hex      = str_replace('#', '', $hex);
        $length   = strlen($hex);
        $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
        $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
        $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));

        if ( $alpha )
        {
           $rgb['a'] = $alpha;
        }

        return $rgb;
     }
}