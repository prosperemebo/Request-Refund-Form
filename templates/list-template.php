<?php
if (!defined('ABSPATH')) {
	exit;
}


global $wpdb;
$table_name = $wpdb->prefix . 'refund_requests';

$results = $wpdb->get_results("SELECT * FROM $table_name");

if ($results) :
?>
	<div id="refund-request-list">
		<div class="refund-request-head">
			<div class="refund-request-col sm">SN</div>
			<div class="refund-request-col">Name</div>
			<div class="refund-request-col">Email</div>
			<div class="refund-request-col">Order Number</div>
			<div class="refund-request-col">Reason</div>
			<div class="refund-request-col">Request Date</div>
			<div class="refund-request-col">Receipt</div>
		</div>
		<?php foreach ($results as $row) : ?>
			<div class="refund-request-row">
				<div class="refund-request-col sm"><?php echo esc_html($row->id); ?></div>
				<div class="refund-request-col"><?php echo esc_html($row->name); ?></div>
				<div class="refund-request-col"><?php echo esc_html($row->email); ?></div>
				<div class="refund-request-col"><?php echo esc_html($row->order_number); ?></div>
				<div class="refund-request-col"><?php echo esc_html($row->reason); ?></div>
				<div class="refund-request-col"><?php echo esc_html($row->request_date); ?></div>
				<div class="refund-request-col">
					<img src="<?php echo esc_url($row->receipt_url); ?>" alt="<?php echo esc_html($row->id); ?>">
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php else : ?>
	<div id="refund-request-list">
		<div class="refund-request-row">
			<div class="refund-request-col center">No refund requests found.</div>
		</div>
	</div>
<?php endif; ?>