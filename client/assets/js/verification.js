/**
 * Formal Verification Dashboard JavaScript Handler
 * Handles verification API calls and UI updates
 */

$(document).ready(function () {
	const runBtn = $('#runVerificationBtn');
	const loadingBtn = $('#loadingBtn');
	const statusDiv = $('#verification-status');
	const summaryPanel = $('#summary-panel');
	const errorList = $('#error-list');
	const warningList = $('#warning-list');

	// Run verification button click handler
	runBtn.on('click', function () {
		runVerification();
	});

	function runVerification() {
		// Show loading state
		runBtn.addClass('d-none');
		loadingBtn.removeClass('d-none');

		// Clear previous results
		statusDiv.html(
			'<div class="alert alert-info"><i class="bi bi-arrow-repeat spin"></i> Running verification...</div>'
		);
		errorList.html('');
		warningList.html('');

		// Reset summary
		$('#summary-total-items').text('-');
		$('#summary-total-transactions').text('-');
		$('#summary-error-count').text('-');
		$('#summary-warning-count').text('-');
		$('#error-badge').text('0');
		$('#warning-badge').text('0');

		// Call verification API
		$.ajax({
			url: 'api/run_verification.php',
			type: 'GET',
			dataType: 'json',
			timeout: 60000, // 60 second timeout
			success: function (response) {
				displayResults(response);
			},
			error: function (xhr, status, error) {
				let errorMessage = 'An error occurred while running verification.';
				let debugInfo = '';

				if (xhr.responseJSON) {
					errorMessage = xhr.responseJSON.message || errorMessage;

					// Show debug info if available
					if (xhr.responseJSON.debug) {
						debugInfo = '<br><br><strong>Debug Information:</strong><br>';
						debugInfo +=
							'<small><pre style="text-align: left; background: #f8f9fa; padding: 10px; border-radius: 5px; max-height: 200px; overflow-y: auto;">';
						debugInfo += JSON.stringify(xhr.responseJSON.debug, null, 2);
						debugInfo += '</pre></small>';
					}
				} else if (xhr.responseText) {
					try {
						const errorResponse = JSON.parse(xhr.responseText);
						if (errorResponse.message) {
							errorMessage = errorResponse.message;
						}
						if (errorResponse.debug) {
							debugInfo = '<br><br><strong>Debug Information:</strong><br>';
							debugInfo +=
								'<small><pre style="text-align: left; background: #f8f9fa; padding: 10px; border-radius: 5px; max-height: 200px; overflow-y: auto;">';
							debugInfo += JSON.stringify(errorResponse.debug, null, 2);
							debugInfo += '</pre></small>';
						}
					} catch (e) {
						errorMessage = 'Failed to connect to verification service.';
					}
				}

				Swal.fire({
					icon: 'error',
					title: 'Verification Failed',
					html: errorMessage + debugInfo,
					confirmButtonText: 'OK',
					width: '600px',
				});

				statusDiv.html(`
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-octagon-fill"></i> <strong>Verification failed:</strong> ${errorMessage}
                        ${
													debugInfo
														? '<div class="mt-3">' + debugInfo + '</div>'
														: ''
												}
                    </div>
                `);
			},
			complete: function () {
				// Hide loading, show button
				loadingBtn.addClass('d-none');
				runBtn.removeClass('d-none');
			},
		});
	}

	function displayResults(data) {
		// Handle ERROR status with debug info
		if (data.status === 'ERROR' && data.debug) {
			let debugHtml =
				'<div class="mt-3"><strong>Debug Information:</strong><br>';
			debugHtml +=
				'<small><pre style="text-align: left; background: #f8f9fa; padding: 10px; border-radius: 5px; max-height: 300px; overflow-y: auto;">';
			debugHtml += JSON.stringify(data.debug, null, 2);
			debugHtml += '</pre></small></div>';

			statusDiv.html(`
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-octagon-fill"></i> <strong>ERROR - Verification failed:</strong> ${
											data.message || 'Unknown error'
										}
                    ${debugHtml}
                </div>
            `);
			return;
		}

		// Update status card
		let statusClass = 'secondary';
		let statusIcon = 'info-circle';
		let statusText = 'Unknown';

		if (data.status === 'PASS') {
			statusClass = 'success';
			statusIcon = 'check-circle-fill';
			statusText = 'PASS - All checks passed';
		} else if (data.status === 'WARN') {
			statusClass = 'warning';
			statusIcon = 'exclamation-triangle-fill';
			statusText = 'WARN - Warnings found';
		} else if (data.status === 'FAIL') {
			statusClass = 'danger';
			statusIcon = 'exclamation-octagon-fill';
			statusText = 'FAIL - Errors found';
		} else if (data.status === 'ERROR') {
			statusClass = 'danger';
			statusIcon = 'exclamation-octagon-fill';
			statusText =
				'ERROR - Verification failed: ' + (data.message || 'Unknown error');
		}

		statusDiv.html(`
            <div class="alert alert-${statusClass}">
                <i class="bi bi-${statusIcon}"></i> <strong>${statusText}</strong>
            </div>
        `);

		// Update summary
		if (data.summary) {
			$('#summary-total-items').text(data.summary.total_items || 0);
			$('#summary-total-transactions').text(
				data.summary.total_transactions || 0
			);
			$('#summary-error-count').text(data.summary.error_count || 0);
			$('#summary-warning-count').text(data.summary.warning_count || 0);
			$('#error-badge').text(data.summary.error_count || 0);
			$('#warning-badge').text(data.summary.warning_count || 0);
		}

		// Display errors
		if (data.errors && data.errors.length > 0) {
			let errorHtml = '<div class="verification-list">';
			data.errors.forEach((error, index) => {
				const hasItemId = error.item_id != null && error.item_id > 0;
				errorHtml += `
                    <div class="verification-list-item verification-item-error">
                        <div class="item-content">
                            <i class="bi bi-exclamation-octagon-fill text-danger me-2"></i>
                            <span class="badge bg-danger me-2">${error.type || 'ERROR'}</span>
                            <span>${error.message || 'Unknown error'}</span>
                        </div>
                        <div class="item-actions">
                            ${hasItemId ? `<button type="button" class="btn btn-sm btn-outline-primary edit-from-verification" data-item-id="${error.item_id}" title="Edit item"><i class="bi bi-pencil-square"></i> Edit</button>` : ''}
                        </div>
                    </div>
                `;
			});
			errorHtml += '</div>';
			errorList.html(errorHtml);
		} else {
			errorList.html(`
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i> No errors found!
                </div>
            `);
		}

		// Display warnings
		if (data.warnings && data.warnings.length > 0) {
			let warningHtml = '<div class="verification-list">';
			data.warnings.forEach((warning, index) => {
				const hasItemId = warning.item_id != null && warning.item_id > 0;
				const itemClass = warning.type === 'STATUS_INCONSISTENCY' ? 'verification-item-status-inconsistency' :
					warning.type === 'LOW_STOCK_WARNING' ? 'verification-item-low-stock' : 'verification-item-other';
				warningHtml += `
                    <div class="verification-list-item ${itemClass}">
                        <div class="item-content">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <span class="badge me-2">${warning.type || 'WARNING'}</span>
                            <span>${warning.message || 'Unknown warning'}</span>
                        </div>
                        <div class="item-actions">
                            ${hasItemId ? `<button type="button" class="btn btn-sm btn-outline-primary edit-from-verification" data-item-id="${warning.item_id}" title="Edit item"><i class="bi bi-pencil-square"></i> Edit</button>` : ''}
                        </div>
                    </div>
                `;
			});
			warningHtml += '</div>';
			warningList.html(warningHtml);
		} else {
			warningList.html(`
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i> No warnings found!
                </div>
            `);
		}

		// Show success notification
		if (data.status === 'PASS') {
			Swal.fire({
				icon: 'success',
				title: 'Verification Complete',
				text: 'All checks passed successfully!',
				timer: 3000,
				showConfirmButton: false,
			});
		} else if (data.status === 'WARN') {
			Swal.fire({
				icon: 'warning',
				title: 'Verification Complete',
				text: 'Verification completed with warnings. Please review the warnings section.',
				timer: 4000,
				showConfirmButton: true,
			});
		} else if (data.status === 'FAIL') {
			Swal.fire({
				icon: 'error',
				title: 'Verification Failed',
				text: 'Errors were found during verification. Please review the errors section.',
				showConfirmButton: true,
			});
		}
	}

	// Edit from verification - delegated handler for dynamically added buttons
	$(document).on('click', '.edit-from-verification', function () {
		const itemId = $(this).data('item-id');
		if (!itemId) return;

		$.ajax({
			url: 'api/get-inventory-item.php',
			type: 'GET',
			data: { id: itemId },
			dataType: 'json',
			success: function (res) {
				if (res.success && res.item) {
					const item = res.item;
					$('#ve_item_id').val(item.id);
					$('#ve_item_name').val(item.item_name);
					$('#ve_description').val(item.description || '');
					$('#ve_stock_number').val(item.stock_number || '');
					$('#ve_category').val(item.category || '');
					$('#ve_unit_of_measure').val(item.unit_of_measure || '');
					$('#ve_unit_value').val(item.unit_value || '');
					$('#ve_quantity').val(item.quantity);
					var qty = parseInt(item.quantity) || 0;
					var statusText = qty === 0 ? 'Out of Stock' : (qty <= 10 ? 'Low Stock' : 'In Stock');
					$('#ve_status_preview').html('<small class="text-muted"><i class="bi bi-info-circle me-1"></i>Auto: ' + statusText + '</small>');
					$('#ve_last_restocked').val(item.last_restocked ? item.last_restocked.split(' ')[0] : '');
					new bootstrap.Modal(document.getElementById('verificationEditModal')).show();
				} else {
					Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to load item' });
				}
			},
			error: function () {
				Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load item data' });
			}
		});
	});

	// Update status preview when quantity changes in verification edit form
	$(document).on('input', '#ve_quantity', function () {
		const qty = parseInt($(this).val()) || 0;
		const statusText = qty === 0 ? 'Out of Stock' : (qty <= 10 ? 'Low Stock' : 'In Stock');
		$('#ve_status_preview').html('<small class="text-muted"><i class="bi bi-info-circle me-1"></i>Auto: ' + statusText + '</small>');
	});

	// Verification edit form submit
	$('#verificationEditForm').on('submit', function (e) {
		e.preventDefault();
		const form = $(this);
		const submitBtn = form.find('button[type="submit"]');
		const originalText = submitBtn.html();
		submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Updating...');

		$.ajax({
			url: 'api/edit-inventory-handler.php',
			type: 'POST',
			data: form.serialize(),
			dataType: 'json',
			success: function (res) {
				if (res.success) {
					Swal.fire({ icon: 'success', title: 'Updated!', text: res.message, timer: 2000, showConfirmButton: false });
					bootstrap.Modal.getInstance(document.getElementById('verificationEditModal')).hide();
					runVerification(); // Re-run to refresh results
				} else {
					Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Update failed' });
					submitBtn.prop('disabled', false).html(originalText);
				}
			},
			error: function (xhr) {
				let msg = 'An error occurred while updating.';
				try {
					const r = JSON.parse(xhr.responseText);
					if (r.message) msg = r.message;
				} catch (e) {}
				Swal.fire({ icon: 'error', title: 'Error', text: msg });
				submitBtn.prop('disabled', false).html(originalText);
			}
		});
	});
});
