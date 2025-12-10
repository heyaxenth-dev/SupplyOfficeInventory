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
			'<div class="alert alert-info"><i class="bi bi-hourglass-split"></i> Running verification...</div>'
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
                        <i class="bi bi-x-circle"></i> <strong>Verification failed:</strong> ${errorMessage}
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
                    <i class="bi bi-x-circle"></i> <strong>ERROR - Verification failed:</strong> ${
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
			statusIcon = 'check-circle';
			statusText = 'PASS - All checks passed';
		} else if (data.status === 'WARN') {
			statusClass = 'warning';
			statusIcon = 'exclamation-triangle';
			statusText = 'WARN - Warnings found';
		} else if (data.status === 'FAIL') {
			statusClass = 'danger';
			statusIcon = 'x-circle';
			statusText = 'FAIL - Errors found';
		} else if (data.status === 'ERROR') {
			statusClass = 'danger';
			statusIcon = 'x-circle';
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
			let errorHtml = '<div class="accordion" id="errorAccordion">';
			data.errors.forEach((error, index) => {
				const errorId = `error-${index}`;
				errorHtml += `
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-${errorId}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse-${errorId}" aria-expanded="false" aria-controls="collapse-${errorId}">
                                <span class="badge bg-danger me-2">${
																	error.type || 'ERROR'
																}</span>
                                ${error.message || 'Unknown error'}
                            </button>
                        </h2>
                        <div id="collapse-${errorId}" class="accordion-collapse collapse" 
                             aria-labelledby="heading-${errorId}" data-bs-parent="#errorAccordion">
                            <div class="accordion-body">
                                <pre class="bg-light p-3 rounded">${JSON.stringify(
																	error,
																	null,
																	2
																)}</pre>
                            </div>
                        </div>
                    </div>
                `;
			});
			errorHtml += '</div>';
			errorList.html(errorHtml);
		} else {
			errorList.html(`
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> No errors found!
                </div>
            `);
		}

		// Display warnings
		if (data.warnings && data.warnings.length > 0) {
			let warningHtml = '<div class="accordion" id="warningAccordion">';
			data.warnings.forEach((warning, index) => {
				const warningId = `warning-${index}`;
				warningHtml += `
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-${warningId}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse-${warningId}" aria-expanded="false" aria-controls="collapse-${warningId}">
                                <span class="badge bg-warning text-dark me-2">${
																	warning.type || 'WARNING'
																}</span>
                                ${warning.message || 'Unknown warning'}
                            </button>
                        </h2>
                        <div id="collapse-${warningId}" class="accordion-collapse collapse" 
                             aria-labelledby="heading-${warningId}" data-bs-parent="#warningAccordion">
                            <div class="accordion-body">
                                <pre class="bg-light p-3 rounded">${JSON.stringify(
																	warning,
																	null,
																	2
																)}</pre>
                            </div>
                        </div>
                    </div>
                `;
			});
			warningHtml += '</div>';
			warningList.html(warningHtml);
		} else {
			warningList.html(`
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> No warnings found!
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
});
