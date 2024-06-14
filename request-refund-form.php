<?php
/*
 * Plugin Name: Request Refund Form
 * Plugin URI: https://github.com/prosperemebo
 * Description: Add a form to request refunds
 * Version: 1.0
 * Requires at least: 5.2
 * Requires PHP: 5.2
 * Author: Prosper Emebo
 * Author URI: https://github.com/prosperemebo
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'api/RefundRequestApi.php';

/**
 * Class RequestRefundForm
 * Handles the main functionality of the Request Refund Form plugin.
 */
class RequestRefundForm
{
	/**
	 * RequestRefundForm constructor.
	 * Initializes the plugin by setting up hooks and actions.
	 */
	public function __construct()
	{
		add_action('init', array($this, 'my_init'));
		register_activation_hook(__FILE__, array($this, 'create_table'));
		register_deactivation_hook(__FILE__, array($this, 'delete_table'));
	}

	/**
	 * Initializes the plugin by adding shortcodes, enqueueing assets, and instantiating the RefundRequestsAPI class.
	 */
	public function my_init()
	{
		add_shortcode('my_form', array($this, 'my_shortcode_form'));
		add_shortcode('my_list', array($this, 'my_shortcode_list'));
		add_action('wp_enqueue_scripts', array($this, 'load_assets'));

		new RefundRequestsAPI();
	}

	/**
	 * Retrieves data from the refund requests table.
	 *
	 * @return array The refund requests data.
	 */
	public function get_my_table_data()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'refund_requests';
		$results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
		return $results;
	}

	/**
	 * Enqueues the plugin's CSS and JavaScript assets.
	 */
	public function load_assets()
	{
		wp_enqueue_style(
			'request-refund-form-styles',
			plugin_dir_url(__FILE__) . 'assets/index.css',
			array(),
			1,
			'all'
		);

		wp_enqueue_script(
			'request-refund-form-scripts',
			plugin_dir_url(__FILE__) . 'assets/index.js',
			array(),
			1,
			true
		);

		wp_localize_script(
			'request-refund-form-scripts',
			'refundRequestData',
			array(
				'requestUrl' => get_rest_url(null, 'refund-requests/v1/submit'),
				'nonce' => wp_create_nonce('wp_rest')
			)
		);
	}

	/**
	 * Creates the refund requests table in the WordPress database.
	 */
	public function create_table()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'refund_requests';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            order_number varchar(255) NOT NULL,
            receipt_url varchar(255) NOT NULL,
            reason text NOT NULL,
            request_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	/**
	 * Deletes the refund requests table from the WordPress database.
	 */
	public function delete_table()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'refund_requests';
		$sql = "DROP TABLE IF EXISTS $table_name;";
		$wpdb->query($sql);
	}

	/**
	 * Renders the refund request form.
	 *
	 * @return string The HTML markup of the refund request form.
	 */
	public function my_shortcode_form()
	{
		ob_start();
		include plugin_dir_path(__FILE__) . 'templates/form-template.php';
		return ob_get_clean();
	}

	/**
	 * Renders the refund requests list.
	 *
	 * @return string The HTML markup of the refund requests list.
	 */
	public function my_shortcode_list()
	{
		ob_start();
		include plugin_dir_path(__FILE__) . 'templates/list-template.php';
		return ob_get_clean();
	}
}

new RequestRefundForm();
