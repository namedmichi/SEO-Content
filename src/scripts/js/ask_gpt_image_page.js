function getHomeUrl() {
	var href = window.location.href;
	var index = href.indexOf('/wp-admin');
	var homeUrl = href.substring(0, index);
	return homeUrl;
}
homeUrl = getHomeUrl();
let loadingText = document.getElementById('loadingText');
var settingsArray;
const request2 = new XMLHttpRequest();
var jsonUrl = homeUrl + '/wp-content/plugins/SEOContent/src/scripts/php/settings.json'; // Replace with the actual URL of your JSON file
var apiKey = '';

let warns = document.getElementsByClassName('notice-warning');

for (let i = 0; i < warns.length; i++) {
	warns[i].style.display = 'none';
}

let tokens;
let premium = 'false';
function checkpremium() {
	jQuery(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			method: 'POST',
			data: {
				action: 'get_tokens',
			},
			success: function (response) {
				let array = JSON.parse(response);
				try {
					tokens = array['tokens'];
					premium = 'true';
				} catch (error) {
					console.log(error);
					premium = 'false';
				}
			},
			error: function (error) {
				console.log(error);
			},
		});
	});
}

checkpremium();

request2.open('GET', jsonUrl, true);
request2.onreadystatechange = function () {
	if (request2.readyState === 4 && request2.status === 200) {
		// Parse the JSON response
		const json = JSON.parse(request2.responseText);

		// Save the JSON data to a variable
		const jsonData = json;
		settingsArray = jsonData;
		apiKey = settingsArray['apiKey'];
		// Use the jsonData variable as needed
		console.log(jsonData);
	}
};
request2.send();

function create_image() {
	document.getElementById('overlay').style.display = 'flex';
	document.body.classList.add('blurred');
	document.body.classList.add('no-scroll');
	count = document.getElementById('count').value;
	image_prompt = document.getElementById('nmd_image_prompt').value;
	if (count == '') {
		count = 1;
		loadingText.innerHTML = 'Bild wird erstellt..';
	} else {
		loadingText.innerHTML = 'Bilder werden erstellt..';
	}
	console.log(count);
	let checkboxes = document.getElementsByName('selectImage');
	for (let index = 1; index <= count; index++) {
		checkboxes[index - 1].checked = true;
		jQuery(document).ready(function ($) {
			$.ajax({
				url: myAjax.ajaxurl,
				type: 'POST',
				data: {
					action: 'gpt_create_image',
					image_prompt: image_prompt,
					premium: premium,
				},
				success: function (response) {
					response = response.slice(0, -1);
					console.log(response);
					document.getElementById('nmd_image_' + index).src = response;
					document.getElementById('overlay').style.display = 'none';
					document.body.classList.remove('blurred');
					document.body.classList.remove('no-scroll');
				},
				error: function (error) {
					console.log(error);
					alert(
						'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dan erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
					);
					document.getElementById('overlay').style.display = 'none';
					document.body.classList.remove('blurred');
					document.body.classList.remove('no-scroll');
				},
			});
		});
	}
}

function add_image() {
	document.getElementById('overlay').style.display = 'flex';
	document.body.classList.add('blurred');
	document.body.classList.add('no-scroll');
	loadingText.innerHTML = 'Bilder werden hinzugefügt...';
	count = document.getElementById('count').value;
	let checkboxes = document.getElementsByName('selectImage');
	for (let index = 0; index <= count; index++) {
		if (checkboxes[index].checked == false) {
			count--;
		}
	}
	if (count == 0) {
		document.getElementById('overlay').style.display = 'none';
		document.body.classList.remove('blurred');
		document.body.classList.remove('no-scroll');

		return;
	}
	var image_urls = ['', '', ''];
	var title = document.getElementById('nmd_image_prompt').value;
	let tempI = 0;
	for (let index = 0; index <= count; index++) {
		if (checkboxes[index].checked == true) {
			image_urls[tempI] = document.getElementById('nmd_image_' + (index + 1)).src;
			tempI++;
		}
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

				alert('Bilder wurden erfolgreich hinzugefügt. Sie können sie nun in der Mediathek finden.');
			},
			error: function (error) {
				console.log(error);
				document.getElementById('overlay').style.display = 'none';
				document.body.classList.remove('blurred');
				document.body.classList.remove('no-scroll');
			},
		});
	});
}
// JavaScript for image upload and drawing
const imageUpload = document.getElementById('image-upload');
const imageCanvas = document.getElementById('image-canvas');
const imageCanvasHidden = document.getElementById('image-canvas-hidden');
let tempCanvas = document.getElementById('tempCanvas');
const ctx = imageCanvas.getContext('2d');
const ctxHidden = imageCanvasHidden.getContext('2d');
let tempCtx = tempCanvas.getContext('2d');
const penSizeInput = document.getElementById('pen-size');
const submitButton = document.getElementById('submit-button');
let eraserCheckbox = document.getElementById('erase');
let orgFile;
let orgSrc;
let isDrawing = false;
let orgFilename;
let orgImage;

imageUpload.addEventListener('change', function (e) {
	const file = e.target.files[0];
	orgFilename = file.name;
	orgSrc = file;
	console.log(orgSrc);
	if (file) {
		const reader = new FileReader();
		reader.onload = function (e) {
			const img = new Image();
			img.src = e.target.result;
			img.onload = function () {
				imageCanvas.width = img.width;
				imageCanvas.height = img.height;
				imageCanvasHidden.width = img.width;
				imageCanvasHidden.height = img.height;
				tempCanvas.width = img.width;
				tempCanvas.height = img.height;
				orgImage = img;
				ctx.drawImage(img, 0, 0, img.width, img.height);
				orgFile = imageCanvas.toDataURL('image/png');
			};
		};
		reader.readAsDataURL(file);
	}
});

tempCanvas.addEventListener('mousedown', () => {
	isDrawing = true;
	tempCtx.lineWidth = penSizeInput.value;
	tempCtx.lineCap = 'round';
	if (eraserCheckbox.checked) {
		tempCtx.globalCompositeOperation = 'destination-out';
	} else {
		tempCtx.globalCompositeOperation = 'source-over';
		tempCtx.strokeStyle = '#000';
	}
	tempCtx.beginPath();
});

tempCanvas.addEventListener('mousemove', (e) => {
	if (!isDrawing) return;
	tempCtx.lineTo(e.clientX - tempCanvas.getBoundingClientRect().left, e.clientY - tempCanvas.getBoundingClientRect().top);
	tempCtx.stroke();
});

tempCanvas.addEventListener('mouseup', () => {
	isDrawing = false;
	tempCtx.closePath();
});
function mergeAndExport() {
	// Zeichne den Inhalt des temporären Canvas auf den Hintergrund-Canvas
	ctx.drawImage(tempCanvas, 0, 0);

	// Konvertiere den Inhalt des Hintergrund-Canvas in einen Base64-String
	let base64String = imageCanvas.toDataURL();

	return base64String;
}

submitButton.addEventListener('click', async () => {
	if (orgFile == null || orgFile == undefined) {
		alert('Bitte wählen Sie ein Bild aus');
		return;
	}
	document.getElementById('overlay').style.display = 'flex';
	document.body.classList.add('blurred');
	document.body.classList.add('no-scroll');
	loadingText.innerHTML = 'Bild wird bearbeitet...';
	const originalImage = imageCanvas.toDataURL('image/png');

	// Create a new image with transparent background for the edited image
	const editedImage = new Image();
	editedImage.src = originalImage;
	editedImage.onload = async function () {
		ctx.clearRect(0, 0, imageCanvas.width, imageCanvas.height); // Clear canvas
		ctx.drawImage(editedImage, 0, 0); // Draw the edited image with transparent background

		// Send originalImage and editedImage to OpenAI API using JavaScript fetch or AJAX here
		// Replace this comment with your API integration code
		document.getElementById('overlay').style.display = 'flex';
		document.body.classList.add('blurred');
		document.body.classList.add('no-scroll');

		draw();
		let baseImage = orgFile;
		let splitarray = baseImage.split('base64,');

		let inpaintImage = imageCanvasHidden.toDataURL('image/png');
		let splitarrayMask = inpaintImage.split('base64,');

		console.log(splitarray[1]);
		console.log(splitarrayMask[1]);
		jQuery(document).ready(function ($) {
			$.ajax({
				url: myAjax.ajaxurl,
				type: 'POST',
				data: {
					action: 'gpt_edit_image',
					orgBase64: splitarray[1],
					maskBase64: splitarrayMask[1],
					prompt: document.getElementById('editPrompt').value,
					premium: premium,
				},
				success: function (response) {
					response = response.slice(0, -1);
					let json = JSON.parse(response);
					let imgUrl;
					try {
						console.log(json[0].url);
						imgUrl = json[0].url;
					} catch (error) {
						try {
							imgUrl = json.data[0].url;
						} catch (error) {
							alert(
								'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dan erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
							);
						}

						document.getElementById('overlay').style.display = 'none';
						document.body.classList.remove('blurred');
						document.body.classList.remove('no-scroll');
					}
					document.getElementById('editedImage').src = imgUrl;

					document.getElementById('editedImage').style.height =
						document.getElementById('image-canvas').getContext('2d').canvas.height + 'px';
					document.getElementById('editedImage').style.width =
						document.getElementById('image-canvas').getContext('2d').canvas.width + 'px';
					ctx.drawImage(orgImage, 0, 0, orgImage.width, orgImage.height);

					document.getElementById('overlay').style.display = 'none';
					document.body.classList.remove('blurred');
					document.body.classList.remove('no-scroll');
				},
				error: function (error) {
					console.log(error);

					alert(
						'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dan erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
					);

					document.getElementById('overlay').style.display = 'none';
					document.body.classList.remove('blurred');
					document.body.classList.remove('no-scroll');
				},
			});
		});
	};
});

function imageVariation() {
	if (orgFile == null || orgFile == undefined) {
		alert('Bitte wählen Sie ein Bild aus');
		return;
	}
	let baseImage = orgFile;
	let splitarray = baseImage.split('base64,');

	document.getElementById('overlay').style.display = 'flex';
	document.body.classList.add('blurred');
	document.body.classList.add('no-scroll');
	loadingText.innerHTML = 'Bild wird bearbeitet...';
	jQuery(document).ready(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			type: 'POST',
			data: {
				action: 'gpt_image_variation',
				orgBase64: splitarray[1],
				premium: premium,
			},
			success: function (response) {
				response = response.slice(0, -1);
				let json = JSON.parse(response);
				let imgUrl;
				try {
					console.log(json.data[0].url);
					imgUrl = json.data[0].url;
				} catch (error) {
					alert(
						'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dan erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
					);

					document.getElementById('overlay').style.display = 'none';
					document.body.classList.remove('blurred');
					document.body.classList.remove('no-scroll');
				}
				document.getElementById('editedImage').src = imgUrl;

				document.getElementById('editedImage').style.height =
					document.getElementById('image-canvas').getContext('2d').canvas.height + 'px';
				document.getElementById('editedImage').style.width =
					document.getElementById('image-canvas').getContext('2d').canvas.width + 'px';

				document.getElementById('overlay').style.display = 'none';
				document.body.classList.remove('blurred');
				document.body.classList.remove('no-scroll');
			},
			error: function (error) {
				console.log(error);

				alert(
					'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dan erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
				);

				document.getElementById('overlay').style.display = 'none';
				document.body.classList.remove('blurred');
				document.body.classList.remove('no-scroll');
			},
		});
	});
}

function draw() {
	mergeAndExport();
	let img = document.getElementById('image-canvas').getContext('2d').canvas;
	var buffer = document.createElement('canvas');
	buffer.width = img.width;
	buffer.height = img.height;
	var bufferctx = buffer.getContext('2d');
	bufferctx.drawImage(img, 0, 0);
	var imageData = bufferctx.getImageData(0, 0, imageCanvas.width, imageCanvas.height);
	var data = imageData.data;
	var removeBlack = function () {
		for (var i = 0; i < data.length; i += 4) {
			if (data[i] + data[i + 1] + data[i + 2] == 0) {
				data[i + 3] = 0; // alpha
			}
		}
		imageData.data = data;
		ctxHidden.putImageData(imageData, 0, 0);
	};
	removeBlack();
}
function setLoadingTest() {
	document.getElementById('overlay').style.display = 'flex';
	document.body.classList.add('blurred');
}
function saveEditedImage() {
	document.getElementById('overlay').style.display = 'flex';
	document.body.classList.add('blurred');
	document.body.classList.add('no-scroll');

	loadingText.innerHTML = 'Bild wird gespeichert...';
	let image_urls = ['', '', ''];
	if (document.getElementById('editedImage').src.includes('image.php')) {
		alert('Bitte erstellen Sie ein bevor Sie es speichern');
		return;
	}
	image_urls[0] = document.getElementById('editedImage').src;
	jQuery(document).ready(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			type: 'POST',
			data: {
				action: 'add_image',
				image_urls: image_urls,
				count: 1,
				title: orgFilename,
			},
			success: function (response) {
				console.log(response);
				document.getElementById('overlay').style.display = 'none';
				document.body.classList.remove('blurred');
				document.body.classList.remove('no-scroll');

				alert('Bilder wurden erfolgreich hinzugefügt. Sie können sie nun in der Mediathek finden.');
			},
			error: function (error) {
				console.log(error);
				document.getElementById('overlay').style.display = 'none';
				document.body.classList.remove('blurred');
				document.body.classList.remove('no-scroll');
			},
		});
	});
}
