<?php

namespace Titan21\SportingInfluence\Helpers;

abstract class CartHelper
{
    /** Helpers - to refactor into own class */
    //@link https://stackoverflow.com/a/41266005/1424591
    public static function matched_cart_items( $search_products, $child_id ) {

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
    /**
     * @todo replace with "find_product_in_cart()"
     */
    public static function get_cart_key($search_products, $child_id)
    {
        if(WC()->cart->is_empty())
        {
            return 0;
        }

        $count = 0; // Initializing
    
        // Loop though cart items
        foreach(WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            // Handling also variable products and their products variations
            $cart_item_ids = array($cart_item['product_id'], $cart_item['variation_id']);

            // Handle a simple product Id (int or string) or an array of product Ids 
            if((is_array($search_products) && array_intersect($search_products, $cart_item_ids)) 
            || ( !is_array($search_products) && in_array($search_products, $cart_item_ids)))
            {
                if($cart_item['child_id'] == $child_id)
                {
                    return $cart_item_key;
                }
            }
        }

        return null;
    }

    public static function has_already_ordered($product_id, $child_id, $variation_id = null)
    {
        $has_already_ordered = false;

        $orders = wc_get_orders(['limit' => -1]);

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

    public static function has_amendment_in_cart($event_product_id, $child_id, $variation_id = null)
    {
        $additional_fee_product_id = rwmb_meta('additional_fee', ['object_type' => 'setting'], 'site-settings');

        $tosearch[] = $additional_fee_product_id;

        if($variation_id)
        {
            $tosearch[] = $variation_id;
        }

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
            if((is_array($tosearch) && array_intersect($tosearch, $cart_item_ids)))
            {
                if($cart_item['additional_fee_original_product_id'] == $event_product_id && isset($cart_item['additional_child_id']) && $cart_item['additional_child_id'] == $child_id && $cart_item['additional_fee_new_variation_id'] == $variation_id)
                {
                    $count++; // incrementing items count
                }
            }
        }

        return $count; // returning matched items count 
    }
}