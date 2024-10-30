jQuery(document).ready(function() {
  var $ = jQuery;

  /** Likes AJAX */
  $('.lss-item-likes').click(function (e) {
    e.preventDefault();
    var element = $(this);
    var counter_text = element.children('.lass-like-counter');
    var post_id_var = $(this).parent().attr('data-post-id');
    var post_name_var = $(this).parent().attr('data-post-name');

    jQuery.ajax({
      type: 'post',
      dataType: 'json',
      data: {action:'likesresponse',post_id:post_id_var,post_name:post_name_var},
      url: wpp.ajax_url,
      beforeSend: function () {
        element.attr('style', 'opacity:0.3;cursor:not-allowed;pointer-events:none;');
      },
      success: function( r ){
        if ( r.status === 'LIKE_INSERTED' ) {
          counter_text.text(r.likes_count);

          if ( r.icons_uses === 'fontawesome' ) {
            element.children('i').removeClass(r.current_img);
            element.children('i').addClass(r.like_liked_icon);
          } else if ( r.icons_uses === 'custom_images' ) {
            element.children('.lass-front-likes').attr('src',r.like_liked_icon);
          }

        } else if ( r.status === 'LIKE_DELETED' ) {
          counter_text.text(r.likes_count);

          if ( r.icons_uses === 'fontawesome' ) {
            element.children('i').removeClass(r.like_liked_icon);
            element.children('i').addClass(r.current_img);
          } else if ( r.icons_uses === 'custom_images' ) {
            element.children('.lass-front-likes').attr('src',r.current_img);
          }
        }
      },
      complete: function () {
        element.removeAttr('style');
      },
      error: function (jqXHR, exception) {
        console.log(jqXHR, exception);
      }
    });
  });
  /** Likes AJAX END */

  /** Open share in new window */
  $('.likes-share-system .lss-item').click(function (e) {
    e.preventDefault();
    var url = $(this).attr('href');
    window.open(url, '', 'width=500,height=500');
  });

  /** Open share in new window END */

});