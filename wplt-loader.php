<?php
/*
Plugin Name: WP Local Toolbox
Description: A simple plugin to set different defaults for production, staging and local servers.
Author: Joe Guilmette
Version: 1.3.1
Author URI: http://joeguilmette.com
Plugin URI: https://github.com/joeguilmette/wp-local-toolbox
License: GPLv2+
Text Domain: wp-local-toolbox
 */

$required_php_version = '7.0.0';
if (version_compare(phpversion(), $required_php_version, '>')) {
	require_once __DIR__ . '/toolbox/wp-local-toolbox.php';
}
