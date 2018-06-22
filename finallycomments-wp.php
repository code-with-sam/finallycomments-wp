<?php
/**
 * @package  FinallyCommentsWP
 */
/*
Plugin Name: Finally Comments WP
Plugin URI: https://finallycomments.com/plugin
Description: Comments Plugin for working with the STEEM blockchain
Version: 0.1.0
Author: Sam Billingham
Author URI: https://sambillingham.com
License: GPLv2 or later
Text Domain: finallycomments-wp
*/

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

function finallycomments_settings_menu() {
	add_options_page(
		'Finally Comments Settings',
		'Finally Comments',
		'manage_options',
		'finallycomments_settings.php',
		'finallycomments_settings_setup'
	);
}
add_action( 'admin_menu', 'finallycomments_settings_menu' );


function finally_extra_content($content) {
	if( is_singular() && is_main_query() ) {
		$finallycomments = '<p>FINALLY COMMENTS GOES HERE</p>';
		$content .= $finallycomments;
	}
	return $content;
}
add_filter('the_content', 'finally_extra_content');

add_action( 'load-post.php', 'finallycomments_meta_boxes_setup' );
add_action( 'load-post-new.php', 'finallycomments_meta_boxes_setup' );

/* Meta box setup function. */
function finallycomments_meta_boxes_setup() {
  add_action( 'add_meta_boxes', 'finallycomments_add_post_meta_boxes' );
  add_action( 'save_post', 'finallycomments_save_post_class_meta', 10, 2 );
}

function finallycomments_add_post_meta_boxes() {
  add_meta_box(
    'finallycomments-data',      // Unique ID
    esc_html__( 'Finally Commnets', 'example' ),    // Title
    'finallycomments_meta_box',   // Callback function
    'post',         // Admin page (or post type)
    'normal',         // Context
    'default'         // Priority
  );
}

function finallycomments_meta_box( $post ) { ?>

  <?php wp_nonce_field( basename( __FILE__ ), 'finallycomments_nonce' ); ?>

	<p>
		<label for="finallycomments-data"><?php _e( "Add a custom Thread using Finally Comment." ); ?></label>
		<br>
		<input type="checkbox" id="custom" name="custom-thread"
				value="custom" />
		 <label for="custom">include new custom thread</label>
	</p>
	<i>*you must </i>

  <p>
    <label for="finallycomments-data"><?php _e( "Add steemit link." ); ?></label>
    <br>
    <input class="widefat" type="text" name="finallycomments-data" id="finallycomments-data" value="<?php echo esc_attr( get_post_meta( $post->ID, 'finallycomments', true ) ); ?>">
  </p>
<?php }


/* Save the meta box's post metadata. */
function finallycomments_save_post_class_meta( $post_id, $post ) {

  /* Verify the nonce before proceeding. */
  if ( !isset( $_POST['finallycomments_nonce'] ) || !wp_verify_nonce( $_POST['finallycomments_nonce'], basename( __FILE__ ) ) )
    return $post_id;

  /* Get the post type object. */
  $post_type = get_post_type_object( $post->post_type );

  /* Check if the current user has permission to edit the post. */
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;

  /* Get the posted data */
  $new_meta_value = ( isset( $_POST['finallycomments-data'] ) ?  $_POST['finallycomments-data'] : '' );

  /* Get the meta key. */
  $meta_key = 'finallycomments';

  /* Get the meta value of the custom field key. */
  $meta_value = get_post_meta( $post_id, $meta_key, true );

  /* If a new meta value was added and there was no previous value, add it. */
  if ( $new_meta_value && '' == $meta_value )
    add_post_meta( $post_id, $meta_key, $new_meta_value, true );

  /* If the new meta value does not match the old value, update it. */
  elseif ( $new_meta_value && $new_meta_value != $meta_value )
    update_post_meta( $post_id, $meta_key, $new_meta_value );

  /* If there is no new meta value but an old value exists, delete it. */
  elseif ( '' == $new_meta_value && $meta_value )
    delete_post_meta( $post_id, $meta_key, $meta_value );
}
