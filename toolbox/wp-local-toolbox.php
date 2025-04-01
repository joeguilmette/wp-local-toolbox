<?php
/**
 * WP Local Toolbox - Modernized Version
 *
 * @package     WPLT
 * @author      Your Name
 * @license     GPL-2.0+
 * @version     2.0.0
 */

namespace WPLT\Core;

defined('ABSPATH') || exit;

// Define constants with default null values to satisfy static analysis
// These will be overridden by wp-config.php definitions
if (!defined('WPLT_SERVER')) define('WPLT_SERVER', null);
if (!defined('WPLT_NOTIFY')) define('WPLT_NOTIFY', null);
if (!defined('WPLT_AIRPLANE')) define('WPLT_AIRPLANE', null);
if (!defined('WPLT_DISABLED_PLUGINS')) define('WPLT_DISABLED_PLUGINS', null);
if (!defined('WPLT_MEDIA_FROM_PROD_URL')) define('WPLT_MEDIA_FROM_PROD_URL', null);
if (!defined('WPLT_ADMINBAR')) define('WPLT_ADMINBAR', null);
if (!defined('WPLT_ROBOTS')) define('WPLT_ROBOTS', null);
if (!defined('WPLT_COLOR')) define('WPLT_COLOR', null);
if (!defined('WPLT_DISABLE_ATTACHMENT_NOTIFY')) define('WPLT_DISABLE_ATTACHMENT_NOTIFY', null);
if (!defined('WPLT_NOTIFY_CHANNEL')) define('WPLT_NOTIFY_CHANNEL', null);
if (!defined('WPLT_MEDIA_FROM_PROD_START_MONTH')) define('WPLT_MEDIA_FROM_PROD_START_MONTH', null);
if (!defined('WPLT_MEDIA_FROM_PROD_START_YEAR')) define('WPLT_MEDIA_FROM_PROD_START_YEAR', null);
if (!defined('WPLT_MEDIA_FROM_PROD_DIRECTORIES')) define('WPLT_MEDIA_FROM_PROD_DIRECTORIES', null);

final class WP_Local_Toolbox {
    /**
     * Plugin version
     */
    const VERSION = '2.0.0';

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Get singleton instance
     */
    public static function get_instance(): self {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        if (defined('WPLT_SERVER') && WPLT_SERVER) {
            $this->init_server_features();
        }

        if (defined('WPLT_NOTIFY') && WPLT_NOTIFY) {
            $this->init_notifications();
        }

        if (defined('WPLT_AIRPLANE') && WPLT_AIRPLANE) {
            $this->init_airplane_mode();
        }

        if (defined('WPLT_DISABLED_PLUGINS') && WPLT_DISABLED_PLUGINS) {
            $this->init_disabled_plugins();
        }

        if (defined('WPLT_MEDIA_FROM_PROD_URL') && WPLT_MEDIA_FROM_PROD_URL) {
            $this->init_media_from_production();
        }
    }

    /**
     * Initialize server environment features
     */
    private function init_server_features(): void {
        add_action('init', [$this, 'setup_server_features']);
    }

    /**
     * Setup server environment features
     */
    public function setup_server_features(): void {
        if (defined('WPLT_ADMINBAR') && WPLT_ADMINBAR) {
            $this->handle_admin_bar_settings();
        }

        if (is_admin_bar_showing()) {
            add_action('admin_bar_menu', [$this, 'add_environment_notice'], 10);
            add_action('admin_head', [$this, 'add_environment_notice_css']);
            add_action('wp_head', [$this, 'add_environment_notice_css']);
            add_filter('admin_bar_menu', [$this, 'goodbye_howdy'], 25);
        }
    }

    /**
     * Handle admin bar settings
     */
    private function handle_admin_bar_settings(): void {
        $adminbar_setting = strtoupper((string) WPLT_ADMINBAR);

        if ($adminbar_setting === 'FALSE') {
            add_filter('show_admin_bar', '__return_false');
        } elseif ($adminbar_setting === 'TRUE' || $adminbar_setting === 'ALWAYS') {
            add_filter('show_admin_bar', '__return_true');

            if ($adminbar_setting === 'ALWAYS') {
                add_action('admin_bar_menu', [$this, 'add_login_to_adminbar'], 1000);
            }
        }
    }

    /**
     * Add environment notice to admin bar
     */
    public function add_environment_notice(\WP_Admin_Bar $wp_admin_bar): void {
        $env_text = strtoupper((string) WPLT_SERVER);

        if (defined('WPLT_ROBOTS') && WPLT_ROBOTS) {
            $robots = strtoupper((string) WPLT_ROBOTS);

            if ($robots === 'NOINDEX') {
                $env_text .= ' (NOINDEX)';
                add_filter('pre_option_blog_public', '__return_zero');
            } elseif ($robots === 'INDEX') {
                $env_text .= ' (INDEX)';
                add_filter('pre_option_blog_public', '__return_true');
            }
        }

        $wp_admin_bar->add_menu([
            'parent' => 'top-secondary',
            'id' => 'environment-notice',
            'title' => '<span>' . esc_html($env_text) . '</span>',
        ]);
    }

    /**
     * Add CSS for environment notice
     */
    public function add_environment_notice_css(): void {
        $env = strtoupper((string) WPLT_SERVER);
        $env_color = $this->get_environment_color($env);

        echo '<!-- WPLT Admin Bar Notice -->
<style type="text/css">
    #wp-admin-bar-environment-notice>div,
    #wpadminbar { background-color: ' . esc_attr($env_color) . '!important }
    #wp-admin-bar-environment-notice { display: none }
    @media only screen and (min-width:1030px) {
        #wp-admin-bar-environment-notice { display: block }
        #wp-admin-bar-environment-notice>div>span {
            color: #EEE!important;
        }
    }
    #wp-admin-bar-airplane-mode-toggle span.airplane-http-count {
        position: relative;
        display: inline-block;
        width: 21px;
        height: 21px;
        line-height: 21px;
        margin-left: 3px;
        border-radius: 50%;
        border: 2px solid #EEE;
        text-align: center;
    }
    #adminbarsearch:before,
    .ab-icon:before,
    .ab-item:before { color: #EEE!important }
</style>';
    }

    /**
     * Get color for environment
     */
    private function get_environment_color(string $env): string {
        if (defined('WPLT_COLOR') && WPLT_COLOR) {
            return strtolower((string) WPLT_COLOR);
        }

        switch ($env) {
            case 'LIVE':
            case 'PRODUCTION':
                return 'red';
            case 'STAGING':
            case 'TESTING':
                return '#FD9300';
            case 'LOCAL':
            case 'DEV':
                return 'green';
            default:
                return 'red';
        }
    }

    /**
     * Remove "Howdy" text from admin bar
     */
    public function goodbye_howdy(\WP_Admin_Bar $wp_admin_bar): void {
		if (is_user_logged_in()) {
			$my_account = $wp_admin_bar->get_node('my-account');
			if (!$my_account || !isset($my_account->title)) {
				return;
			}
			$newtitle = str_replace('Howdy,', '', $my_account->title);
			$wp_admin_bar->add_node(array(
				'id' => 'my-account',
				'title' => $newtitle,
			));
		}
	}

    /**
     * Add login link to admin bar when not logged in
     */
    public function add_login_to_adminbar(\WP_Admin_Bar $wp_admin_bar): void {
        if (!is_user_logged_in()) {
            $wp_admin_bar->add_menu([
                'id'    => 'wpadminbar',
                'title' => __('Log In'),
                'href' => wp_login_url()
            ]);
        }
    }

    /**
     * Initialize notifications
     */
    private function init_notifications(): void {
        add_action('transition_post_status', [$this, 'handle_post_status_change'], 10, 3);

        if (!(defined('WPLT_DISABLE_ATTACHMENT_NOTIFY') && WPLT_DISABLE_ATTACHMENT_NOTIFY)) {
            add_action('add_attachment', [$this, 'handle_attachment_update'], 1, 1);
        }
    }

    /**
     * Handle post status changes
     */
    public function handle_post_status_change(string $new_status, string $old_status, int $post_id): void {
        if (wp_is_post_revision($post_id)) {
            return;
        }

        if ('publish' === $new_status) {
            $this->send_notification($new_status, $old_status, $post_id);
        }
    }

    /**
     * Handle attachment updates
     */
    public function handle_attachment_update(int $post_id): void {
        $this->send_notification('publish', 'new', $post_id);
    }

    /**
     * Send notification
     */
    private function send_notification(string $new_status, string $old_status, int $post_id): void {
        $post_title = get_the_title($post_id);
        $post_url = get_permalink($post_id);
        $post_type = get_post_type_object(get_post_type($post_id));
        $post_type_name = ucwords($post_type->labels->singular_name);

        $is_new = $this->is_new_post($new_status, $old_status);

        if ($is_new) {
            $title = "New $post_type_name";
            $short_message = "New $post_type_name Added";
            $email_body = "A new " . strtolower($post_type_name) . ", '$post_title' ($post_url), has been published.";
        } else {
            $title = "Updated $post_type_name";
            $short_message = "$post_type_name Updated";
            $email_body = "The " . strtolower($post_type_name) . " '$post_title' ($post_url) has been updated.";
        }

        if (get_post_type($post_id) != 'attachment' && get_the_modified_author($post_id)) {
            $author = " by " . get_the_modified_author($post_id);
            $short_message .= $author;
            $email_body = str_replace('published.', "published$author.", $email_body);
            $email_body = str_replace('updated.', "updated$author.", $email_body);
        }

        $subject = get_bloginfo('name') . ': ' . $short_message;

        if (strpos((string) WPLT_NOTIFY, 'hooks.slack.com') !== false) {
            $this->send_slack_notification($short_message, $post_title, $post_url, $subject);
        } else {
            wp_mail(WPLT_NOTIFY, $subject, $email_body);
        }
    }

    /**
     * Send Slack notification
     */
    private function send_slack_notification(string $short_message, string $post_title, string $post_url, string $subject): void {
        $bot_args = [
            'attachments' => [
                [
                    'fallback' => $short_message,
                    'color' => '#F40101',
                    'author_name' => $short_message,
                    'author_link' => $post_url,
                    'title' => $post_title,
                    'title_link' => $post_url,
                    'text' => $post_url,
                ],
            ],
            'icon_emoji' => ':triangular_flag_on_post:',
            'username' => get_bloginfo('name'),
            'unfurl_links' => true,
        ];

        if (defined('WPLT_NOTIFY_CHANNEL') && WPLT_NOTIFY_CHANNEL) {
            $bot_args['channel'] = WPLT_NOTIFY_CHANNEL;
        }

        wp_remote_post(WPLT_NOTIFY, [
            'body' => ['payload' => json_encode($bot_args)],
            'timeout' => 30,
        ]);
    }

    /**
     * Check if post is new
     */
    private function is_new_post(string $new_status, string $old_status): bool {
        return $new_status === 'publish' && $old_status !== 'publish';
    }

    /**
     * Initialize airplane mode
     */
    private function init_airplane_mode(): void {
        require_once __DIR__ . '/lib/airplane-mode/airplane-mode.php';
        add_action('wp_enqueue_scripts', [$this, 'enqueue_airplane_css'], 99999);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_airplane_css'], 99999);
    }

    /**
     * Enqueue airplane mode CSS
     */
    public function enqueue_airplane_css(): void {
        if (is_admin_bar_showing()) {
            wp_dequeue_style('airplane-mode');
            echo '<!-- WPLT Airplane Mode -->
<style type="text/css">
#wp-admin-bar-airplane-mode-toggle span.airplane-toggle-icon { padding-right: 3px }
#wp-admin-bar-airplane-mode-toggle span.airplane-toggle-icon-on:before { content: "✓" }
#wp-admin-bar-airplane-mode-toggle span.airplane-toggle-icon-off:before { content: "✗" }
.airplane-mode-enabled .plugin-install-php a.upload.add-new-h2,.airplane-mode-enabled .theme-browser.content-filterable.rendered,.airplane-mode-enabled .wp-filter,.airplane-mode-enabled a.browse-themes.add-new-h2{display:none!important}
</style>';
        }
    }

    /**
     * Initialize disabled plugins
     */
    private function init_disabled_plugins(): void {
        require_once __DIR__ . '/inc/WPLT_Disable_Plugins.php';
        $disabled_plugins = [];

        if (!defined('WPLT_DISABLED_PLUGINS') || empty(WPLT_DISABLED_PLUGINS)) {
            new \WPLT_Disable_Plugins($disabled_plugins);
            return;
        }

        // Ensure we're working with a string
        $plugins_config = is_string(WPLT_DISABLED_PLUGINS) ? WPLT_DISABLED_PLUGINS : '';

        if (!empty($plugins_config)) {
            // Use json_decode instead of unserialize for security
            $decoded = json_decode($plugins_config, true);

            // Only accept if it's a non-empty array
            if (is_array($decoded) && !empty($decoded)) {
                $disabled_plugins = $decoded;
            }
        }

        new \WPLT_Disable_Plugins($disabled_plugins);
    }

    /**
     * Initialize media from production
     */
    private function init_media_from_production(): void {
        require_once __DIR__ . '/lib/BE-Media-from-Production/be-media-from-production.php';

        add_filter('be_media_from_production_url', function($url) {
            return WPLT_MEDIA_FROM_PROD_URL;
        });

        if (defined('WPLT_MEDIA_FROM_PROD_START_MONTH') && WPLT_MEDIA_FROM_PROD_START_MONTH) {
            add_filter('be_media_from_production_start_month', function($month) {
                return WPLT_MEDIA_FROM_PROD_START_MONTH;
            });
        }

        if (defined('WPLT_MEDIA_FROM_PROD_START_YEAR') && WPLT_MEDIA_FROM_PROD_START_YEAR) {
            add_filter('be_media_from_production_start_year', function($year) {
                return WPLT_MEDIA_FROM_PROD_START_YEAR;
            });
        }

        if (defined('WPLT_MEDIA_FROM_PROD_DIRECTORIES') && WPLT_MEDIA_FROM_PROD_DIRECTORIES) {
            add_filter('be_media_from_production_directories', function($directories) {
                return WPLT_MEDIA_FROM_PROD_DIRECTORIES;
            });
        }
    }
}

// Initialize the plugin
\WPLT\Core\WP_Local_Toolbox::get_instance();
