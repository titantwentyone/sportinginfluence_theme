<?php

namespace Titan21\SportingInfluence\WooCommerce;

class WCCart
{
    public function __construct()
    {
        //add child info to cart items
        add_action('woocommerce_add_cart_item_data', [$this, 'add_child_data_to_cartitems'], 10, 3);

        //display select data in cart
        add_filter('woocommerce_get_item_data', [$this, 'display_data_in_cart'], 10, 2);

        //add cart data to order
        add_action('woocommerce_checkout_create_order_line_item', [$this, 'add_child_data_to_order'], 10, 4);

        //apply additional fee
        add_action("woocommerce_before_calculate_totals", array($this, "calculate_additional_fee"));

        add_action("woocommerce_checkout_order_processed", array($this, "add_note_for_additional_fee"));
    }

    public function add_child_data_to_cartitems($cart_item_data, $product_id, $variation_id)
    {
        if(!isset($_POST['childid']))
        {
            return;
        }

        $child_id = $_POST['childid'];
        $fields = rwmb_get_object_fields( $child_id );

        $cart_item_data['child_id'] = $child_id;
        $child_post = get_post($child_id);
        $cart_item_data['child_name'] = $child_post->post_title;

        foreach($fields as $field_name => $field)
        {
            if($field_name == "children-to-user_to")
            {
                $user_id = rwmb_get_value( $field_name, [], $child_id )[0];
                $user = get_user_by('id', $user_id);
                $cart_item_data['parent_user_id'] = $user_id;
                $cart_item_data['parent_user_name'] = $user->user_nicename;
            }
            else
            {
                $cart_item_data[$field_name] = rwmb_get_value( $field_name, [], $child_id );
            }
        }

        return $cart_item_data;
    }

    public function display_data_in_cart($item_data, $cart_item_data)
    {
        if(!isset($cart_item_data['child_id']))
        {
            return;
        }

        //if it is an additional fee, display the details of the change
        if($cart_item_data['product_id'] == rwmb_meta('additional_fee', ['object_type' => 'setting'], 'site-settings'))
        {
            $product = wc_get_product($cart_item_data['additional_fee_original_product_id']);
            $product_name = $product->get_title();

            $original_variation = wc_get_product($cart_item_data['additional_fee_original_variation_id']);
            $original_variation_name = implode(" ", $original_variation->get_variation_attributes());

            $new_variation = wc_get_product($cart_item_data['additional_fee_new_variation_id']);
            $new_variation_name = implode(" ", $new_variation->get_variation_attributes());

            $change_text = "{$product_name} from {$original_variation_name} to {$new_variation_name}";

            $item_data[] = [
                'key' => 'Change',
                'value' => $change_text
            ];
        }

        $item_data[] = [
            'key' => 'Child',
            'value' => $cart_item_data['child_name']
        ];

        /*
        //Option to add other fields here

        $fields_to_output = array_intersect_key($cart_item_data, $fields);

        foreach($fields_to_output as $key => $value)
        {
            $item_data[] = [
                'key' => $key,
                'value' => $value
            ];
        }
        */

        return $item_data;
    }

    public function add_child_data_to_order($item, $cart_item_key, $values, $order)
    {
        if(!isset($values['child_id']))
        {
            return;
        }

        $additional_fee_product_id = rwmb_meta('additional_fee', ['object_type' => 'setting'], 'site-settings');

        if($item->get_product_id() == $additional_fee_product_id)
        {
            $item->update_meta_data('child_id', $values['child_id']);
            $item->update_meta_data('additional_fee_original_product_id', $values['additional_fee_original_product_id']);
            $item->update_meta_data('additional_fee_original_variation_id', $values['additional_fee_original_variation_id']);
            $item->update_meta_data('additional_fee_new_variation_id', $values['additional_fee_new_variation_id']);
            $item->update_meta_data('additional_fee_original_order', $values['additional_fee_original_order']);
        }
        else
        {
            $child_id = $values['child_id'];

            $fields = rwmb_get_object_fields( $child_id );

            $fields_to_store = array_intersect_key($values, $fields);

            $item->update_meta_data('child_id', $values['child_id']);
            $item->update_meta_data('parent_user_id', $values['parent_user_id']);
            $item->update_meta_data('parent_user_name', $values['parent_user_name']);

            foreach($fields_to_store as $key => $value)
            {
                $item->update_meta_data($key, $value);
            }

            /*
            foreach($fields as $field_name => $field)
            {
                if($field_name == "children-to-user_to")
                {
                    $user_id = rwmb_get_value( $field_name, [], $child_id )[0];
                    $user = get_user_by('id', $user_id);
                    $order->update_meta('parent_user_id', $user_id);
                    $order->update_meta('parent_user_name', $user->user_nicename);
                }
                else
                {
                    //$cart_item_data[$field_name] = rwmb_get_value( $field_name, [], $child_id );
                    $order->update_meta($field_name, rwmb_get_value( $field_name, [], $child_id ));
                }
            }
            */
        }
    }

    public function calculate_additional_fee($cart)
    {
        $additional_fee_product_id = rwmb_meta('additional_fee', ['object_type' => 'setting'], 'site-settings');

        foreach($cart->get_cart() as $cartitem)
        {
            if($cartitem['product_id'] == $additional_fee_product_id)
            {
                //get the new price
                $price = $cartitem['additional_fee_price'];
                $cartitem['data']->set_price($price);
            }
        }
    }

    public function add_note_for_additional_fee($order_id)
    {
        $order = wc_get_order($order_id);

        $order_items = $order->get_items();
        $additional_fee_product_id = rwmb_meta('additional_fee', ['object_type' => 'setting'], 'site-settings');

        foreach($order_items as $order_item)
        {
            if($order_item->get_product_id() == $additional_fee_product_id)
            {
                //get original order
                $original_order_id = $order_item->get_meta('additional_fee_original_order');
                $original_order = wc_get_order($original_order_id);
                $order_note = "Additional Fee Order Number: {$order_id}";
                $original_order->add_order_note($order_note);
            }
        }
    }
}