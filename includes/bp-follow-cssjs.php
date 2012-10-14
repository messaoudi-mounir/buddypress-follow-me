<?php

/**
 *
 * Css and js enqueue  
 *
 * @package BP-Follow-Me
 */


// Exit if accessed directly
// It's a good idea to include this in each of your plugin files, for increased security on
// improperly configured servers
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * NOTE: You should always use the wp_enqueue_script() and wp_enqueue_style() functions to include
 * javascript and css files.
 */
 
/**
 * bp_follow_add_js()
 *
 * This function will enqueue the components javascript file, so that you can make
 * use of any javascript you bundle with your component within your interface screens.
 */
function bp_follow_add_js() {
	global $bp;
	if ( $bp->current_component == $bp->follow->slug  
	  || bp_is_page(BP_MEMBERS_SLUG) 
	  || ( $bp->current_action == 'members') 
	  || ( $bp->current_action == 'my-friends' ) ) {
		wp_enqueue_script( 'bp-follow-js', plugins_url( '/buddypress-follow-me/includes/js/general.js' ), array('jquery') );
		}
}
add_action( 'template_redirect', 'bp_follow_add_js', 1 );

?>