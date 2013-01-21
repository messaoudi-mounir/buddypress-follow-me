<?php
/*
 * If you want the users of your component to be able to change the values of your other custom constants,
 * you can use this code to allow them to add new definitions to the wp-config.php file and set the value there.
 *
 *
 * @package BP-Follow-ME
 */
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( file_exists( dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' ) )
	load_textdomain( 'bp-follow', dirname( __FILE__ ) . '/bp-follow/languages/' . get_locale() . '.mo' );

/**
 * Implementation of BP_Component
 *
 * BP_Component is the base class that all BuddyPress components use to set up their basic
 * structure, including global data, navigation elements, and admin bar information. If there's
 * a particular aspect of this class that is not relevant to your plugin, just leave it out.
 *
 * @package BuddyPress_Skeleton_Component
 * @since 1.6
 */
class BP_Follow_Component extends BP_Component {

	/**
	 * Constructor method
	 *
	 * You can do all sorts of stuff in your constructor, but it's recommended that, at the
	 * very least, you call the parent::start() function. This tells the parent BP_Component
	 * to begin its setup routine.
	 *
	 * BP_Component::start() takes three parameters:
	 *   (1) $id   - A unique identifier for the component. Letters, numbers, and underscores
	 *		 only.
	 *   (2) $name - This is a translatable name for your component, which will be used in
	 *               various places through the BuddyPress admin screens to identify it.
	 *   (3) $path - The path to your plugin directory. Primarily, this is used by
	 *		 BP_Component::includes(), to include your plugin's files. See loader.php
	 *		 to see how BP_FOLLOW_PLUGIN_DIR was defined.
	 *
	 * @package BuddyPress_Skeleton_Component
	 * @since 1.6
	 */
	function __construct() {
		global $bp;

		parent::start(
			'follow',
			__( 'Follow', 'bp-follow' ),
			BP_FOLLOW_PLUGIN_DIR
		);

		/**
		 * BuddyPress-dependent plugins are loaded too late to depend on BP_Component's
		 * hooks, so we must call the function directly.
		 */
		 $this->includes();

		/**
		 * Put your component into the active components array, so that
		 *   bp_is_active( 'follow' );
		 * returns true when appropriate. We have to do this manually, because non-core
		 * components are not saved as active components in the database.
		 */
		$bp->active_components[$this->id] = '1';

		/**
		 * Hook the register_post_types() method. If you're using custom post types to store
		 * data (which is recommended), you will need to hook your function manually to
		 * 'init'.
		 */
		//add_action( 'init', array( &$this, 'register_post_types' ) );
	}

	/**
	 * Include your component's files
	 *
	 * BP_Component has a method called includes(), which will automatically load your plugin's
	 * files, as long as they are properly named and arranged. BP_Component::includes() loops
	 * through the $includes array, defined below, and for each $file in the array, it tries
	 * to load files in the following locations:
	 *   (1) $this->path . '/' . $file - For follow, if your $includes array is defined as
	 *           $includes = array( 'notifications.php', 'filters.php' );
	 *       BP_Component::includes() will try to load these files (assuming a typical WP
	 *       setup):
	 *           /wp-content/plugins/bp-follow/notifications.php
	 *           /wp-content/plugins/bp-follow/filters.php
	 *       Our includes function, listed below, uses a variation on this method, by specifying
	 *       the 'includes' directory in our $includes array.
	 *   (2) $this->path . '/bp-' . $this->id . '/' . $file - Assuming the same $includes array
	 *       as above, BP will look for the following files:
	 *           /wp-content/plugins/bp-follow/bp-follow/notifications.php
	 *           /wp-content/plugins/bp-follow/bp-follow/filters.php
	 *   (3) $this->path . '/bp-' . $this->id . '/' . 'bp-' . $this->id . '-' . $file . '.php' -
	 *       This is the format that BuddyPress core components use to load their files. Given
	 *       an $includes array like
	 *           $includes = array( 'notifications', 'filters' );
	 *       BP looks for files at:
	 *           /wp-content/plugins/bp-follow/bp-follow/bp-follow-notifications.php
	 *           /wp-content/plugins/bp-follow/bp-follow/bp-follow-filters.php
	 *
	 * If you'd prefer not to use any of these naming or organizational schemas, you are not
	 * required to use parent::includes(); your own includes() method can require the files
	 * manually. For follow:
	 *    require( $this->path . '/includes/notifications.php' );
	 *    require( $this->path . '/includes/filters.php' );
	 *
	 * Notice that this method is called directly in $this->__construct(). While this step is
	 * not necessary for BuddyPress core components, plugins are loaded later, and thus their
	 * includes() method must be invoked manually.
	 *
	 * Our follow component includes a fairly large number of files. Your component may not
	 * need to have versions of all of these files. What follows is a short description of
	 * what each file does; for more details, open the file itself and see its inline docs.
	 *   - -actions.php       - Functions hooked to bp_actions, mainly used to catch action
	 *			    requests (save, delete, etc)
	 *   - -screens.php       - Functions hooked to bp_screens. These are the screen functions
	 *			    responsible for the display of your plugin's content.
	 *   - -filters.php	  - Functions that are hooked via apply_filters()
	 *   - -classes.php	  - Your plugin's classes. Depending on how you organize your
	 *			    plugin, this could mean: a database query class, a custom post
	 *			    type data schema, and so forth
	 *   - -activity.php      - Functions related to the BP Activity Component. This is where
	 *			    you put functions responsible for creating, deleting, and
	 *			    modifying activity items related to your component
	 *   - -template.php	  - Template tags. These are functions that are called from your
	 *			    templates, or from your screen functions. If your plugin
	 *			    contains its own version of the WordPress Loop (such as
	 *			    bp_follow_has_items()), those functions should go in this file.
	 *   - -functions.php     - Miscellaneous utility functions required by your component.
	 *   - -notifications.php - Functions related to email notification, as well as the
	 *			    BuddyPress notifications that show up in the admin bar.
	 *   - -widgets.php       - If your plugin includes any sidebar widgets, define them in this
	 *			    file.
	 *   - -buddybar.php	  - Functions related to the BuddyBar.
	 *   - -adminbar.php      - Functions related to the WordPress Admin Bar.
	 *   - -cssjs.php	  - Here is where you set up and enqueue your CSS and JS.
	 *   - -ajax.php	  - Functions used in the process of AJAX requests.
	 *
	 * @package BuddyPress_Skeleton_Component
	 * @since 1.6
	 */
	function includes() {

		// Files to include
		$includes = array(
			'includes/bp-follow-actions.php',
			'includes/bp-follow-screens.php',
			'includes/bp-follow-filters.php',
			'includes/bp-follow-classes.php',
			'includes/bp-follow-activity.php',
			'includes/bp-follow-template.php',
			'includes/bp-follow-functions.php',
			'includes/bp-follow-notifications.php',
			'includes/bp-follow-widgets.php',
			'includes/bp-follow-cssjs.php',
			'includes/bp-follow-ajax.php'
		);

		parent::includes( $includes );

		// As an follow of how you might do it manually, let's include the functions used
		// on the WordPress Dashboard conditionally:
		/*
		if ( is_admin() || is_network_admin() ) {
			include( BP_FOLLOW_PLUGIN_DIR . '/includes/bp-follow-admin.php' );
		}
		*/
	}

	/**
	 * Set up your plugin's globals
	 *
	 * Use the parent::setup_globals() method to set up the key global data for your plugin:
	 *   - 'slug'			- This is the string used to create URLs when your component
	 *				  adds navigation underneath profile URLs. For follow,
	 *				  in the URL http://testbp.com/members/boone/follow, the
	 *				  'follow' portion of the URL is formed by the 'slug'.
	 *				  Site admins can customize this value by defining
	 *				  BP_FOLLOW_SLUG in their wp-config.php or bp-custom.php
	 *				  files.
	 *   - 'root_slug'		- This is the string used to create URLs when your component
	 *				  adds navigation to the root of the site. In other words,
	 *				  you only need to define root_slug if your component is a
	 *				  "root component". Eg, in:
	 *				    http://testbp.com/follow/test
	 *				  'follow' is a root slug. This should always be defined
	 *				  in terms of $bp->pages; see the follow below. Site admins
	 *				  can customize this value by changing the permalink of the
	 *				  corresponding WP page in the Dashboard. NOTE:
	 *				  'root_slug' requires that 'has_directory' is true.
	 *   - 'has_directory'		- Set this to true if your component requires a top-level
	 *				  directory, such as http://testbp.com/follow. When
	 *				  'has_directory' is true, BP will require that site admins
	 *				  associate a WordPress page with your component. NOTE:
	 *				  When 'has_directory' is true, you must also define your
	 *				  component's 'root_slug'; see previous item. Defaults to
	 *				  false.
	 *   - 'notification_callback'  - The name of the function that is used to format BP
	 *				  admin bar notifications for your component.
	 *   - 'search_string'		- If your component is a root component (has_directory),
	 *				  you can provide a custom string that will be used as the
	 *				  default text in the directory search box.
	 *   - 'global_tables'		- If your component creates custom database tables, store
	 *				  the names of the tables in a $global_tables array, so that
	 *				  they are available to other BP functions.
	 *
	 * You can also use this function to put data directly into the $bp global.
	 *
	 * @package BuddyPress_Skeleton_Component
	 * @since 1.6
	 *
	 * @global obj $bp BuddyPress's global object
	 */
	function setup_globals() {
		global $bp;

		// Defining the slug in this way makes it possible for site admins to override it
		if ( !defined( 'BP_FOLLOW_SLUG' ) )
			define( 'BP_FOLLOW_SLUG', $this->id );

		// Global tables for the follow component. Build your table names using
		// $bp->table_prefix (instead of hardcoding 'wp_') to ensure that your component
		// works with $wpdb, multisite, and custom table prefixes.
		$global_tables = array(
			'table_name'      => $bp->table_prefix . 'bp_follow'
		);

		// Set up the $globals array to be passed along to parent::setup_globals()
		$globals = array(
			'slug'                  => BP_FOLLOW_SLUG,
			'root_slug'             => isset( $bp->pages->{$this->id}->slug ) ? $bp->pages->{$this->id}->slug : BP_FOLLOW_SLUG,
			'has_directory'         => false, // Set to false if not required
			'notification_callback' => 'bp_follow_format_notifications',
			'search_string'         => __( 'Search Follows...', 'buddypress' ),
			'global_tables'         => $global_tables
		);
		
		// Let BP_Component::setup_globals() do its work.
		parent::setup_globals( $globals );

		// BP 1.2.x only
		if ( version_compare( BP_VERSION, '1.3' ) < 0 ) {
			$bp->{$this->id}->format_notification_function = 'bp_follow_format_notifications';
		}
		
		if ( !defined( 'BP_FOLLOWERS_SLUG' ) )
			define( 'BP_FOLLOWERS_SLUG', 'followers' );

		if ( !defined( 'BP_FOLLOWING_SLUG' ) )
			define( 'BP_FOLLOWING_SLUG', 'following' );

		$bp->{$this->id}->followers->slug = BP_FOLLOWERS_SLUG;
		$bp->{$this->id}->following->slug = BP_FOLLOWING_SLUG;

	}

	/**
	 * Set up your component's navigation.
	 *
	 * The navigation elements created here are responsible for the main site navigation (eg
	 * Profile > Activity > Mentions), as well as the navigation in the BuddyBar. WP Admin Bar
	 * navigation is broken out into a separate method; see
	 * BP_Follow_Component::setup_admin_bar().
	 *
	 * @global obj $bp
	 */
	function setup_nav() {
		// Add 'Follow' to the main navigation
		$main_nav = array(
			'name' 		          => __( 'Follow', 'bp-follow' ),
			'slug' 		          => bp_get_follow_slug(),
			'position' 	          => 80,
			'screen_function'     => 'bp_follow_screen',
			'default_subnav_slug' => 'following'
		);

		$user_domain = bp_is_user() ? bp_displayed_user_domain() : bp_loggedin_user_domain();
		
		$follow_link = trailingslashit( $user_domain . bp_get_follow_slug() );
		// Need to change the user ID, so if we're not on a member page, $counts variable is still calculated
		$user_id = bp_is_user() ? bp_displayed_user_id() : bp_loggedin_user_id();
		$counts  = bp_follow_total_follow_counts( array( 'user_id' => $user_id ) );
		
		$sub_nav[] = array(
			'name'            =>  sprintf( __( 'Following <span>%d</span>', 'bp-follow' ), $counts['following'] ),
			'slug'            => 'following',
			'parent_url'      => $follow_link,
			'parent_slug'     => bp_get_follow_slug(),
			'screen_function' => 'bp_following_screen',
			'position'        => 10
		);

		$sub_nav[] = array(
			'name'            =>  sprintf( __( 'Followers <span>%d</span>', 'bp-follow' ), $counts['followers'] ),
			'slug'            => 'followers',
			'parent_url'      => $follow_link,
			'parent_slug'     => bp_get_follow_slug(),
			'screen_function' => 'bp_followers_screen',
			'position'        => 20
		);

		parent::setup_nav( $main_nav, $sub_nav );


	}


	function setup_admin_bar() {
		global $bp;

		// Prevent debug notices
		$wp_admin_nav = array();

		// Menus for logged in user
		if ( is_user_logged_in() ) {

			$user_domain = bp_is_user() ? bp_displayed_user_domain() : bp_loggedin_user_domain();
			
			$follow_link = trailingslashit( $user_domain . bp_get_follow_slug() );
			$followers_link = trailingslashit( $user_domain . bp_get_follow_slug() . '/' . bp_get_followers_slug() );

			// Need to change the user ID, so if we're not on a member page, $counts variable is still calculated
			$user_id = bp_is_user() ? bp_displayed_user_id() : bp_loggedin_user_id();
			$counts  = bp_follow_total_follow_counts( array( 'user_id' => $user_id ) );
			
			$wp_admin_nav[] = array(
				'parent' => 'my-account-buddypress',
				'id'     => 'my-account-' . bp_get_follow_slug(),
				'title'  => __( 'Follow', 'bp-follow' ),
				'href'   => trailingslashit( $follow_link )
			);
			
			// Add main bp following my places submenu
			$wp_admin_nav[] = array(
				'parent' => 'my-account-' . bp_get_follow_slug(),
				'title'  => sprintf( __( 'Following <span class="count">%d</span>', 'bp-follow' ), $counts['following'] ),
				'href'   => trailingslashit( $follow_link )
			);

			// Add main bp followers my places submenu
			$wp_admin_nav[] = array(
				'parent' => 'my-account-' . bp_get_follow_slug(),
				'title'  => sprintf( __( 'Followers <span class="count">%d</span>', 'bp-follow' ), $counts['followers'] ),
				'href'   => trailingslashit( $followers_link )
			);
		}

		parent::setup_admin_bar( $wp_admin_nav );
	}
}


/**
 * Loads your component into the $bp global
 *
 * This function loads your component into the $bp global. By hooking to bp_loaded, we ensure that
 * BP_Follow_Component is loaded after BuddyPress's core components. This is a good thing because
 * it gives us access to those components' functions and data, should our component interact with
 * them.
 *
 * Keep in mind that, when this function is launched, your component has only started its setup
 * routine. Using print_r( $bp->follow ) or var_dump( $bp->follow ) at the end of this function
 * will therefore only give you a partial picture of your component. If you need to dump the content
 * of your component for troubleshooting, try doing it at bp_init, ie
 *   function bp_follow_var_dump() {
 *   	  global $bp;
 *	  var_dump( $bp->follow );
 *   }
 *   add_action( 'bp_init', 'bp_follow_var_dump' );
 * It goes without saying that you should not do this on a production site!
 *
 * @package BuddyPress_Skeleton_Component
 * @since 1.6
 */
function bp_follow_load_core_component() {
	global $bp;

	$bp->follow = new BP_Follow_Component;
	do_action('bp_follow_load_core_component');
}
// to-test (bp_loaded cause the excution of setup_admin_bar after the loading of the view of the menu)
//add_action( 'bp_init', 'bp_follow_load_core_component' );

add_action( 'bp_loaded', 'bp_follow_load_core_component', 5 );