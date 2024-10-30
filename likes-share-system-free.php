<?php
/**
 * Plugin Name: Likes and Share System Free
 * Plugin URI:  https://likes-and-share.inchoweb.com/
 * Description: Independent likes system and share buttons for your website. Custom post types are supported! Working with WooCommerce as well.
 * Version:     1.1
 * Author:      Conic Solutions
 * Author URI:  https://conic-solutions.com/
 * Text Domain: lass
 */

defined( 'ABSPATH' ) OR die( 'No script kiddies, please!' );
require_once 'configs/init.php';
global $wp_version;

/** Compare PHP version */
if ( version_compare( phpversion(), '5.4', '<' ) ) {
	add_action( 'admin_notices', 'lass_like_share_system_php_version_error' );
	return false;
}
/** Compare PHP version END */

/** Compare WP version */
if ( version_compare( $wp_version, '3.4.0', '<' ) ) {
	add_action( 'admin_notices', '_like_share_system_wordpress_version_error' );
	return false;
}
/** Compare WP version */


/** PHP version error functions */
function lass_like_share_system_php_version_error() {
	printf( '<div class="error"><p>%s</p></div>', esc_html( lass_like_share_system_php_version_text() ) );
}

function lass_like_share_system_php_version_text() {
	return __( 'Like and share system error: Your version of PHP is too old to run this plugin. You must be running PHP 5.4 or higher.', 'lass' );
}
/** PHP version error functions END */

/** WP version error functions */
function lass_like_share_system_wordpress_version_error() {
	printf( '<div class="error"><p>%s</p></div>', esc_html( lass_like_share_system_wordpress_version_text() ) );
	//print_r( get_plugin_data( __FILE__ )['Version'] );
}

function lass_like_share_system_wordpress_version_text() {
	return __( 'Like and share system error: Your version of Wordpress is too old to run this plugin. You must be runing 3.4 or higher.', 'lass' );
}
/** WP version error functions END */

/** Add plugin links */
function lass_plugin_links( $links ) {
	$links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=lass-settings') ) .'">'.__('Settings','lass').'</a>';
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'lass_plugin_links' );

/** Create instance */
$LikesAndShareSystem = new Lass_LikesAndShareSystem();

register_activation_hook( __FILE__, array( $LikesAndShareSystem, 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( $LikesAndShareSystem, 'plugin_deactivation' ) );
register_uninstall_hook(__FILE__, array( 'Lass_LikesAndShareSystem', 'plugin_delete' ) );


