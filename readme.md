#WP Local Toolbox

Through constants defined in wp-config, you can disable plugins, set search engine visibility, display the server name and change the color of the admin bar, or literally anything else you can think of.

This is an invaluable tool if you often work in production, staging, and local servers at the same time. 

WP Local Toolbox uses three constants defined in wp-config.php:

* **WPLT_SERVER**: This is the name of your server environment. It will be displayed in the admin bar at browser widths greater than 1030px. If left undefined, the plugin will make no changes to the admin bar. 

	If not defined as 'PRODUCTION' or 'LIVE', the plugin will enable 'Discourage search engines from indexing this site' to prevent your development and staging servers from being indexed. This option is not stored in the database, so your production server will still look to the actual setting on the Reading page.

* **WPLT_COLOR**: This determines the color of the admin bar. You can set this to any CSS color. If left undefined, will use the following defaults: 
	
	* Production / Live: red
	* Staging / Testing: orange
	* Local / Dev: green

* **WPLT_DISABLED_PLUGINS**: An array of plugins to disable. This does not store any data in the database, so plugins that are manually deactivated or activated will stay so when undefined in this constant.

* **WPLT_AIRPLANE**: Control loading of external files when developing locally. WP loads certain external files (fonts, gravatar, etc) and makes external HTTP calls. This isn't usually an issue, unless you're working in an evironment without a web connection. This plugin removes / unhooks those actions to reduce load time and avoid errors due to missing files.

##Modification

You can add code that will be executed depending on server name by modifying the following in wp-local-toolbox.php.

I'd love a pull request if you come up with something useful.

```
if (strtoupper(WPLT_SERVER) != 'LIVE' && strtoupper(WPLT_SERVER) != 'PRODUCTION') {
	// Everything except PRODUCTION/LIVE SERVER

	// Hide from robots
	add_filter( 'pre_option_blog_public', '__return_zero' );

} else {
	// PRODUCTION/LIVE SERVER

}
```

##Example

```
define('WPLT_SERVER', 'local');

define('WPLT_COLOR', 'purple');

define('WPLT_DISABLED_PLUGINS', serialize(array( 'w3-total-cache/w3-total-cache.php', 'updraftplus/updraftplus.php', 'nginx-helper/nginx-helper.php', 'wpremote/plugin.php' )));

define('WPLT_AIRPLANE', 'true');
```

##Notes

As a special thank you, this plugin will remove the ridiculous `Howdy, ` that is prepended to the username in the admin bar.

You're welcome.

##Credit

* Plugin disabling from Mark Jaquith: https://gist.github.com/markjaquith/1044546
	Using this fork: https://gist.github.com/Rarst/4402927

* Airplane Mode from Andrew Norcross: https://github.com/norcross/airplane-mode