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
