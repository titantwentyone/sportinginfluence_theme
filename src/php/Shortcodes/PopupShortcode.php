<?php

namespace Titan21\SportingInfluence\Shortcodes;

class PopupShortcode
{
    public function __construct()
    {
        add_shortcode('sportinginfluence_popup', [$this, 'display'], 10, 3);
    }

    public function display($atts, $content, $tag)
    {
        $popup_group = $atts['id'];

        $mb_popup_group = rwmb_meta($popup_group, ['object_type' => 'setting'], 'site-settings');
        $output = $mb_popup_group[$atts['field']];

        return $output;
    }
}