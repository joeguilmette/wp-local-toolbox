<?php
/**
 * Plugin disabling engine class
 * Author: Mark Jaquith
 * Author URI: http://markjaquith.com/
 * Plugin URI: https://gist.github.com/markjaquith/1044546
 * Using fork: https://gist.github.com/Rarst/4402927
 */

class WPLT_Disable_Plugins {
	static $instance;
	private $disabled = array();

	/**
	 * Sets up the options filter, and optionally handles an array of plugins to disable
	 * @param array $disables Optional array of plugin filenames to disable
	 */
	public function __construct( Array $disables = NULL) {
		/**
		 * Handle what was passed in
		 */
		if ( is_array( $disables ) ) {
			foreach ( $disables as $disable )
				$this->disable( $disable );
		}

		/**
		 * Add the filters
		 */
		add_filter( 'option_active_plugins', array( $this, 'do_disabling' ) );
		add_filter( 'site_option_active_sitewide_plugins', array( $this, 'do_network_disabling' ) );

		/**
		 * Allow other plugins to access this instance
		 */
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
	
	/**
	 * Hooks in to the site_option_active_sitewide_plugins filter and does the disabling
	 *
	 * @param array $plugins
	 *
	 * @return array
	 */
	public function do_network_disabling( $plugins ) {

		if ( count( $this->disabled ) ) {
			foreach ( (array) $this->disabled as $plugin ) {

				if( isset( $plugins[$plugin] ) )
					unset( $plugins[$plugin] );
			}
		}

		return $plugins;
	}
}