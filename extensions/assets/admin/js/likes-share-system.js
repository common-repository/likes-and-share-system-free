jQuery(document).ready(function() {
  var $ = jQuery;

  /* Color picker */
  $('.lass_get_color').ColorPicker({
    onSubmit: function(hsb, hex, rgb, el) {
      $(el).val(hex);
      $(el).ColorPickerHide();
      $(el).siblings('.lass_get_color_preview').css('background','#'+hex);
    },
    onBeforeShow: function () {
      $(this).ColorPickerSetColor(this.value);
    }
  })
    .bind('keyup', function(){
      $(this).ColorPickerSetColor(this.value);
      $(this).siblings('.lass_get_color_preview').css('background','#'+this.value);
    });
  /* Color picker end */

  /* Plugin options script */
  if ($('.lass_set_custom_images').length > 0) {
    if ( typeof wp !== 'undefined' && wp.media && wp.media.editor) {
      $(document).on('click', '.lass_set_custom_images', function(e) {
        e.preventDefault();
        var button = $(this);
        var input = button.siblings('.lass_icons_width_input');
        var selectedImg = button.siblings('.lass-selected-img');
        wp.media.editor.send.attachment = function(props, attachment) {
          input.val(attachment.url);
          selectedImg.html('<img src="'+attachment.url+'">');
        };
        wp.media.editor.open(button);
        return false;
      });
    }
  }

  /*
  * On change -> I want to use option { Font Awesome - Custom images }
  */
  var lass_icons_uses = $('input[name="lass_icons_uses"]');
  lass_icons_uses.change(function (e) {
    if ( e.target.value === 'custom_images' ) {
      $('.lass_custom_images_row').css({'display':'none'});
      $('.lass_custom_images_icon_active').css({'display':'block'});
      $('.lass_fontawesome_icon_active').css({'display':'none'});
    } else if ( e.target.value === 'fontawesome' ) {
      $('.lass_custom_images_row').css({'display':'table-row'});
      $('.lass_fontawesome_icon_active').css({'display':'block'});
      $('.lass_custom_images_icon_active').css({'display':'none'});
    }
  });

  /*
  * On WooCommerce archive options change
  */
  var lass_woo_archive = $('input[name="lass_woo_archive"]');
  lass_woo_archive.change(function (e) {
    if ( e.target.value === 'yes' ) {
      $('.lass-woo-archive-position').css({'display': 'table-row'});
    } else if ( e.target.value === 'no' ) {
      $('.lass-woo-archive-position').css({'display': 'none'});
    }
  });

  /*
  * On change -> Select social networks
  */
  $('.lass_ssn_input').change(function (e) {
    $('.show_option_'+$(this).val()).toggle(300);
  });

  /*
  * On change -> Archive category (the excerpt)
  */

  var lass_archive_set = $('#lass_id_show_on_category');
  lass_archive_set.change(function (e) {
    if ( $(this).is(':checked') === true ) {
      $('.lass-the-archive-set-opt').css({'display': 'block'});
      $('.lass-the-archive-set-row').css({'display': 'table-row'});
    } else {
      $('.lass-the-archive-set-opt').css({'display': 'none'});
      $('.lass-the-archive-set-row').css({'display': 'none'});
    }
  });



  /*
  * On click on the custom images - filled the social field
  */
  $(document).on('click', '.lass-custom-icons', function () {
    var inputID = $(this).attr('data-input-id');
    var dataNameSocial = $(this).attr('data-name-social');

    $('img[data-name-social="'+dataNameSocial+'"]').removeClass('lass-social-img-selected');
    $(this).addClass('lass-social-img-selected');

    $('#'+inputID).val( $(this).attr('src') );
    $('#'+inputID).siblings( '.lass-selected-img' ).html('<img src="'+$(this).attr('src')+'">');
  });


  /* Plugin options script END */

});