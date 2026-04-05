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
	 * Initiate Datatables
	 */
	const UA_EXPORT_HEADER = {
		line1: 'Republic of the Philippines',
		line2: 'UNIVERSITY OF ANTIQUE–HAMTIC CAMPUS',
		line3: 'Guintas, Hamtic, Antique',
		// Embedded from admin/assets/img/apple-touch-icon.png
		logoDataUrl: '' /*
			'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAALQAAAC0CAYAAAA9zQYyAAAAAXNSR0IArs4c6QAAIABJREFUeF7sfQWcVVX39nPq9p3unqFjyEFaUmmQEOlWQkUMQixQXxRFBAUEFBEURBAMbEEaBBGQjiGH6Z65c/PE96197oAgvKIvvvk//BBkTp+1137Ws561Nof/2/70G9A0jdu6davgNXhDDF6Dwe12W53O0gCv18sD0pXzqqqqATIAEZV/8rzC+XyAyWT3WCz2Mp73eoOCgpzNmzcv4ThO/dM39T9+IPc//vy3/PhkvD///HOKy+Wq5XA4UhwOR4jP56lSUeEyQDMkaPBYK8rdoRUOOVKWIcg+skkePM9BVVVo0MBzvP4nz4HnVWicFyaTVKGqvlyOExyixBcLAp8VFBQkGwyGI3a7/VJoaGiuLMs727VrRyPi/7bfeQP/Z9A3eEFr1641BAa6JVGMDeJ59C4vr7jD5ZS7OByOsOJCD/JzSlFW4oXTwaOk1AmPU4XPy0OWeZjNBmiaCp4XIIoCZFkGNAD0pjUevMBD0xSAk2G3W2E2G6GqMkSjDwGBBnjlCmhwg+O94CUfbDYDwiPCERBg22W323cHBFh3FBWVbvP5fF6r1err37+/8n9WfvUN/J9B+9/FgQMHwktLHW3Kyx01HOWetJzsgvolRc7kgjwvivPcKC+TUV6qQfZpUBQVqqZBkVXIPh84joOiAqpChgpoGv3WwIE8MRm6Ao7XXzX9V/87WTkgSSIkSQI4FUajQf9/AwerzQjJKEAyAAajDINJhmRUYbFqsNqkCvDyIbNZOmy3B+6PjY09brfbj7Zr187xv27c/9MGPWPGDL5jx7Yd3G7vtIJsOfXokYv2U8dyTXk5big+MzgIcLvdcFdUgOMkgCePq0BRFAiCBEX2wCAZQBDZ66uAJBqgcRIzdkCFQTKy4wVBBMeBHcc8tCqz35JkBM9rcLmckAxm8IIIRfEyiCIKBnaM2WKFKAKC6EVoWDCCg+2w2EVYbBpktQhepcDHi25XckpCUVRM6Prw0Lj37r777mMcx+kj5n9s+58y6LVr1wqxsbFJHo9SMze7pGd5iWfwyROZ1pNH85F1qQw8TCAnq6gyVEWDLBN04CGAg0w/EAj/qsx7BoZYYDFLCLBbYbMbEBAQAKPRiJBIG6Kig2CyCMxYDZIImdy3jqihajo8KSlwIzs7D2WlFfB6VJQ6ylHh8KCiXIbD4UFuTgE4jfPfA51LhKoqMBgEmMwGmC0SgoKsCAo2wxZAMWgFNK4MoeFW1WyRDgQGmjfbbAFfxcXFHevYsWPh/4pd/68YNLdv377a5eWlI44czOx1cF9B4oVzxYaKchVerxuyTPABLGgjzCsI+mvxyTJEQURyQjQaN4tCtTpRqFItDoHBJgQFWiEaNNjtATCZjf5j/K+T/eGHGARB6H+1yn+hv9K/MFwC5oY1wOtV4XBUwOV0wevRUJBfjKICJ04ey8ap49nYuycdDofuvWmQqCog8ARXOIgGHsGhNoSFWxAQJMEaIIMXy+DTyotDw83nqyTX/Co2NnZJu3btLv+3G/Z/tUEfOLA9/HKeM96Zr0w/eSyn2+4dp00Z5126r+QE5i0VmXCvyiCFKHEIDjQjLEJCozuqon6TKmjeqgZCQm1QOTpKBM/2FW67XXhVBapPhuKogMBz0MwWCJJIPh08A+YCTp28hP1707F961GcTy9ARTmHsuJyNiBpNqDdrFYTQsJsiI4JQ0SUGQoKUOHLRHx8GEJCg5eoqjovqXnSpR5pPZy3/SH+DU74X2nQJ06cSMrJyWm5b8/ZiccP5zY+d7JMKMhzMzjBCxqgkJnoMEAQNNRJTUS9xvFolJaE5KrhSKkSDYNJAMdr0DQOqqzBdzkTskmCNTKGwZDbtVHwSF63/Of9yNmwGuLFXFA4aUyMh7lbZwS2aAORXY7+1e/5NR75+SW4cC4L6SfzsWPrKRw7fBm5WWXQOBUCx8NoMsBkFhAWFoCo+EBYA7wwWlyQtZICu92wq0qV6p/UqVNnXVpa2n+VYf9XGbSmafxPuw8M//nn07M2rD4XlXEhH4IgsECMGAkWkHHklWUEh5rQs09jjJpwJ8LCggFNgQYjMxuNvLHGQeG8gFvF5TtTobo5RKxeBVvdxozVoN//6EbQgyYI97F9KHjgYVjKS1DByeA1HjQlaJKC4Mdnwjh4MIy8wmaR6y9LA4I2t6pg0+c7sXHDCfy44zS8bkCU6BgziFSJiApAQmI4ElNC4JYzUVh+EnXr1nQlJMTd5/WqX/230H//+Ff5R7/qbTj+2LFjhuNHsgann86evndnetWTRwugKZSV4xis4DhycT4kVwtFw7SqaNuhAZq3qgp7ID0+4VEOjoMHIaefAmc0w961J3hRhMbJcPxyGAUD+0ALi0PSJ+shhEcwY/+NZf2J5yBjdGRnoHDYcDjPnwaq1ULwXR2h8jw8Rw/Du3cvVM6IqLcWIbB5C8aC3OiD0Xk0TWZUoarxSD+dhX27zmLHDydw4OfTcJZrUFQNRiOPwEAz4hOjEJdkg4I8iEa3ZrMbtsXERrwfFBT0af/+/Yv+xKP82xzyH23Q5JEP/XxowBef75/+xacHUorzeDNhTVVToCo8BJECLwk1a4Thoald0LBxCuzBZghGXk9Ma4IenmkKcla+g4oXX4ImGGB5eTYievYEzynI+fBDuF98HlzjO5D01jsQ7BaGwW/HpmgaLr7wLHyrV0FLTETU++/DHhIJTRAhF2Tj0sMPwbtrNwJGjUL0U89DkAj7X3vtypxN3v6fUHRgH5IHDgJvCQR4GZ4yJwoLZaxbtRsr3/0BPjfHkj6yIjMKMD4hCNHxdtiCXCguP+uLT4z6uXG9xk936dFl8+14vn/FOf4jDVrTNOHTT79PzbromrJry/GB+/degChYQFhBo+iNUxAWEYC6jaLRd8AduLN9Kgu0VBZccRAIMvgTHcyuNY1xzdlPTwP39ZfQUlIQ+up82KpXQ8aLM6B8vAbmYWMQPWUaOP72BYRyaSkujR4J7tgRmB9/HOFDxwIGsPuje8pf8wEqnn8OYloTRL3zAUQjeehrPxlL4kBDzqsvwPXuSsg2K4LbtYXpnvvAV0mCITyMHVGQXYaNn+zFl58ewfn0witsDvHa8YnhSKoWAKf3EgxmF6KiQ94PD4+cN2TIkIP/aXz2f6RBz5/z0fAfNh3+29mTpbE+F0EGv5aH42GxAff0uwO9+zVG1VpxMFsotaxBPp+OnC+/QGC7Dgis0wC8eO2jy6oK96XzuDRuPKSzJ6E2aoz42a8i67np8P30IyKfn4vgvn1vC3auHETO9DPIun8YpFInghYtgblpUxg4nqEZSs4U7dyGkmFDgSqJSP58E2Ax4frh5FNVcF4vzrRvAy03B4zsEAHeYIQ3MQlBd7aFpWcv2GvUZIxJXk45jhzMwLLF3+PowRz4fF5IBgOCQoxISolEYoodKp8LpyfvYvUaiStiYhLe6NOnz38Mj/0fZdBrV3/d/MTR3I9XvbcjRpUliAJl81SWig4MCUSLNnF4YlpfRCYEwstx4N0+ePMykP/2SvBffA5NroBtyGhYnpgKm0CGc/XxK4OrsqyLyB0yDFz2OWipabA5HPDl5iBs1TpYata8TQZNmJaDZ/8e5EwcD1E0Ifyd92CuUfMKWU33U7RtG0ofHANDYgqi138Ozmz6DdjxqTIyP1kP+enp0JKTYX7pRYhvLoXj2F6YHB54FA7BL82CrXs/SDxBf/L+lLV047P1u7Fk/lbkZfvgdZJhAxGxAahZOxbhMRoycn5BzdqJ56pWSR7q9Wr7+/fv7/1XwIg/cs3/CIPesmWL+PqsT3ufP1c+zes0NoJqZJQaeTGLRUKHTjXQo98daNqiBkSDAMXhgHvbTpTs2ATXlm0QSsoBIwcutR6sfe5FaK++MBgMN3xP5PFKftiE0menQysohMpp4KJikfjd9zAYaQDdjldGBg04dm9D9oQxMIbHIvK9lTDHJejnZ3oQDQWfb0DZ4xMhNWqGyBVrYDBTzvJaDO11OXBpyGCoR44geNqTCBg2EoLmg3L0OHL37YX74CEkvjQLnD0EBp3/Y2l5NoA1FSVFHnz1+X58tGIn0k8VMO9uNpuQkByKarUiwRtyoXKl3vCIoEWJiYkv/LsHjbfj6/yRAfSH9x0xYobp+P4zr+dnC6MlKUAiDEwpaU7kYLMJWLpyHBrekcS0D2QJl/fuguPp5+A5cwwmQQAnqPAKZoTNehVh3bszXQUFUkTn3WjTVEBW3cj/6EMUT58MXhNgn/QQwh56EkbDjVmGP/xQZFKqioq9P+Li6CEQgyIQ894KWKokg+cMoLiPfn7hqSdQvvoDBHTriei5i2Ewkbrk6n3TcxT89CPyB90HMSgUQcvfQWD1+qBdKEYQFIopFIgGYnxuvtG1nOVOLHz9e6xYupMFyT5FRkREIGrUikGt1AhczNqtxSdG5TZqnHpPp07d9v6ZZ/5nHPNva9CNGz8glRbmtxDkgDmK15AmiTaWKiZjjk8OQrfe9TFmfGfYA0jEQ56HZJkanKeO4fKIIZAov9asOZTTZ8CfT4f50UcQNWIi093TfjdLjpDjkqHBq/pQMOVJyF9tgNSzP2JmzYJ4k0HwZz4UM6LzZ1E0dhR8+cUImP0ygu/uwlgM+iiu7CzkPDgW6uljsE2ahtDRIyFCN/bKTVNV5L0xF46lC6FxIqTU+rB27gKhSRME1KwNsASQBuEWMptkxPRejh48h0XzN2HPjnR4PVSAICIhMYIZtY+/BNHoLI6Ojni+Ro0a73bt2rXszzz33ZNzWl/IPnuzfzqC7tn8wMSOj5HV3ubE3z5kBleZPDuHhwRhyfxqGjr4TJqNZZzZ4+KHG1bfAqkP8yRTC2JrmxdkeXSCduwDzY5MRMmYsTFQK9TvsG3m/0rNnkHNfX0hQEbXhC1iSUm4Lgq68W+Khy7b9gKIpkyCXFYEXLTDWTYMz4xz4gmzwRgn2kY8i9JHxzNsSrU7vQmGBMM1GR5DV91426wQuXw35y08h7NwLT/YleGo3QpUP3mfeWbyCnW/dWjTNh/zcMsx8cj22bz4JRdZgNAmoWTsetRuEI6/4F4SEWfYmJyeOS0pKIi32v0VFzb+VQSfGdGlolULfdjsNDQySXSDHTAbapmMtPPBwWzRoXB3gFXj27UPJth8Q/shk8Eaisji/eEj3MqS/IC5aoxQycbRrPoBj5nMQUpIRvXINzKFhfphy8w9MXtBVkI/ccaPAHz8O29xFCO7cCfxtSHlfgQxMx6Gg9OuvUPjGfHCXzkLjNIiyBjkwCJZhIxE+ehxEi8mfmNRDUmJ1fMRTT50E95dfQejeDYmz57G0vpyVhYrjxyHGRCGgTj2Wyv9z90zvUUVpiQvff/0TFr62GZkZxTCZrIiND0L9xvEw2YtQVHo+v25qrRdat269pE6dOv9yFuTfxKBn8PVrHRpckq+tMAiRHFSJUUsGkxn3DqqP6TPvYwGRrPFQKspxoHkzRJY7oLw4E0kDR0Jkuoeb5+8cp0+hYPwouLJzEPbaPIR27vabjNv1ps3SyF4X0idNgLpjM0yjHkbSpCfA3UZhEht+VPmiafA6y+Hdvg3qmQuQJQHmrh1gSKgKiRR+1yEGGuTewnycaNkMRk1F1Y1fQKxeh8UFehpcBxC3R0SlQUUF0k+UYuLo5Th79jIEzojAIDPSmiWjSi0bLmb+rNZNrTE1Li7hza5du3pufR64/Xv+yw16xowZ4prlB6covsDJnBIQxHPEQiiISwjB49O7oHOPhrpck9N1GYrGIXfVe3DPeQUwWhG+fCVMtetQgu2mHLHX60LW889AXrcOpru7IHrOmxCMV6uyb/RaqU6bGIL8b7+GM/MCwlu0ha12nVvioSsNis6r8743Lh759c8qn42krEzeSrCJMBUVGFz3lRS6r3cXo/zVuRDTGiFq0bswB9qvBotU/nXbZhJdt01PUFRYiiXzv8fHq/fD7VJhD5RQq24SEqoIcHjOITIqcHlCQsKTgwYNyr39pnprZ/yXGvQTT7xq3bhu+zyfK2ykAKtAr41otZTqwXhr+TgWXf86RV3p0eSSEmQ9OA6On/Yh6L4BCH9mBgSDeNNonqbosiM/IXfQAEAwI277JliDonQ8fJ024tevjek8VNKckvqO3KQefF4xUArQVPKIejxVXubFmVO5uHQxB26XG16vFx63zC7Bpn1Og8lkgEEywWq1oEbtJCQkBUI00mnJgGkfFeAEVq1yhfO+7h5lr4Lcbz5H+ZsLYenZE1HjJ8BAdVp/+abB4/bh03U/Ysa0dVAVAZIkoFbdGLRoUxUXMneqicmR37Vp065nWlqa7y+/nRtc4F9m0D3aPhB27lLpq65y0zBJCKQcFjRVRI9+dfDkzF4ICQ0ARyz/dZse4QOFm79FyYQHoVhNiFryNgKaNL8pVpTJY6kKzjwyHtI330Lt3QcpL70CTiCW5O8zAJXGS0Fifn4xCvJLkZVZgOzMUpw8lomcyyW4nFGOi+dzoWkGBg9oqlcVmk2ocpYqvTkIJGFVqB5RhiTqRbH08lXSlggKqtdOQGS0HQlJIahaLQLRscGIiglBdGwQM36iJlnhLcfBS95b0alFweuDaLJBvC6V/1caEwWMu7eewvNPbUDGhVJWGJFYJQIN74hAuSsdEZGmvQmJyaOGDBly/K+8jxud+19i0C3q9Y4oc5jXeCosbQXewgkk+Pn/LNS057qhx72NYQ8KgAKOtWXxu9Fr2AUyaLfXhdxXZkP7YBXkmGTEbVgFKSAYAvHPN8G5jsO/IHP4AEg+BWGffg5bcg1mbGy7Agv0Alf6X4IdF89lYe/Os/hh0wkU5JWjMN+FkuIKFszRz8mLaqSSUFQITLHKA5oMjpNhswZBUd2oKKdEDmC0qOBlAS6XWz+WJ2M3wq3JkGQOvMizhIZEFeA2CYGBVkREBqJKtWg0bZmIlm1qITjEpktXKYOp+tWCnOqnL/9J5qNp8Ck+nDmViScfWoWzZ8rYe4yNC0azVsmQ+QsqLzn3tGjZomfnzp3/qXLUf7pBt0odFHw5v/QdTg7rIwlWaKrAikwnTumCISObQeONUEpKoXplcEF2GEzEvQrXYUINHlWFUlaEjL59gMsZsAy/H5GPTYZooNYBN8bHLkcZMh+bBLUoF+FPvghbw3qQrqjnZFanV1xYzuiq778+iC8+248LZx1QqbcGoXROgcnEwWo1wmA2ITDAzopUG6SlIDbejti4cFSrHomAQDMMTBnnw4wnN+Cj9/fAHmhAv6ea4mz4WUiiCXDIMJaZ4SsC7F4bzl0sRM7ZMqjlXvjKVchk+OUV8HhUCFQ3yBnBCR60alcVXXo2RsO0FISG22G1WHRo8iv14F9t1sSwCLIMlVehejWMG74MO7edZJ46JjYMd3aoinL3CYRGWI5HRUV3Gzly5IW/+p4qz/9PNeiWLe9NyD7vW6b5AjtKfAAEXkJwmBlPzuiBDl3qwWDkkbFkCbDleygVbggJSQh4eDwCajaEyLzS1ddClBLxzIUffYTSWc9CC/j/0/Pby2CuVVff9wYbBVPuvHxIwSbwov2qDWjAnt2HsHtrBvbtOo/z53JQXlYBVRFZsxijGUijGsPGiUhMDkZ8EtXn2REZFQKjkQLWX79OggU88+AHf0rHmIHvwe2idhlG1GgaifTevyC9PFvvO6P62ExAXHGwNRBRplCYVBOkEgHxajgCSgNgyg/C2SN5OH+hAG63Cmg+hltDwu2oVTcaTZonssA5OiYYPG+onND8N3SbPi8xPuyGdSqv8PgxlH67CZFDh8IaForMjAK8OftrfPnZEQiCioiIMDRrEw8fzkIwuHfUrVtv0JAhQ/4pBbq36Yl/f/w1aHBPkCNPWav6wu6SBDt43oiAQA6vLx6NZnemQK5w4uK48eD272WZMk0UwKkyhKg4RK38EIb4+GsMuvKKPmcFzg25D+KxI5A6dkbUGwsh8eQd/RXVN7g1ndpSQO26Dv98CbNmrMOZkwXw+VRWvSIS5hVl1G8Sh4HDWqBjp0ZMYlk5TnQG4WbYm1gB0pv40KPdS0g/mw0bz8MN6t8hY9jsVphftBRlkgJNoWoa8q5MvMHuVMfV+r1TUa7Rx6Fd9Wa4O+wOqHsUfL72Z1y+7GLBKrVb0IGZhvuGtsDYie0RFmFj/T50OcA/vlHtJUcGDR6ytwLFq9bBueB1KM5C2Np1RtiCJUzxSE3Opj++DhvXHoLGq4iJCcHd3eqjoHQ/ImNtJ1NT66d16tSp4h+/o79/hn+KQTeu3iMsp8i3UEJYf6MUCA4GBIZIeOGVvmjfqQHDz9lvvQXPwjnwBYbA3ncArJHhKN2+HZYBwxHaoS0T1jMNBvvqV2+b5JPlJ04gZ+QQGIrLYZ8zC6E9+oHFSNcFlbohA5kZufjhu8P4+vOjOPjTRWac5IkjYyxo2KgKmreuglbtaiIyOthvZBSzXku9sZJVZnfXv0IVFEB+svZHPPPYp+BFN54acxCfbI7F4VOxCI0yY/hbLTA3fTXOFlzSuWJRYF2XNJka0egenxkkzUI+HwTJAItmwA/D3kYdWyyOHDqPn348ix1b0nHy2CWUlrogcUaYrQJatKmGnv0ao2XbWjCZGH3iv8fKpP6tmxQLvymWUGSmzS5d+QHkXVtYIE01mlLdOohb/ykMEMHxlIRxYN5LX2DVir1MlhAbG4nmd8bBKZ9BYIi4OSUlZcRf7an/coOeOvXlwE1f7ltXVhh0l8TbIPsEhEYYsGj5/UhtHAdOk+ArLUPOxAnw/rgHlsmTET5iFDRRhK/CDdFEiWryRAIUpkkQYKCIyh/VMyNVVeQuWYyyxfNhi4lF8KJlEJOSYLgSHJL3U+F0uPHBsr1YuWwbykqdrOhUVjyMWRg5rjXadqyHwAArS/Hqg+Z6L0fZR8Djk3HkxCn4ZBUGHmiQWheinzajKdlR7sDYQYtx6lg+albJwJzJm3AxJxIPPtcaCoIw7pF24Nu6MWHvPAiMgeEgKWQ+HBTBXwdZyWHTs1JbAwB1Qqvj/uSO6Jd6F4KMNjgrXCjIcWDN+z9gw+ojcDq9MEgcTEYDAkJ5zJw9BGnNEyBJfpz9B742zWDEWPpKC1A4cxbk3TugOEqBuBjIbi+EogIEPT4ZIaPH/QoJanBVuDHnxc/w4cp94DQBydXC0e6umsgq+FGNSwzb+Nhjj99z60Pqj+/5Bx7xj5+cOnbWqdL72YpSw1MGIUIib2a1WTBjdg90732H7p1UHt6yMmSMHw1+7wEEzH0Nti6doGbmwnPiKIq/+wYF+/ZCUTjE9euDsMFDwUfHwuAPgiozY96sLGQ9OhFCVCiCJ02GKakaDILOGRcVlmHb5qN4Z+H3OHemmKXT7XYTatePwr0D7kSHrjVhsVDFN3kjHopK0EOFQCyEf1DoWTgZF7Pz8fk3PyCvIB8R4eHo1rE9qibGXOGMCTt/vm4Xpk38CF4BeGXcHnRqfxLOUiOeXXgnvt8XgVq1k7H4o/uxIv0LHMo7CYfmg0N24kT+OZSrriszEIMfCvXFIzZEYO3DSMORYonF2l5/Q2p4CuvHQYMsJ7MQH73/EzZ9fRiXzufC55UAyYUu3Rti8Ki2qFMvjnnsmyVcKinBSprSWVIC9/YdyH1rLnynT8AQHg17l64wtWyJwmlPgpdERL29DPbUhtcahqagwuHC04+vwdcbj7Kf1agZi+ZtY5BfegR161ddGRudMOGvgh9/qUHXq9l5RmlBwBQRwWZKFpjNZry2aAg6dKoDgbhY/6bIXmTNfhHu95aDi0wCEqKg5mYDWZlMBupVZchOFbCKMDVugugl78JstfqnU/0k9OHdlzMgRYZDNJihcT6asbFjy2EsnrcDxw5nwuH0wmQwoEffVPQb2BR16sWyEi2fouHAkZO4mJGN4vJylpHUaTzC0wLTTpOBE49c5nCiuMxBbXAxtHd3pMTH6s7cv3/mxSwM7LkA2VnFqFuvBO89tQm8wQlOU3DgaBLGzegEmdMw7pG2eHhqL6aao+dzeL3YlXEQIza9CK/qZUZdOfuwQVWZcfR3YHqzw2SMqtMNoh8rE42mcTzysgqxa9tpLF/2A04fyWGtG0KDbWjVLgWPTO2G+IRINvNcj7EVTWGoigaM4+Rx5L/0N/iO7YHo0mBJbQjr49Ngb9QIF+a9BO+y5bC27ojotxbCSBHzdRvNUgV5JZgy8QMGi6jTU+3UGNRvEobL+fvk2rVqPPrII48s/CvqFf8qg+aa1R94b06Wc6VJijEK1BtC4DFxSkeMHNee9Wm7vvzJcekCMkYNhJRbBImRwCpkEZACwqE1bQqxSizk1RugFGTDMu4xRE58mAUfgh8nk8Hp59QDPuKMl765Ces+2A+fT4HBSNNfIKY8ew+atagJjVNY+69LObn4dusulDkcLK3u9nj1wMx/Pjqn3lX0ai8OCpFapTVEp9Yt/XBex5o+xY2Fr36HpfO3wWarwMoXv0FKsj4jMAXg/y8ueO6VxvhiX30moPpk0xOITgqkliDwcFTF4sXQ71/AzssHdS6dri3LOq6mGYLug/UAEzCt4VA83WIEBMoochwyyvKRL5chULLBrgn4IeMX7N+Qjv2f5SAvuxQ8J8FsVzHz5fvQpkMtWKzkrUkOVgVtAAAgAElEQVRyqJsANaF0FxfC+/E6FL27GHA6IfJGgJiYZq0Q9bfZgFlAbrceEIrzYHtnOezNWt2UUaLnJRZn+H0LcODHy6yzauOm1VCvSSAuZPxUnFqvdqeRI0f+9Mfn/X9BUFi3WsdaJYXSJwYhsobIUUk9MPz+5pg4pTtsVsuVZMmvb02mqSr7Mnxr16L02DFIRhNsaWkQm7eGsWoyRI1D0XtLkf/6HFjiqiJy3TpINjtEP49ML5CsitLNX35yAMuXbMXpE/nMuKvVjMSw0XeiS6+GsNmtV5qQn7+QgWKnE1azCXazCbKiIaegED/s3ouy8grWoJGwsd4SV+/zTAMzJS4KQ/veo0/h7CH0gPHMqQw8MHgZ8rKK0bPtKTw6fCdsVl18xHR/nICcXBvun9ENWfkh6Nw7BYZeKk6UpsPDeeGDhjOlmShwFV+BHWTEzLjJoGlgqCrrGfJQrb54uf2D1D8SmY4C3Pv5UzhUnI4kWyTCjAE4XJyOGGM4mgr1EHwqAjs3nILTIUMwCGjeKgUPPdYFdRvE+Tl7fSDnvv0mPG/MhyxIMLdtC0udhihd+Q7kwlyY2nSCITkRFcuXg09LQ+KqtayfH0uK3XCjEjkPTh8vxKPjluJ8eiksNgNat62JoIhSyCjIaNy43j19+w44cDuN+rZ76Nq1axvgTjjrLo+O42GEQQxE+87JmLtkJNNp3LxSRDcK9vGpaoR1+6Qu90aINNo1HqWffYTS6dMh16iC2LffgyUsGpw/00eG6/F48dyU9fh03W5YLBZ4PB7cO6gZnpnVG7xA4IVSzldf31WvflVERHTYwZNn8MX32+CVqeWt5PewGvt41NOiTaMG6NCm+bXPorkwd9Y3WLZwGyRzOd59bhuqJ2cwHK6DFw4KdT/yGPDWJ7WxaHUzxEba0ftv9TDtwpts1iKrJ4qMDJj1nKZUOTWPFEX2m/7OZguex8N1++LlthOhKD4M+HwGvs3ezeg+0ozozSH1FmP0vqOtoZgWeR/WzjqBnDNl4EQqKjZg5suD0KFrLSaCorjBo/hw6ZFHENKxC0J7dIdPFFH83VdwPvEENNUFm2ZAscQhbNZcBHXrzqjRSshzM6OkmOL8mVz06DgLqs+MoBAD7uqaihLXAYRFmr5v2CCtb69evcpvl1HfVoNu3LiHpSjbPZ/zhY+RxFDW5KVuwzDMXXw/4uMCddENMZbX5kiueRa9+48GT1EBCj/+CMFpdzDGwnvyJPJmvQjxwlko7bsiae5ccMx70pIPwL49J+FP3cXx1fzK3Hy3Wx8LFYadyM5K0UAfm7jdcf6SpeIaHMTsbtYha9MCKA8IVduQYK/yQ9TK/dD2wI1YVBowHTbyKCnHVkKT+8ADIx7fdcnLL5R1Qm4ezsHJcoEYsPPQ1l3mujEt6Z8A68DfyAtJxnmDBucei2PhMswGBFSvz6M3bpCHR4Brd4El04FrZvCKgGP7mWhTqXP+N5VqV4YWq8HKF02emXPnr07/61Bd+48wP+3HecSPfUlOO0bMKwZBo5oIJ4/FxQIvHcg7eIZZC5dAse+fZAcdhYW8e41EIbq1aAymxlmEtoXgutAN3HVj/vw1djf4R+swcZfBiM0jLrGRSXtHw/CQV0OFot59PQ5Hj19gfSMTJFQsTFTQiUwV3pf2hZvJNyH1W5DsfwF0KJ+dRZdFIvMvehkHNl/FUP7roQlx4HebQ6jW4tL7OUEG07gy8LLiqZWhuHIalkNScBtSo8pe2X6LhROiCqgSzTAvpKDMaGKwgkustB1ROGEY2sWPBfxNjP4uPhCSaEIb5idRwuJOCFOF7LsBgz4vDbO3YxEXNk8yO7wBHsSTytcZiGqx/2Fyv0liQW6Lwa1Bg7iihOYpghiErpB8JpWkmBzORHtmQe/Nv8eYT4BynN8ufPa/0cB+PLwjwj1Cn4l5BBP0CXbsHbpUXw1jrp7nPjl4ChExgSzkmxi/z7IvHYZhsBgaAsUhCO2JMxVqsGnSGFIBgMnosT4c7/Ta09KllGv0kQ8fJDC7VlFSknw9M1+MmbMZ+ESPTC3E33TkPJHV+iUley1wtNQBAa9jMnfdUXjlqXfOM3F27k9IwPWM2fwaOH30J6/DJXGAG3FMvAbOhLGgkWVZE9sNRZLBrOndu04jxr1iuDbBe3h5Unb/z/DamR+9H679x7A1TsJSMvMYoOhB0QvF1xksXXzTXWvbFmCQQP0aB+P0OCAlzsGUytIFEZCl7bf48SR+wj0ycbq6b/Az+uFKB8z8iAwZjc8x/N6OOQQ/YJkmG6QgN+XDJpDDtHF7VYfpc/mLn2zsRIhiQyU+Rtk0CKMEbGm0MkQuRydI9ASJjAx3VSENG7U5ed9xTFxSXmoNXr0GVcNvwUcxqWEm0h35CBHZYWd8Gl2RGRsEny1HhhUsgWuJD3CloT93MnCjkByQHJqUco7EoUCY7An4Tiu9t4Ck0rLzunVg7phhuydg7iAaPQq+TLkUEwaDxKS0brhN8hMc2LS9FZo1f4DuBx2JG7fAXWwP4xBeaAKDYXWaGKP+7bd/G0Orn+XOdj/+y0EhfqgTqMYPE48mtqgQaPqzZs3v/BOgw7PU3oBLJF9jLpwBASYsWBlHxQtkTfX/kX59KVcAIkz2CzpyPxxOTI3r4TzeQqXPXV9+iOkczfo/Hz5/MePXqDXR4tw61oyBgyvjkEjmrECPxMR/uHIyMjAsVPn4ennjWB/P/h6eeFJUjKOnjuH+3cesuE4SAmTixSidMwJmUqNZvWqonzxuDfgI0rwHNi68SjGDN7KGhTT5tRFrRIH4Hi+AhoXTfClBIyMWRRQmN9MxsSSAy+nYpGRkUFyvMtIgltEhs537xZiUdBBMXQuH5qaZFWCwCRYe4LzwTE0VRWJTcf8EEoQhdAjw3zKZyC8OM2iR/fRzZHw0B/hsT7Ysnko9F46OF0qjDu5CD+c3YYwn2CkWzJQzDsK82sPR7A5BFZrNlbc3I2N1w/AqnKhtH8BtI6qgmrhJfD73VPouH88VlSfiCb5K/H3e/UgVazOu75C03yV8VHhOq8/PdmFtPQU9O+yDKePP0LT1iUweWYHMb2A7geJeWho5+AaOHfFv++xYtF+TJ2wAyazBs3blcO1uzty6tSp1b579+7b32nQQb7Fb3loiucz6IKQN9IL638aAk8PEzIOHoR8/SYkSw7/Z8+xQEUG6cjmLc6ilWG/fAv2m1cBGz0QFfRfTUUwNcACPCewY/OZcLp0mL20PWrULvG+34M5Em/SG+nFVpsNyzfuQMLTZ2yA3AXibtcnT+mSMbDrhzwg89WDwpNnT5IwoOtqXLtwD5Wq58Xslf05+bCl7IfrwafQWu+yYZHFacmYCJJzSw4olUI2REra6L1eMTgKCcjQubBCzb/sAARWTX2HdC6HHOR++fcibGKPrrRIMQODGwUEcYlyShH2UBIqqKqCnafHiQv+GDSpISwuLfoPr4v+Q+pCVkn44/YpdN8/HVvazMDOG3vxMOUhfqg9hhsHuDXiTWya7qIk4eKLW2jx82jUz1sF82p/krvzue8htbaVX9EF02sMQb2ocq/fW5I9djoxbdIOrFh4HMXLhGDO0q4ICPD9z7WGPb+cwYiP1/HOTELpNtVVOSzCb9SIESOmvdWgP/lkdNTqZX+cMqmL+GtUHihTIRTrdgxHit2BxK6doTl9CmnZmVyKNWglslvINIOQKAH2HOj1Br4h1CmtNRgRumMnvArGQq22Y8Gsvfh20nZ4/K8YcODMZHj7mN7boN92InMLnE4s37QD1+7eY1KOu3jhVunUq9UY3L0DQkPcvYniSoS2rF99CJNGb4NBr8fUWS1Qt2klUNcA9d+50q8i59qHcKUncHxMCRlJeFFpmxANd2hD17IRTixR8kcC406BeigGygiGElrQQ6b512IkhSA2EWxH3pZEKWnBUGJIVUQRbojkzWJxQaeTWDKYKpE0rYCuI5AP0b1tzwKmLKuDHXsLwRjiwKatoxCdLwQ5sg3VV/VBq8hq0Hh54EbybSysOZJZfexzX0XRFPYbFWgSc9LRYONgDmN2tf4W/nrB33En+Q8yX6DS6p5Y32gSKocX/+vjIbXWjYcxbuhv0JkzsXb7cBQoFPK3RZZXOSO5TD8F5nP/+8TRy/i421okpyehWrVYhEVnQe9hW1qlSpXebvbda/4+T2CRajaLz09+phKeapURPQZUxtAxzeHIseDhJ32htdigNVEGagSMZkCnZRqkRP8nDWGdBhai5RlMcBqMCGjeFEaDmdusOredhTNHn6JE+UCs3jpS6En8h4O87NMXSVi6aRsysyyvZe4s90pYpkqFj7t8iEB/f+WdeAUiI8OCtg2n4d7dDFSuGYoZc7vBy4fieTIiooaq4Mi+DMvNPlBlnle8o0jahKEp4DNP7BJJozuZox94ngpBe0riKCQChBGzoDqxFTm0eCk64+ZMM2rDgbkQm3RXCsUse3FNt7GLXI+YfS5cvp0XA6fUQKrFhL4D6mDwiEbcZ3j08WUMOzgDFfOUZHrbd9UHMrX0XdA/7SQUV/fe9TX2JJ7EhjqTUD5PEdxLeo7kzGR4m71w4NF5TDmzHLtazEChAOol/Otx58ZjtK47C9nWdMxa3AN1G5V6hb2XCxUwCxB2B1LvJUCb+AKW1HSojXpIvr4wFSgErZFUtdTsNJ89S0aH5nM4MSwaG4Ya9SPwOPHMH9Wr12jYqFEjKy+6Vz9KWEjJtrCFrPDQFTSQ56CZ3G0/qsbt6cjI4gzUTWaXQHP1VNw+z8tdgYnE2F2ejgM4aEKFDJfViXJFR0N2qNF3SGX0+6TZXyS33te23ZBdWmYOFq/ZhExLNs/qI+IQcxCIZ6x4a2+zB3q1bwEfTy/l8rRlOzB5/Eas/vE0DzZavrk7SpYqBEkl4EMyVqEdLcFhvQTbtb7Q284K5IEm4FLFzkoNSooivxv+kkn6lngXGvbiFO9SYihyHlGt46ZVq1JYIb4H9x0KrFrQUcV5xJmmhJNDF6omMqtSkKO4NYuKMjYbWwUVbog1Z3fqMHleBWw9HAdvLz12HRwNTz8Tc6PHn1iMHy/uQNdiLfFlpS6QCTZ8xw3n+o7LiQ1X/kDvQ1+jb9lWQEGmaAAAIABJREFUuHrvBi6nJiAbFmiou/t/NIE8XgHY02YOfHSv77S5JXu7EzVLj0FyshPxXYnY1or18MRNFtxtV0YGMrZuQebm9ZATEyE7LJDtxCWXofPUQQrNC59hg2Eq/wEktY6FPVvUnYKb1xMRHhGCRi0b43HSiVv58kWX6NOnT/ZfDDrQN3aM2hU52VufD1A7cODk1wgKNbLVC/lVypgFAZLGErMPYalR5e/uFaLolbFHkoFTR6+iU6vFzGDbe+wLhEXSFqZ8ufe1ZGWHpDDjwaMn2Pn7fjx6kcRriYjqWrWGx0dkZmXiaWIKt9hH5g1F55aNhbYHHy5cPHsXHVrOgdUio13Hkvjim3ie8Ep/k0hYkg1PyBc4nRIcaYch3+4EZ85zDhfIgAQBiLw18aEFgkGcZgpLaGG75QoY5uOytmDnMSNPGRpE8TH9TAYg+NAKKqLIhTF/gyA0WiREeGKFUsEIFO1eArmg87jiqFXh0eMAdBzbBBmZOtRpUhjTZ3WBzqBHQtpTdP51IkKNwZhbaxj8TT65YzPe3Cdpou+L7Ax8dXgJFt3aCbVM8CLh8ipoVRo4yHmoAG+1GXXzlkecf36UzlMIBf0jEe4RqGiHiHs9dvAqbF53FkWKB2PrbyPEcFEFDUq9fR2p48bBceYULC4HtF7eUPsGwKmmmeN2OFIz4EhLh6/eB7qpkxHQoAk0aic+G7oBG9ceg7+/F5q0Kg6rdB1584b49OnTh7QsXnroCRMmqFYs3jPFYckz0qwNg9FDwskr0zlefh9o7d12KWPVkgP4Yuw2BAaZcejcF7lq8//CltmT5VgsOHzyLM5fv4W09AxGFbQU8shApbIlUL1iWTx+8QJL12/nOJcMun2zhoowOmC1WDBl/A6sXf4nwiKNWLiiK/IXDIc14yRs6QdhDO4PrcY3twOFPbXsgD31KOw3WkNly+I7xjNGuPKlKCYpCR/jvu64T4Hg3Fgqe3jqFyQ0xr1fkD40hytKHyNXBd18aDpPkRFjApOo5LHHJtiPK4BiYYlJceRedFi+PQpz19SBUy1j9uLOqFk3jmeYn3t2A212fYpqeUpiQd1RPOuQZMPefLaE7My6tAWfH5wLKwtDiZEXJDXmpqe+pBeouKdTLwMFvSMwrc4nKOMfDYOG+gqdWLt8HyaN/YVaaHDmxjQYTTpewNkP7+N5n+6w3b3FxTht954wlasIvY83ZLWB57DYHj5E2uxZkC+dgRydH2Fbt0CnM2LF4t8xZfyv8DDrWFlJNtxA/gIRYT169GAqae4CzZ8/vz4rxXuHxlWwHmlvxJUMx8ZdQ5VE4J+x4ncapwwM778EO7ecR7lK+bF8Uy+o1dQZ/P7XpLjzeVIKdv62H3cfPkKO08ZxFY2sIPspXaQwWjVtAKo3PEtMxoLV65GenoZK5cuiRZ0aDBnRcfvGY7RrMgPWHDXadymBEZ+3giv7GBw3BsCZ/hAI+wjmfJ9DpQ5WSsRCJsHhyoTj7uew3V8Aoq4IqqdAJygpVBPXQqfmSiFZKCV51AFOUZeQ0xXELLeHprCEIjUbxXIEcRIllbyv3SUmaFFszeVxwJJDySSVu4UWHsF7er1SNlcI+0Qx1RKcogWePjBj4PR6uP84FFXr5se0OR1gMNKkAzWOPbyAjrs/R4jkg+VtvkCUVxhDmyKZFqZAfJEZJ1dhwvHFcHJ7iQsyT+LSCI71q3NeeCqBUkSi7nVZhb5xzfFNtYGQNDIsmS6UzDcQKsmIpZt64INqxXhHf75yGZ5/Ph7a4ACEzJkHXemy0LNoNEGvxPSjDhvAfi8Bj1s0hi0zCxGbt8Jcojj2/HoW/Tuv5kS5Rt1YmP0foHrNMpObNGn26V8MOiPR91cPXfHqGsmEJq1KYdrcrv+ZOEQPv3PLmTh9/B6ZC6/JS0FsY1HzeN0HkFyVaYvtTKbxeT1g3sa4sDFbgK7eChldqRWNkE/IBv9eoGo0R1BZo4U6FxGl4mAHvDv0u1f4OI8XjXmFp/2GWX3MLvRSGHJ5xmmhJ+SAAU7Q9qUHA2n7XWc6enIPMzAzoMuzKnGvXrpl2sV2dGpWwZz+By/3FgGFjKnHqpP8gOkYDcckoENnBb3g7Hih+zzQgj6YoYScujQKPe7w20dLIQJtCTua6DSoyLUTFy14kttdcf1pD68DPI2mHWrUhm3YJnEcL17LXDGRA/y3XgSsKhR1tlVwufDJicgNc5wHCskWZ+yGFTWtNFoYfU5qX+TRPJoGWC5DQemLa4GNb8XpTZiGOWNkSnUxNBfDoO6ilsdos1KqR+DjnEgxK0UbfmtELwd4vvEeGIfmbUgc+R4SUbsKP5t6gYFssvp51i0P7vUNw/P/qUaKbcaoHkjBu+HFvWXkREtDe2/D4cJuos4vhZhEC5B3FRbDbYrdnIvHQWOet3wrpnN1xyJhySBJ2kheeIMfDr2BM6yYUa5Ufi+RMgLK83ajUsCKf6Dho1rq97cwTzWy0pNKD01xpXwZF6TSD8Azxx8OxEfpDJ1y4jZdgwyHduwqXVwaNRY3h1+AjGoiTwJ7ys6LZ++0Fl7vimX+PSuRQUjvPHpp+HMzb9n453JId0TSolP36WiMSUFM62Sb4rwP8Nkr/swKljd9AjfgFy7HZ0anIdQ7sdhlrWsqI+xa8UYlGiRnK3rLOhlLsZvaBqn1aoqTLBn5l0VNYW3poQC7YRWhV8jlDkJ4Mlr0/Wx3wPRdGRrknkJO4R5N5K0a/I16E4m3oWtS7cux+Erp/WRlKmBypXLorMTo+wI+EIJInED15q2YmKpthSOIbO5WgrTbW5VU/lHOXfAu4jIRwduhRqgq9rD4BRGSNCvZyTDi9GiHcI+hd3G7Sw10vn7qFtw+mQVBosWNkNVWvFcm+vk7pmKPzMTILq7AXYr91ExtULyLh8GfKDR1Cp7dA61FBFRcFVvDj0RYrCXK4CDMVKQKeyM3lt3rd/wOyhRf2mcbDiCmrW/qBl06ZNt/2jh44Oq34I1ugqKskDH1TLhxWbPuHXyHYZWcmPkDT1G6T/tB06GszuYYbcrQciB/SHxuQJQbEXx5ue98bVe2hS42tunP16Vnu0/LAsj3j7t0duBwx7GmX5KHHqq2qh+w4dxYFT52BxOFGxZBwa1aoKg6IBJ74QkJWVgeH9VmLvL9cQnjcTi7/chkAPK7fxZefYeTqq0ahjopLV4oLeoHCSFeJRjsXBKIXeoIXd6uCOLJ1ONIKQMdIoDMKS6WmKbnChaKpWU1c7fX7AYNSyAI3dRiV0xegYBgSys2ichgSDSQdrjp1RD6NBy80Q2/YXx1fzK3Hy3Wx8LFYadyM5K0UAfm7jdcf6SpeIaHMTsbtYha9MCKA8IVduQYK/yQ9TK/dD2wI1YVBowHTbyKCnHVkKT+8ADIx7fdcnLL5R1Qm4ezsHJcoEYsPPQ1l3mujEt6Z8A68DfyAtJxnmDBucei2PhMswGBFSvz6M3bpCHR4Brd4El04FrZvCKgGP7mWhTqXP+N5VqV4YWq8HKF02emXPnr07/61Bd+48wP+3HecSPfUlOO0bMKwZBo5oIJ4/FxQIvHcg7eIZZC5dAse+fZAcdhYW8e41EIbq1aAymxlmEtoXgutAN3HVj/vw1djf4R+swcZfBiM0jLrGRSXtHw/CQV0OFot59PQ5Hj19gfSMTJFQsTFTQiUwV3pf2hZvJNyH1W5DsfwF0KJ+dRZdFIvMvehkHNl/FUP7roQlx4HebQ6jW4tL7OUEG07gy8LLiqZWhuHIalkNScBtSo8pe2X6LhROiCqgSzTAvpKDMaGKwgkustB1ROGEY2sWPBfxNjP4uPhCSaEIb5idRwuJOCFOF7LsBgz4vDbO3YxEXNk8yO7wBHsSTytcZiGqx/2Fyv0liQW6Lwa1Bg7iihOYpghiErpB8JpWkmBzORHtmQe/Nv8eYT4BynN8ufPa/0cB+PLwjwj1Cn4l5BBP0CXbsHbpUXw1jrp7nPjl4ChExgSzkmxi/z7IvHYZhsBgaAsUhCO2JMxVqsGnSGFIBgMnosT4c7/Ta09KllGv0kQ8fJDC7VlFSknw9M1+MmbMZ+ESPTC3E33TkPJHV+iUley1wtNQBAa9jMnfdUXjlqXfOM3F27k9IwPWM2fwaOH30J6/DJXGAG3FMvAbOhLGgkWVZE9sNRZLBrOndu04jxr1iuDbBe3h5Unb/z/DamR+9H679x7A1TsJSMvMYoOhB0QvF1xksXXzTXWvbFmCQQP0aB+P0OCAlzsGUytIFEZCl7bf48SR+wj0ycbq6b/Az+uFKB8z8iAwZjc8x/N6OOQQ/YJkmG6QgN+XDJpDDtHF7VYfpc/mLn2zsRIhiQyU+Rtk0CKMEbGm0MkQuRydI9ASJjAx3VSENG7U5ed9xTFxSXmoNXr0GVcNvwUcxqWEm0h35CBHZYWd8Gl2RGRsEny1HhhUsgWuJD3CloT93MnCjkByQHJqUco7EoUCY7An4Tiu9t4Ck0rLzunVg7phhuydg7iAaPQq+TLkUEwaDxKS0brhN8hMc2LS9FZo1f4DuBx2JG7fAXWwP4xBeaAKDYXWaGKP+7bd/G0Orn+XOdj/+y0EhfqgTqMYPE48mtqgQaPqzZs3v/BOgw7PU3oBLJF9jLpwBASYsWBlHxQtkTfX/kX59KVcAIkz2CzpyPxxOTI3r4TzeQqXPXV9+iOkczfo/Hz5/MePXqDXR4tw61oyBgyvjkEjmrECPxMR/uHIyMjAsVPn4ennjWB/P/h6eeFJUjKOnjuH+3cesuE4SAmTixSidMwJmUqNZvWqonzxuDfgI0rwHNi68SjGDN7KGhTT5tRFrRIH4Hi+AhoXTfClBIyMWRRQmN9MxsSSAy+nYpGRkUFyvMtIgltEhs537xZiUdBBMXQuH5qaZFWCwCRYe4LzwTE0VRWJTcf8EEoQhdAjw3zKZyC8OM2iR/fRzZHw0B/hsT7Ysnko9F46OF0qjDu5CD+c3YYwn2CkWzJQzDsK82sPR7A5BFZrNlbc3I2N1w/AqnKhtH8BtI6qgmrhJfD73VPouH88VlSfiCb5K/H3e/UgVazOu75C03yV8VHhOq8/PdmFtPQU9O+yDKePP0LT1iUweWYHMb2A7geJeWho5+AaOHfFv++xYtF+TJ2wAyazBs3blcO1uzty6tSp1b579+7b32nQQb7Fb3loiucz6IKQN9IL638aAk8PEzIOHoR8/SYkSw7/Z8+xQEUG6cjmLc6ilWG/fAv2m1cBGz0QFfRfTUUwNcACPCewY/OZcLp0mL20PWrULvG+34M5Em/SG+nFVpsNyzfuQMLTZ2yA3AXibtcnT+mSMbDrhzwg89WDwpNnT5IwoOtqXLtwD5Wq58Xslf05+bCl7IfrwafQWu+yYZHFacmYCJJzSw4olUI2REra6L1eMTgKCcjQubBCzb/sAARWTX2HdC6HHOR++fcibGKPrrRIMQODGwUEcYlyShH2UBIqqKqCnafHiQv+GDSpISwuLfoPr4v+Q+pCVkn44/YpdN8/HVvazMDOG3vxMOUhfqg9hhsHuDXiTWya7qIk4eKLW2jx82jUz1sF82p/krvzue8htbaVX9EF02sMQb2ocq/fW5I9djoxbdIOrFh4HMXLhGDO0q4ICPD9z7WGPb+cwYiP1/HOTELpNtVVOSzCb9SIESOmvdWgP/lkdNTqZX+cMqmL+GtUHihTIRTrdgxHit2BxK6doTl9CmnZmVyKNWglslvINIOQKAH2HOj1Br4h1CmtNRgRumMnvArGQq22Y8Gsvfh20nZ4/K8YcODMZHj7mN7boN92InMLnE4s37QD1+7eY1KOu3jhVunUq9UY3L0DQkPcvYniSoS2rF99CJNGb4NBr8fUWS1Qt2klUNcA9d+50q8i59qHcKUncHxMCRlJeFFpmxANd2hD17IRTixR8kcC406BeigGygiGElrQQ6b512IkhSA2EWxH3pZEKWnBUGJIVUQRbojkzWJxQaeTWDKYKpE0rYCuI5AP0b1tzwKmLKuDHXsLwRjiwKatoxCdLwQ5sg3VV/VBq8hq0Hh54EbybSysOZJZfexzX0XRFPYbFWgSc9LRYONgDmN2tf4W/nrB33En+Q8yX6DS6p5Y32gSKocX/+vjIbXWjYcxbuhv0JkzsXb7cBQoFPK3RZZXOSO5TD8F5nP/+8TRy/i421okpyehWrVYhEVnQe9hW1qlSpXebvbda/4+T2CRajaLz09+phKeapURPQZUxtAxzeHIseDhJ32htdigNVEGagSMZkCnZRqkRP8nDWGdBhai5RlMcBqMCGjeFEaDmdusOredhTNHn6JE+UCs3jpS6En8h4O87NMXSVi6aRsysyyvZe4s90pYpkqFj7t8iEB/f+WdeAUiI8OCtg2n4d7dDFSuGYoZc7vBy4fieTIiooaq4Mi+DMvNPlBlnle8o0jahKEp4DNP7BJJozuZox94ngpBe0riKCQChBGzoDqxFTm0eCk64+ZMM2rDgbkQm3RXCsUse3FNt7GLXI+YfS5cvp0XA6fUQKrFhL4D6mDwiEbcZ3j08WUMOzgDFfOUZHrbd9UHMrX0XdA/7SQUV/fe9TX2JJ7EhjqTUD5PEdxLeo7kzGR4m71w4NF5TDmzHLtazEChAOol/Otx58ZjtK47C9nWdMxa3AN1G5V6hb2XCxUwCxB2B1LvJUCb+AKW1HSojXpIvr4wFSgErZFUtdTsNJ89S0aH5nM4MSwaG4Ya9SPwOPHMH9Wr12jYqFEjKy+6Vz9KWEjJtrCFrPDQFTSQ56CZ3G0/qsbt6cjI4gzUTWaXQHP1VNw+z8tdgYnE2F2ejgM4aEKFDJfViXJFR0N2qNF3SGX0+6TZXyS33te23ZBdWmYOFq/ZhExLNs/qI+IQcxCIZ6x4a2+zB3q1bwEfTy/l8rRlOzB5/Eas/vE0DzZavrk7SpYqBEkl4EMyVqEdLcFhvQTbtb7Q284K5IEm4FLFzkoNSooivxv+kkn6lngXGvbiFO9SYihyHlGt46ZVq1JYIb4H9x0KrFrQUcV5xJmmhJNDF6omMqtSkKO4NYuKMjYbWwUVbog1Z3fqMHleBWw9HAdvLz12HRwNTz8Tc6PHn1iMHy/uQNdiLfFlpS6QCTZ8xw3n+o7LiQ1X/kDvQ1+jb9lWQEGmaAAAIABJREFUuHrvBi6nJiAbFmiou/t/NIE8XgHY02YOfHSv77S5JXu7EzVLj0FyshPxXYnY1or18MRNFtxtV0YGMrZuQebm9ZATEyE7LJDtxCWXofPUQQrNC59hg2Eq/wEktY6FPVvUnYKb1xMRHhGCRi0b43HSiVv58kWX6NOnT/ZfDDrQN3aM2hU52VufD1A7cODk1wgKNbLVC/lVypgFAZLGErMPYalR5e/uFaLolbFHkoFTR6+iU6vFzGDbe+wLhEXSFqZ8ufe1ZGWHpDDjwaMn2Pn7fjx6kcRriYjqWrWGx0dkZmXiaWIKt9hH5g1F55aNhbYHHy5cPHsXHVrOgdUio13Hkvjim3ie8Ep/k0hYkg1PyBc4nRIcaYch3+4EZ85zDhfIgAQBiLw18aEFgkGcZgpLaGG75QoY5uOytmDnMSNPGRpE8TH9TAYg+NAKKqLIhTF/gyA0WiREeGKFUsEIFO1eArmg87jiqFXh0eMAdBzbBBmZOtRpUhjTZ3WBzqBHQtpTdP51IkKNwZhbaxj8TT65YzPe3Cdpou+L7Ax8dXgJFt3aCbVM8CLh8ipoVRo4yHmoAG+1GXXzlkecf36UzlMIBf0jEe4RqGiHiHs9dvAqbF53FkWKB2PrbyPEcFEFDUq9fR2p48bBceYULC4HtF7eUPsGwKmmmeN2OFIz4EhLh6/eB7qpkxHQoAk0aic+G7oBG9ceg7+/F5q0Kg6rdB1584b49OnTh7QsXnroCRMmqFYs3jPFYckz0qwNg9FDwskr0zlefh9o7d12KWPVkgP4Yuw2BAaZcejcF7lq8//CltmT5VgsOHzyLM5fv4W09AxGFbQU8shApbIlUL1iWTx+8QJL12/nOJcMun2zhoowOmC1WDBl/A6sXf4nwiKNWLiiK/IXDIc14yRs6QdhDO4PrcY3twOFPbXsgD31KOw3WkNly+I7xjNGuPKlKCYpCR/jvu64T4Hg3Fgqe3jqFyQ0xr1fkD40hytKHyNXBd18aDpPkRFjApOo5LHHJtiPK4BiYYlJceRedFi+PQpz19SBUy1j9uLOqFk3jmeYn3t2A212fYpqeUpiQd1RPOuQZMPefLaE7My6tAWfH5wLKwtDiZEXJDXmpqe+pBeouKdTLwMFvSMwrc4nKOMfDYOG+gqdWLt8HyaN/YVaaHDmxjQYTTpewNkP7+N5n+6w3b3FxTht954wlasIvY83ZLWB57DYHj5E2uxZkC+dgRydH2Fbt0CnM2LF4t8xZfyv8DDrWFlJNtxA/gIRYT169GAqae4CzZ8/vz4rxXuHxlWwHmlvxJUMx8ZdQ5VE4J+x4ncapwwM778EO7ecR7lK+bF8Uy+o1dQZ/P7XpLjzeVIKdv62H3cfPkKO08ZxFY2sIPspXaQwWjVtAKo3PEtMxoLV65GenoZK5cuiRZ0aDBnRcfvGY7RrMgPWHDXadymBEZ+3giv7GBw3BsCZ/hAI+wjmfJ9DpQ5WSsRCJsHhyoTj7uew3V8Aoq4IqqdAJygpVBPXQqfmSiFZKCV51AFOUZeQ0xXELLeHprCEIjUbxXIEcRIllbyv3SUmaFFszeVxwJJDySSVu4UWHsF7er1SNlcI+0Qx1RKcogWePjBj4PR6uP84FFXr5se0OR1gMNKkAzWOPbyAjrs/R4jkg+VtvkCUVxhDmyKZFqZAfJEZJ1dhwvHFcHJ7iQsyT+LSCI71q3NeeCqBUkSi7nVZhb5xzfFNtYGQNDIsmS6UzDcQKsmIpZt64INqxXhHf75yGZ5/Ph7a4ACEzJkHXemy0LNoNEGvxPSjDhvAfi8Bj1s0hi0zCxGbt8Jcojj2/HoW/Tuv5kS5Rt1YmP0foHrNMpObNGn26V8MOiPR91cPXfHqGsmEJq1KYdrcrv+ZOEQPv3PLmTh9/B6ycqK';
	*/,
	};

	// Use a lightweight logo URL (HTML/Print) and only convert to data URL for PDF.
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
			img.onerror = function () {
				// keep logoDataUrl as-is
			};
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
		// Tight logo + text (side-by-side); centered as one group — not messageTop + prepend (that doubled the header)
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

	// Check if datatable exists and is not already initialized
	const datatableElement = document.querySelector('#datatable');
	if (datatableElement && typeof DataTable !== 'undefined') {
		// Check if DataTable is already initialized using jQuery API
		if (
			typeof $ !== 'undefined' &&
			$.fn.DataTable &&
			$.fn.DataTable.isDataTable('#datatable')
		) {
			// Already initialized, skip
		} else {
			// Initialize DataTable
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
				// If initialization fails (e.g., already initialized), ignore
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
