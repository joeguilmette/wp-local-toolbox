<?php
/*
Plugin Name: WP Local Dev Suite
Description: A simple plugin to set different defaults for local, staging and production servers.
Author: Joe Guilmette
Version: .1
Author URI: http://joeguilmette.com
*/

/*
We'll get $env from wp-config.php
Then we'll choose a color and an admin notice based on $env
Then we'll generate the admin notice and CSS based on $env

Then we'll set the robots based on $env
*/


add_action( 'admin_notices', 'hello_dolly' );

function environment_notice($env) {
	echo "<p id='dolly'>$env</p>";
}

function admin_css($env) {

	echo "
	<style type='text/css'>
	.local-dev .notice {
		float: left;
		padding-left: 15px;
		padding-top: 5px;		
		margin: 0;
		font-size: 15px;
		font-weight: bold;
		color: $env;
	}

	</style>
	";
}

add_action( 'admin_head', 'admin_css' );

?>