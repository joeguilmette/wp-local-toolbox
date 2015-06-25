<?php
/*
Plugin Name: WP Local Toolbox
Description: A simple plugin to set different defaults for production, staging and local servers.
Author: Joe Guilmette
Version: 1.2.3
Author URI: http://joeguilmette.com
Plugin URI: https://github.com/joeguilmette/wp-local-toolbox
License: GPLv2+
 */

$required_php_version = '5.3.0';
if (version_compare(phpversion(), $required_php_version, '>')) {
	require_once __DIR__ . '/toolbox/wp-local-toolbox.php';
}
