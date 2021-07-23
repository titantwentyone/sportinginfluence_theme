<?php

namespace Titan21\SportingInfluence\Ajax;

use Titan21\SportingInfluence\Data\EventData;
use Titan21\SportingInfluence\Helpers\CartHelper;
use Titan21\SportingInfluence\Shortcodes\BookingFormShortcode;

class CartAjax
{
    private $shortcode;

    public function __construct(BookingFormShortcode $shortcode)
    {
        $this->shortcode = $shortcode;
        add_action("wp_ajax_add_to_cart", array($this, "ajax_add_to_cart"));
        add_action("wp_ajax_nopriv_add_to_cart", array($this, "ajax_add_to_cart"));

        add_action("wp_ajax_remove_from_cart", array($this, "ajax_remove_from_cart"));
        add_action("wp_ajax_nopriv_remove_from_cart", array($this, "ajax_remove_from_cart"));

        add_action("wp_ajax_swap_in_cart", array($this, "ajax_swap_in_cart"));
        add_action("wp_ajax_nopriv_swap_in_cart", array($this, "ajax_swap_in_cart"));

        add_action("wp_ajax_swap_in_order", array($this, "ajax_swap_in_order"));
        add_action("wp_ajax_nopriv_swap_in_order", array($this, "ajax_swap_in_order"));

        add_action("wp_ajax_reset_swap", array($this, "ajax_reset_swap"));
        add_action("wp_ajax_nopriv_reset_swap", array($this, "ajax_reset_swap"));
    }

    public function ajax_add_to_cart()
    {
        $child_id = $_POST['childid'];
        $variation_id = $_POST['variationid'];
        $product_id = wp_get_post_parent_id($variation_id);
        $product = wc_get_product($product_id);

        $this->add_to_cart($child_id, $variation_id);

        ob_start();
        $this->shortcode->display_options($product, get_post($child_id));
        $content = ob_get_clean();

        $response = [
            "content" => $content
        ];

        echo json_encode($response, JSON_FORCE_OBJECT);

        wp_die();

    }

    public function add_to_cart($child_id, $variation_id)
    {
        $product_id = wp_get_post_parent_id($variation_id);
        $product = wc_get_product($product_id);

        if(EventData::has_event_expired($product_id))
        {
            wp_die();
        }

        //if already in cart, return
        if($this->matched_cart_items($product_id, $child_id))
        {
            wp_die();
        }

        //if the product is out of stock, return
        if(!$product->get_stock_quantity())
        {
            wp_die();
        }

        //if the child and product combination already exists in a previous order
        if($this->has_already_ordered($variation_id, $product_id, $child_id))
        {
            wp_die();
        }

        \WC()->cart->add_to_cart($product_id, 1, $variation_id);
    }

    public function ajax_remove_from_cart()
    {
        $child_id = $_POST['childid'];
        $variation_id = $_POST['variationid'];
        $product_id = wp_get_post_parent_id($variation_id);
        $product = wc_get_product($product_id);

        $this->remove_from_cart($child_id, $variation_id);

        ob_start();
        $this->shortcode->display_options($product, get_post($child_id));
        $content = ob_get_clean();

        $response = [
            "content" => $content
        ];

        echo json_encode($response, JSON_FORCE_OBJECT);

        wp_die();
    }

    public function ajax_swap_in_cart()
    {
        //get product of variation
        $child_id = $_POST['childid'];
        $variation_id = $_POST['variationid'];
        $product_id = wp_get_post_parent_id($variation_id);
        $product = wc_get_product($product_id);

        $this->swap_in_cart($child_id, $variation_id, $product_id);

        ob_start();
        $this->shortcode->display_options($product, get_post($child_id));
        $content = ob_get_clean();

        $response = [
            "content" => $content
        ];

        echo json_encode($response, JSON_FORCE_OBJECT);

        wp_die();
    }

    public function swap_in_cart($child_id, $variation_id, $product_id)
    {
        $this->remove_from_cart($child_id, $variation_id, $product_id);

        $this->add_to_cart($child_id, $variation_id);
    }

    public function remove_from_cart($child_id, $variation_id, $product_id = null)
    {
        if(!$product_id)
        {
            $product_id = wp_get_post_parent_id($variation_id);
        }
        $product = wc_get_product($product_id);

        if(CartHelper::matched_cart_items($product->get_id(), $child_id))
        {
            $cart_item_key = CartHelper::get_cart_key($product->get_id(), $child_id);
            WC()->cart->remove_cart_item($cart_item_key);
        }
    }

    public function ajax_swap_in_order()
    {
        $child_id = $_POST['childid'];
        $variation_id = $_POST['variationid'];
        $product_id = wp_get_post_parent_id($variation_id);
        $product = wc_get_product($product_id);

        if(EventData::has_event_expired($product_id))
        {
            wp_die();
        }

        $additional_fee_product_id = rwmb_meta('additional_fee', ['object_type' => 'setting'], 'site-settings');

        if(!$order_item_id = CartHelper::has_already_ordered($product_id, $child_id))
        {
            wp_die();
        }

        ///amending a previosuly swapped item, remove additional fee
        $this->reset_swap($product_id, $child_id, $variation_id);

        if($order_item_id = CartHelper::has_already_ordered($product_id, $child_id))
        {
            $order_item = new \WC_Order_Item_Product($order_item_id);
            $current_subtotal = $order_item->get_subtotal();

            $variation = wc_get_product($variation_id);
            $proposed_subtotal = $variation->get_price();

            if($proposed_subtotal > $current_subtotal)
            {
                $cart_item_data['additional_fee_price'] = $proposed_subtotal - $current_subtotal;
                $cart_item_data['additional_fee_original_product_id'] = $product_id;
                $cart_item_data['additional_fee_original_variation_id'] = $order_item->get_variation_id();
                $cart_item_data['additional_fee_new_variation_id'] = $variation->get_id();
                //$cart_item_data['additional_child_id'] = $child_id;
                $cart_item_data['additional_fee_original_order'] = $order_item->get_order_id();

                \WC()->cart->add_to_cart($additional_fee_product_id, 1, null, null, $cart_item_data);
            }
        }

        $order = wc_get_order($order_item->get_order_id());

        $date = $order_item->get_product()->get_title();
        $original_variation_name = implode(" ", $order_item->get_product()->get_attributes());
        $new_variation_name = implode(" ", $variation->get_attributes());

        $amount = $proposed_subtotal > $current_subtotal ? $proposed_subtotal - $current_subtotal : 0;

        $order_note = "Changed booking on {$date} from {$original_variation_name} to {$new_variation_name}. £{$amount} is payable";
        $order->add_order_note($order_note);

        //amend order
        $order_item->set_variation_id($variation_id);
        $order_item->save();

        $child = get_post($child_id);
        $child_name = $child->post_title;

        $to = 'tim@titan21.co.uk';
        $subject = "Change To Booking for {$child_name} on {$date}";
        $message = $order_note;
        
        wp_mail($to, $subject, $message);
        

        ob_start();
        $this->shortcode->display_options($product, get_post($child_id));
        $content = ob_get_clean();

        $response = [
            "content" => $content
        ];

        echo json_encode($response, JSON_FORCE_OBJECT);

        wp_die();
    }

    public function ajax_reset_swap()
    {
        $child_id = $_POST['childid'];
        $variation_id = $_POST['variationid'];
        $product_id = wp_get_post_parent_id($variation_id);
        $product = wc_get_product($product_id);

        $additional_fee_product_id = rwmb_meta('additional_fee', ['object_type' => 'setting'], 'site-settings');

        if(!$order_item_id = CartHelper::has_already_ordered($product_id, $child_id))
        {
            wp_die();
        }
        
        $this->reset_swap($product_id, $child_id);


        ob_start();
        $this->shortcode->display_options($product, get_post($child_id));
        $content = ob_get_clean();

        $response = [
            "content" => $content
        ];

        echo json_encode($response, JSON_FORCE_OBJECT);

        wp_die();
    }

    public function reset_swap($product_id, $child_id)
    {
        if($order_item_id = CartHelper::has_already_ordered($product_id, $child_id))
        {
            $cart = WC()->cart->get_cart();

            foreach($cart as $cart_item_key => $cart_item)
            {
                if($cart_item['additional_fee_original_product_id'] == $product_id)
                {
                    WC()->cart->remove_cart_item($cart_item_key);
                }
            }
        }
    }

    /** Helpers - to refactor into own class */
    //@link https://stackoverflow.com/a/41266005/1424591
    public function matched_cart_items( $search_products, $child_id ) {

        if(WC()->cart->is_empty())
        {
            return 0;
        }

        $count = 0; // Initializing
    
        // Loop though cart items
        foreach(WC()->cart->get_cart() as $cart_item ) {
            // Handling also variable products and their products variations
            $cart_item_ids = array($cart_item['product_id'], $cart_item['variation_id']);

            // Handle a simple product Id (int or string) or an array of product Ids 
            if((is_array($search_products) && array_intersect($search_products, $cart_item_ids)) 
            || ( !is_array($search_products) && in_array($search_products, $cart_item_ids)))
            {
                if($cart_item['child_id'] == $child_id)
                {
                    $count++; // incrementing items count
                }
            }
        }

        return $count; // returning matched items count 
    }

    private function has_already_ordered($product_id, $child_id, $variation_id = null)
    {
        $has_already_ordered = false;

        $orders = wc_get_orders([]);

        foreach($orders as $order)
        {
            $items = $order->get_items();

            foreach($items as $item)
            {
                if($variation_id)
                {
                    if($item->get_variation_id() == $variation_id && $item->get_product_id() == $product_id && $item->get_meta('child_id') == $child_id)
                    {
                        $has_already_ordered = $item->get_id();
                    }
                }
                else
                {
                    if($item->get_product_id() == $product_id && $item->get_meta('child_id') == $child_id)
                    {
                        $has_already_ordered = $item->get_id();
                    }
                }
            }
        }

        return $has_already_ordered;
    }
}