<?php
/*
Plugin Name: BuddyPress FollowMe
Plugin URI: 
Description: BP FollowMe Allow members to follow other members activity.
Version: 1.2.1
Requires at least:  WP 3.4, BuddyPress 1.5
Tested up to: BuddyPress 1.5, 1.7
License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
Author: Meg@Info
Author URI: http://profiles.wordpress.org/megainfo 
Network: true
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/*************************************************************************************************************
 --- FollowMe 1.2.1 ---

 IMPORTANT: DO NOT configure your component so that it has to run in the /plugins/buddypress/ directory. If you
 do this, whenever the user auto-upgrades BuddyPress - your custom component will be deleted automatically. Design
 your component to run in the /wp-content/plugins/ directory
 *************************************************************************************************************/

// Define a constant that can be checked to see if the component is installed or not.
define( 'BP_FOLLOW_IS_INSTALLED', 1 );

// Define a constant that will hold the current version number of the component
// This can be useful if you need to run update scripts or do compatibility checks in the future
define( 'BP_FOLLOW_VERSION', '1.2.1' );

// Define a constant that we can use to construct file paths throughout the component
define( 'BP_FOLLOW_PLUGIN_DIR', dirname( __FILE__ ) );

/* Define a constant that will hold the database version number that can be used for upgrading the DB
 *
 * NOTE: When table defintions change and you need to upgrade,
 * make sure that you increment this constant so that it runs the install function again.
 *
 * Also, if you have errors when testing the component for the first time, make sure that you check to
 * see if the table(s) got created. If not, you'll most likely need to increment this constant as
 * BP_FOLLOW_DB_VERSION was written to the wp_usermeta table and the install function will not be
 * triggered again unless you increment the version to a number higher than stored in the meta data.
 */
define ( 'BP_FOLLOW_DB_VERSION', '1.0' );

/* Only load the component if BuddyPress is loaded and initialized. */
function bp_follow_init() {
	// Because our loader file uses BP_Component, it requires BP 1.5 or greater.
	if ( version_compare( BP_VERSION, '1.3', '>' ) )
		require( dirname( __FILE__ ) . '/includes/bp-follow-loader.php' );
}
add_action( 'bp_include', 'bp_follow_init' );

/* Put setup procedures to be run when the plugin is activated in the following function */
function bp_follow_activate() {
	global $bp, $wpdb;

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'buddypress/bp-loader.php' ) ) {

		$charset_collate = !empty( $wpdb->charset ) ? "DEFAULT CHARACTER SET $wpdb->charset" : '';
		if ( !$table_prefix = $bp->table_prefix )
			$table_prefix = apply_filters( 'bp_core_get_table_prefix', $wpdb->base_prefix );

		$sql[] = "CREATE TABLE {$table_prefix}bp_follow (
				id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				leader_id bigint(20) NOT NULL,
				follower_id bigint(20) NOT NULL,
			    KEY followers (leader_id,follower_id)
			) {$charset_collate};";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$bp_followme_error_log = dbDelta( $sql );
		unset( $bp_followme_error_log );
		update_site_option( 'bp-follow-db-version', BP_FOLLOW_DB_VERSION );
	}else {
		//deactivate_plugins( basename( __FILE__ ) ); // Deactivate this plugin
		die( _e( 'You cannot enable BuddyPress FollowMe because <strong>BuddyPress</strong> is not active. Please install and activate BuddyPress before trying to activate Buddypress FollowMe again.' , 'bp-follow' ) );
	}	
}
register_activation_hook( __FILE__, 'bp_follow_activate' );


/* On deacativation, clean up anything your component has added. */
function bp_follow_deactivate() {
	/* You might want to delete any options or tables that your component created. */
}
register_deactivation_hook( __FILE__, 'bp_follow_deactivate' );