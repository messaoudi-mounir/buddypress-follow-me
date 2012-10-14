<?php
/**
 * BP Follow ajax
 *
 * @package BP-Follow-Me
 */
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Register AJAX handlers for a list of actions of example plugin.
 *
 * This function is registered to the after_setup_theme hook with priority 20 as
 * this file is included in a function hooked to after_setup_theme at priority 10.
 *
 * @since BuddyPress (1.6)
 */
function bp_follow_register_actions() {
	$actions = array(
		// Directory filters
		'follow_filter'       => 'bp_follow_object_template_loader',
		'following_filter'    => 'bp_follow_object_template_loader',
		'followers_filter'    => 'bp_follow_object_template_loader',
	);

	/**
	 * Register all of these AJAX handlers
	 *
	 * The "wp_ajax_" action is used for logged in users, and "wp_ajax_nopriv_"
	 * executes for users that aren't logged in. This is for backpat with BP <1.6.
	 */
	foreach( $actions as $name => $function ) {
		add_action( 'wp_ajax_'        . $name, $function );
		add_action( 'wp_ajax_nopriv_' . $name, $function );
	}
}
add_action( 'bp_follow_load_core_component', 'bp_follow_register_actions', 1 );

/**
 * Load the template loop for the current object.
 *
 * @return string Prints template loop for the specified object
 * @since BuddyPress (1.2)
 */
function bp_follow_object_template_loader() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;
	
	// Sanitize the post object
	$object = esc_attr( $_POST['object'] );
	
	// Locate the object template
	//locate_template( array( "$object/$object-loop.php" ), true );
	bp_core_load_template( apply_filters( 'bp_follow_template', "members/single/follow/follow-loop" ) );
	exit;
}