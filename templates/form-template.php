<?php
if (!defined('ABSPATH')) {
	exit;
}
?>

<form id="refund-request-form" method="post" enctype="multipart/form-data">

	<div class="refund-request-form__input-group">
		<h3>Refund Request Form</h3>
	</div>
	<div class="refund-request-form__input-group">
		<label for="rrf_name">Name</label>
		<input type="text" id="rrf_name" name="name" required>
	</div>
	<div class="refund-request-form__input-group">
		<label for="rrf_email">Email</label>
		<input type="email" id="rrf_email" name="email" required>
	</div>
	<div class="refund-request-form__input-group">
		<label for="rrf_order_number">Order Number</label>
		<input type="text" id="rrf_order_number" name="orderNumber" required>
	</div>
	<div class="refund-request-form__input-group">
		<label for="rrf_receipt">Upload Receipt</label>
		<input type="file" id="rrf_receipt" name="receipt" accept=".jpg,.jpeg,.png,.pdf" required>
	</div>
	<div class="refund-request-form__input-group">
		<label for="rrf_reason">Reason for Refund</label>
		<textarea id="rrf_reason" name="reason" required></textarea>
	</div>
	<div class="refund-request-form__input-group">
		<button type="submit" name="rrf_submit">
			Submit Request
		</button>
	</div>
</form>