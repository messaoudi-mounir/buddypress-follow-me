<?php

/**
 * BuddyPress - Users Home
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

get_header( 'buddypress' ); ?>

	<div id="content">
		<div class="padder">

			<?php do_action( 'bp_before_member_home_content' ); ?>

			<div id="item-header" role="complementary">

				<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>

			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
					<ul>

						<?php bp_get_displayed_user_nav(); ?>

						<?php do_action( 'bp_member_options_nav' ); ?>

					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body">

				<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
							
					<ul class="nav nav-tabs">
					
						<?php do_action( 'bp_follow_directory_follow_filter' ); ?>		
						<?php bp_get_options_nav(); ?>
					</ul>
								
				</div><!-- .item-list-tabs -->

				<?php do_action( 'bp_before_member_body' ); ?>
				
				<div id="follow-dir-list" class="follow dir-list">
				
				<?php bp_follow_load_sub_template( array('members/single/follow/follow-loop.php') ); ?>
					
				</div><!-- #follows-dir-list -->

				<?php do_action( 'bp_after_member_body' ); ?>

			</div><!-- #item-body -->

			<?php do_action( 'bp_after_member_home_content' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

<?php get_sidebar( 'buddypress' ); ?>
<?php get_footer( 'buddypress' ); ?>