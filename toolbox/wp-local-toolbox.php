<?php

if (defined('WPLT_SERVER') && WPLT_SERVER) {
	/*
	You can edit this to do certain things depending on your how you've
	defined the WPLT_SERVER constant. This can be very useful if
	you want to perform certain actions depending on which server you're
	using.

	If you come up with something cool I'd love a pull request!
	 */
	if (strtoupper(WPLT_SERVER) != 'LIVE' && strtoupper(WPLT_SERVER) != 'PRODUCTION') {
		/**
		 * Everything except PRODUCTION/LIVE Environment
		 *
		 * Hide from robots
		 */
		add_filter('pre_option_blog_public', '__return_zero');

	} else {
		/**
		 * PRODUCTION/LIVE Environment
		 */
	}

/**
 * =======================================
 * ===============Admin Bar===============
 * =======================================
 */
	function environment_notice() {
		$env_text = strtoupper(WPLT_SERVER);

		$admin_notice = array(
			'parent' => 'top-secondary', /** puts it on the right side. */
			'id' => 'environment-notice',
			'title' => '<span>' . $env_text . ' SERVER</span>',
		);
		global $wp_admin_bar;
		$wp_admin_bar->add_menu($admin_notice);
	}

	/**
	 * Style the admin bar
	 */
	function environment_notice_css() {

		if (defined('WPLT_COLOR') && WPLT_COLOR) {
			$env_color = strtolower(WPLT_COLOR);
		} else {
			$env = strtoupper(WPLT_SERVER);

			if ($env == 'LIVE' or $env == 'PRODUCTION') {
				$env_color = 'red';

			} elseif ($env == 'STAGING' or $env == 'TESTING') {
				$env_color = '#FD9300';

			} elseif ($env == 'LOCAL' or $env == 'DEV') {
				$env_color = 'green';

			} else {
				$env_color = 'red';
			}

		}
		/**
		 * Some nice readable CSS so no one wonder's what's going on
		 * when inspecting the head. I think it's best to just jack
		 * these styles into the head and not bother loading another
		 * stylesheet.
		 */
		echo "
<!-- WPLT Admin Bar Notice -->
<style type='text/css'>
	#wp-admin-bar-environment-notice>div,
	#wpadminbar { background-color: $env_color!important }
	#wp-admin-bar-environment-notice { display: none }
	@media only screen and (min-width:1030px) {
	    #wp-admin-bar-environment-notice { display: block }
	    #wp-admin-bar-environment-notice>div>span {
	        color: #EEE!important;
	        font-size: 20px!important;
	    }
	}
	#adminbarsearch:before,
	.ab-icon:before,
	.ab-item:before { color: #EEE!important }
</style>";
	}

	/**
	 * Literally cannot even
	 */
	function goodbye_howdy($wp_admin_bar) {
		if (is_user_logged_in()) {
			$my_account = $wp_admin_bar->get_node('my-account');
			$newtitle = str_replace('Howdy,', '', $my_account->title);
			$wp_admin_bar->add_node(array(
				'id' => 'my-account',
				'title' => $newtitle,
			));
		}
	}

	function wplt_server_init() {

		/**
		 * Control the frontend admin bar
		 */
		if (defined('WPLT_ADMINBAR') && WPLT_ADMINBAR) {
			if (strtoupper(WPLT_ADMINBAR) == 'FALSE') {
				add_filter('show_admin_bar', '__return_false');
			} elseif (strtoupper(WPLT_ADMINBAR) == 'TRUE' or strtoupper(WPLT_ADMINBAR) == 'ALWAYS') {
				add_filter('show_admin_bar', '__return_true');
			}
			if (strtoupper(WPLT_ADMINBAR) == 'ALWAYS') {
				/**
				 * @author Jeff Star (https://twitter.com/perishable)
				 * @link http://digwp.com/2011/04/admin-bar-tricks/
				 */
				function always_show_adminbar($wp_admin_bar) {
					if (!is_user_logged_in()) {
						$wp_admin_bar->add_menu(array(
							'id'    => 'wpadminbar',
							'title' => __('Log In'),
							'href' => wp_login_url()
						));
					}
				}
				add_action('admin_bar_menu', 'always_show_adminbar');
				add_filter('show_admin_bar', '__return_true', 1000);
			}
		}

		if (is_admin_bar_showing()) {
			/**
			 * Add the environment to the admin panel
			 */
			add_action('admin_bar_menu', 'environment_notice');

			/**
			 * Add CSS to admin and wp head
			 */
			add_action('admin_head', 'environment_notice_css');
			add_action('wp_head', 'environment_notice_css');

			/**
			 * Cannot. Even.
			 */
			add_filter('admin_bar_menu', 'goodbye_howdy', 25);
		}
	}
	add_action('init', 'wplt_server_init');
}

/**
 * =======================================
 * =============Notifications=============
 * =======================================
 */

if (defined('WPLT_NOTIFY') && WPLT_NOTIFY) {
	function notify_on_post_update($new_status, $old_status, $post_id) {

		/**
		 * Not a post revision
		 */
		if (wp_is_post_revision($post_id)) {
			return;
		}

		/**
		 * And only if it's published
		 */
		if (get_post_status($post_id) == 'publish') {
			/**
			 * Only tell us about the author if he has a name.
			 */
			if (get_the_modified_author($post_id) != null) {
				$author = " by " . get_the_modified_author($post_id);
			}
			$post_title = get_the_title($post_id);
			$post_url = get_permalink($post_id);

			/**
			 * Building the subject and body depending on
			 * post status transition.
			 */
			if (is_new_post($new_status,$old_status)) {
				$subject = get_bloginfo('name') . ': A new post has been published';
				$message = "A new post, '" . $post_title . "' (" . $post_url . "), has been published" . $author . ".";
			} else {
				$subject = get_bloginfo('name') . ': A post has been updated';
				$message = "The post '" . $post_title . "' (" . $post_url . ") has been updated" . $author . ".";
			}

			/**
			 * Send email to admin.
			 */
			wp_mail(WPLT_NOTIFY, $subject, $message);
		}
	}

	/** 
	 * Detect if this is a new post or not
	 */
	function is_new_post( $new_status, $old_status ) {
		$published = false;
		if ( $new_status === 'publish' && $old_status !== 'publish' ) {
			$published = true;
		}
		return $published;
	}

	/** 
	 * Send email when a post status changes
	 */
	add_action( 'transition_post_status', 'notify_on_post_update', 10, 3 );
}

/**
 * =======================================
 * =============Airplane Mode=============
 * =======================================
 */

if (defined('WPLT_AIRPLANE') && WPLT_AIRPLANE) {

	if (!defined('AIRMDE_BASE ')) {
		define('AIRMDE_BASE', plugin_basename(__FILE__));
	}
	if (!defined('AIRMDE_DIR')) {
		define('AIRMDE_DIR', plugin_dir_path(__FILE__));
	}
	if (!defined('AIRMDE_VER')) {
		define('AIRMDE_VER', '0.0.1');
	}

	/**
	 * Include Airplane_Mode_Core Class
	 */
	require_once __DIR__ . '/inc/WPLT_Airplane_Mode_Core.php';
	$Airplane_Mode_Core = WPLT_Airplane_Mode_Core::getInstance();

	function wplt_airplane_css() {
		if (is_admin_bar_showing()) {

			/**
			 * Some nice readable CSS so no one wonder's what's going on
			 * when inspecting the head. I think it's best to just jack
			 * these styles into the head and not bother loading another
			 * stylesheet.
			 */
			echo "
<!-- WPLT Airplane Mode -->
<style type='text/css'>
	#wp-admin-bar-airplane-mode-toggle span.airplane-toggle-icon { padding-right: 3px }
	#wp-admin-bar-airplane-mode-toggle span.airplane-toggle-icon-on:before { content: '✓' }
	#wp-admin-bar-airplane-mode-toggle span.airplane-toggle-icon-off:before { content: '✗' }
</style>";
		}
	}

	add_action('wp_head', 'wplt_airplane_css');
	add_action('admin_head', 'wplt_airplane_css');
}

/**
 * =======================================
 * ===========Disabled Plugins============
 * =======================================
 */
if (defined('WPLT_DISABLED_PLUGINS') && WPLT_DISABLED_PLUGINS) {

	/**
	 * Include
	 */
	require_once __DIR__ . '/inc/WPLT_Disable_Plugins.php';
	new WPLT_Disable_Plugins(unserialize(WPLT_DISABLED_PLUGINS));
}
