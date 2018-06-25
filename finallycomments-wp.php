<?php
/**
 * @package  FinallyCommentsWP
 */
/*
Plugin Name: Finally Comments WP
Plugin URI: https://finallycomments.com
Description: Comments Plugin for working with the STEEM blockchain
Version: 0.1.0
Author: Sam Billingham
Author URI: https://sambillingham.com
License: GPLv2 or later
Text Domain: finallycomments-wp
*/

/* Don't allow direct execution of code */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/* Create a settings menu item for plugin */
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

/* Run custom code in use for the settings menu */
function finallycomments_settings_setup() {
	// Currently no custom settings needed
	echo '<h2>Thank you for using FinallyComments.</h2><p>There are currently no settings for Finally Comments, all actions can be controlled when creating/editing a post.<p>';
}

/* Add Finally Comments code to the end of post content if in use */
function finally_extra_content($content) {
	$finallyData = get_post_meta( get_the_ID(), 'finallycomments', true );

	if( is_singular() && is_main_query() ) {
		$finally_thread_type = $finallyData['thread'];

		if ($finally_thread_type == 'custom'){
			$finally_username = $finallyData['username'];
			$finallycomments = finally_generate_custom_thread($finally_username);
			$content .= $finallycomments;
		}

		if ($finally_thread_type == 'steem'){
			$finally_steemlink = $finallyData['link'];
			$finallycomments = finally_generate_steem_thread($finally_steemlink);
			$content .= $finallycomments;
		}

	}
	return $finally_thread_type . $content;
}
add_filter('the_content', 'finally_extra_content');

/* Generate correct data attributes and html coce to be used for a Steem based Finally embed */
function finally_generate_steem_thread($steemlink) {
	$slug = get_post_field( 'post_name', get_post() );
	return '<section class="finally-comments"
    data-id="' . $steemlink . '"
    data-reputation="true"
    data-values="true"
    data-profile="true"
    data-generated="false">
</section>';
}

/* Generate correct data attributes and html coce to be used for a Custom Finally embed */
function finally_generate_custom_thread($username) {
	$slug = get_post_field( 'post_name', get_post() );
	return '<section class="finally-comments"  data-id="https://finallycomments.com/api/thread/'  .$username . '/' . $slug .'"
  data-reputation="true"
  data-values="true"
  data-profile="true"
  data-generated="true"
  data-api="true"></section>';
}

/* Load the Finally library on with enqueue */
function finally_enqueue_script() {
    wp_enqueue_script( 'finally_script', plugin_dir_url( __FILE__ ) . 'js/finally.min.js',  array(), false, true );
}
add_action('wp_enqueue_scripts', 'finally_enqueue_script');


/* Meta box setup */
add_action( 'load-post.php', 'finallycomments_meta_boxes_setup' );
add_action( 'load-post-new.php', 'finallycomments_meta_boxes_setup' );

function finallycomments_meta_boxes_setup() {
  add_action( 'add_meta_boxes', 'finallycomments_add_post_meta_boxes' );
  add_action( 'save_post', 'finallycomments_save_post_meta', 10, 2 );
}

/* Meta boxes for use on post page  */
function finallycomments_add_post_meta_boxes() {
  add_meta_box(
    'finallycomments-data',      // Unique ID
    esc_html__( 'Finally Comments', 'example' ),    // Title
    'finallycomments_meta_box',   // Callback function
    'post',         // Admin page (or post type)
    'normal',         // Context
    'default'         // Priority
  );
}

/* Metabox html and form fields for post page */
function finallycomments_meta_box( $post ) {
 	wp_nonce_field( basename( __FILE__ ), 'finallycomments_nonce' );
	$finallyData = get_post_meta( $post->ID, 'finallycomments', true ); ?>

	<fieldset>
	    <legend>Select Thread Type</legend>
			<div>
					<input type="radio" id="none" name="finallycomments[thread]" value="none"
					<?php echo ( isset( $finallyData['thread'] ) ?  '' : 'checked' ); ?>
					<?php echo ( $finallyData['thread'] == 'none' ?  'checked' : '' ); ?>
					/>
					<label for="none">No Thread</label>
			</div>
	    <div>
	        <input type="radio" id="steem" name="finallycomments[thread]" value="steem"
					<?php echo ( $finallyData['thread'] == 'steem' ?  'checked' : '' ); ?>
					/>
	        <label for="steem">Steem</label>
	    </div>

	    <div>
	        <input type="radio" id="custom" name="finallycomments[thread]" value="custom"
					<?php echo ( $finallyData['thread'] == 'custom' ?  'checked' : '' ); ?>
					/>
	        <label for="custom">Custom</label>
	    </div>
	</fieldset>
	<hr>
	<p>
		<h3><?php _e( "Steem Threads" ); ?></h3>
		<p><?php _e( "Your post is related to content you have created on the Steem network and you want the comments to match across your Wordpress site and Steem sites." ); ?></p>
		<label><?php _e( "Steemit.com link: " ); ?></p></label>
		<input class="widefat" type="text" name="finallycomments[link]" value="<?php echo ( isset($finallyData['link'] ) ? $finallyData['link'] : '' ) ?>">
	</p>
	<hr>
	<p>
			<h3><?php _e( "Custom Threads" ); ?></h3>
			<p><?php _e( "Your post is not related to content on the Steem network and you want a standalone blank comments thread." ); ?></p>
	</p>
	<label><?php _e( "Steem username: " ); ?></p></label>
	<input class="widefat" type="text" name="finallycomments[username]" value="<?php echo ( isset($finallyData['username'] ) ? $finallyData['username'] : '' ) ?>">

	<i>*You must have authorised your Steem account and registered your domain for custom threads. Vist <a href="https://finallycomments.com/dashboard">https://finallycomments.com/dashboard</a> (Activate your site under the API settings).</i>
<?php }


/* Save the meta box's post metadata. */
function finallycomments_save_post_meta( $post_id, $post ) {

  /* Verify the nonce before proceeding. */
  if ( !isset( $_POST['finallycomments_nonce'] ) || !wp_verify_nonce( $_POST['finallycomments_nonce'], basename( __FILE__ ) ) )
    return $post_id;

  /* Get the post type object. */
  $post_type = get_post_type_object( $post->post_type );

  /* Check if the current user has permission to edit the post. */
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;

  /* Get the posted data */
  $new_meta_value = ( isset( $_POST['finallycomments'] ) ?  $_POST['finallycomments'] : '' );

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
