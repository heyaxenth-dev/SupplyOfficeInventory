document
	.getElementById('runVerificationBtn')
	.addEventListener('click', async () => {
		// Show loader
		document.getElementById('loadingSpinner').classList.remove('d-none');

		// Hide previous results
		document.getElementById('verificationSummary').classList.add('d-none');
		document.getElementById('verificationResults').classList.add('d-none');

		try {
			const res = await fetch('/api/verify');
			const data = await res.json();

			// Update Summary Section
			let badge = document.getElementById('statusBadge');

			if (data.status === 'PASS') {
				badge.className = 'badge bg-success';
				badge.textContent = 'PASS';
			} else if (data.status === 'FAIL') {
				badge.className = 'badge bg-danger';
				badge.textContent = 'FAIL';
			} else {
				badge.className = 'badge bg-warning text-dark';
				badge.textContent = 'WARNING';
			}

			// Error + Warning counts
			document.getElementById('errorCount').textContent = data.errors.length;
			document.getElementById('warningCount').textContent =
				data.warnings.length;

			// Display Summary
			document.getElementById('verificationSummary').classList.remove('d-none');

			// Populate Errors
			const errorList = document.getElementById('errorList');
			errorList.innerHTML = '';
			data.errors.forEach((err) => {
				let li = document.createElement('li');
				li.className = 'list-group-item list-group-item-danger';
				li.textContent = err;
				errorList.appendChild(li);
			});

			// Populate Warnings
			const warningList = document.getElementById('warningList');
			warningList.innerHTML = '';
			data.warnings.forEach((warn) => {
				let li = document.createElement('li');
				li.className = 'list-group-item list-group-item-warning';
				li.textContent = warn;
				warningList.appendChild(li);
			});

			// Show results
			document.getElementById('verificationResults').classList.remove('d-none');
		} catch (error) {
			alert('Verification failed to run. Check backend.');
		}

		// Hide loader
		document.getElementById('loadingSpinner').classList.add('d-none');
	});
