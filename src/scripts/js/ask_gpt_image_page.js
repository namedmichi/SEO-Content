function create_image() {
	document.getElementById('overlay').style.display = 'flex';
	document.body.classList.add('blurred');
	document.body.classList.add('no-scroll');
	document.getElementsByTagName('html')[0].style.paddingTop = '0';
	count = document.getElementById('count').value;
	image_prompt = document.getElementById('nmd_image_prompt').value;
	if (count == '') {
		count = 1;
	}
	console.log(count);
	for (let index = 1; index <= count; index++) {
		jQuery(document).ready(function ($) {
			$.ajax({
				url: myAjax.ajaxurl,
				type: 'POST',
				data: {
					action: 'gpt_create_image',
					image_prompt: image_prompt,
				},
				success: function (response) {
					response = response.slice(0, -1);
					console.log(response);
					document.getElementById('nmd_image_' + index).src = response;
					document.getElementById('overlay').style.display = 'none';
					document.body.classList.remove('blurred');
					document.body.classList.remove('no-scroll');
					document.getElementsByTagName('html')[0].style.paddingTop = '32px';
				},
				error: function (error) {
					console.log(error);
					alert(
						'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dan erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
					);
					document.getElementById('overlay').style.display = 'none';
					document.body.classList.remove('blurred');
					document.body.classList.remove('no-scroll');
					document.getElementsByTagName('html')[0].style.paddingTop = '32px';
				},
			});
		});
	}
}

function add_image() {
	document.getElementById('overlay').style.display = 'flex';
	document.body.classList.add('blurred');
	document.body.classList.add('no-scroll');
	document.getElementsByTagName('html')[0].style.paddingTop = '0';
	count = document.getElementById('count').value;
	var image_urls = ['', '', ''];
	var title = document.getElementById('nmd_image_prompt').value;
	for (let index = 1; index <= count; index++) {
		image_urls[index - 1] = document.getElementById('nmd_image_' + index).src;
	}
	console.log(image_urls);
	jQuery(document).ready(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			type: 'POST',
			data: {
				action: 'add_image',
				image_urls: image_urls,
				count: count,
				title: title,
			},
			success: function (response) {
				console.log(response);
				document.getElementById('overlay').style.display = 'none';
				document.body.classList.remove('blurred');
				document.body.classList.remove('no-scroll');
				document.getElementsByTagName('html')[0].style.paddingTop = '32px';
				alert('Bilder wurden erfolgreich hinzugefügt. Sie können sie nun in der Mediathek finden.');
			},
			error: function (error) {
				console.log(error);
				document.getElementById('overlay').style.display = 'none';
				document.body.classList.remove('blurred');
				document.body.classList.remove('no-scroll');
				document.getElementsByTagName('html')[0].style.paddingTop = '32px';
			},
		});
	});
}
