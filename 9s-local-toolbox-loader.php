<?php
/*
Plugin Name: 9seeds Local Toolbox
Plugin URI: https://github.com/9seeds/9s-local-toolbox
Description: A simple plugin to set different defaults for production, staging and local servers. This plugin does nothing critical to site function and can safely be removed. 
Author: Jon Brown
Version: 1.0
Author URI: http://9seeds.com
License: GPLv2+
Original Author: Joe Guilmette.com
Forked from URI: https://github.com/joeguilmette/wp-local-toolbox
*/

$required_php_version = '5.3.0';
if ( version_compare( phpversion(), $required_php_version, '>') ) {
	require_once( __DIR__ . '/9s-local-toolbox/wp-local-toolbox.php');	
}
