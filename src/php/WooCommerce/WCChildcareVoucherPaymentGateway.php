<?php

namespace Titan21\SportingInfluence\WooCommerce;

class WCChildcareVoucherPaymentGateway extends \WC_Payment_Gateway
{
	public function __construct()
	{
		$this->id = "cvpg_payment_gateway";
		$this->method_title = "Childcare Voucher";
		$this->method_description = "Pay with Childcare Vouchers. To configure the list of providers, access <a href='/wp-admin/admin.php?page=site-settings'>Site Settings</a>.";
		$this->description = $this->get_description();
		$this->title = "Childcare Vouchers";
		$this->icon = null;
		$this->has_fields = true;
		$this->supports = array();
		
		$this->init_form_fields();
		
		$this->init_settings();

		foreach($this->settings as $setting_key => $value)
		{
			$this->$setting_key = $value;
		}
		
		if(is_admin())
		{
			add_action("woocommerce_update_options_payment_gateways_" . $this->id, array($this, "process_admin_options"));
		}
	}
	
	public function get_description()
	{
        $childcare_vouchers_fields = rwmb_meta('childcare_vouchers', ['object_type' => 'setting'], 'site-settings');
		$desc = $childcare_vouchers_fields['checkout_text'];

		$desc .= "<div style='overflow:auto'>";
		$desc .= "<select class='select' style='width:100%' name='cvpg_provider'>";
		
		$providers = $childcare_vouchers_fields['vouchers'];
		
		foreach($providers as $provider)
		{
			$desc .= "<option value='".$provider['key']."'>".$provider['name']."</option>";
		}
		
		$desc .= "</select>";
		$desc .= "</div>";
		
		return $desc;
	}
	
	public function init_form_fields()
	{
		$this->form_fields = array(
			"enabled" => array(
				"title" => "Enable / Disable",
				"label" => "Enable Childcare Vouchers",
				"type" => "checkbox",
				"default" => "no",
			)
		);
	}
	
	public function process_payment($order_id)
	{
		global $woocommerce;
		
		$order = new \WC_Order($order_id);
		
		$order->payment_complete();

        $childcare_vouchers_fields = rwmb_meta('childcare_vouchers', ['object_type' => 'setting'], 'site-settings');
		
		$providers = $childcare_vouchers_fields['vouchers'];
		$provider = array_search($_POST["cvpg_provider"], array_column($providers, "key"));
		
		$order->add_order_note( __("Childcare Voucher: ".$providers[$provider]['name'], "woothemes") );
		
		$order->update_meta_data("_cvpg_provider", $providers[$provider]['key']);
		$order->save();
		
		$woocommerce->cart->empty_cart();
		
		return array(
			"result" => "success",
			"redirect" => $this->get_return_url($order)."&cvpg_provider=".$_POST["cvpg_provider"]
		);
	}
}