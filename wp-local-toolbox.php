<?php
/*
Plugin Name: WP Local Toolbox
Description: A simple plugin to set different defaults for production, staging and local servers.
Author: Joe Guilmette
Version: 1.0
Author URI: http://joeguilmette.com
*/

class WPLT_Disable {
	// Author: Mark Jaquith
	// Author URI: http://coveredwebservices.com/
	static $instance;
	private $disabled = array();

	/**
	 * Sets up the options filter, and optionally handles an array of plugins to disable
	 * @param array $disables Optional array of plugin filenames to disable
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
	 * Adds a filename to the list of plugins to disable
	 */
	public function disable( $file ) {
		$this->disabled[] = $file;
	}

	/**
	 * Hooks in to the option_active_plugins filter and does the disabling
	 * @param array $plugins WP-provided list of plugin filenames
	 * @return array The filtered array of plugin filenames
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

if (defined('WPLT_ENVIRONMENT') && WPLT_ENVIRONMENT ) {

	// Add admin notice
	function environment_notice() {
		$env_text = strtoupper(WPLT_ENVIRONMENT);
		echo "<p id='environment-notice'>$env_text SERVER</p>";
	}

	// Style the admin notice and admin bar on the backend
	function environment_notice_css_admin() {

		if (defined( 'WPLT_COLOR' ) && WPLT_COLOR) {
			$env_color = strtolower(WPLT_COLOR);
		} else {
			$env_color = 'red';
		}

		echo "
		<style type='text/css'>
		#environment-notice {
			float: right;
			padding-right: 15px;
			// padding-top: 5px;		
			margin: 0;
			font-size: 20px;
			font-weight: bold;
			color: $env_color;
		}

		#wpadminbar {
			background-color: $env_color !important;
		}

		</style>
		";
	}

	// Style the admin bar on the front end
	function environment_notice_css_frontend() {

		if (defined( 'WPLT_COLOR' ) && WPLT_COLOR) {
			$env_color = strtolower(WPLT_COLOR);
		} else {
			$env_color = 'red';
		}

		echo "
		<style type='text/css'> #wpadminbar { background-color: $env_color !important;} </style>
		";
	}

	// Add the environment to the admin panel
	add_action( 'admin_notices', 'environment_notice' );

	// Add CSS to admin and wp head
	add_action( 'admin_head', 'environment_notice_css_admin' );
	add_action( 'wp_head', 'environment_notice_css_frontend' );
	if (defined('WPLT_DISABLED_PLUGINS') && WPLT_DISABLED_PLUGINS ) {
		new WPLT_Disable( unserialize (WPLT_DISABLED_PLUGINS) );
	}

	if (strtoupper(WPLT_ENVIRONMENT) != 'LIVE' && strtoupper(WPLT_ENVIRONMENT) != 'PRODUCTION') {
		// EVERYTHING EXCEPT PRODUCTION/LIVE ENVIRONMENT

		// Hide from robots
		add_filter( 'pre_option_blog_public', '__return_zero' );

	} else {
		// PRODUCTION/LIVE ENVIRONMENT

	}
}

?>