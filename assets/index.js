const refundRequestForm = document.getElementById('refund-request-form');

async function submitFormHandler(event) {
  event.preventDefault();

  const formData = new FormData(refundRequestForm);
  const data = {
    name: formData.get('rrf_name'),
    email: formData.get('rrf_email'),
    orderNumber: formData.get('rrf_order_number'),
    receipt: formData.get('rrf_receipt'),
    reason: formData.get('rrf_reason'),
  };

  try {
    const response = await fetch(refundRequestData.requestUrl, {
      method: 'POST',
      headers: {
        'X-WP-Nonce': refundRequestData.nonce,
      },
      body: formData,
    });

    if (!response.ok) {
      throw new Error('Network response was not ok');
    }

    const result = await response.json();
    console.log(result);

    alert('Your refund request has been submitted successfully.');

    refundRequestForm.reset();
  } catch (error) {
    console.error('Error:', error);
    alert('There was a problem with your refund request. Please try again.');
  }
}

refundRequestForm.addEventListener('submit', submitFormHandler);
