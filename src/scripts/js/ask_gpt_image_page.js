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
				try {
					let array = JSON.parse(response);
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
	setLoadingScreen();
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
		makeImageRequest(image_prompt, premium, index);
	}
}

async function makeImageRequest(image_prompt, premium, index) {
	jQuery(document).ready(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			type: 'POST',
			data: {
				action: 'gpt_create_image',
				image_prompt: image_prompt,
				premium: premium,
			},
			success: async function (response) {
				let task_id = JSON.parse(response)['task_id'];
				let finished = false;
				while (!finished) {
					jQuery.ajax({
						url: myAjax.ajaxurl,
						method: 'POST',
						data: {
							action: 'check_task_status_image',
							task_id: task_id,
						},
						success: function (response) {
							console.log(response);
							if (!response.includes('Task still processing.')) {
								finished = true;
								console.log(response);
								let obj = JSON.parse(response);
								response = obj['data'][0]['url'];
								document.getElementById('nmd_image_' + index).src = response;
								removeLoadingScreen();
							} else if (response.includes('failed.')) {
								alert(
									'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dann erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
								);
								removeLoadingScreen();
							} else {
								console.log("Task still processing. Let's wait 1 seconds and try again.");
								console.log(response);
							}
						},
						error: function (error) {
							console.log(error);
							finished = true;
						},
					});
					await new Promise((r) => setTimeout(r, 3000));
				}
			},
			error: function (error) {
				console.log(error);
				alert(
					'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dann erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
				);
				removeLoadingScreen();
			},
		});
	});
}

function add_image() {
	document.getElementById('overlay').style.display = 'flex';
	document.body.classList.add('blurred');
	document.body.classList.add('no-scroll');
	loadingText.innerHTML = 'Bilder werden hinzugefügt...';
	count = document.getElementById('count').value;
	let checkboxes = document.getElementsByName('selectImage');
	for (let index = 0; index < count; index++) {
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
	for (let index = 0; index < count; index++) {
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
				success: async function (response) {
					let task_id = JSON.parse(response)['task_id'];
					let finished = false;
					while (!finished) {
						jQuery.ajax({
							url: myAjax.ajaxurl,
							method: 'POST',
							data: {
								action: 'check_task_status_image',
								task_id: task_id,
							},
							success: function (response) {
								console.log(response);
								if (!response.includes('Task still processing.')) {
									finished = true;
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
												'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dann erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
											);
										}

										removeLoadingScreen();
									}
									document.getElementById('editedImage').src = imgUrl;
									document.getElementById('editedImage').style.height =
										document.getElementById('image-canvas').getContext('2d').canvas.height + 'px';
									document.getElementById('editedImage').style.width =
										document.getElementById('image-canvas').getContext('2d').canvas.width + 'px';
									ctx.drawImage(orgImage, 0, 0, orgImage.width, orgImage.height);

									removeLoadingScreen();
								} else if (response.includes('failed.')) {
									alert(
										'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dann erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
									);
									removeLoadingScreen();
								} else {
									console.log("Task still processing. Let's wait 1 seconds and try again.");
									console.log(response);
								}
							},
							error: function (error) {
								console.log(error);
								finished = true;
							},
						});
						await new Promise((r) => setTimeout(r, 3000));
					}
				},
				error: function (error) {
					console.log(error);

					alert(
						'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dann erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
					);

					document.getElementById('overlay').style.display = 'none';
					document.body.classList.remove('blurred');
					document.body.classList.remove('no-scroll');
				},
			});
		});
	};
});

// function imageVariation() {
// 	if (orgFile == null || orgFile == undefined) {
// 		alert('Bitte wählen Sie ein Bild aus');
// 		return;
// 	}
// 	let baseImage = orgFile;
// 	let splitarray = baseImage.split('base64,');

// 	setLoadingScreen();
// 	loadingText.innerHTML = 'Bild wird bearbeitet...';
// 	jQuery(document).ready(function ($) {
// 		$.ajax({
// 			url: myAjax.ajaxurl,
// 			type: 'POST',
// 			data: {
// 				action: 'gpt_image_variation',
// 				orgBase64: splitarray[1],
// 				premium: premium,
// 			},
// 			success: function (response) {
// 				response = response.slice(0, -1);
// 				let json = JSON.parse(response);
// 				let imgUrl;
// 				try {
// 					console.log(json.data[0].url);
// 					imgUrl = json.data[0].url;
// 				} catch (error) {
// 					alert(
// 						'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dann erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
// 					);

// 					removeLoadingScreen();
// 				}
// 				document.getElementById('editedImage').src = imgUrl;

// 				document.getElementById('editedImage').style.height =
// 					document.getElementById('image-canvas').getContext('2d').canvas.height + 'px';
// 				document.getElementById('editedImage').style.width =
// 					document.getElementById('image-canvas').getContext('2d').canvas.width + 'px';

// 				removeLoadingScreen();
// 			},
// 			error: function (error) {
// 				console.log(error);

// 				alert(
// 					'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dann erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
// 				);

// 				removeLoadingScreen();
// 			},
// 		});
// 	});
// }

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
	setLoadingScreen();

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
				removeLoadingScreen();

				alert('Bilder wurden erfolgreich hinzugefügt. Sie können sie nun in der Mediathek finden.');
			},
			error: function (error) {
				console.log(error);
				removeLoadingScreen();
			},
		});
	});
}
function editImage(n) {
	loadingText.innerHTML = 'Bild wird geladen...';
	let url = document.getElementById('nmd_image_' + n).src;
	if (url == '' || url == undefined || url == null) {
		alert('Bitte erstellen Sie erst ein Bild');
		return;
	}
	setLoadingScreen();
	console.log(url);
	jQuery(document).ready(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			type: 'POST',
			data: {
				action: 'fetch_image_as_blob',
				url: url,
			},
			success: function (response) {
				console.log(response);
				let blob = dataURLtoBlob(response);
				const reader = new FileReader();
				reader.onload = function (e) {
					const img = new Image();
					img.width = 512;
					img.height = 512;
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
						removeLoadingScreen();
					};
				};
				reader.readAsDataURL(blob);
			},
			error: function (error) {
				console.log(error);
				removeLoadingScreen();
			},
		});
	});
}

function reuseImage() {
	loadingText.innerHTML = 'Bild wird geladen...';
	url = document.getElementById('editedImage').src;
	console.log(url);
	if (url == '' || url == undefined || url == null || url.includes('image.php')) {
		alert('Bitte erstellen Sie erst ein Bild');
		return;
	}
	setLoadingScreen();
	console.log(url);
	jQuery(document).ready(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			type: 'POST',
			data: {
				action: 'fetch_image_as_blob',
				url: url,
			},
			success: function (response) {
				console.log(response);
				let blob = dataURLtoBlob(response);
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
						removeLoadingScreen();
					};
				};
				reader.readAsDataURL(blob);
			},
			error: function (error) {
				console.log(error);
				removeLoadingScreen();
			},
		});
	});
}

function dataURLtoBlob(dataurl) {
	let arr = dataurl.split(','),
		mime = arr[0].match(/:(.*?);/)[1];
	let byteString = atob(arr[1]);
	let n = byteString.length;
	let u8arr = new Uint8Array(n);

	while (n--) {
		u8arr[n] = byteString.charCodeAt(n);
	}

	return new Blob([u8arr], { type: mime });
}
let startText = true;

function setLoadingScreen() {
	document.getElementById('overlay').style.display = 'flex';
	document.body.classList.add('blurred');
	if (startText) {
		startText = false;
		startCycleText();
	}
}
function removeLoadingScreen() {
	document.getElementById('overlay').style.display = 'none';
	document.body.classList.remove('blurred');
	startText = true;
}

const sprueche = [
	'Die Kunst der Bildgenerierung erfordert ihre Zeit – wir sind gleich fertig!',
	'Unsere kreativen Pixelkünstler arbeiten im Hintergrund – gleich kommt etwas Beeindruckendes!',
	'Jedes Bild ist ein kleines Meisterwerk – bitte habe Geduld während des Generierungsprozesses.',
	'Das Erschaffen von visueller Magie braucht seine Zeit – wir sind auf dem richtigen Weg!',
	'Bilder sagen mehr als tausend Worte – wir erstellen gerade welche für dich!',
	'Während die Pixel tanzen, zaubern wir für dich beeindruckende Bilder!',
	'Deine Website wird gleich mit atemberaubenden Grafiken verschönert.',
	'Wir formen Pixel in Kunstwerke um – die Wartezeit wird sich lohnen!',
	'Während wir arbeiten, entstehen einzigartige Bilder für deine Website.',
	'Die Bildgenerierung ist im Gange... gleich ist es soweit!',
	'Wie lange es wohl dauert so ein Bild selber zu erstellen? Zum Glück wirst du das nicht herausfinden müssen!',
	'Diese Ladezeit ist der Schlüssel zu beeindruckenden Bildern, die deine Besucher fesseln werden.',
	'Während wir hier laden, entstehen Bilder, die deine Konkurrenz beneiden wird.',
	'In der Zeit, die wir hier brauchen, um Bilder zu erstellen, würden andere gerade erst mit dem Zeichnen beginnen.',
	'Nutze diese Ladezeit, um zu erkennen, wie unsere Bildgenerierung dich voranbringt.',
	'Gib uns diese kurze Zeit, um deine Website mit erstaunlichen Bildern zu verschönern.',
	'Während wir hier arbeiten, haben andere noch nicht einmal angefangen, über Bilder nachzudenken.',
];

let firstImageTry = true;
async function startCycleText() {
	let loadingTextElement = document.getElementById('loadingText');
	if (firstImageTry) {
		loadingTextElement.innerHTML = 'Dieser Vorgang kann einige Minuten dauern. Bitte warten Sie einen Moment...';
		await new Promise((r) => setTimeout(r, 5000));
		firstImageTry = false;
	}
	while (!startText) {
		randInt = Math.floor(Math.random() * sprueche.length);
		loadingTextElement.innerHTML = sprueche[randInt];
		randTImeout = Math.floor(Math.random() * 1000) + 8000;
		await new Promise((r) => setTimeout(r, randTImeout));
	}
}

jQuery(document).ready(function ($) {
	var mediaUploader;

	$('#upload_image_button').click(function (e) {
		e.preventDefault();
		console.log('test');
		// If the uploader object has already been created, reopen the dialog
		if (mediaUploader) {
			mediaUploader.open();
			return;
		}

		// Extend the wp.media object
		mediaUploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Image',
			button: {
				text: 'Choose Image',
			},
			multiple: false,
		});

		// When an image is selected, run a callback
		mediaUploader.on('select', function () {
			var attachment = mediaUploader.state().get('selection').first().toJSON();
			$('#image_url').val(attachment.url);
			loadMediaImage();
		});

		// Open the uploader dialog
		mediaUploader.open();
	});
});

function loadMediaImage() {
	loadingText.innerHTML = 'Bild wird geladen...';
	let url = document.getElementById('image_url').value;
	if (url == '' || url == undefined || url == null) {
		alert('Bitte erstellen Sie erst ein Bild');
		return;
	}
	setLoadingScreen();
	console.log(url);
	jQuery(document).ready(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			type: 'POST',
			data: {
				action: 'fetch_image_as_blob',
				url: url,
			},
			success: function (response) {
				console.log(response);
				let blob = dataURLtoBlob(response);
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
						removeLoadingScreen();
					};
				};
				reader.readAsDataURL(blob);
			},
			error: function (error) {
				console.log(error);
				removeLoadingScreen();
			},
		});
	});
}
