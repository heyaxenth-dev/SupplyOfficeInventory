(function () {
	'use strict';

	/**
	 * Easy selector helper function
	 */
	const select = (el, all = false) => {
		el = el.trim();
		if (all) {
			return [...document.querySelectorAll(el)];
		} else {
			return document.querySelector(el);
		}
	};

	/**
	 * Easy event listener function
	 */
	const on = (type, el, listener, all = false) => {
		if (all) {
			select(el, all).forEach((e) => e.addEventListener(type, listener));
		} else {
			select(el, all).addEventListener(type, listener);
		}
	};

	/**
	 * Easy on scroll event listener
	 */
	const onscroll = (el, listener) => {
		el.addEventListener('scroll', listener);
	};

	/**
	 * Sidebar toggle
	 */
	if (select('.toggle-sidebar-btn')) {
		on('click', '.toggle-sidebar-btn', function (e) {
			select('body').classList.toggle('toggle-sidebar');
		});
	}

	/**
	 * Search bar toggle
	 */
	if (select('.search-bar-toggle')) {
		on('click', '.search-bar-toggle', function (e) {
			select('.search-bar').classList.toggle('search-bar-show');
		});
	}

	/**
	 * Navbar links active state on scroll
	 */
	let navbarlinks = select('#navbar .scrollto', true);
	const navbarlinksActive = () => {
		let position = window.scrollY + 200;
		navbarlinks.forEach((navbarlink) => {
			if (!navbarlink.hash) return;
			let section = select(navbarlink.hash);
			if (!section) return;
			if (
				position >= section.offsetTop &&
				position <= section.offsetTop + section.offsetHeight
			) {
				navbarlink.classList.add('active');
			} else {
				navbarlink.classList.remove('active');
			}
		});
	};
	window.addEventListener('load', navbarlinksActive);
	onscroll(document, navbarlinksActive);

	/**
	 * Toggle .header-scrolled class to #header when page is scrolled
	 */
	let selectHeader = select('#header');
	if (selectHeader) {
		const headerScrolled = () => {
			if (window.scrollY > 100) {
				selectHeader.classList.add('header-scrolled');
			} else {
				selectHeader.classList.remove('header-scrolled');
			}
		};
		window.addEventListener('load', headerScrolled);
		onscroll(document, headerScrolled);
	}

	/**
	 * Back to top button
	 */
	let backtotop = select('.back-to-top');
	if (backtotop) {
		const toggleBacktotop = () => {
			if (window.scrollY > 100) {
				backtotop.classList.add('active');
			} else {
				backtotop.classList.remove('active');
			}
		};
		window.addEventListener('load', toggleBacktotop);
		onscroll(document, toggleBacktotop);
	}

	/**
	 * Initiate tooltips
	 */
	var tooltipTriggerList = [].slice.call(
		document.querySelectorAll('[data-bs-toggle="tooltip"]'),
	);
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl);
	});

	/**
	 * Initiate quill editors
	 */
	if (select('.quill-editor-default')) {
		new Quill('.quill-editor-default', {
			theme: 'snow',
		});
	}

	if (select('.quill-editor-bubble')) {
		new Quill('.quill-editor-bubble', {
			theme: 'bubble',
		});
	}

	if (select('.quill-editor-full')) {
		new Quill('.quill-editor-full', {
			modules: {
				toolbar: [
					[
						{
							font: [],
						},
						{
							size: [],
						},
					],
					['bold', 'italic', 'underline', 'strike'],
					[
						{
							color: [],
						},
						{
							background: [],
						},
					],
					[
						{
							script: 'super',
						},
						{
							script: 'sub',
						},
					],
					[
						{
							list: 'ordered',
						},
						{
							list: 'bullet',
						},
						{
							indent: '-1',
						},
						{
							indent: '+1',
						},
					],
					[
						'direction',
						{
							align: [],
						},
					],
					['link', 'image', 'video'],
					['clean'],
				],
			},
			theme: 'snow',
		});
	}

	/**
	 * Initiate TinyMCE Editor
	 */

	const useDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
	const isSmallScreen = window.matchMedia('(max-width: 1023.5px)').matches;

	tinymce.init({
		selector: 'textarea.tinymce-editor',
		plugins:
			'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons accordion',
		editimage_cors_hosts: ['picsum.photos'],
		menubar: 'file edit view insert format tools table help',
		toolbar:
			'undo redo | accordion accordionremove | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image | table media | lineheight outdent indent| forecolor backcolor removeformat | charmap emoticons | code fullscreen preview | save print | pagebreak anchor codesample | ltr rtl',
		autosave_ask_before_unload: true,
		autosave_interval: '30s',
		autosave_prefix: '{path}{query}-{id}-',
		autosave_restore_when_empty: false,
		autosave_retention: '2m',
		image_advtab: true,
		link_list: [
			{
				title: 'My page 1',
				value: 'https://www.tiny.cloud',
			},
			{
				title: 'My page 2',
				value: 'http://www.moxiecode.com',
			},
		],
		image_list: [
			{
				title: 'My page 1',
				value: 'https://www.tiny.cloud',
			},
			{
				title: 'My page 2',
				value: 'http://www.moxiecode.com',
			},
		],
		image_class_list: [
			{
				title: 'None',
				value: '',
			},
			{
				title: 'Some class',
				value: 'class-name',
			},
		],
		importcss_append: true,
		file_picker_callback: (callback, value, meta) => {
			/* Provide file and text for the link dialog */
			if (meta.filetype === 'file') {
				callback('https://www.google.com/logos/google.jpg', {
					text: 'My text',
				});
			}

			/* Provide image and alt text for the image dialog */
			if (meta.filetype === 'image') {
				callback('https://www.google.com/logos/google.jpg', {
					alt: 'My alt text',
				});
			}

			/* Provide alternative source and posted for the media dialog */
			if (meta.filetype === 'media') {
				callback('movie.mp4', {
					source2: 'alt.ogg',
					poster: 'https://www.google.com/logos/google.jpg',
				});
			}
		},
		height: 600,
		image_caption: true,
		quickbars_selection_toolbar:
			'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
		noneditable_class: 'mceNonEditable',
		toolbar_mode: 'sliding',
		contextmenu: 'link image table',
		skin: useDarkMode ? 'oxide-dark' : 'oxide',
		content_css: useDarkMode ? 'dark' : 'default',
		content_style:
			'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
	});

	/**
	 * Initiate Bootstrap validation check
	 */
	var needsValidation = document.querySelectorAll('.needs-validation');

	Array.prototype.slice.call(needsValidation).forEach(function (form) {
		form.addEventListener(
			'submit',
			function (event) {
				if (!form.checkValidity()) {
					event.preventDefault();
					event.stopPropagation();
				}

				form.classList.add('was-validated');
			},
			false,
		);
	});

	/**
	 * Initiate Datatables (same UA header / exports as admin)
	 */
	const UA_EXPORT_HEADER = {
		line1: 'Republic of the Philippines',
		line2: 'UNIVERSITY OF ANTIQUE–HAMTIC CAMPUS',
		line3: 'Guintas, Hamtic, Antique',
		logoDataUrl: '',
	};

	UA_EXPORT_HEADER.logoUrl = 'assets/img/ua-logo.png';
	(function preloadUaLogoDataUrl() {
		if (!UA_EXPORT_HEADER.logoUrl) return;
		try {
			const img = new Image();
			img.onload = function () {
				try {
					const canvas = document.createElement('canvas');
					canvas.width = img.naturalWidth || img.width;
					canvas.height = img.naturalHeight || img.height;
					const ctx = canvas.getContext('2d');
					if (!ctx) return;
					ctx.drawImage(img, 0, 0);
					UA_EXPORT_HEADER.logoDataUrl = canvas.toDataURL('image/png');
				} catch (e) {
					// ignore
				}
			};
			img.onerror = function () {};
			img.src = UA_EXPORT_HEADER.logoUrl;
		} catch (e) {
			// ignore
		}
	})();

	function uaExportHeaderPlain() {
		return (
			UA_EXPORT_HEADER.line1 +
			'\n' +
			UA_EXPORT_HEADER.line2 +
			'\n' +
			UA_EXPORT_HEADER.line3 +
			'\n'
		);
	}

	// Inventory table: last column is Actions — omit from Copy/CSV/Excel/PDF/Print
	const INVENTORY_EXPORT_COLUMNS = [0, 1, 2, 3, 4, 5, 6, 7];

	function uaExportHeaderHtml() {
		const imgSrc =
			UA_EXPORT_HEADER.logoDataUrl || UA_EXPORT_HEADER.logoUrl || '';
		return (
			'<div class="dt-ua-export-header" style="width:100%;text-align:center;margin-bottom:12px;">' +
			'<div style="display:inline-flex;align-items:center;gap:10px;text-align:left;">' +
			'<img src="' +
			imgSrc +
			'" alt="" style="height:64px;width:auto;flex-shrink:0;display:block;" />' +
			'<div style="line-height:1.2;text-align:left;">' +
			'<div style="font-size:11px;">' +
			UA_EXPORT_HEADER.line1 +
			'</div>' +
			'<div style="font-size:13px;font-weight:700;letter-spacing:0.02em;">' +
			UA_EXPORT_HEADER.line2 +
			'</div>' +
			'<div style="font-size:11px;">' +
			UA_EXPORT_HEADER.line3 +
			'</div>' +
			'</div></div></div>'
		);
	}

	const datatableElement = document.querySelector('#datatable');
	if (datatableElement && typeof DataTable !== 'undefined') {
		if (
			typeof $ !== 'undefined' &&
			$.fn.DataTable &&
			$.fn.DataTable.isDataTable('#datatable')
		) {
			// Already initialized, skip
		} else {
			try {
				new DataTable('#datatable', {
					layout: {
						topStart: {
							buttons: [
								{
									extend: 'copyHtml5',
									text: 'Copy',
									messageTop: uaExportHeaderPlain(),
									exportOptions: {
										columns: INVENTORY_EXPORT_COLUMNS,
									},
								},
								{
									extend: 'csvHtml5',
									text: 'CSV',
									bom: true,
									messageTop: uaExportHeaderPlain(),
									exportOptions: {
										columns: INVENTORY_EXPORT_COLUMNS,
									},
								},
								{
									extend: 'excelHtml5',
									text: 'Excel',
									messageTop: uaExportHeaderPlain(),
									exportOptions: {
										columns: INVENTORY_EXPORT_COLUMNS,
									},
								},
								{
									extend: 'pdfHtml5',
									text: 'PDF',
									orientation: 'landscape',
									pageSize: 'A4',
									exportOptions: {
										columns: INVENTORY_EXPORT_COLUMNS,
									},
									customize: function (doc) {
										try {
											const logoDataUrl = UA_EXPORT_HEADER.logoDataUrl;
											const columns = [];
											if (logoDataUrl) {
												columns.push({
													image: logoDataUrl,
													width: 60,
												});
											}

											columns.push({
												stack: [
													{
														text: UA_EXPORT_HEADER.line1,
														fontSize: 10,
													},
													{
														text: UA_EXPORT_HEADER.line2,
														bold: true,
														fontSize: 12,
													},
													{
														text: UA_EXPORT_HEADER.line3,
														fontSize: 10,
													},
												],
												alignment: 'left',
											});
											doc.content.unshift({
												columns: [...columns],
												margin: [0, 0, 0, 12],
											});
										} catch (e) {
											// ignore
										}
									},
								},
								{
									extend: 'print',
									text: 'Print',
									exportOptions: {
										columns: INVENTORY_EXPORT_COLUMNS,
									},
									customize: function (win) {
										try {
											var $body = $(win.document.body);
											$body.find('h1').remove();
											$body.prepend(uaExportHeaderHtml());
										} catch (e) {
											// ignore
										}
									},
								},
							],
						},
					},
				});
			} catch (e) {
				console.log('DataTable initialization skipped:', e.message);
			}
		}
	}

	/**
	 * Autoresize echart charts
	 */
	const mainContainer = select('#main');
	if (mainContainer) {
		setTimeout(() => {
			new ResizeObserver(function () {
				select('.echart', true).forEach((getEchart) => {
					echarts.getInstanceByDom(getEchart).resize();
				});
			}).observe(mainContainer);
		}, 200);
	}
})();
