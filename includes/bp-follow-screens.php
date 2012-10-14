<?php

/********************************************************************************
 * Screen Functions
 *
 * Screen functions are the controllers of BuddyPress. They will execute when their
 * specific URL is caught. They will first save or manipulate data using business
 * functions, then pass on the user to a template file.
 *
 * @package BP-Follow-Me
 */

// Exit if accessed directly
// It's a good idea to include this in each of your plugin files, for increased security on
// improperly configured servers
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * If your component uses a top-level directory, this function will catch the requests and load
 * the index page.
 *
 * @package BuddyPress_Template_Pack
 * @since 1.6

function bp_follow_directory_setup() {
	if ( bp_is_follow_component() && !bp_current_action() && !bp_current_item() ) {
		// This wrapper function sets the $bp->is_directory flag to true, which help other
		// content to display content properly on your directory.
		bp_update_is_directory( true, 'follow' );

		// Add an action so that plugins can add content or modify behavior
		do_action( 'bp_follow_directory_setup' );

		bp_core_load_template( apply_filters( 'follow_directory_template', 'members/single/follow' ) );
	}
}
add_action( 'bp_screens', 'bp_follow_directory_setup' );
*/



/**
 * bp-follow_screen_one()
 *
 * Sets up and displays the screen output for the sub nav item "follow/screen-one"
 */
function bp_follow_screen() {
	global $bp;
	bp_update_is_directory( true, 'follow' );
	/* Add a do action here, so your component can be extended by others. */
	do_action( 'bp_follow_screen' );
	bp_core_load_template( apply_filters( 'bp_follow_screen', 'members/single/follow' ) );
}


/**
 * bp_following_screen()
 *
 * Sets up and displays the screen output for the sub nav item "follow/screen-two"
 */
function bp_following_screen() {
	global $bp;
	bp_update_is_directory( true, 'following' );
	/* Add a do action here, so your component can be extended by others. */
	do_action( 'bp_following_screen' );
	bp_core_load_template( apply_filters( 'bp_following_screen', 'members/single/follow' ) );
}

/**
 * bp_following_screen()
 *
 * Sets up and displays the screen output for the sub nav item "follow/screen-two"
 */
function bp_followers_screen() {
	global $bp;
	bp_update_is_directory( true, 'followers' );
	
	/* Add a do action here, so your component can be extended by others. */
	do_action( 'bp_followers_screen' );
	
	/** remove notifications if exist */ 
	if ( isset( $_GET['new'] ) )
		bp_core_delete_notifications_by_type( bp_loggedin_user_id(), $bp->follow->id, 'new_follow' );

	bp_core_load_template( apply_filters( 'bp_followers_screen', 'members/single/follow' ) );
}
	
/**
 * Catches any visits to the "Activity > Following" tab on a users profile.
 *
 * @uses bp_core_load_template() Loads a template file.
 */
function bp_follow_screen_activity_following() {
	bp_update_is_item_admin( is_super_admin(), 'activity' );
	do_action( 'bp_activity_screen_following' );
	bp_core_load_template( apply_filters( 'bp_activity_template_following', 'members/single/home' ) );
}
	
?>