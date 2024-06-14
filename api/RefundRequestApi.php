<?php

/**
 * Class RefundRequestsAPI
 * Handles the refund request API endpoints.
 */
class RefundRequestsAPI
{
	/**
	 * RefundRequestsAPI constructor.
	 * Initializes the class and registers the API routes.
	 */
	public function __construct()
	{
		add_action('rest_api_init', array($this, 'register_routes'));
	}

	/**
	 * Registers the refund request API routes.
	 */
	public function register_routes()
	{
		register_rest_route('refund-requests/v1', '/submit', array(
			'methods' => 'POST',
			'callback' => array($this, 'handle_insert_request'),
			'permission_callback' => '__return_true'
		));

		register_rest_route('refund-requests/v1', '/list', array(
			'methods' => 'GET',
			'callback' => array($this, 'handle_select_request'),
			'permission_callback' => '__return_true'
		));
	}

	/**
	 * Handles the insertion of a refund request.
	 *
	 * @param WP_REST_Request $request The REST request object.
	 * @return WP_Error|WP_REST_Response The response object or error object.
	 */
	public function handle_insert_request(WP_REST_Request $request)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'refund_requests';

		$nonce = $request->get_header('X-WP-Nonce');

		if (!wp_verify_nonce($nonce, 'wp_rest')) {
			return new WP_REST_Response('Session is expired', 422);
		}

		$name = sanitize_text_field($request->get_param('name'));
		$email = sanitize_email($request->get_param('email'));
		$order_number = sanitize_text_field($request->get_param('orderNumber'));
		$reason = sanitize_textarea_field($request->get_param('reason'));

		// Validate input fields
		if (empty($name)) {
			return new WP_Error('invalid_name', 'Name is required.', array('status' => 400));
		}

		if (empty($email) || !is_email($email)) {
			return new WP_Error('invalid_email', 'A valid email address is required.', array('status' => 400));
		}

		if (empty($order_number)) {
			return new WP_Error('invalid_order_number', 'Order number is required.', array('status' => 400));
		}

		if (empty($reason)) {
			return new WP_Error('invalid_reason', 'Reason for refund is required.', array('status' => 400));
		}

		// Handle file upload
		if (!function_exists('wp_handle_upload')) {
			require_once(ABSPATH . 'wp-admin/includes/file.php');
		}

		$uploadedfile = $_FILES['receipt'];
		$upload_overrides = array('test_form' => false);

		$movefile = wp_handle_upload($uploadedfile, $upload_overrides);

		if ($movefile && !isset($movefile['error'])) {
			$receipt_url = $movefile['url'];
		} else {
			return new WP_Error('upload_error', 'There was an error uploading the file.', array('status' => 500));
		}

		// Insert the refund request into the database
		$result = $wpdb->insert(
			$table_name,
			array(
				'name' => $name,
				'email' => $email,
				'order_number' => $order_number,
				'receipt_url' => $receipt_url,
				'reason' => $reason,
			)
		);

		if ($result === false) {
			return new WP_Error('db_insert_error', 'There was an error inserting the data.', array('status' => 500));
		}

		return new WP_REST_Response('Your refund request has been submitted.', 200);
	}

	/**
	 * Handles the selection of refund requests.
	 *
	 * @param WP_REST_Request $request The REST request object.
	 * @return WP_Error|WP_REST_Response The response object or error object.
	 */
	public function handle_select_request(WP_REST_Request $request)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'refund_requests';

		// Retrieve all refund requests from the database
		$results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

		if (empty($results)) {
			return new WP_Error('no_refund_requests', 'No refund requests found.', array('status' => 404));
		}

		$results_count = count($results);

		$response = array(
			"status" => "success",
			"message" => "Found " . $results_count . " requests.",
			"length" => $results_count,
			"data" => $results,
		);

		return new WP_REST_Response($response, 200);
	}
}

new RefundRequestsAPI();
