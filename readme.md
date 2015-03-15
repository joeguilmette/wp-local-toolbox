#WP Local Toolbox Readme

A simple plugin to set different defaults for production, staging and local servers.

WP Local Toolbox uses three constants defined in wp-config.php:

* **WPLT_ENVIRONMENT**: This is the name of your environment. If left undefined, the plugin will do nothing. 

* * If defined as 'PRODUCTION' or 'LIVE', the plugin will display the environment as an admin notice on the admin panel and modify the color of the admin bar. 

* * If defined as anything else, it will also enable 'Discourage search engines from indexing this site' to prevent your development site from being indexed, and it will disable any plugins defined in WPLT_DISABLED_PLUGINS.

* **WPLT_COLOR**: This determines the color of the admin bar. You can set this to any CSS color. Will default to red if left undefined.

* **WPLT_DISABLED_PLUGINS**: An array of plugins to disable in everything except your production/live environment.


##Example:

define( 'WPLT_ENVIRONMENT', 'local');
define( 'WPLT_COLOR', 'green');
define ('WPLT_DISABLED_PLUGINS', serialize ( array( 'w3-total-cache/w3-total-cache.php', 'updraftplus/updraftplus.php', 'nginx-helper/nginx-helper.php', 'wpremote/plugin.php' ) ));