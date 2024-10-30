<?php

class Lass_Dashboard extends Lass_LikesAndShareSystem
{
	public $all_post_types = array();
	public $default_plugin_settings;

	public function __construct() {
		// Set default values
		$this->set_default_values();

		// Register admin menu
		add_action( 'admin_menu', array($this, 'admin_menu') );

		// Register Settings
		add_action( 'admin_init', array($this, 'register_settings') );

		// Set all social sharing networks
		$this->set_social_sharing();
	}

	// Admin menu
	public function admin_menu() {
		add_menu_page('','Likes and Share', 'manage_options', 'lass-settings', array($this, 'lass_settings'), LASS_ASSETS_ADMIN . '/img/like-share-icon.png', 90);

		add_submenu_page('lass-settings', '','Settings', 'manage_options', 'lass-settings', array($this, 'lass_settings'));

		if ( Lass_WoocommerceSettings::CheckWooCommerceExist() === true ) {
			add_submenu_page('lass-settings', '','WooCommerce options', 'manage_options', 'lass-woo-settings', array($this, 'lass_woo_settings'));
		}

		add_submenu_page('lass-settings', '','Instructions', 'manage_options', 'lass-instructions', array($this, 'lass_instructions'));
	}

	// Register settings
	public function register_settings() {
		foreach ( $this->default_plugin_settings as $option ) {
			if ( $option['sanitize_callback'] === '' ) {
				register_setting( $option['option_group'], $option['option_name'], array(
					'sanitize_callback' => array($this, 'validate_options')
				));
			} else {
				register_setting( $option['option_group'], $option['option_name'], array(
					'sanitize_callback' => array($this, $option['sanitize_callback'])
				));
			}
		}
	}

	public function validate_options($option) {
		if ( is_array($option) ) {
			$sanitize = array_map( 'sanitize_text_field', $option );
		} else {
			$sanitize = sanitize_text_field($option);
		}

		return $sanitize;
	}

	public function validate_texarea($option) {
		if ( is_array($option) ) {
			$sanitize = array_map( 'sanitize_textarea_field', $option );
		} else {
			$sanitize = sanitize_textarea_field($option);
		}

		return $sanitize;
	}

	public function clean_value_esc_attr($value) {
		if ( is_array($value) ) {
			$sanitize = array_map( 'esc_attr', $value );
		} else {
			$sanitize = esc_attr( $value );
		}

		return $sanitize;
	}

	public function clean_value_esc_url($value) {
		if ( is_array($value) ) {
			$sanitize = array_map( 'esc_url', $value );
		} else {
			$sanitize = esc_url( $value );
		}

		return $sanitize;
	}


	public function set_default_values() {
		$this->default_plugin_settings = array(
			array(
				'option_group' => 'lass-general-options',
				'option_name' => 'lass_social_sharing_network',
				'req' => 'true',
				'def_value' => array('likes', 'facebook', 'twitter', 'pinterest', 'linkedin'),
				'sanitize_callback' => ''
			),
			array(
				'option_group' => 'lass-general-options',
				'option_name' => 'lass_icons_uses',
				'req' => 'true',
				'def_value' => 'custom_images',
				'sanitize_callback' => ''
			),
			array(
				'option_group' => 'lass-general-options',
				'option_name' => 'lass_fa_icons',
				'req' => 'true',
				'def_value' => array('likes' => 'fa-heart-o', 'facebook' => 'fa-facebook-square', 'twitter' => 'fa-twitter-square', 'pinterest' => 'fa-pinterest-square', 'linkedin' => 'fa-linkedin-square'),
				'sanitize_callback' => ''
			),
			array(
				'option_group' => 'lass-general-options',
				'option_name' => 'lass_img_icons',
				'req' => 'true',
				'def_value' => array(
					'likes' => LASS_ASSETS_FRONT . '/lass-icons/style-1/likes.png',
					'facebook' => LASS_ASSETS_FRONT . '/lass-icons/style-1/facebook.png',
					'twitter' => LASS_ASSETS_FRONT . '/lass-icons/style-1/twitter.png',
					'pinterest' => LASS_ASSETS_FRONT . '/lass-icons/style-1/pinterest.png',
					'linkedin' => LASS_ASSETS_FRONT . '/lass-icons/style-1/linkedin.png'),
				'sanitize_callback' => ''
			),
			array(
				'option_group' => 'lass-general-options',
				'option_name' => 'lass_fa_colors',
				'req' => 'true',
				'def_value' => array('likes' => '787878', 'facebook' => '787878', 'twitter' => '787878', 'pinterest' => '787878', 'linkedin' => '787878'),
				'sanitize_callback' => ''
			),
			array(
				'option_group' => 'lass-general-options',
				'option_name' => 'lass_fa_colors_hover',
				'req' => 'true',
				'def_value' => array('likes' => '595959', 'facebook' => '595959', 'twitter' => '595959', 'pinterest' => '595959', 'linkedin' => '595959'),
				'sanitize_callback' => ''
			),
			array(
				'option_group' => 'lass-general-options',
				'option_name' => 'lass_selected_post_types',
				'req' => 'true',
				'def_value' => array('post'),
				'sanitize_callback' => ''
			),
			array(
				'option_group' => 'lass-general-options',
				'option_name' => 'lass_position',
				'req' => 'true',
				'def_value' => 'bottom',
				'sanitize_callback' => ''
			),
			array(
				'option_group' => 'lass-general-options',
				'option_name' => 'lass_like_liked',
				'req' => 'true',
				'def_value' => 'fa-heart',
				'sanitize_callback' => ''
			),
			array(
				'option_group' => 'lass-general-options',
				'option_name' => 'lass_like_liked_img',
				'req' => 'true',
				'def_value' => LASS_ASSETS_FRONT . '/lass-icons/style-1/liked.png',
				'sanitize_callback' => ''
			),
			array(
				'option_group' => 'lass-general-options',
				'option_name' => 'lass_align',
				'req' => 'true',
				'def_value' => 'center',
				'sanitize_callback' => ''
			),
			array(
				'option_group' => 'lass-general-options',
				'option_name' => 'lass_font_size',
				'req' => 'true',
				'def_value' => '30',
				'sanitize_callback' => ''
			),
			array(
				'option_group' => 'lass-general-options',
				'option_name' => 'lass_custom_style',
				'req' => 'false',
				'def_value' => '',
				'sanitize_callback' => 'validate_texarea'
			),
			array(
				'option_group' => 'lass-general-options',
				'option_name' => 'lass_show_on_single',
				'req' => 'true',
				'def_value' => 'show',
				'sanitize_callback' => ''
			),

			array(
				'option_group' => 'lass-general-options',
				'option_name' => 'lass_show_on_archive',
				'req' => 'false',
				'def_value' => 'show',
				'sanitize_callback' => ''
			),
			array(
				'option_group' => 'lass-general-options',
				'option_name' => 'lass_show_on_exclude_home',
				'req' => 'false',
				'def_value' => 'exclude',
				'sanitize_callback' => ''
			),

			array(
				'option_group' => 'lass-general-options',
				'option_name' => 'lass_first_time_activation',
				'req' => 'false',
				'def_value' => '',
				'sanitize_callback' => ''
			),

			array(
				'option_group' => 'lass-general-options',
				'option_name' => 'lass_custom_img_width',
				'req' => 'true',
				'def_value' => '35px',
				'sanitize_callback' => ''
			),
			array(
				'option_group' => 'lass-general-options',
				'option_name' => 'lass_custom_img_height',
				'req' => 'true',
				'def_value' => '35px',
				'sanitize_callback' => ''
			),
			array(
				'option_group' => 'lass-general-options',
				'option_name' => 'lass_social_sharing_network_exclude',
				'req' => 'true',
				'def_value' => array(),
				'sanitize_callback' => ''
			),

			// WooCommerce
			array(
				'option_group' => 'lass-woo-options',
				'option_name' => 'lass_woo_position',
				'req' => 'true',
				'def_value' => 'woocommerce_share',
				'sanitize_callback' => ''
			),
			array(
				'option_group' => 'lass-woo-options',
				'option_name' => 'lass_woo_archive',
				'req' => 'true',
				'def_value' => 'no',
				'sanitize_callback' => ''
			),
			array(
				'option_group' => 'lass-woo-options',
				'option_name' => 'lass_woo_archive_position',
				'req' => 'true',
				'def_value' => 'woocommerce_after_shop_loop_item',
				'sanitize_callback' => ''
			)
		);
	}

	// Admin instructions page
	public function lass_instructions() {
		require_once LASS_TEMPLATES_ADMIN . '/lass-instructions.php';
	}

	public function lass_woo_settings() {
		require_once LASS_TEMPLATES_ADMIN . '/lass-woo-settings.php';
	}

	// Admin options page
	public function lass_settings() {
		require_once LASS_TEMPLATES_ADMIN . '/lass-settings.php';
	}

	/* HELPERS FUNCTIONS */

	/* HELPERS FUNCTIONS END*/


}