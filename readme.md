#WP Local Toolbox

A simple plugin to set different defaults for production, staging, and local servers.

WP Local Toolbox uses three constants defined in wp-config.php:

* **WPLT_ENVIRONMENT**: This is the name of your environment. If left undefined, the plugin will do nothing. 

	If not defined as 'PRODUCTION' or 'LIVE', the plugin will enable 'Discourage search engines from indexing this site' to prevent your development and staging servers from being indexed. This option is not stored in the database, so your production server will still look to the actual setting on the Reading page.

* **WPLT_COLOR**: This determines the color of the admin bar. You can set this to any CSS color. Will default to red if left undefined.

* **WPLT_DISABLED_PLUGINS**: An array of plugins to disable. This does not store any data in the database, so plugins that are manually deactivated or activated will stay so when undefined in this constant.

##Modification

You can add code that will be executed depending on server environment by modifying the following in wp-local-toolbox.php.

I'd love a pull request if you come up with something useful.

```
if (strtoupper(WPLT_ENVIRONMENT) != 'LIVE' && strtoupper(WPLT_ENVIRONMENT) != 'PRODUCTION') {
	// Everything except PRODUCTION/LIVE Environment

	// Hide from robots
	add_filter( 'pre_option_blog_public', '__return_zero' );

} else {
	// PRODUCTION/LIVE Environment

}
```

##Example

```
define('WPLT_ENVIRONMENT', 'local');

define('WPLT_COLOR', 'green');

define('WPLT_DISABLED_PLUGINS', serialize(array( 'w3-total-cache/w3-total-cache.php', 'updraftplus/updraftplus.php', 'nginx-helper/nginx-helper.php', 'wpremote/plugin.php' )));
```

##Credit
Plugin disabling from Mark Jaquith: https://gist.github.com/markjaquith/1044546