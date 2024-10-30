<?php

class Lass_LikesAndShareSystem
{
	public $table_name;
	public static $table_name_s;
	public $plugin_settings;
	public $social_sharing_default_fa = array();
	public $dashboard;
	public $all_social_sharing;
	public $style_called = false;
	public $style_called_custom_css = false;
	public $global_isset_id = array();
	
	public function __construct() {
		$this->dashboard = new Lass_Dashboard();
		
		$this->set_table_name();
		$this->get_plugin_settings();
		
		// Set all social sharing networks
		$this->set_social_sharing();
		
		// Create a shortcode for custom template
		add_shortcode('lass_system_archive', array($this, 'output_lass_to_posts_exc'));
		add_shortcode('lass_system', array($this, 'output_lass_to_posts'));
		
		// Action to add lass to content
		add_filter('the_content', array($this, 'output_lass_to_posts') );
		add_filter('the_excerpt', array($this, 'output_lass_to_posts_exc') );
		
		
		// Action to add necessary scripts
		add_action( 'wp_enqueue_scripts', array($this, 'include_front_scripts') );
		add_action( 'wp_enqueue_scripts', array($this, 'include_front_scripts_fontawesome'), 999 );
		add_action( 'admin_enqueue_scripts', array($this, 'include_admin_scripts') );
		
		// AJAX
		add_action( 'wp_ajax_likesresponse', array($this, 'get_likes_response_ajax' ) );
		add_action( 'wp_ajax_nopriv_likesresponse', array($this, 'get_likes_response_ajax' ) );
	}
	
	/* INCLUDE NECESSARY SCRIPTS */
	public function include_front_scripts() {
		wp_register_style('lass-front-css', LASS_ASSETS_FRONT . '/css/likes-share-system.css');
		wp_enqueue_style('lass-front-css');
		
		wp_register_script('lass-front-script', LASS_ASSETS_FRONT . '/js/likes-share-system.js', array( 'jquery' ) );
		
		$admin_script = array( 'ajax_url' => admin_url( 'admin-ajax.php' ) );
		wp_localize_script( 'lass-front-script', 'wpp', $admin_script );
		wp_enqueue_script( 'lass-front-script' );
	}
	
	public function include_front_scripts_fontawesome() {
		if ( $this->is_fontawesome_loaded() === 0 ) {
			wp_register_style( 'fontawesome', LASS_ASSETS_FRONT . '/fontawesome/css/font-awesome.min.css', false, '4.7.0' );
			wp_enqueue_style( 'fontawesome' );
		}
	}
	
	public function include_admin_scripts() {
		wp_register_style('lass-admin-style', LASS_ASSETS_ADMIN . '/css/likes-share-system.css');
		wp_enqueue_style('lass-admin-style');
		
		wp_register_script('lass-admin-script', LASS_ASSETS_ADMIN . '/js/likes-share-system.js', array( 'jquery' ) );
		wp_enqueue_script( 'lass-admin-script' );
		
		
		wp_register_style('lass-colorpicker-style', LASS_ASSETS_ADMIN . '/colorpicker/css/colorpicker.css');
		wp_enqueue_style('lass-colorpicker-style');
		
		wp_register_script('lass-colorpicker-script', LASS_ASSETS_ADMIN . '/colorpicker/js/colorpicker.js', array('jquery'));
		wp_enqueue_script('lass-colorpicker-script');
	}
	/* INCLUDE NECESSARY SCRIPTS END */
	
	
	/* GENERAL FUNCTIONS */
	public function get_ip_address() {
		if ( isset($_SERVER['HTTP_CLIENT_IP']) )
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) )
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if( isset($_SERVER['HTTP_X_FORWARDED']) )
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if( isset($_SERVER['HTTP_FORWARDED_FOR']) )
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if( isset($_SERVER['HTTP_FORWARDED']) )
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if( isset($_SERVER['REMOTE_ADDR']) )
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		
		return $ipaddress . '_' . md5($_SERVER['HTTP_USER_AGENT']);
	}
	
	public function create_table() {
		global $wpdb;
		
		$charset_collate = $wpdb->get_charset_collate();
		
		if($wpdb->get_var("SHOW TABLES LIKE '$this->table_name'") !== $this->table_name) {
			$sql = "CREATE TABLE $this->table_name (
				likes_id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
				post_id INT(11) NOT NULL,
				ip_address VARCHAR(240) NOT NULL,
				likes_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY  (likes_id),
				UNIQUE INDEX ix (post_id, ip_address)
			) $charset_collate;";
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		} else {
		
		}
	}
	
	public static function delete_table () {
		global $wpdb;
		$wpdb->query( "DROP TABLE IF EXISTS ".self::$table_name_s );
	}
	
	public function get_plugin_settings() {
		
		$icons_uses = (!empty(get_option('lass_icons_uses'))) ? $this->dashboard->clean_value_esc_attr(get_option('lass_icons_uses')) : 'fontawesome';
		
		$icons_class_img = array();
		$lass_like_liked = $this->dashboard->clean_value_esc_attr(get_option('lass_like_liked'));
		
		if ( $icons_uses === 'fontawesome' ) {
			
			$icons_class_img = $this->dashboard->clean_value_esc_attr((array)get_option('lass_fa_icons'));
			
		} else if ( $icons_uses === 'custom_images' ) {
			
			$icons_class_img = $this->dashboard->clean_value_esc_url((array)get_option('lass_img_icons'));
			$lass_like_liked = $this->dashboard->clean_value_esc_url(get_option('lass_like_liked_img'));
		}
		
		$settings = array(
			'all_included_social' => $this->dashboard->clean_value_esc_attr((array)get_option('lass_social_sharing_network')),
			'icons_uses' => $icons_uses,
			'icons_class_img' => $icons_class_img,
			'icons_color' => $this->dashboard->clean_value_esc_attr((array)get_option('lass_fa_colors')),
			'icons_hover_color' => $this->dashboard->clean_value_esc_attr((array)get_option('lass_fa_colors_hover')),
			'like_liked_icon' => $lass_like_liked,
			'selected_post_types' => $this->dashboard->clean_value_esc_attr((array)get_option('lass_selected_post_types')),
			'position' => $this->dashboard->clean_value_esc_attr(get_option('lass_position')),
			'horizontal_align' => $this->dashboard->clean_value_esc_attr(get_option('lass_align')),
			'font_size' => $this->dashboard->clean_value_esc_attr(get_option('lass_font_size')),
			'custom_style' => $this->dashboard->clean_value_esc_attr(get_option('lass_custom_style')),
			'show_on_single' => $this->dashboard->clean_value_esc_attr(get_option('lass_show_on_single')),
			'show_on_archive' => $this->dashboard->clean_value_esc_attr(get_option('lass_show_on_archive')),
			'show_on_exclude_home' => $this->dashboard->clean_value_esc_attr(get_option('lass_show_on_exclude_home')),
			'custom_img_width' => $this->dashboard->clean_value_esc_attr(get_option('lass_custom_img_width')),
			'custom_img_height' => $this->dashboard->clean_value_esc_attr(get_option('lass_custom_img_height')),
			'lass_woo_position' => $this->dashboard->clean_value_esc_attr(get_option('lass_woo_position')),
			'lass_woo_archive' => $this->dashboard->clean_value_esc_attr(get_option('lass_woo_archive')),
			'lass_woo_archive_position' => $this->dashboard->clean_value_esc_attr(get_option('lass_woo_archive_position')),
			'lass_social_sharing_network_exclude' => $this->dashboard->clean_value_esc_attr((array)get_option('lass_social_sharing_network_exclude'))
		);
		$this->plugin_settings = $settings;
	}
	
	public function set_table_name(){
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'likes_share_system';
		self::$table_name_s = $wpdb->prefix . 'likes_share_system';
	}
	/* GENERAL FUNCTIONS END */
	
	
	/* AJAX RESPONSE */
	public function get_likes_response_ajax() {
		global $wpdb;
		$post_id = (int)$_POST['post_id'];
		$post_name = sanitize_key($_POST['post_name']);
		
		$post = get_post($post_id);
		
		$output = array();
		if ($post->post_name === $post_name) {
			$output['icons_uses'] = $this->plugin_settings['icons_uses'];
			$output['current_img'] = !(empty($this->plugin_settings['icons_class_img']['likes']))?$this->plugin_settings['icons_class_img']['likes'] : $this->social_sharing_default_fa['likes']['custom_img'];
			
			/*
			 * Check if used fa or custom img
			 * set liked class or img if no empty or set default one
			 */
			if ( $output['icons_uses'] === 'fontawesome' ) {
				$output['like_liked_icon'] = !(empty($this->plugin_settings['like_liked_icon']))?$this->plugin_settings['like_liked_icon']:$this->social_sharing_default_fa['liked']['fa'];
			} else if (  $output['icons_uses'] === 'custom_images' ) {
				$output['like_liked_icon'] = !(empty($this->plugin_settings['like_liked_icon']))?$this->plugin_settings['like_liked_icon']:$this->social_sharing_default_fa['liked']['custom_img'];
			}
			
			if ( $this->check_likes_exists($post->ID) ) {
				if ( isset($_COOKIE['lass_'.$post->ID]) && $this->check_likes_cookies($post->ID) !== false ) {
					$cookie_cs = $this->lass_sanitize_key($_COOKIE['lass_'.$post->ID]);
					$data = array(
						'post_id' => $post->ID,
						'ip_address' => $cookie_cs
					);
				} else {
					$data = array(
						'post_id' => $post->ID,
						'ip_address' => $this->get_ip_address()
					);
				}
				
				if ( $wpdb->delete( $this->table_name, $data ) !== false) {
					$output['status'] = 'LIKE_DELETED';
					$output['likes_count'] = $this->get_likes_number($post->ID);
					
					if ( isset($_COOKIE['lass_'.$post->ID]) ) {
						setcookie('lass_'.$post->ID, '',time() - 3600, '/');
					}
					
				} else {
					$output['status'] = 'ERROR';
				}
				
			} else {
				// Insert new
				$data = array(
					'post_id' => $post->ID,
					'ip_address' => $this->get_ip_address()
				);
				if ($wpdb->insert($this->table_name, $data) !== false) {
					$output['status'] = 'LIKE_INSERTED';
					$output['likes_count'] = $this->get_likes_number($post->ID);
					$output['ip_address'] = $this->get_ip_address();
					
					setcookie('lass_'.$post->ID, $this->get_ip_address(),time() + (86400*90), '/');
					
				} else {
					$output['status'] = 'ERROR';
				}
			}
		} else {
			$output['status'] = 'ERROR POST NAME';
		}
		
		$output = json_encode($output);
		echo $output;
		die();
	}
	/* AJAX RESPONSE END */
	
	/* FUNCTONS TO SHOW LASS ON THE FRONT */
	public function output_lass_to_posts($content) {
		global $post;
		$feature_image = get_the_post_thumbnail_url( $post->ID );
		if ( $feature_image === false ) {
			$feature_image = '';
		}
		
		/* if ( ! in_the_loop() ) {
			return $content;
		} */
		
		if ( !is_singular() ) {
			return $content;
		}
		
		if ( has_shortcode($content, 'lass_system') ) {
			return $content;
		}
		
		$output = $this->set_social_share_on_front($post,get_permalink($post->ID),$post->post_title,$feature_image,site_url());
		
		$final_content = $content;
		if ( in_array($post->post_type, $this->plugin_settings['selected_post_types']) ) {
			if ( $this->plugin_settings['position'] === 'top' ) {
				$content = $output . $content;
			} else if ( $this->plugin_settings['position'] === 'bottom' ) {
				$content = $content . $output;
			}
		}
		
		#if ( (int)get_option('page_on_front') === $post->ID ) {
		if ( is_front_page() || is_home() ) {
			if ( $this->plugin_settings['show_on_exclude_home'] !== 'exclude') {
				$final_content = $content;
			} else {
				$txt = '<style>.likes-share-system {display: none;}</style>';
				$final_content .= $txt;
			}
		} else if ( is_singular() ) {
			if ( $this->plugin_settings['show_on_single'] === 'show' ) {
				$final_content = $content;
			}
		} else if ( is_archive() ||
		            is_category() ||
		            is_post_type_archive() ||
		            is_tag() ||
		            is_tax())
		{
			if ( $this->plugin_settings['show_on_archive'] === 'show' ) {
				$final_content = $content;
			}
		}
		
		return $final_content;
	}
	
	public function output_lass_to_posts_exc($content) {
		global $post;
		$feature_image = get_the_post_thumbnail_url( $post->ID );
		if ( $feature_image === false ) {
			$feature_image = '';
		}
		
		/* if ( ! in_the_loop() ) {
			return $content;
		} */
		
		if ( is_singular() ) {
			if ( (int)get_option('page_on_front') === $post->ID ) {
				return $content;
			}
		}
		
		if ( has_shortcode($content, 'lass_system') ) {
			return $content;
		}
		
		$output = $this->set_social_share_on_front($post,get_permalink($post->ID),$post->post_title,$feature_image,site_url());
		
		$final_content = $content;
		if ( in_array($post->post_type, $this->plugin_settings['selected_post_types']) ) {
			if ( $this->plugin_settings['position'] === 'top' ) {
				$content = $output . $content;
			} else if ( $this->plugin_settings['position'] === 'bottom' ) {
				$content = $content . $output;
			}
		}
		
		#if ( (int)get_option('page_on_front') === $post->ID ) {
		if ( is_front_page() || is_home() ) {
			if ( $this->plugin_settings['show_on_exclude_home'] !== 'exclude') {
				$final_content = $content;
			} else {
				$txt = '<style>.likes-share-system {display: none;}</style>';
				$final_content .= $txt;
			}
		} else if ( is_singular() ) {
			if ( $this->plugin_settings['show_on_single'] === 'show' ) {
				$final_content = $content;
			}
		} else if ( is_archive() ||
		            is_category() ||
		            is_post_type_archive() ||
		            is_tag() ||
		            is_tax())
		{
			if ( $this->plugin_settings['show_on_archive'] === 'show' ) {
				$final_content = $content;
			}
		}
		
		return $final_content;
	}
	/* FUNCTONS TO SHOW LASS ON THE FRONT END */
	
	
	/* SOCIAL MEDIA */
	public function set_social_sharing(){
		$this->all_social_sharing = array(
			array('slug' => 'likes', 'name' => 'Likes'),
			array('slug' => 'facebook', 'name' => 'Facebook'),
			array('slug' => 'twitter', 'name' => 'Twitter'),
			array('slug' => 'pinterest', 'name' => 'Pinterest'),
			array('slug' => 'linkedin', 'name' => 'Linkedin'),
			array('slug' => 'reddit', 'name' => 'Reddit'),
			array('slug' => 'stumbleupon', 'name' => 'Stumbleupon'),
			array('slug' => 'tumblr', 'name' => 'Tumblr'),
			array('slug' => 'vk', 'name' => 'Vk'),
			array('slug' => 'xing', 'name' => 'Xing')
		);
		
		$this->social_sharing_default_fa['likes'] = array(
			'fa' => 'fa-heart-o',
			'custom_img' => plugins_url('/extensions/assets/front/lass-icons/style-1/likes.png', plugin_dir_path( __FILE__ ))
		);
		$this->social_sharing_default_fa['liked'] = array(
			'fa' => 'fa-heart',
			'custom_img' => plugins_url('/extensions/assets/front/lass-icons/style-1/liked.png', plugin_dir_path( __FILE__ ))
		);
		$this->social_sharing_default_fa['facebook'] = array(
			'fa' => 'fa-facebook-square',
			'custom_img' => plugins_url('/extensions/assets/front/lass-icons/style-1/facebook.png', plugin_dir_path( __FILE__ )) );
		$this->social_sharing_default_fa['twitter'] = array(
			'fa' => 'fa-twitter-square',
			'custom_img' => plugins_url('/extensions/assets/front/lass-icons/style-1/twitter.png', plugin_dir_path( __FILE__ ))
		);
		$this->social_sharing_default_fa['pinterest'] = array(
			'fa' => 'fa-pinterest-square',
			'custom_img' => plugins_url('/extensions/assets/front/lass-icons/style-1/pinterest.png', plugin_dir_path( __FILE__ ))
		);
		$this->social_sharing_default_fa['linkedin'] = array(
			'fa' => 'fa-linkedin-square',
			'custom_img' => plugins_url('/extensions/assets/front/lass-icons/style-1/linkedin.png', plugin_dir_path( __FILE__ ))
		);
		$this->social_sharing_default_fa['reddit'] = array(
			'fa' => 'fa-reddit',
			'custom_img' => plugins_url('/extensions/assets/front/lass-icons/style-1/reddit.png', plugin_dir_path( __FILE__ ))
		);
		$this->social_sharing_default_fa['stumbleupon'] = array(
			'fa' => 'fa-stumbleupon',
			'custom_img' => plugins_url('/extensions/assets/front/lass-icons/style-1/stumbleupon.png', plugin_dir_path( __FILE__ ))
		);
		$this->social_sharing_default_fa['tumblr'] = array(
			'fa' => 'fa-tumblr',
			'custom_img' => plugins_url('/extensions/assets/front/lass-icons/style-1/tumblr.png', plugin_dir_path( __FILE__ ))
		);
		$this->social_sharing_default_fa['vk'] = array(
			'fa' => 'fa-vk',
			'custom_img' => plugins_url('/extensions/assets/front/lass-icons/style-1/vk.png', plugin_dir_path( __FILE__ ))
		);
		$this->social_sharing_default_fa['xing'] = array(
			'fa' => 'fa-xing',
			'custom_img' => plugins_url('/extensions/assets/front/lass-icons/style-1/xing.png', plugin_dir_path( __FILE__ ))
		);
	}
	
	public function social_share_links($post, $url='',$title='',$image='',$source='') {
		$text = wp_trim_words($post->post_content,'150');
		
		$settings = array(
			'likes' => '#',
			'facebook' => 'https://facebook.com/sharer.php?u='.$url,
			'twitter' => 'https://twitter.com/intent/tweet?url='.$url.'&text='.$title,
			'pinterest' => 'http://pinterest.com/pin/create/button/?url='.$url.'&description='.$title.'&media='.$image,
			'linkedin' => 'https://www.linkedin.com/shareArticle?mini=true&url='.$url.'&title='.$title.'&summary='.$text.'&source'.$source,
			'reddit' => 'https://www.reddit.com/submit?url='.$url.'&title='.$title,
			'stumbleupon' => 'http://mix.com/add?url='.$url,
			'tumblr' => 'https://www.tumblr.com/widgets/share/tool?canonicalUrl='.$url.'&title='.$title.'&caption='.$text.'&tags=',
			'vk' => 'http://vk.com/share.php?url='.$url.'&title='.$title.'&comment='.$text,
			'xing' => 'https://www.xing.com/spi/shares/new?url='.$url
		);
		return $settings;
	}
	
	public function set_social_share_on_front($post,$url='',$title='',$image='',$source='') {
		$txt = '';
		$setted = false;
		
		if ( in_array($post->ID, $this->global_isset_id) ) {
			$setted = true;
		} else {
			if ( !is_singular() ) {
				$this->global_isset_id[] = $post->ID;
			}
		}
		
		if ( $setted === true ) {
			return '';
		}
		
		$social_links = $this->social_share_links($post, $url,$title,$image,$source);
		
		if (  $this->plugin_settings ['icons_uses'] === 'fontawesome' ) {
			if ( $this->style_called === false ) {
				$txt .= $this->include_fa_style();
				if ( !is_singular() ) {
					$this->style_called = true;
				}
			}
			
			$txt .= '<div data-post-name="'.$post->post_name.'" data-post-id="'.$post->ID.'" class="likes-share-system lass-front-wrapper lass-wrapper-position-'.$this->plugin_settings['position'].'">';
			foreach ( $this->plugin_settings['all_included_social'] as $active_social )
			{
				$fa_class = !(empty($this->plugin_settings['icons_class_img'][$active_social])) ? $this->plugin_settings['icons_class_img'][$active_social] : $this->social_sharing_default_fa[$active_social]['fa'];
				
				if ( $active_social === 'likes' ) {
					$lass_icon_liked = '<i class="lass-front-'.$active_social.' fa '.$fa_class.'" aria-hidden="true"></i>';
					if ( $this->check_likes_exists($post->ID) ) {
						$liked_class = !(empty($this->plugin_settings['like_liked_icon']))?$this->plugin_settings['like_liked_icon']:$this->social_sharing_default_fa['liked']['fa'];;
						
						$lass_icon_liked = '<i class="lass-front-'.$active_social.' fa '.$liked_class.'" aria-hidden="true"></i>';
					}
					
					if ( $this->lass_archive_social_excluded($active_social) === false ) {
						$txt .= '<a class="lss-item-likes" href="'.$social_links[$active_social].'" target="_blank"><span class="lass-like-counter">'.$this->get_likes_number($post->ID).'</span>'.$lass_icon_liked.'</a>';
					}
				} else {
					if ( $this->lass_archive_social_excluded($active_social) === false ) {
						$txt .= '<a id="lass-id-'.$active_social.'" class="lss-item" href="'.$social_links[$active_social].'" target="_blank"><i class="lass-front-'.$active_social.' fa '.$fa_class.'" aria-hidden="true"></i></a>';
					}
				}
			}
			$txt .= '</div>';
			
		} else if ( $this->plugin_settings ['icons_uses'] === 'custom_images' ) { // if is custom images
			if ( $this->style_called === false ) {
				$txt .= $this->include_img_style();
				if ( !is_singular() ) {
					$this->style_called = true;
				}
			}
			
			$txt .= '<div data-post-name="'.$post->post_name.'" data-post-id="'.$post->ID.'" class="likes-share-system lass-front-wrapper lass-wrapper-position-'.$this->plugin_settings['position'].'">';
			foreach ( $this->plugin_settings['all_included_social'] as $active_social )
			{
				$icon_img = !(empty($this->plugin_settings['icons_class_img'][$active_social])) ? $this->plugin_settings['icons_class_img'][$active_social] : $this->social_sharing_default_fa[$active_social]['custom_img'];
				
				if ( $active_social === 'likes' ) {
					$lass_icon_liked = '<img class="lass-front-'.$active_social.'" src="'.$icon_img.'">';
					if ( $this->check_likes_exists($post->ID) ) {
						
						$liked_img = !(empty($this->plugin_settings['like_liked_icon']))?$this->plugin_settings['like_liked_icon']:$this->social_sharing_default_fa['liked']['custom_img'];
						
						$lass_icon_liked = '<img class="lass-front-'.$active_social.'" src="'.$liked_img.'">';
					}
					
					if ( $this->lass_archive_social_excluded($active_social) === false ) {
						$txt .= '<a class="lss-item-likes" href="'.$social_links[$active_social].'" target="_blank"><span class="lass-like-counter">'.$this->get_likes_number($post->ID).'</span>'.$lass_icon_liked.'</a>';
					}
				} else {
					if ( $this->lass_archive_social_excluded($active_social) === false ) {
						$txt .= '<a id="lass-id-'.$active_social.'" class="lss-item" href="'.$social_links[$active_social].'" target="_blank"><img class="lass-front-'.$active_social.'" src="'.$icon_img.'"></a>';
					}
				}
			}
			
			$txt .= '</div>';
			
		}
		
		if ( !empty( $this->plugin_settings ['custom_style'] ) ) {
			if ( $this->style_called_custom_css === false ) {
				$txt .= '<style>' . $this->plugin_settings ['custom_style'] . '</style>';
				$this->style_called_custom_css = true;
			}
		}
		
		return $txt;
	}
	
	public function include_fa_style() {
		$style = '';
		$style .= '<style>';
		
		foreach ( $this->plugin_settings['all_included_social'] as $active_social ) {
			$icon_color = (empty($this->plugin_settings['icons_color'][$active_social]))?'888888':$this->plugin_settings['icons_color'][$active_social];
			$icon_hover_color = (empty($this->plugin_settings['icons_hover_color'][$active_social]))?$icon_color:$this->plugin_settings['icons_hover_color'][$active_social];
			
			$style .= '
				.lass-front-'.$active_social.' {
					color: #'.$icon_color.';
				}
				.lass-front-'.$active_social.':hover {
					color: #'.$icon_hover_color.';
				}
			';
			if ( $active_social === 'likes' ) {
				$style .= '
				  .lss-item-likes i.lass-front-likes {
				    font-size: '.((int)$this->plugin_settings['font_size'] - 3).'px;
				  }
					.lass-like-counter {
						color: #'.$icon_color.';
						font-size: '.((int)$this->plugin_settings['font_size'] - 7).'px;
						top: -1px;
						vertical-align: unset;
						font-weight: 400;
					}
					.lass-like-counter:hover {
						color: #'.$icon_hover_color.';
					}
				';
			}
		}
		
		$style .= '
			.lass-front-wrapper {
				text-align: '.$this->plugin_settings['horizontal_align'].';
			}
			.lss-item i, .lss-item-likes i {
				font-size: '.$this->plugin_settings['font_size'].'px;
				-webkit-transition: all 0.2s ease-in-out;
			  -moz-transition: all 0.2s ease-in-out;
			  -ms-transition: all 0.2s ease-in-out;
			  -o-transition: all 0.2s ease-in-out;
			  transition: all 0.2s ease-in-out;
			}
		';
		
		$style .= '</style>';
		
		return $style;
	}
	
	public function include_img_style() {
		$style = '';
		$style .= '<style>';
		
		$style .= '
			.lass-front-wrapper {
				text-align: '.$this->plugin_settings['horizontal_align'].';
			}
			.lss-item img, .lass-front-likes {
			  width: '.$this->plugin_settings['custom_img_width'].' !important;
			  height: '.$this->plugin_settings['custom_img_height'].' !important;
			  object-fit: contain;
			}
		';
		
		$style .= '</style>';
		
		return $style;
	}
	/* SOCIAL MEDIA END*/
	
	
	/* HELPERS FUNCTIONS */
	
	public function lass_sanitize_key( $key ) {
		$raw_key = $key;
		$key     = strtolower( $key );
		$key     = preg_replace( '/[^a-z0-9_\-\.]/', '', $key );
		
		return apply_filters( 'sanitize_key', $key, $raw_key );
	}
	
	public function lass_archive_social_excluded( $active_social = '' ) {
		$output = false;
		if ( is_category() || is_archive() || is_post_type_archive() || is_tag() || is_tax() ) {
			if ( in_array($active_social, $this->plugin_settings['lass_social_sharing_network_exclude']) ) {
				$output = true;
			}
		} else if ( is_singular() ) {
			if ( is_home() || is_front_page() ) {
				if ( in_array($active_social, $this->plugin_settings['lass_social_sharing_network_exclude']) ) {
					$output = true;
				}
			}
		}
		
		return $output;
	}
	
	public function get_likes_number($post_id) {
		global $wpdb;
		$likesCount = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM $this->table_name WHERE post_id = %d", $post_id ));
		if ( count($likesCount) == 0 ) {
			return '';
		} else {
			return count($likesCount);
		}
	}
	
	public function check_likes_exists($post_id) {
		if ( isset( $_COOKIE['lass_'.$post_id] ) && $this->check_likes_cookies($post_id) !== false ) {
			return true;
		} else {
			global $wpdb;
			$check_likes_exist = $wpdb->get_col( $wpdb->prepare("SELECT post_id FROM $this->table_name WHERE post_id = %d AND ip_address = %s", $post_id, $this->get_ip_address() ) );
			return count($check_likes_exist);
		}
	}
	
	public function check_likes_cookies($post_id) {
		global $wpdb;
		$cookie_ch = $this->lass_sanitize_key($_COOKIE['lass_'.$post_id]);
		
		if ( isset($_COOKIE['lass_'.$post_id])  ) {
			$check_likes_exist = $wpdb->get_col( $wpdb->prepare("SELECT post_id FROM $this->table_name WHERE post_id= %d AND ip_address = %s", $post_id, $cookie_ch ) );
			return count($check_likes_exist);
		} else {
			return false;
		}
	}
	
	/*
	 * Check if font awesome already loaded
	*/
	public function is_fontawesome_loaded() {
		global $wp_styles;
		$srcs = array_map('basename', (array) wp_list_pluck($wp_styles->registered, 'src') );
		
		$font_awesome = 0;
		foreach ( $srcs as $source ) {
			if ( strpos($source, 'fontawesome') !== FALSE ||
			     strpos($source, 'font-awesome') !== FALSE) {
				$font_awesome = 1;
			}
		}
		return $font_awesome;
	}
	
	/* HELPERS FUNCTIONS END */
	
	/* PLUGIN ACTIVATION, DEACTIVATION & DELETE */
	public function plugin_activation() {
		$this->create_table();
		
		if ( get_option('lass_first_time_activation') !== '1' ) {
			foreach ( $this->dashboard->default_plugin_settings as $option ) {
				if ( empty(get_option($option['option_name'])) ) {
					if ( $option['req'] === 'true' ) {
						update_option($option['option_name'],$option['def_value']);
					}
				}
			}
			update_option('lass_first_time_activation', '1');
		}
	}
	public function plugin_deactivation() {}
	
	public static function plugin_delete() {
		self::delete_table();
		
		$dashboard = new Lass_Dashboard();
		foreach ( $dashboard->default_plugin_settings as $option ) {
			delete_option($option['option_name']);
		}
	}
	/* PLUGIN ACTIVATION, DEACTIVATION & DELETE END*/
	
}