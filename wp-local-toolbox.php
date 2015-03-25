<?php
/*
Plugin Name: WP Local Toolbox
Description: A simple plugin to set different defaults for production, staging and local servers.
Author: Joe Guilmette
Version: 1.0
Author URI: http://joeguilmette.com
*/

if (defined('WPLT_SERVER') && WPLT_SERVER ) {
	/*
	You can edit this to do certain things depending on your how you've
	defined the WPLT_SERVER constant. This can be very useful if
	you want to perform certain actions depending on which server you're
	using. 

	If you come up with something cool I'd love a pull request!
	*/ 
	if (strtoupper(WPLT_SERVER) != 'LIVE' && strtoupper(WPLT_SERVER) != 'PRODUCTION') {
		// Everything except PRODUCTION/LIVE Environment

		// Hide from robots
		add_filter( 'pre_option_blog_public', '__return_zero' );

	} else {
		// PRODUCTION/LIVE Environment

	}

	// Add admin notice
	function environment_notice() {
		$env_text = strtoupper(WPLT_SERVER);

		$admin_notice = array(
			'parent'	=> 'top-secondary', // puts it on the right side.
			'id'		=> 'environment-notice',
			'title'		=> '<span>'.$env_text.' SERVER</span>',
		);
		global $wp_admin_bar;
		$wp_admin_bar->add_menu($admin_notice);
	}

	// Style the admin bar
	function environment_notice_css() {

		if (defined( 'WPLT_COLOR' ) && WPLT_COLOR) {
			$env_color = strtolower(WPLT_COLOR);
		} else {
			$env = strtoupper(WPLT_SERVER);

			if ($env == 'LIVE' or $env == 'PRODUCTION') {
				$env_color = 'red';

			} elseif ($env == 'STAGING' or $env == 'TESTING') {
				$env_color = '#FD9300';

			} elseif ($env == 'LOCAL' or $env == 'DEVELOPMENT') {
				$env_color = 'green';
				
			} else {
				$env_color = 'red';
			}
			
		}

		echo "
		<style type='text/css'>#wp-admin-bar-environment-notice>div,#wpadminbar{background-color:$env_color!important}#wp-admin-bar-environment-notice{display:none}@media only screen and (min-width:1030px){#wp-admin-bar-environment-notice{display:block}#wp-admin-bar-environment-notice>div>span{color:#EEEFE6!important;font-size:20px!important}}#adminbarsearch:before,.ab-icon:before,.ab-item:before{color:#EEEFE6!important}</style>";
	}

	// Add the environment to the admin panel
	add_action( 'admin_bar_menu', 'environment_notice' );

	// Add CSS to admin and wp head
	add_action( 'admin_head', 'environment_notice_css' );
	add_action( 'wp_head', 'environment_notice_css' );

	//I literally can't even
	function goodbye_howdy( $wp_admin_bar ) {
		$my_account=$wp_admin_bar->get_node('my-account');
		$newtitle = str_replace( 'Howdy,', '', $my_account->title );
		$wp_admin_bar->add_node( array(
			'id' => 'my-account',
			'title' => $newtitle,
		) );
	}
	add_filter( 'admin_bar_menu', 'goodbye_howdy',25 );
}

// Disable plugins regardless of environment
if (defined('WPLT_DISABLED_PLUGINS') && WPLT_DISABLED_PLUGINS ) {
	new WPLT_DISABLE( unserialize (WPLT_DISABLED_PLUGINS) );
}

// Plugin disabling engine
class WPLT_DISABLE {
	// Author: Mark Jaquith
	// Author URI: http://coveredwebservices.com/
	static $instance;
	private $disabled = array();

	/**
	 * Sets up the options filter, and optionally handles an array of plugins to disable
	 * @param array $disables Optional array of plugin fileSERVERs to disable
	 */
	public function __construct( Array $disables = NULL) {
		// Handle what was passed in
		if ( is_array( $disables ) ) {
			foreach ( $disables as $disable )
				$this->disable( $disable );
		}

		// Add the filter
		add_filter( 'option_active_plugins', array( $this, 'do_disabling' ) );

		// Allow other plugins to access this instance
		self::$instance = $this;
	}

	/**
	 * Adds a fileSERVER to the list of plugins to disable
	 */
	public function disable( $file ) {
		$this->disabled[] = $file;
	}

	/**
	 * Hooks in to the option_active_plugins filter and does the disabling
	 * @param array $plugins WP-provided list of plugin fileSERVERs
	 * @return array The filtered array of plugin fileSERVERs
	 */
	public function do_disabling( $plugins ) {
		if ( count( $this->disabled ) ) {
			foreach ( (array) $this->disabled as $plugin ) {
				$key = array_search( $plugin, $plugins );
				if ( false !== $key )
					unset( $plugins[$key] );
			}
		}
		return $plugins;
	}
}

?>