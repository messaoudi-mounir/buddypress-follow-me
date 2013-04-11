<?php
/**
 * BP Follow Template Tags
 *
 * @package BP-Follow-Me
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * this function use bp_has_mebers
 *
 */
function bp_follow_has_items( $args = '' ) {
	global $bp;
	$page         = 1;
	
	$defaults = array(
		'page'            => $page,
		'per_page'        => 20,
		'include'         => bp_get_following_ids(),         // Pass a user_id or a list (comma-separated or array) of user_ids to only show these users
	);
	
	$r = wp_parse_args( $args, $defaults );
	extract( $r );
	return bp_has_members ($r);
}

/**
 * Is this page part of the Follow component?
 *
 * Having a special function just for this purpose makes our code more readable elsewhere, and also
 * allows us to place filter 'bp_is_follow_component' for other components to interact with.
 *
 * @package BuddyPress_Skeleton_Component
 * @since 1.6
 *
 * @uses bp_is_current_component()
 * @uses apply_filters() to allow this value to be filtered
 * @return bool True if it's the follow component, false otherwise
 */
function bp_is_follow_component() {
	$is_follow_component = bp_is_current_component( 'follow' );

	return apply_filters( 'bp_is_follow_component', $is_follow_component );
}

/**
 * Echo the component's slug
 *
 * @package BuddyPress_Skeleton_Component
 * @since 1.6
 */
function bp_follow_slug() {
	echo bp_get_follow_slug();
}
	/**
	 * Return the component's slug
	 *
	 * Having a template function for this purpose is not absolutely necessary, but it helps to
	 * avoid too-frequent direct calls to the $bp global.
	 *
	 * @package BuddyPress_Skeleton_Component
	 * @since 1.6
	 *
	 * @uses apply_filters() Filter 'bp_get_follow_slug' to change the output
	 * @return str $follow_slug The slug from $bp->follow->slug, if it exists
	 */
	function bp_get_follow_slug() {
		global $bp;

		// Avoid PHP warnings, in case the value is not set for some reason
		$follow_slug = isset( $bp->follow->slug ) ? $bp->follow->slug : '';

		return apply_filters( 'bp_get_follow_slug', $follow_slug );
	}


/**
 * Echo the component's slug
 *
 * @package BuddyPress_Skeleton_Component
 * @since 1.6
 */
function bp_followers_slug() {
	echo bp_get_followers_slug();
}
	/**
	 * Return the component's slug
	 *
	 * Having a template function for this purpose is not absolutely necessary, but it helps to
	 * avoid too-frequent direct calls to the $bp global.
	 *
	 * @package BuddyPress_Skeleton_Component
	 * @since 1.6
	 *
	 * @uses apply_filters() Filter 'bp_get_followers_slug' to change the output
	 * @return str $follow_slug The slug from 'followers', if it exists
	 */
	function bp_get_followers_slug() {
		global $bp;

		// Avoid PHP warnings, in case the value is not set for some reason
		$followers_slug = 'followers';

		return apply_filters( 'bp_get_followers_slug', $followers_slug );
	}

/**
 * Echo the component's root slug
 *
 * @package BuddyPress_Skeleton_Component
 * @since 1.6
 */
function bp_follow_root_slug() {
	echo bp_get_follow_root_slug();
}
	/**
	 * Return the component's root slug
	 *
	 * Having a template function for this purpose is not absolutely necessary, but it helps to
	 * avoid too-frequent direct calls to the $bp global.
	 *
	 * @package BuddyPress_Skeleton_Component
	 * @since 1.6
	 *
	 * @uses apply_filters() Filter 'bp_get_follow_root_slug' to change the output
	 * @return str $follow_root_slug The slug from $bp->follow->root_slug, if it exists
	 */
	function bp_get_follow_root_slug() {
		global $bp;

		// Avoid PHP warnings, in case the value is not set for some reason
		$follow_root_slug = isset( $bp->follow->root_slug ) ? $bp->follow->root_slug : '';

		return apply_filters( 'bp_get_follow_root_slug', $follow_root_slug );
	}

/**
 * Output a comma-separated list of user_ids for a given user's followers. 
 *
 * @param mixed $args Arguments can be passed as an associative array or as a URL argument string
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @uses bp_get_follower_ids() Returns comma-seperated string of user IDs on success. Integer zero on failure.
 */
function bp_follower_ids( $args = '' ) {
	echo bp_get_follower_ids( $args );
}
	/**
	 * Returns a comma separated list of user_ids for a given user's followers.
	 *
	 * This can then be passed directly into the members loop querystring.
	 * On failure, returns an integer of zero. Needed when used in a members loop to prevent SQL errors.
	 *
	 * Arguments include:
	 * 	'user_id' - The user ID you want to check for followers
	 *
	 * @param mixed $args Arguments can be passed as an associative array or as a URL argument string
	 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
	 * @return Mixed Comma-seperated string of user IDs on success. Integer zero on failure.
	 */
	function bp_get_follower_ids( $args = '' ) {

		$defaults = array(
			'user_id' => bp_displayed_user_id()
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );
		
		$ids = implode( ',', (array)bp_follow_get_followers( array( 'user_id' => $user_id ) ) );
		
		$ids = empty( $ids ) ? 0 : $ids;

 		return apply_filters( 'bp_get_follower_ids', $ids, $user_id );
	}

/**
 * Output a comma-separated list of user_ids for a given user's following. 
 *
 * @param mixed $args Arguments can be passed as an associative array or as a URL argument string
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @uses bp_get_following_ids() Returns comma-seperated string of user IDs on success. Integer zero on failure.
 */
function bp_following_ids( $args = '' ) {
	echo bp_get_following_ids( $args );
}
	/**
	 * Returns a comma separated list of user_ids for a given user's following.
	 *
	 * This can then be passed directly into the members loop querystring.
	 * On failure, returns an integer of zero. Needed when used in a members loop to prevent SQL errors.
	 *
	 * Arguments include:
	 * 	'user_id' - The user ID you want to check for a following
	 *
	 * @param mixed $args Arguments can be passed as an associative array or as a URL argument string
	 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
	 * @return Mixed Comma-seperated string of user IDs on success. Integer zero on failure.
	 */
	function bp_get_following_ids( $args = '' ) {

		$defaults = array(
			'user_id' => bp_displayed_user_id()
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		$ids = implode( ',', (array)bp_follow_get_following( array( 'user_id' => $user_id ) ) );
		
		$ids = empty( $ids ) ? 0 : $ids;

 		return apply_filters( 'bp_get_following_ids', $ids, $user_id );
	}

/**
 * Output a follow / unfollow button for a given user depending on the follower status.
 *
 * @param mixed $args Arguments can be passed as an associative array or as a URL argument string. See bp_follow_get_add_follow_button() for full arguments.
 * @uses bp_follow_get_add_follow_button() Returns the follow / unfollow button
 * @author r-a-y
 * @since 1.1
 */
function bp_follow_add_follow_button( $args = '' ) {
	echo bp_follow_get_add_follow_button( $args );
}
	/**
	 * Returns a follow / unfollow button for a given user depending on the follower status.
	 *
	 * Checks to see if the follower is already following the leader.  If is following, returns
	 * "Stop following" button; if not following, returns "Follow" button.
	 *
	 * Arguments include:
	 * 	'leader_id'   - The user you want to follow
	 * 	'follower_id' - The user who is initiating the follow request
	 *
	 * @param mixed $args Arguments can be passed as an associative array or as a URL argument string
	 * @return mixed String of the button on success.  Boolean false on failure.
	 * @uses bp_get_button() Renders a button using the BP Button API
	 * @author r-a-y
	 * @since 1.1
	 */
	function bp_follow_get_add_follow_button( $args = '' ) {
		global $bp, $members_template;

		$defaults = array(
			'leader_id'   => bp_displayed_user_id(),
			'follower_id' => bp_loggedin_user_id()
		);
	
		$r = wp_parse_args( $args, $defaults );
		extract( $r );

		if ( !$leader_id || !$follower_id )
			return false;

		// if we're checking during a members loop, then follow status is already queried via bp_follow_inject_member_follow_status()
		if ( !empty( $members_template->member ) && $follower_id == bp_loggedin_user_id() && $follower_id == bp_displayed_user_id() ) {
			$is_following = $members_template->member->is_following;
		}
		// else we manually query the follow status
		else {
			$is_following = bp_follow_is_following( array( 'leader_id' => $leader_id, 'follower_id' => $follower_id ) );
		}

		// if the logged-in user is the leader, use already-queried variables
		if ( bp_loggedin_user_id() && $leader_id == bp_loggedin_user_id() ) {
			$leader_domain   = bp_loggedin_user_domain();
			$leader_fullname = bp_get_loggedin_user_fullname();
		}
		// else we do a lookup for the user domain and display name of the leader
		else {
			$leader_domain   = bp_core_get_user_domain( $leader_id );
			$leader_fullname = bp_core_get_user_displayname( $leader_id );
		}

		// setup some variables
		if ( $is_following ) {
			$id        = 'following';
			$action    = 'stop';
			$class     = 'unfollow';
			$link_text = $link_title = sprintf( __( 'Unfollow', 'bp-follow' ), apply_filters( 'bp_follow_leader_name', bp_get_user_firstname( $leader_fullname ), $leader_id ) );
			//$link_text = $link_title = sprintf( __( 'Stop Following %s', 'bp-follow' ), apply_filters( 'bp_follow_leader_name', bp_get_user_firstname( $leader_fullname ), $leader_id ) );
		}
		else {
			$id        = 'not-following';
			$action    = 'start';
			$class     = 'follow';
			$link_text = $link_title = sprintf( __( 'Follow', 'bp-follow' ), apply_filters( 'bp_follow_leader_name', bp_get_user_firstname( $leader_fullname ), $leader_id ) );
			//$link_text = $link_title = sprintf( __( 'Follow %s', 'bp-follow' ), apply_filters( 'bp_follow_leader_name', bp_get_user_firstname( $leader_fullname ), $leader_id ) );
		}

		// setup the button arguments
		$button = array(
			'id'                => $id,
			'component'         => 'follow',
			'must_be_logged_in' => true,
			'block_self'        => empty( $members_template->member ) ? true : false,
			'wrapper_class'     => 'follow-button ' . $id,
			'wrapper_id'        => 'follow-button-' . $leader_id,
			'link_href'         => wp_nonce_url( $leader_domain . $bp->follow->followers->slug . '/' . $action .'/', $action . '_following' ),
			'link_text'         => $link_text,
			'link_title'        => $link_title,
			'link_id'           => $class . '-' . $leader_id,
			'link_class'        => $class
		);

		// Filter and return the HTML button
		return bp_get_button( apply_filters( 'bp_follow_get_add_follow_button', $button, $leader_id, $follower_id ) );
	}

	add_shortcode('bp_follow_me', 'bp_follow_get_add_follow_button');	


/*
@TODO Add Follow  Checkbox Setting in member profil settings

add_action( 'show_user_profile', 'my_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'my_show_extra_profile_fields' );

function my_show_extra_profile_fields( $user ) { ?>

	<h3>Extra profile information</h3>

	<table class="form-table">

		<tr>
			<th><label for="twitter">Twitter</label></th>

			<td>
				<input type="text" name="twitter" id="twitter" value="<?php echo esc_attr( get_the_author_meta( 'twitter', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please enter your Twitter username.</span>
			</td>
		</tr>

	</table>
<?php 
}


add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );

function my_save_extra_profile_fields( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	// Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. 
	update_usermeta( $user_id, 'twitter', $_POST['twitter'] );
}

 */

/*
function bpd_add_new_xprofile_field_type($field_types){
    $image_field_type = array('image');
    $field_types = array_merge($field_types, $image_field_type);
    return $field_types;
}

add_filter( 'xprofile_field_types', 'bpd_add_new_xprofile_field_type' );


function bpd_admin_render_new_xprofile_field_type($field, $echo = true){

    ob_start();
        switch ( $field->type ) {
            case 'image':
                ?>
                    <input type="file" name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>" value="" />
                <?php
                break;    
            default :
                ?>
                    <p>Field type unrecognized</p>
                <?php
        }

        $output = ob_get_contents();
    ob_end_clean();

    if($echo){
        echo $output;
        return;
    }
    else{
        return $output;
    }
    
}

add_filter( 'xprofile_admin_field', 'bpd_admin_render_new_xprofile_field_type' );



function bpd_edit_render_new_xprofile_field($echo = true){

    if(empty ($echo)){
        $echo = true;
    }
   
    ob_start();
        if ( bp_get_the_profile_field_type() == 'image' ){
            $imageFieldInputName = bp_get_the_profile_field_input_name();
            $image = WP_CONTENT_URL . bp_get_the_profile_field_edit_value();

        ?>
                <label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
                <input type="file" name="<?php echo $imageFieldInputName; ?>" id="<?php echo $imageFieldInputName; ?>" value="" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>/>
                <img src="<?php echo $image; ?>" alt="<?php bp_the_profile_field_name(); ?>" />
                
        <?php
            
        } 

        $output = ob_get_contents();
    ob_end_clean();

    if($echo){
        echo $output;
        return;
    }
    else{
        return $output;
    }

}

add_action( 'bp_custom_profile_edit_fields', 'bpd_edit_render_new_xprofile_field');


// Override default action hook in order to support images
function bpd_override_xprofile_screen_edit_profile(){
    $screen_edit_profile_priority = has_filter('bp_screens', 'xprofile_screen_edit_profile');

    if($screen_edit_profile_priority !== false){
        //Remove the default profile_edit handler
        remove_action( 'bp_screens', 'xprofile_screen_edit_profile', $screen_edit_profile_priority );

        //Install replalcement hook
        add_action( 'bp_screens', 'bpd_screen_edit_profile', $screen_edit_profile_priority );
    }
}

add_action( 'bp_actions', 'bpd_override_xprofile_screen_edit_profile', 10 );



//Create profile_edit handler
function bpd_screen_edit_profile(){

    if ( isset( $_POST['field_ids'] ) ) {
        if(wp_verify_nonce( $_POST['_wpnonce'], 'bp_xprofile_edit' )){

            $posted_field_ids = explode( ',', $_POST['field_ids'] );

            $post_action_found = false;
            $post_action = '';
            if (isset($_POST['action'])){
                $post_action_found = true;
                $post_action = $_POST['action'];

            }

            foreach ( (array)$posted_field_ids as $field_id ) {
                $field_name = 'field_' . $field_id;

                if ( isset( $_FILES[$field_name] ) ) {
                    require_once( ABSPATH . '/wp-admin/includes/file.php' );
                    $uploaded_file = $_FILES[$field_name]['tmp_name'];

                    // Filter the upload location
                    add_filter( 'upload_dir', 'bpd_profile_upload_dir', 10, 1 );

                    //ensure WP accepts the upload job
                    $_POST['action'] = 'wp_handle_upload';

                    $uploaded_file = wp_handle_upload( $_FILES[$field_name] );

                    $uploaded_file = str_replace(WP_CONTENT_URL, '', $uploaded_file['url']) ;

                    $_POST[$field_name] = $uploaded_file;

                }
            }

            if($post_action_found){
                $_POST['action'] = $post_action;
            }
            else{
                unset($_POST['action']);
            }

        }
    }

    if(!defined('DOING_AJAX')){
        if(function_exists('xprofile_screen_edit_profile')){
            xprofile_screen_edit_profile();
        }
    }

}

function bpd_profile_upload_dir( $upload_dir ) {
    global $bp;

    $user_id = $bp->displayed_user->id;
    $profile_subdir = '/profiles/' . $user_id;

    $upload_dir['path'] = $upload_dir['basedir'] . $profile_subdir;
    $upload_dir['url'] = $upload_dir['baseurl'] . $profile_subdir;
    $upload_dir['subdir'] = $profile_subdir;

    return $upload_dir;
}
*/
