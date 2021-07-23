<?php

namespace Titan21\SportingInfluence\WooCommerce;

class WCChildcareVoucherPaymentGatewayRegistration
{
    public function __construct()
    {
        //add_action('plugins_loaded', [$this, 'plugins_loaded']);
        add_filter('woocommerce_payment_gateways', [$this, 'add_payment_gateway']);
        add_action('woocommerce_thankyou', [$this, 'thankyou']);
    }

    public function plugins_loaded()
    {
        add_filter('woocommerce_payment_gateways', [$this, 'add_payment_gateway']);
    }

    public function add_payment_gateway($gateways)
    {
        //@see class-wc-payment-gateways.php L118 - have changed to check for \WC_Payment_Gateway - @link https://github.com/woocommerce/woocommerce/issues/30276
        $gateways[] = "\Titan21\SportingInfluence\WooCommerce\WCChildcareVoucherPaymentGateway";
        return $gateways;
    }

    public function thankyou($order_id)
    {
        $order = new \WC_Order($order_id);
	
        $provider_key = $order->get_meta("_cvpg_provider");
        
        if($provider_key)
        {
            $childcare_vouchers = rwmb_meta('childcare_vouchers', ['object_type' => 'setting'], 'site-settings');
            $providers = $childcare_vouchers['vouchers'];
            $provider = array_search($provider_key, array_column($providers, "key"));
            
            echo <<<EOF
            <h2>Childcare Vouchers</h2>
            <p>Thank you for paying with Childcare Vouchers. Please make sure you visit the <a href="{$providers[$provider]['link']}" target="_blank">{$providers[$provider]['name']} website</a> and arrange the vouchers.</p>
EOF;
        }
    }
}