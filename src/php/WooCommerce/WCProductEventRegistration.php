<?php

namespace Titan21\SportingInfluence\WooCommerce;

class WCProductEventRegistration
{
    public function setUp()
    {
        //tab and tab content
        add_filter('woocommerce_product_data_tabs', [$this, 'create_tabs']);
        add_action('woocommerce_product_data_panels', [$this, 'create_tab_content']);

        //save post meta
        add_action('woocommerce_process_product_meta', [$this, 'save_product_meta']);

        add_action('admin_enqueue_scripts', [$this, 'enqueue_enable_jquery_ui_datepicker']);
    }

    public function create_tabs($tabs)
    {
		$tabs['variable'] = [
			'label' => 'Event',
			'target' => 'event_product_options',
			'class' => 'show_if_variable'
		];

        return $tabs;
    }

    public function enqueue_enable_jquery_ui_datepicker($hook_suffix)
    {
        if($hook_suffix == 'post.php' && get_post_type() == 'product')
        {
            wp_enqueue_script( 'jquery-ui-datepicker' );

            wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );
            wp_enqueue_style( 'jquery-ui' );

            wp_register_script('enable_jquery_ui_datepicker', plugins_url()."/sportinginfluence/dist/js/enable_jquery_ui_datepicker.min.js");
            wp_enqueue_script('enable_jquery_ui_datepicker');
        }
    }

    public function create_tab_content()
    {
        ?>
            <div id='event_product_options' class='panel woocommerce_options_panel show_if_variable'>
                <div class='options_group'>
                    <?php $this->display_tab_content_date_field(); ?>
                    <p>--pricing presets here?--</p>
                    <?php $this->display_tab_content_default_variation_field(); ?>

                </div>
            </div>
        <?php
    }

    private function display_tab_content_date_field()
    {
        global $post;

        $date = "";

        if($date = get_post_meta($post->ID, "event_date", true))
        {
			$date = date_create_from_format('U', $date);
			$date = $date->format('j M Y');
        }

        woocommerce_wp_text_input([
            'id' => 'event_date',
            'label' => 'Event Date',
            'type' => 'text',
            'class' => 'is_datepicker',
            'value' => $date
        ]);
    }

    private function display_tab_content_default_variation_field()
    {
        global $post;
        $product = wc_get_product($post->ID);

        if($product && $product->is_type("variable") && !empty($product->get_available_variations()))
        {
            $variations = $product->get_available_variations();
            $variation_options = [];
            $variation_options['notselected'] = "-- Please Select --";
            foreach($variations as $variation)
            {
                $variation_name = implode(" - ", $variation['attributes']);
                $variation_options[$variation['variation_id']] = $variation_name;
            }

            woocommerce_wp_select([
                'id' => 'default_variation',
                'label' => 'Default Variation',
                'options' => $variation_options,
                'value' => get_post_meta($post->ID, "default_variation", true) ?: "notselected"
            ]);
        }
        else
        {
            echo "<p>Ensure variations have been generated and each one has a price.</p>";
        }
    }

    public function save_product_meta($post_id)
    {
        if(!empty($_POST['event_date']))
        {
			$date = esc_attr($_POST['event_date']);
			$date = strtotime($date);
			if($date)
            {
				//$date = date_create_from_format('U', $date);
				//$date = $date->format('j-m-Y');
			    update_post_meta($post_id, 'event_date', $date);
            }
            else
            {
                //error
            }
		}

		//save default option
		if(!empty($_POST['default_variation'])) {
			update_post_meta($post_id, 'default_variation', esc_attr($_POST['default_variation']));
		}
    }
}