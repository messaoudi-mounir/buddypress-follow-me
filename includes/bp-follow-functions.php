<?php
/**
 *
 * Functions in this file allow this component to hook into BuddyPress so it interacts
 * seamlessly with the interface and existing core components.
 *
 * @package BP-Follow-Me
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Start following a user's activity
 *
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @uses wp_parse_args() Parses arguments from an array or request string.
 * @param $args/leader_id - user ID of user to follow
 * @param $args/follower_id - user ID of the user who follows
 * @return bool
 */
function bp_follow_start_following( $args = '' ) {
	global $bp;

	$defaults = array(
		'leader_id'   => bp_displayed_user_id(),
		'follower_id' => bp_loggedin_user_id()
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$follow = new BP_Follow;
	$follow->leader_id = (int)$leader_id;
	$follow->follower_id = (int)$follower_id;

	if ( !$follow->save() )
		return false;

	/* Add a screen count notification */
	bp_core_add_notification( $follower_id, $leader_id, $bp->follow->id, 'new_follow' );

	/* Add a more specific email notification */
	bp_follow_new_follow_email_notification( array( 'leader_id' => $leader_id, 'follower_id' => $follower_id ) );

	do_action_ref_array( 'bp_follow_start_following', array( &$follow ) );

	return true;
}

/**
 * Stop following a user's activity
 *
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @uses wp_parse_args() Parses arguments from an array or request string.
 * @param $args/leader_id - user ID of user to stop following
 * @param $args/follower_id - user ID of the user who wants to stop following
 * @return bool
 */
function bp_follow_stop_following( $args = '' ) {
	global $bp;
	
	$defaults = array(
		'leader_id'   => bp_displayed_user_id(),
		'follower_id' => bp_loggedin_user_id()
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$follow = new BP_Follow( $leader_id, $follower_id );

	if ( !$follow->delete() )
		return false;

	do_action_ref_array( 'bp_follow_stop_following', array( &$follow ) );

	// Remove following notifications from user(add in v1.1)
	bp_core_delete_notifications_by_type( $leader_id, $bp->follow->id, 'new_follow' );

	return true;
}

/**
 * Check if a user is already following another user.
 *
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @uses wp_parse_args() Parses arguments from an array or request string.
 * @param $args/leader_id - user ID of user to check is being followed
 * @param $args/follower_id - user ID of the user who is doing the following
 * @return bool
 */
function bp_follow_is_following( $args = '' ) {

	$defaults = array(
		'leader_id'   => bp_displayed_user_id(),
		'follower_id' => bp_loggedin_user_id()
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$follow = new BP_Follow( $leader_id, $follower_id );
	
	return apply_filters( 'bp_follow_is_following', (int)$follow->id, &$follow );
}

/**
 * Fetch the user_ids of all the followers of a particular user.
 *
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @uses wp_parse_args() Parses arguments from an array or request string.
 * @param $args/user_id - the user ID of the user to get followers for.
 * @return array of user ids
 */
function bp_follow_get_followers( $args = '' ) {

	$defaults = array(
		'user_id' => bp_displayed_user_id()
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	return apply_filters( 'bp_follow_get_followers', BP_Follow::get_followers( $user_id ) );
}

/**
 * Fetch the user_ids of all the users a particular user is following.
 *
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @uses wp_parse_args() Parses arguments from an array or request string.
 * @param $args/user_id - the user ID of the user to get a list of users followed for.
 * @return array of user ids
 */
function bp_follow_get_following( $args = '' ) {

	$defaults = array(
		'user_id' => bp_displayed_user_id()
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	return apply_filters( 'bp_follow_get_following', BP_Follow::get_following( $user_id ) );
}

/**
 * Get the total followers and total following counts for a user.
 *
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @uses wp_parse_args() Parses arguments from an array or request string.
 * @param $args/user_id - the user ID of the user to get counts for.
 * @return array [ followers => int, following => int ]
 */
function bp_follow_total_follow_counts( $args = '' ) {

	$defaults = array(
		'user_id' => bp_loggedin_user_id()
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	return apply_filters( 'bp_follow_total_follow_counts', BP_Follow::get_counts( $user_id ) );
}

/**
 * Once the members loop has queried and built a members_template object, fetch
 * all of the member IDs in the object and bulk fetch the following status for all the
 * members in one query. This is significantly more efficient that querying for every
 * member inside of the loop.
 *
 * @global $members_template The members template object containing all fetched members in the loop
 * @uses bulk_check_follow_status() Check the following status for more than one member
 * @param $has_members - Whether any members where actually returned in the loop
 * @return $has_members - Return the original $has_members param as this is a filter function.
 */
function bp_follow_inject_member_follow_status( $has_members ) {
	global $members_template;

	if ( empty( $has_members ) )
		return $has_members;

	$user_ids = array();

	foreach( (array)$members_template->members as $i => $member ) {
		if ( $member->id != bp_loggedin_user_id() )
			$user_ids[] = $member->id;

		$members_template->members[$i]->is_following = false;
	}

	if ( empty( $user_ids ) )
		return $has_members;

	$following = BP_Follow::bulk_check_follow_status( $user_ids );

	if ( empty( $following ) )
		return $has_members;

	foreach( (array)$following as $is_following ) {
		foreach( (array)$members_template->members as $i => $member ) {
			if ( $is_following->leader_id == $member->id )
				$members_template->members[$i]->is_following = true;
		}
	}

	return $has_members;
}
add_filter( 'bp_has_members', 'bp_follow_inject_member_follow_status' );

/**
 * Once the group members loop has queried and built a members_template object, fetch
 * all of the member IDs in the object and bulk fetch the following status for all the
 * group members in one query. This is significantly more efficient that querying for
 * every member inside of the loop.
 *
 * @global $members_template The members template object containing all fetched members in the loop
 * @uses BP_Follow::bulk_check_follow_status() Check the following status for more than one member
 * @param $has_members - Whether any members where actually returned in the loop
 * @return $has_members - Return the original $has_members param as this is a filter function.
 * @author r-a-y
 * @since 1.1
 */
function bp_follow_inject_group_member_follow_status( $has_members ) {
	global $members_template;

	if ( empty( $has_members ) )
		return $has_members;

	$user_ids = array();

	foreach( (array)$members_template->members as $i => $member ) {
		if ( $member->user_id != bp_loggedin_user_id() )
			$user_ids[] = $member->user_id;

		$members_template->members[$i]->is_following = false;
	}

	if ( empty( $user_ids ) )
		return $has_members;

	$following = BP_Follow::bulk_check_follow_status( $user_ids );

	if ( empty( $following ) )
		return $has_members;

	foreach( (array)$following as $is_following ) {
		foreach( (array)$members_template->members as $i => $member ) {
			if ( $is_following->leader_id == $member->user_id )
				$members_template->members[$i]->is_following = true;
		}
	}

	return $has_members;
}
add_filter( 'bp_group_has_members', 'bp_follow_inject_group_member_follow_status' );

/**
 * Add a "Follow User/Stop Following" button to the profile header for a user.
 *
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @uses bp_follow_is_following() Check the following status for a user
 * @uses bp_is_my_profile() Return true if you are looking at your own profile when logged in.
 * @uses is_user_logged_in() Return true if you are logged in.
 */
function bp_follow_add_profile_follow_button() {
	bp_follow_add_follow_button();
}
add_action( 'bp_member_header_actions', 'bp_follow_add_profile_follow_button' );

/**
 * Add a "Follow User/Stop Following" button to each member shown in a member listing
 *
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @global $members_template The members template object containing all fetched members in the loop
 * @uses is_user_logged_in() Return true if you are logged in.
 */
function bp_follow_add_listing_follow_button() {
	global $members_template;

	if ( $members_template->member->id == bp_loggedin_user_id() )
		return false;

	bp_follow_add_follow_button( 'leader_id=' . $members_template->member->id );
}
add_action( 'bp_directory_members_actions', 'bp_follow_add_listing_follow_button' );

/**
 * Add a "Follow User/Stop Following" button to each member shown in a group member listing
 *
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @global $members_template The members template object containing all fetched members in the loop
 * @author r-a-y
 * @since 1.1
 */
function bp_follow_add_group_member_follow_button() {
	global $members_template;

	if ( $members_template->member->user_id == bp_loggedin_user_id() || !bp_loggedin_user_id() )
		return false;

	bp_follow_add_follow_button( 'leader_id=' . $members_template->member->user_id );
}
add_action( 'bp_group_members_list_item_action', 'bp_follow_add_group_member_follow_button' );

/* Hook into the activity stream tabs and scope ********************/

/**
 * Adds a "Following (X)" tab to the activity stream so that users can select to filter on only
 * users they are following.
 *
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @uses bp_follow_total_follow_counts() Get the following/followers counts for a user.
 */
function bp_follow_add_activity_tab() {

	$counts = bp_follow_total_follow_counts( array( 'user_id' => bp_loggedin_user_id() ) );

	if ( empty( $counts['following'] ) )
		return false;
	?>
	<li id="activity-following"><a href="<?php echo bp_loggedin_user_domain() . BP_ACTIVITY_SLUG . '/' . BP_FOLLOWING_SLUG . '/' ?>" title="<?php _e( 'The public activity for everyone you are following on this site.', 'bp-follow' ) ?>"><?php printf( __( 'Following <span>%d</span>', 'bp-follow' ), (int)$counts['following'] ) ?></a></li><?php
}
add_action( 'bp_before_activity_type_tab_friends', 'bp_follow_add_activity_tab' );

/**
 * Modify the querystring passed to the activity loop so we return only users that the
 * current user is following.
 *
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @uses bp_get_following_ids() Get the user_ids of all users a user is following.
 */
function bp_follow_add_activity_scope_filter( $qs, $object, $filter, $scope ) {
	global $bp;

	// Only filter on directory pages (no action) and the following scope on activity object.
	if ( ( !empty( $bp->current_action ) && !bp_is_current_action( 'following' ) ) || 'following' != $scope || 'activity' != $object )
		return $qs;

	$user_id = bp_displayed_user_id() ? bp_displayed_user_id() : bp_loggedin_user_id();

	$following_ids = bp_get_following_ids( array( 'user_id' => $user_id ) );

	// if $following_ids is empty, pass a negative number so no activity can be found
	$following_ids = empty( $following_ids ) ? -1 : $following_ids;

	$qs .= '&user_id=' . $following_ids;

	return apply_filters( 'bp_follow_add_activity_scope_filter', $qs, $filter );
}
add_filter( 'bp_dtheme_ajax_querystring', 'bp_follow_add_activity_scope_filter', 10, 4 );

/* Hook into the member directory tabs and filtering */

/**
 * Add a "Following (X)" tab to the members directory so that only users that a user
 * is following will show in the listing.
 *
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @uses bp_follow_total_follow_counts() Get the following/followers counts for a user.
 */
function bp_follow_add_following_tab() {

	if ( bp_displayed_user_id() )
		return false;

	$counts = bp_follow_total_follow_counts( array( 'user_id' => bp_loggedin_user_id() ) );

	if ( empty( $counts['following'] ) )
		return false;
	?>
	<li id="members-following"><a href="<?php echo bp_loggedin_user_domain() . BP_FOLLOWING_SLUG ?>"><?php printf( __( 'Following <span>%d</span>', 'bp-follow' ), $counts['following'] ) ?></a></li><?php
}
add_action( 'bp_members_directory_member_types', 'bp_follow_add_following_tab' );

/**
 * Modify the querystring passed to the members loop so we return only users that the
 * current user is following.
 *
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @uses bp_get_following_ids() Get the user_ids of all users a user is following.
 */
function bp_follow_add_member_directory_filter( $qs, $object, $filter, $scope  ) {
	global $bp;

	if ( ($object != $bp->follow->slug) && (!empty( $bp->current_action ) || 'following' != $scope || 'members' != $object ) )
		return $qs;
		
	if( $object == $bp->follow->slug ){
		if( $bp->current_action == $bp->follow->following->slug ){
				$qs .= '&include=' . bp_get_following_ids() ;
		}elseif( $bp->current_action == $bp->follow->followers->slug ){
			$qs .= '&include=' . bp_get_follower_ids() ;
		}
	}else{
		$qs .= '&include=' . bp_get_following_ids( array( 'user_id' => bp_loggedin_user_id() ) );
	}
	
	return apply_filters( 'bp_follow_add_member_directory_filter', $qs, $filter );

}
add_filter( 'bp_dtheme_ajax_querystring', 'bp_follow_add_member_directory_filter', 10, 4 );

/**
 * On a user's "Activity > Following" screen, set the activity scope to "following".
 *
 * Unfortunately for 3rd-party components, this is the only way to set the scope in
 * {@link bp_dtheme_ajax_querystring()} due to the way that function handles cookies.
 *
 * Yes, this is considered a hack, or more appropriately, a loophole!
 *
 * @author r-a-y
 * @since 1.1.1
 */
function bp_follow_set_activity_following_scope() {
	// set the activity scope to 'following' by faking an ajax request (loophole!)
	$_POST['cookie'] = 'bp-activity-scope%3Dfollowing%3B%20bp-activity-filter%3D-1';

	// reset the dropdown menu to 'Everything'
	@setcookie( 'bp-activity-filter', '-1', 0, '/' );
}
add_action( 'bp_activity_screen_following', 'bp_follow_set_activity_following_scope' );

/**
 * On a user's "Activity > Following" screen, set the activity scope to "following"
 * during AJAX requests - "Load More" button or via activity dropdown filter menu.
 *
 * Unfortunately for 3rd-party components, this is the only way to set the scope in
 * {@link bp_dtheme_ajax_querystring()} due to the way that function handles cookies.
 *
 * Yes, this is considered a hack, or more appropriately, a loophole!
 *
 * @author r-a-y
 * @since 1.1.1
 */
function bp_follow_set_activity_following_scope_on_ajax() {

	// are we in an ajax request?
	$is_ajax = ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' );

	// set the activity scope to 'following'
	if ( bp_is_current_action( 'following' ) && $is_ajax ) {
		// if we have a post value already, let's add our scope to the existing cookie value
		if ( !empty( $_POST['cookie'] ) )
			$_POST['cookie'] .= '%3B%20bp-activity-scope%3Dfollowing';
		else 
			$_POST['cookie'] .= 'bp-activity-scope%3Dfollowing';
	}
}
add_action( 'bp_before_activity_loop', 'bp_follow_set_activity_following_scope_on_ajax' );

/**
 * Removes follow relationships for all users from a user who is deleted or spammed
 *
 * @uses BP_Follow::delete_all_for_user() Deletes user ID from all following / follower records
 */
function bp_follow_remove_data( $user_id ) {
	global $bp;

	do_action( 'bp_follow_before_remove_data', $user_id );

	BP_Follow::delete_all_for_user( $user_id );

	// Remove following notifications from user
	bp_core_delete_notifications_from_user( $user_id, $bp->follow->id, 'new_follow' );

	do_action( 'bp_follow_remove_data', $user_id );
}
add_action( 'wpmu_delete_user',	'bp_follow_remove_data' );
add_action( 'delete_user',	'bp_follow_remove_data' );
add_action( 'make_spam_user',	'bp_follow_remove_data' );

/**
 * bp_follow_load_template_filter()
 *
 * You can define a custom load template filter for your component. This will allow
 * you to store and load template files from your plugin directory.
 *
 * This will also allow users to override these templates in their active theme and
 * replace the ones that are stored in the plugin directory.
 *
 * If you're not interested in using template files, then you don't need this function.
 *
 * This will become clearer in the function bp_follow_screen_one() when you want to load
 * a template file.
 */
function bp_follow_load_template_filter( $found_template, $templates ) {
	global $bp;

	/**
	 * Only filter the template location when we're on the follow component pages.
	 */
	if ( $bp->current_component != $bp->follow->slug )
		return $found_template;

	foreach ( (array) $templates as $template ) {
		if ( file_exists( STYLESHEETPATH . '/' . $template ) )
			$filtered_templates[] = STYLESHEETPATH . '/' . $template;
		else
			$filtered_templates[] = dirname( __FILE__ ) . '/templates/' . $template;
	}

	$found_template = $filtered_templates[0];

	return apply_filters( 'bp_follow_load_template_filter', $found_template );
}
add_filter( 'bp_located_template', 'bp_follow_load_template_filter', 10, 2 );

/**
* http://buddypress.trac.wordpress.org/ticket/2198
*/
function bp_follow_load_sub_template( $template ) {
	if ( $located_template = apply_filters( 'bp_located_template', locate_template( $template , false ), $template ) )	
		load_template( apply_filters( 'bp_load_template', $located_template ) );
}