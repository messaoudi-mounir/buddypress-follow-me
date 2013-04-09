<?php
/**
 * Screen Functions
 *
 * Screen functions are the controllers of BuddyPress. They will execute when their
 * specific URL is caught. They will first save or manipulate data using business
 * functions, then pass on the user to a template file.
 *
 * @package BP-Follow-Me
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * bp-follow_screen_one()
 *
 * Sets up and displays the screen output for the sub nav item "follow/screen-one"
 */
/*
function bp_follow_screen() {
	global $bp;
	//bp_update_is_directory( true, 'follow' );
    bp_update_is_directory( true, 'members/single/follow' );
	
     Add a do action here, so your component can be extended by others. 
	//do_action( 'bp_follow_screen' );

	bp_core_load_template( apply_filters( 'bp_follow_screen', 'members/single/follow' ) );
}
add_action('bp_screens', 'bp_follow_screen');
 */


function bp_follow_screen() {
        bp_update_is_directory( true, 'follow' );
        do_action( 'bp_follow_screen' );
        bp_core_load_template( apply_filters( 'bp_follow_screen', 'members/single/follow' ) );
}
add_action( 'bp_screens', 'bp_follow_screen' );

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
	bp_core_load_template( apply_filters( 'bp_following_screen', 'follow' ) );
}
add_action('bp_screens', 'bp_following_screen');

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


//mod:bp1.7
class BP_Follow_Theme_Compat {
 
    /**
     * Setup the bp plugin component theme compatibility
     */
    public function __construct() { 
        /* this is where we hook bp_setup_theme_compat !! */
        add_action( 'bp_setup_theme_compat', array( $this, 'is_bp_plugin' ) );
    }
 
    /**
     * Are we looking at something that needs theme compatability?
     */
    public function is_bp_plugin() {
       
            // first we reset the post
            add_action( 'bp_template_include_reset_dummy_post_data', array( $this, 'directory_dummy_post' ) );
            // then we filter ‘the_content’ thanks to bp_replace_the_content
            add_filter( 'bp_replace_the_content', array( $this, 'directory_content'    ) );


    }

    /**
     * Update the global $post with directory data
     */
    public function directory_dummy_post() {

    }
    /**
     * Filter the_content with bp-plugin index template part
     */
    public function directory_content() {
        bp_buffer_template_part( 'members/single/follow' );
    }
}
 
new BP_Follow_Theme_Compat ();


function bp_follow_add_template_stack( $templates ) {
    // if we're on a page of our plugin and the theme is not BP Default, then we
    // add our path to the template path array
    if ( bp_is_current_component( 'follow' ) && !bp_follow_is_bp_default() ) {
        $templates[] = BP_FOLLOW_PLUGIN_DIR . '/includes/templates/';
    }

    return $templates;
}
 
add_filter( 'bp_get_template_stack', 'bp_follow_add_template_stack', 10, 1 );

/* todo (add sub nav  profil > member > activity > follow )
function bp_setup_nav_follow() {
    global $bp;
   bp_core_new_nav_item( array(
    'name' => __( 'My Links', 'bp-my-links' ),
    'slug' => 'my-link',
    'position' => 80,
    'screen_function' => 'bp_my_link_screen_my_links',
    'default_subnav_slug' => 'my-link'
    ) );
     
   
    bp_core_new_subnav_item( array(
        'name' => __( 'My Links', 'bp-my-links' ),
        'slug' => $bp->members->current_member->slug,
        'parent_slug' => $bp->settings->slug,
        'position' => 80,
        'screen_function' => 'bp_my_link_screen_my_links',
        'default_subnav_slug' => 'my-link'
    ) );
    echo $bp->members->current_member->slug;

}
add_action( 'bp_setup_nav', 'bp_setup_nav_follow', 15 );
*/