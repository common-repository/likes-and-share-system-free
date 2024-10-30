<?php
defined( 'ABSPATH' ) OR die( 'No script kiddies, please!' );
wp_enqueue_media();

// Get all post types
$args = array(
	'public' => true,
);
$lass_post_types = get_post_types($args, 'objects');
if ( isset( $lass_post_types['attachment'] )) unset($lass_post_types['attachment']);
//if ( isset( $lass_post_types['page'] )) unset($lass_post_types['page']);

$lass_all_post_Types = array();
foreach ( $lass_post_types as $pt ) {
	$lass_all_post_Types[] = array('slug'=>$pt->name, 'name'=>$pt->label);
}

$all_social_network = $this->clean_value_esc_attr((array)get_option('lass_social_sharing_network')); // return array list
$lass_icons_uses = $this->clean_value_esc_attr(get_option('lass_icons_uses')); // return fontawesome or custom_images
$lass_fa_icons = $this->clean_value_esc_attr((array)get_option('lass_fa_icons')); // return array lists
$lass_img_icons = $this->clean_value_esc_url((array)get_option('lass_img_icons')); // return array lists
$lass_fa_colors = $this->clean_value_esc_attr((array)get_option('lass_fa_colors')); // return array lists
$lass_fa_colors_hover = $this->clean_value_esc_attr((array)get_option('lass_fa_colors_hover')); // return array lists
$lass_selected_post_types = $this->clean_value_esc_attr((array)get_option('lass_selected_post_types')); // return array lists
$lass_position = $this->clean_value_esc_attr(get_option('lass_position')); // return top or bottom
$lass_like_liked = $this->clean_value_esc_attr(get_option('lass_like_liked')); // return string value
$lass_like_liked_img = $this->clean_value_esc_url(get_option('lass_like_liked_img')); // return string value
$lass_align = $this->clean_value_esc_attr(get_option('lass_align')); // return string value
$lass_font_size = $this->clean_value_esc_attr(get_option('lass_font_size')); // return string value
$lass_custom_style = $this->clean_value_esc_attr(get_option('lass_custom_style'));
$lass_show_on_single = $this->clean_value_esc_attr(get_option('lass_show_on_single'));
$lass_show_on_archive = $this->clean_value_esc_attr(get_option('lass_show_on_archive'));
$lass_show_on_exclude_home = $this->clean_value_esc_attr(get_option('lass_show_on_exclude_home'));

$lass_custom_img_width = $this->clean_value_esc_attr(get_option('lass_custom_img_width'));
$lass_custom_img_height = $this->clean_value_esc_attr(get_option('lass_custom_img_height'));

$archive_excluded = $this->clean_value_esc_attr((array)get_option('lass_social_sharing_network_exclude')); // return array list

?>
<div class="lass_options_wrapper">
  <h2 class="options-title"><?php _e('Likes and social share settings','lass'); ?></h2>
  <form method="post" action="options.php">
		<?php settings_fields( 'lass-general-options' ); ?>
<?php //do_settings_sections( '' ); ?>
    <table class="form-table-lass">
      <tr class="lass_select_social_row">
        <td><?php _e('Select social networks:','lass'); ?></td>
        <td>
            <?php
              foreach ( $this->all_social_sharing as $social_network ) {
                $checked = '';
                if ( !empty($all_social_network) && in_array($social_network['slug'], $all_social_network) )
                  $checked = 'checked';
                else
                  $checked = '';
            ?>
            <span class="lass-span-wrapp-form">
              <label for="lass_id-<?php echo $social_network['slug']; ?>">
              <input class="lass_ssn_input" type="checkbox" name="lass_social_sharing_network[]" id="lass_id-<?php echo $social_network['slug']; ?>" value="<?php echo $social_network['slug']; ?>" <?php echo $checked; ?>> <?php echo $social_network['name']; ?></label>
            </span>
            <?php
              }
            ?>
        </td>
      </tr>

      <tr>
        <td><?php _e('Build with:','lass'); ?></td>
        <td>
           <span class="lass-span-wrapp-form">
             <label for="lass_use_font_awesome">
               <input type="radio" name="lass_icons_uses" value="<?php echo esc_attr('fontawesome'); ?>" id="lass_use_font_awesome" <?php echo ($lass_icons_uses === 'fontawesome')?'checked':''; ?>> <?php _e('Font Awesome', 'lass'); ?>
             </label>
           </span>

          <span class="lass-span-wrapp-form">
             <label for="lass_use_custom_images">
               <input type="radio" name="lass_icons_uses" value="<?php echo esc_attr('custom_images'); ?>" id="lass_use_custom_images" <?php echo ($lass_icons_uses === 'custom_images')?'checked':''; ?>> <?php _e('Custom images', 'lass'); ?>
             </label>
           </span>
        </td>
      </tr>

      <tr>
        <td><?php _e('Customize your icons:','lass'); ?></td>
        <td>
          <div id="lass_select_social_v">
            <h4 class="lass-title-h4 l-t-h4-style-2">
		          <?php _e("If you leave these fields blank, default values will be used, so don't worry.", 'lass'); ?>
            </h4>
	          <?php
            $lass_fontawesome_active = '';
            $lass_custom_images_active = '';
	          if ( $lass_icons_uses === 'fontawesome' ) {
		          $lass_fontawesome_active = 'display: inline-block;';
		          $lass_custom_images_active = 'display: none;';
            } else if ( $lass_icons_uses === 'custom_images' ) {
		          $lass_custom_images_active = 'display: inline-block;';
		          $lass_fontawesome_active = 'display: none;';
            }


	            echo '<div class="lass_fontawesome_icon_active" style="'.$lass_fontawesome_active.'">';
		          foreach ( $this->all_social_sharing as $social_network ) {
			          if ( !isset( $lass_fa_icons[ $social_network['slug'] ] ) ) {
				          $lass_fa_icons[$social_network['slug']] = '';
			          }
			          if ( !in_array( $social_network['slug'], $all_social_network ) ) {
				          $lass_class = 'lass_hidden_option';
			          } else {
				          $lass_class = '';
			          }
			          ?>
                <p class="lass-span-wrapp-form show_option_<?php echo $social_network['slug']; ?> <?php echo $lass_class; ?>">
                  <label for="lass-fa-<?php echo $social_network['slug']; ?>">
                    <input class="lass_icons_width_input" type="text" name="lass_fa_icons[<?php echo $social_network['slug']; ?>]" id="lass-fa-<?php echo $social_network['slug']; ?>" value="<?php echo $lass_fa_icons[ $social_network['slug'] ]; ?>"> <?php echo $social_network['name']; ?></label>
                </p>
			          <?php
		          }
		          ?>
              <p class="lass_fa_helper lass_helper_<?php echo $lass_icons_uses; ?>" ><?php _e('Here you can find all Font Awesome icons','lass'); ?> <a href="https://fontawesome.com/v4.7.0/icons/" target="_blank"><?php _e('Font Awesome icons', 'lass'); ?></a> <br>
                <span><?php _e('Instructions for adding a Font Awesome class name:', 'lass'); ?> <a target="_blank" href="<?php echo LASS_ASSETS_ADMIN; ?>/img/fontawesome_explanation.png"><?php _e('Instructions', 'lass'); ?></a></span>
              </p>
            <p class="lass_fa_helper"><strong><?php _e("You don't need to worry about including Font Awesome.<br>If you don't have it included, we will do it automatically for you, if you already had it included we will not include it again.", "lass"); ?></strong></p>
		          <?php
              echo '</div>';// end of the class lass_fontawesome_icon_active

		          echo '<div class="lass_custom_images_icon_active" style="'.$lass_custom_images_active.'">';
		          foreach ( $this->all_social_sharing as $social_network ) {
			          if ( !isset( $lass_img_icons[ $social_network['slug'] ] ) ) {
				          $lass_img_icons[$social_network['slug']] = '';
			          }

			          if ( ! in_array( $social_network['slug'], $all_social_network ) ) {
				          $lass_class = 'lass_hidden_option';
			          } else {
				          $lass_class = '';
			          }
			          ?>
                <p class="lass-span-wrapp-form show_option_<?php echo $social_network['slug']; ?> <?php echo $lass_class; ?>">
                  <label for="lass-fa-<?php echo $social_network['slug']; ?>">
                    <input class="lass_icons_width_input" type="text" name="lass_img_icons[<?php echo $social_network['slug']; ?>]" id="lass-img-<?php echo $social_network['slug']; ?>" value="<?php echo $lass_img_icons[ $social_network['slug'] ]; ?>">
                    <span class="lass-selected-img">
                      <?php
                        if ( !empty($lass_img_icons[ $social_network['slug'] ]) ) {
                          ?>
                          <img src="<?php echo $lass_img_icons[ $social_network['slug'] ]; ?>">
                      <?php
                        }
                      ?>
                    </span>
                    <button class="lass_set_custom_images button"><?php _e('Select file', 'lass'); ?></button> <?php echo $social_network['name']; ?></label>
                </p>
			          <?php
		          }
		          ?>
		          <div class="lass_defined_icons">
                <h4 class="lass-title-h4">
                  <?php _e('To choose from defined social images bellow, you just need to click on the image you want.', 'lass'); ?></h4>
                <?php
                $lass_icons_dir = new DirectoryIterator(LASS_ICONS_PATH);
                foreach ($lass_icons_dir as $fileinfo) {
	                if ($fileinfo->isDir() && !$fileinfo->isDot()) {
		                $style = $fileinfo->getFilename();
		                ?>
                    <div class="lass_custom_icons_<?php echo $style; ?>">
			                <?php
			                foreach ( $this->all_social_sharing as $social_network ) {
			                  if ( !isset( $lass_img_icons[ $social_network['slug'] ] ) ) {
                          $lass_img_icons[$social_network['slug']] = '';
                        }
				                if ( ! in_array( $social_network['slug'], $all_social_network ) ) {
					                $lass_class = ' lass_hidden_option';
				                } else {
					                $lass_class = '';
				                }

				                $selected_class = '';
				                $check_img      = LASS_ASSETS_FRONT . '/lass-icons/' . $style . '/' . $social_network['slug'] . '.png';
				                if ( $lass_img_icons[ $social_network['slug'] ] === $check_img ) {
					                $selected_class = ' lass-social-img-selected';
				                }
				                ?>
                        <img class="lass-custom-icons show_option_<?php echo $social_network['slug'] . $lass_class . $selected_class; ?>" data-input-id="lass-img-<?php echo $social_network['slug']; ?>" data-name-social="<?php echo $social_network['slug']; ?>" src="<?php echo $check_img; ?>">
				                <?php
			                }
			                ?>
                    </div>

		                <?php
	                }
                }
                ?>


                <div class="lass-custom-img-size">
                  <h4 class="lass-title-h4 lass-h-h-4"><?php _e('Set image size', 'lass'); ?></h4>

                  <p>
                    <?php _e('Width:', 'lass') ?>&nbsp; <input type="text" name="lass_custom_img_width" class="lass_icons_width_input_s" value="<?php echo $lass_custom_img_width; ?>">
                    <span class="lass_info_input"><?php _e('E.g. 20px, 30px, 40px, auto', 'lass'); ?></span>
                  </p>
                  <p>
                    <?php _e('Height:', 'lass') ?> <input type="text" name="lass_custom_img_height" class="lass_icons_width_input_s" value="<?php echo $lass_custom_img_height; ?>">
                    <span class="lass_info_input"><?php _e('E.g. 20px, 30px, 40px, auto', 'lass'); ?></span>
                  </p>

                </div>

              </div>
		          <?php
	          echo '</div>';// end of the class lass_custom_images_icon_active
	          ?>
          </div>
        </td>
      </tr>

	    <?php
	    if ( !empty($all_social_network) && in_array('likes', $all_social_network)) {
		    ?>
        <tr class="lass_like_liked show_option_likes">
          <td><?php _e('Select style for like icon when is liked.','lass'); ?></td>
          <td>
				    <?php
				    //$lass_custom_images_active
				    //$lass_fontawesome_active


				    echo '<div class="lass_fontawesome_icon_active" style="'.$lass_fontawesome_active.'">';
				    ?>
            <label for="lass-like-liked">
              <input class="lass_icons_width_input" type="text" name="lass_like_liked" id="lass-like-liked" value="<?php echo $lass_like_liked; ?>"> <a target="_blank" href="<?php echo LASS_ASSETS_ADMIN; ?>/img/likes_liked_explanation.png"><?php _e('Explanation','lass'); ?></a></label>

            <p class="lass_fa_helper lass_helper_<?php echo $lass_icons_uses; ?>" ><?php _e('This icon will be showed when post is liked.<br>Here you can find all Font Awesome icons','lass'); ?> <a href="https://fontawesome.com/v4.7.0/icons/" target="_blank"><?php _e('Font Awesome icons', 'lass'); ?></a> <br>
              <span><?php _e('Instructions for adding a Font Awesome class name:', 'lass'); ?> <a target="_blank" href="<?php echo LASS_ASSETS_ADMIN; ?>/img/fontawesome_explanation.png"><?php _e('Instructions', 'lass'); ?></a></span>
            </p>
				    <?php
				    echo '</div>';

				    echo '<div class="lass_custom_images_icon_active" style="'.$lass_custom_images_active.'">';
				    ?>
            <label for="lass-like-liked">
              <input class="lass_icons_width_input" type="text" name="lass_like_liked_img" id="lass-like-liked-img" value="<?php echo $lass_like_liked_img; ?>">
              <span class="lass-selected-img">
                  <?php
                  if ( !empty($lass_like_liked_img) ) {
                    ?>
                    <img src="<?php echo $lass_like_liked_img; ?>">
                    <?php
                  }
                  ?>
                </span>
              <button class="lass_set_custom_images button"><?php _e('Select file', 'lass'); ?></button> <a target="_blank" href="<?php echo LASS_ASSETS_ADMIN; ?>/img/likes_liked_explanation.png"><?php _e('Explanation','lass'); ?></a></label>

            <p class="lass_fa_helper lass_helper_<?php echo $lass_icons_uses; ?>" ><?php _e('This image will be showed when post is liked.','lass'); ?></p>

            <div class="lass_defined_icons">
              <?php
              foreach ($lass_icons_dir as $fileinfo) {
	              if ( $fileinfo->isDir() && ! $fileinfo->isDot() ) {
		              $style = $fileinfo->getFilename();


		              $selected_class = '';
		              $check_img_liked = LASS_ASSETS_FRONT . '/lass-icons/' . $style . '/liked.png';
		              if ( $lass_like_liked_img === $check_img_liked ) {
			              $selected_class = ' lass-social-img-selected';
		              }
		              ?>
                  <div class="lass_custom_icons_style_<?php echo $style; ?>">
                    <img class="lass-custom-icons show_option_liked<?php echo $selected_class; ?>" data-input-id="lass-like-liked-img" data-name-social="liked" src="<?php echo $check_img_liked; ?>">
                  </div>
		              <?php
	              }
              }
              ?>
            </div>

				    <?php
				    echo '</div>';
				    ?>


          </td>
        </tr>
		    <?php
	    }
	    ?>

      <?php
        $table_row_fa_colors = '';
        if ( $lass_icons_uses === 'fontawesome' ) {
	        $table_row_fa_colors = 'display:table-row;';
        } else if ( $lass_icons_uses === 'custom_images' ) {
	        $table_row_fa_colors = 'display:none;';
        }
      ?>
      <tr class="lass_custom_images_row lass-no-border-line" style="<?php echo $table_row_fa_colors; ?>">
        <td><?php _e('Font size:','lass'); ?></td>
        <td><label for="lass_font_size"><input id="lass_font_size" class="lass_icons_width_input" type="text" name="lass_font_size" value="<?php echo $lass_font_size; ?>"> px</label></td>
      </tr>

      <tr class="lass_custom_images_row lass-no-border-line" style="<?php echo $table_row_fa_colors; ?>">
        <td><?php _e('Select a color:','lass'); ?></td>
        <td>
			    <?php
			    foreach ( $this->all_social_sharing as $social_network ) {
				    if ( !isset( $lass_fa_colors[ $social_network['slug'] ] ) ) {
					    $lass_fa_colors[$social_network['slug']] = '';
				    }
				    if ( !in_array( $social_network['slug'], $all_social_network ) ) {
					    $lass_class = 'lass_hidden_option';
				    } else {
					    $lass_class = '';
				    }
					    ?>
              <p class="lass-span-wrapp-form show_option_<?php echo $social_network['slug']; ?> <?php echo $lass_class; ?>">
              <label for="lass-color-<?php echo $social_network['slug']; ?>">
              #<input class="lass_get_color" type="text" name="lass_fa_colors[<?php echo $social_network['slug']; ?>]" id="lass-color-<?php echo $social_network['slug']; ?>" value="<?php echo $lass_fa_colors[ $social_network['slug'] ]; ?>"> <span class="lass_get_color_preview" style="background: #<?php echo $lass_fa_colors[ $social_network['slug'] ] ?>"></span> <?php echo $social_network['name']; ?></label>
            </p>
          <?php
			    }
			    ?>
        </td>
      </tr>

      <tr class="lass_custom_images_row lass-sep-col" style="<?php echo $table_row_fa_colors; ?>">
        <td><?php _e('On hover:','lass'); ?></td>
        <td>
			    <?php
			    foreach ( $this->all_social_sharing as $social_network ) {
				    if ( !isset( $lass_fa_colors_hover[ $social_network['slug'] ] ) ) {
					    $lass_fa_colors_hover[$social_network['slug']] = '';
				    }

            if ( !in_array( $social_network['slug'], $all_social_network ) ) {
              $lass_class = 'lass_hidden_option';
            } else {
	            $lass_class = '';
            }
					    ?>
              <p class="lass-span-wrapp-form show_option_<?php echo $social_network['slug']; ?> <?php echo $lass_class; ?>">
              <label for="lass-color-hover-<?php echo $social_network['slug']; ?>">
              #<input class="lass_get_color" type="text" name="lass_fa_colors_hover[<?php echo $social_network['slug']; ?>]" id="lass-color-hover-<?php echo $social_network['slug']; ?>" value="<?php echo $lass_fa_colors_hover[ $social_network['slug'] ]; ?>"> <span class="lass_get_color_preview" style="background: #<?php echo $lass_fa_colors_hover[ $social_network['slug'] ] ?>"></span> <?php echo $social_network['name']; ?></label>
            </p>
					   <?php
			    }
			    ?>
        </td>
      </tr>

      <tr>
        <td><?php _e('Select post types', 'lass'); ?></td>
        <td>
		      <?php
		      foreach ( $lass_all_post_Types as $post_type ) {
			      $checked = '';
			      if ( !empty($lass_selected_post_types) && in_array($post_type['slug'], $lass_selected_post_types) )
				      $checked = 'checked';
			      else
				      $checked = '';
			      ?>
            <span class="lass-span-wrapp-form">
              <label for="lass_post_type_id-<?php echo $post_type['slug']; ?>">
              <input class="lass_ssn_input" type="checkbox" name="lass_selected_post_types[]" id="lass_post_type_id-<?php echo $post_type['slug']; ?>" value="<?php echo $post_type['slug']; ?>" <?php echo $checked; ?>> <?php echo $post_type['name']; ?></label>
            </span>
			      <?php
		      }
		      ?>
        </td>
      </tr>

      <tr>
        <td><?php _e('Position:','lass'); ?></td>
        <td>
           <span class="lass-span-wrapp-form">
             <label for="lass_position_top">
               <input type="radio" name="lass_position" value="<?php echo esc_attr('top'); ?>" id="lass_position_top" <?php echo ($lass_position === 'top')?'checked':''; ?>> <?php _e('Before content', 'lass'); ?>
             </label>
           </span>

          <span class="lass-span-wrapp-form">
             <label for="lass_position_bottom">
               <input type="radio" name="lass_position" value="<?php echo esc_attr('bottom'); ?>" id="lass_position_bottom" <?php echo ($lass_position === 'bottom')?'checked':''; ?>> <?php _e('After content', 'lass'); ?>
             </label>
           </span>
        </td>
      </tr>

      <tr>
        <td><?php _e('Horizontal align:','lass'); ?></td>
        <td>
           <span class="lass-span-wrapp-form">
             <label for="lass_align_left">
               <input type="radio" name="lass_align" value="<?php echo esc_attr('left'); ?>" id="lass_align_left" <?php echo ($lass_align === 'left')?'checked':''; ?>> <?php _e('Left','lass'); ?>
             </label>
           </span>

          <span class="lass-span-wrapp-form">
             <label for="lass_align_center">
               <input type="radio" name="lass_align" value="<?php echo esc_attr('center'); ?>" id="lass_align_center" <?php echo ($lass_align === 'center')?'checked':''; ?>> <?php _e('Center','lass'); ?>
             </label>
           </span>
          <span class="lass-span-wrapp-form">
             <label for="lass_align_right">
               <input type="radio" name="lass_align" value="<?php echo esc_attr('right'); ?>" id="lass_align_right" <?php echo ($lass_align === 'right')?'checked':''; ?>> <?php _e('Right','lass'); ?>
             </label>
           </span>
        </td>
      </tr>

      <tr>
        <td><?php _e('Show on:','lass'); ?>
        <br>
          <p><em><?php _e('Show on archive/category page is possible only if you use the <strong>the_excerpt()</strong> functon in your theme or custom template. Also, you are able to add the shortcode in every loop in your template.','lass'); ?></em></p>
          <p><?php _e('On archive:', 'lass'); ?>
            <br>
            <?php echo esc_html("<?php echo do_shortcode('[lass_system_archive]'); ?>"); ?></p>

          <p><?php _e('On single page:', 'lass'); ?>
            <br>
            <?php echo esc_html("<?php echo do_shortcode('[lass_system]'); ?>"); ?></p>

        </td>
        <td>
          <span class="lass-span-wrapp-form">
              <label for="lass_id_show_on_single">
              <input class="lass_ssn_input" type="checkbox" name="lass_show_on_single" id="lass_id_show_on_single" value="<?php echo esc_attr('show'); ?>" <?php echo ($lass_show_on_single === 'show') ? 'checked':''; ?>> <?php _e('Single post','lass'); ?></label>
            </span>

          <span class="lass-span-wrapp-form">
              <label for="lass_id_show_on_category">
              <input class="lass_ssn_input" type="checkbox" name="lass_show_on_archive" id="lass_id_show_on_category" value="<?php echo esc_attr('show'); ?>" <?php echo ($lass_show_on_archive === 'show') ? 'checked':''; ?>> <?php _e('Archive/category page.</em>','lass'); ?></label>
            </span>

          <div id="show_option_11_2" class="lass-span-wrapp-form lass-the-archive-set-opt" style="display: <?php echo $lass_show_on_archive === 'show'?'table-row':'none'; ?>">
              <label for="lass_id_show_on_exclude_home">
              <input class="lass_ssn_input" type="checkbox" name="lass_show_on_exclude_home" id="lass_id_show_on_exclude_home" value="<?php echo esc_attr('exclude'); ?>" <?php echo ($lass_show_on_exclude_home === 'exclude') ? 'checked':''; ?>> <?php _e('Exclude from homepage','lass'); ?></label>
            </div>
        </td>
      </tr>

      <!-- exclude from archive -->
      <tr class="lass-the-archive-set-row" style="display: <?php echo $lass_show_on_archive === 'show'?'table-row':'none'; ?>">
        <td><?php _e('Here you can exclude some social share buttons from archive/category page:','lass'); ?>
          <p><em><?php _e('Default category/archive pages:', 'lass'); ?><br>is_category(), is_archive(), is_post_type_archive(), is_tag(), is_tax()</em></p>

          <?php
           if ( Lass_WoocommerceSettings::CheckWooCommerceExist() === true ) {
             ?>
          <p><em><?php _e('WooCommerce:', 'lass'); ?><br>is_product_category(), is_shop(), is_product_tag()</em></p>
          <?php
           }
          ?>

          <p><em><strong><?php _e('The social share buttons you select here will not be displayed on category/archive pages.','lass'); ?></strong></em></p>
        </td>

        <td>
			    <?php
			    foreach ( $this->all_social_sharing as $social_network ) {
				    $checked = '';
				    if ( !empty($archive_excluded) && in_array($social_network['slug'], $archive_excluded) )
					    $checked = 'checked';
				    else
					    $checked = '';
				    ?>
            <span class="lass-span-wrapp-form">
              <label for="lass_id-<?php echo $social_network['slug']; ?>-excluded">
              <input class="lass_ssn_input" type="checkbox" name="lass_social_sharing_network_exclude[]" id="lass_id-<?php echo $social_network['slug']; ?>-excluded" value="<?php echo $social_network['slug']; ?>" <?php echo $checked; ?>> <?php echo $social_network['name']; ?></label>
            </span>
				    <?php
			    }
			    ?>
        </td>
      </tr>
      <!-- exclude from archive END -->

      <tr>
        <td><?php _e('Custom CSS','lass'); ?></td>
        <td><textarea class="lass_custom_style" name="lass_custom_style"><?php echo $lass_custom_style; ?></textarea><br>
          <p class="lass_code_info">
            <span><strong>Default classes:</strong></span><br>
            Main wrapper: <code>.likes-share-system {}</code><br>
            Like item: <code>a.lss-item-likes {}</code><br>
            Share item: <code>a.lss-item {}</code><br>
            Counter text: <code>.lass-like-counter {}</code><br>
            Custom image size: <code>.lss-item img, .lass-front-likes {}</code><br>
            Font awesome size: <code>i.lass-front-likes, a.lss-item i {}</code><br>
          </p>

          <p><em><?php _e('Where is set two classes, one is for like system second is for share buttons.', 'lass'); ?></em></p>
        </td>
      </tr>
    </table>
	  <?php submit_button(); ?>
  </form>
</div>