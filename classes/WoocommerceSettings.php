<?php

class Lass_WoocommerceSettings
{

	public static function CheckWooCommerceExist() {
		if ( class_exists( 'WooCommerce' ) ) {
			return true;
		} else {
			return false;
		}
	}

}