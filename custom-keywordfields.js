// custom-button.js
jQuery(document).ready(function ($) {
	// This function targets the specific area where the current action links are.
	// It appends a new link/button. You might need to adjust the selector based on your WordPress version or admin theme.
	$('.row-actions').each(function () {
		var $this = $(this);

		let trParent = $this.parent().parent();
		let trParentId = trParent.attr('id');

		let id = trParentId.split('-')[1];

		let span = document.createElement('span');
		span.className = 'seo-change-keyword';
		let a = document.createElement('a');
		a.href = '#';
		a.className = 'seo-change-keyword-link';
		a.innerText = ' | Fokus Keyword Ändern';
		a.onclick = function (e) {
			jQuery(document).ready(function ($) {
				$.ajax({
					url: myAjax.ajaxurl,
					method: 'POST',
					data: {
						action: 'get_page_keyword',
						pageId: id,
					},
					success: function (response) {
						responseObject = JSON.parse(response);
						let keyword = '';
						try {
							keyword = responseObject['keyword'];
						} catch (error) {}
						if (keyword == undefined || keyword == null) {
							keyword = '';
						}
						document.getElementById('seo-overlay-input').value = keyword;
					},
					error: function (error) {
						console.log(error);
					},
				});
			});
			e.preventDefault();
			console.log('test');

			let overlay = document.createElement('div');
			overlay.className = 'seo-overlay';
			overlay.id = 'seo-overlay';
			overlay.style.display = 'block';
			overlay.style.position = 'fixed';
			overlay.style.zIndex = '9999';
			overlay.style.top = '0';
			overlay.style.left = '0';
			overlay.style.width = '100%';
			overlay.style.height = '100%';
			overlay.style.backgroundColor = 'rgba(0,0,0,0.5)';
			overlay.style.display = 'block';

			let overlayContent = document.createElement('div');
			overlayContent.className = 'seo-overlay-content';
			overlayContent.id = 'seo-overlay-content';
			overlayContent.style.position = 'absolute';
			overlayContent.style.top = '50%';
			overlayContent.style.left = '50%';
			overlayContent.style.transform = 'translate(-50%, -50%)';
			overlayContent.style.backgroundColor = '#fefefe';
			overlayContent.style.padding = '20px';
			overlayContent.style.width = '50%';
			overlayContent.style.height = '17%';

			let overlayClose = document.createElement('span');
			overlayClose.className = 'seo-overlay-close';
			overlayClose.id = 'seo-overlay-close';
			overlayClose.innerText = 'x';
			overlayClose.style.color = '#aaa';
			overlayClose.style.float = 'right';
			overlayClose.style.fontSize = '28px';
			overlayClose.style.fontWeight = 'bold';
			overlayClose.style.cursor = 'pointer';

			overlayClose.onclick = function () {
				document.getElementById('seo-overlay').remove();
			};

			let overlayForm = document.createElement('form');
			overlayForm.className = 'seo-overlay-form';
			overlayForm.id = 'seo-overlay-form';
			overlayForm.method = 'post';
			overlayForm.action = '';

			let overlayFormInput = document.createElement('input');
			overlayFormInput.className = 'seo-overlay-input';
			overlayFormInput.id = 'seo-overlay-input';
			overlayFormInput.type = 'text';
			overlayFormInput.name = 'seo-overlay-input';
			overlayFormInput.placeholder = 'Fokus Keyword';
			overlayFormInput.style.width = '100%';
			overlayFormInput.style.padding = '12px 20px';
			overlayFormInput.style.margin = '8px 0';
			overlayFormInput.style.boxSizing = 'border-box';

			let overlayFormSubmit = document.createElement('button');
			overlayFormSubmit.className = 'seo-overlay-submit';
			overlayFormSubmit.id = 'seo-overlay-submit';
			overlayFormSubmit.type = 'submit';
			overlayFormSubmit.name = 'seo-overlay-submit';
			overlayFormSubmit.value = 'Speichern';
			overlayFormSubmit.innerText = 'Speichern';
			overlayFormSubmit.style.width = '100%';
			overlayFormSubmit.style.padding = '12px 20px';
			overlayFormSubmit.style.fontFamily = '"Gilroy", sans-serif;';
			overlayFormSubmit.style.margin = '8px 0';
			overlayFormSubmit.style.boxSizing = 'border-box';
			overlayFormSubmit.style.backgroundColor = '#afcb08';
			overlayFormSubmit.style.color = 'white';
			overlayFormSubmit.style.border = 'none';
			overlayFormSubmit.style.borderRadius = '4px';
			overlayFormSubmit.style.cursor = 'pointer';

			overlayFormSubmit.onclick = function (e) {
				e.preventDefault();
				jQuery(document).ready(function ($) {
					$.post({
						url: myAjax.ajaxurl,
						method: 'POST',

						data: {
							action: 'update_page_keyword',
							page_id: id,
							keyword: document.getElementById('seo-overlay-input').value,
						},
						success: function (response) {
							console.log(response);
							document.getElementById('seo-overlay').remove();
						},
						error: function (error) {
							console.log(error);
							document.getElementById('seo-overlay').remove();
						},
					});
				});
			};

			overlayForm.appendChild(overlayFormInput);
			overlayForm.appendChild(overlayFormSubmit);

			overlayContent.appendChild(overlayClose);
			overlayContent.appendChild(overlayForm);

			overlay.appendChild(overlayContent);

			document.body.appendChild(overlay);
		};
		span.appendChild(a);
		$this.append(span);
	});
});
