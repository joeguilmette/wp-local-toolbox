#WP Local Toolbox

Through constants defined in wp-config, you can disable plugins, set search engine visibility, display the server name and change the color of the admin bar.

This is an invaluable tool if you often work in production, staging, and local servers at the same time. 

WP Local Toolbox uses three constants defined in wp-config.php:

* **WPLT_SERVER**: This is the name of your server environment. If left undefined, the plugin will make no changes to the admin bar, but will still deactivate plugins as desired. 

	If not defined as 'PRODUCTION' or 'LIVE', the plugin will enable 'Discourage search engines from indexing this site' to prevent your development and staging servers from being indexed. This option is not stored in the database, so your production server will still look to the actual setting on the Reading page.

* **WPLT_COLOR**: This determines the color of the admin bar. You can set this to any CSS color. Will default to red if left undefined.

* **WPLT_DISABLED_PLUGINS**: An array of plugins to disable. This does not store any data in the database, so plugins that are manually deactivated or activated will stay so when undefined in this constant.

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

define('WPLT_COLOR', 'green');

define('WPLT_DISABLED_PLUGINS', serialize(array( 'w3-total-cache/w3-total-cache.php', 'updraftplus/updraftplus.php', 'nginx-helper/nginx-helper.php', 'wpremote/plugin.php' )));
```

##Notes

As a special thank you, this plugin will remove the ridiculous `Howdy, ` that is prepended to the username in the admin bar.

You're welcome.

##Credit
Plugin disabling from Mark Jaquith: https://gist.github.com/markjaquith/1044546