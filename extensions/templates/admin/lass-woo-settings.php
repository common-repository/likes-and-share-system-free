<?php
defined( 'ABSPATH' ) OR die( 'No script kiddies, please!' );

$lass_woo_position = $this->clean_value_esc_attr(get_option('lass_woo_position'));
$lass_woo_archive = $this->clean_value_esc_attr(get_option('lass_woo_archive'));
$lass_woo_archive_position = $this->clean_value_esc_attr(get_option('lass_woo_archive_position'));

$lass_selected_post_types = $this->clean_value_esc_attr((array)get_option('lass_selected_post_types')); // return array lists

if ( Lass_WoocommerceSettings::CheckWooCommerceExist() === false ) {
	echo '<h2>' . __("Sorry, we didn't find WooCommerce" ) . '</h2>';
	return;
}

if ( !in_array('product', $lass_selected_post_types) ) {
	echo '<h2>' . __('You must enable Products from settings page.') . '</h2>';
	echo '<a href="'.esc_url( get_admin_url(null, 'admin.php?page=lass-settings') ).'">'.__('Settings page', 'lass').'</a>';

	return;
}

?>
<div class="lass_options_wrapper">
	<h2 class="options-title"><?php _e('Likes and share options - WooCommerce','lass'); ?></h2>
  <p>Only available in PRO version.</p>
  <p><a href="https://conic-solutions.com/product/likes-and-share-buttons-wordpress-and-woocommerce/" target="_blank">Buy PRO version NOW!</a></p>
