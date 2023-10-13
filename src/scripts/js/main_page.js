let dropArea;
let dropAreaTemplates;
let dropAreaVariables;
let warns = document.getElementsByClassName('notice-warning');

for (let i = 0; i < warns.length; i++) {
	warns[i].style.display = 'none';
}
let tokens;
let premium = false;
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
					premium = true;
				} catch (error) {
					premium = false;
				}
				setPremiumfields();
			},
			error: function (error) {
				console.log(error);
			},
		});
	});
}

checkpremium();

function setPremiumfields() {
	if (premium == true) {
		document.getElementById('apiKey').style.display = 'none';
		document.getElementById('apiKeyLabel').style.display = 'none';
		document.getElementById('tokensLeft').style.display = 'block';
		document.getElementById('tokensLeft').textContent = parseInt(tokens, 10).toLocaleString('de-DE');
		document.getElementById('tokensLeftLable').style.display = 'block';
	} else {
		document.getElementById('apiKey').style.display = 'block';
		document.getElementById('apiKeyLabel').style.display = 'block';
	}
}

function saveSettings() {
	var apiKey = document.getElementById('apiKey').value;
	var firmenname = document.getElementById('firmenname').value;
	var adresse = document.getElementById('adresse').value;
	var gewerbe = document.getElementById('Gewerbe').value;
	var whyUs = document.getElementById('whyUs').value;
	var usps = document.getElementById('usps').value;
	var cta = document.getElementById('cta').value;
	var shortcode = document.getElementById('shortcode').value;
	jQuery(document).ready(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			method: 'POST',
			data: {
				action: 'save_main_settings',
				apiKey: apiKey,
				firmenname: firmenname,
				adresse: adresse,
				Gewerbe: gewerbe,
				whyUs: whyUs,
				usps: usps,
				cta: cta,
				shortcode: shortcode,
			},
			success: function (response) {
				console.log(response);
				updateSettingsOption();
			},
			error: function (error) {
				console.log(error);
			},
		});
	});
}
function getHomeUrl() {
	var href = window.location.href;
	var index = href.indexOf('/wp-admin');
	var homeUrl = href.substring(0, index);
	return homeUrl;
}
var homeUrl = getHomeUrl();
var variables;
function getSettings() {
	jQuery(document).ready(function ($) {
		fetch(homeUrl + '/wp-content/plugins/SEOContent/src/scripts/php/settings.json')
			.then((response) => response.json())
			.then((json) => {
				console.log(json);
				const prompts = json;
				document.getElementById('apiKey').value = prompts.apiKey;
				document.getElementById('firmenname').value = prompts.firmenname;
				document.getElementById('adresse').value = prompts.adresse;
				document.getElementById('Gewerbe').value = prompts.Gewerbe;
				document.getElementById('whyUs').value = prompts.warumWir;
				document.getElementById('usps').value = prompts.usps;
				document.getElementById('cta').value = prompts.cta;
			});

		fetch(homeUrl + '/wp-content/plugins/SEOContent/src/scripts/php/variables.json')
			.then((response) => response.json())
			.then((json) => {
				console.log(json);
				variables = json;
				createVariables();
			});

		addButtonsToContainer();

		dropArea = document.getElementById('drop-area');
		// Handle the dropped file
		dropArea.addEventListener('drop', handleDrop, false);

		dropAreaTemplates = document.getElementById('drop-area-templates');
		// Handle the dropped file
		dropAreaTemplates.addEventListener('drop', handleDropTemplates, false);

		dropAreaVariables = document.getElementById('drop-area-variables');
		// Handle the dropped file
		dropAreaVariables.addEventListener('drop', handleDropVariables, false);

		dropArea.addEventListener('change', handleFileSelect, false);
		dropAreaTemplates.addEventListener('change', handleFileSelectTemplates, false);
		dropAreaVariables.addEventListener('change', handleFileSelectVariables, false);
	});
}

function closePopup() {
	setCookie('popup', 'closed', 7);
	document.getElementById('topText').style.display = 'none';
}
async function updateSettingsOption() {
	jQuery.ajax({
		url: myAjax.ajaxurl,
		type: 'POST',
		data: {
			action: 'update_seocontent_settings_action',
		},
		success: async function (response) {
			console.log('SEO-Content-Einstellungen wurden aktualisiert.');
			alertDone();
		},
		error: function (error) {
			console.error('Fehler beim Aktualisieren der SEO-Content-Einstellungen: ' + error.responseText);
		},
	});
	await new Promise((r) => setTimeout(r, 1000));
}
async function updateVariablesOption() {
	jQuery.ajax({
		url: myAjax.ajaxurl,
		type: 'POST',
		data: {
			action: 'update_seocontent_variables_action',
		},
		success: async function (response) {
			console.log('SEO-Content-Einstellungen wurden aktualisiert.');
			alertDone();
		},
		error: function (error) {
			console.error('Fehler beim Aktualisieren der SEO-Content-Einstellungen: ' + error.responseText);
		},
	});
	await new Promise((r) => setTimeout(r, 1000));
}
getSettings();
function changeTab(e, name) {
	let element = document.getElementById(name + 'SettingsTab');
	let tabs = document.getElementsByClassName('settingsTab');
	let tabHeader = document.getElementsByClassName('tabHeader');
	for (let i = 0; i < tabHeader.length; i++) {
		if (tabHeader[i].classList.contains('activeTab')) {
			tabHeader[i].classList.remove('activeTab');
		}
	}
	e.classList.add('activeTab');
	element.style.display = 'flex';
	for (let i = 0; i < tabs.length; i++) {
		if (tabs[i].id != name + 'SettingsTab') {
			tabs[i].style.display = 'none';
		}
	}
}
function addVariable() {
	var divElement = document.createElement('div');
	divElement.className = 'variable';
	var inputElement = document.createElement('input');
	inputElement.type = 'text';
	inputElement.name = 'variableName';
	inputElement.id = 'variableName';
	inputElement.className = 'variableInput';
	inputElement.placeholder = 'Name der Variable';
	var spanElement = document.createElement('span');
	spanElement.className = 'variableSpan';
	spanElement.textContent = '=>';
	var textareaElement = document.createElement('textarea');
	textareaElement.name = 'variableValue';
	textareaElement.id = 'variableValue';
	textareaElement.className = 'variableInput variableInputWert';
	textareaElement.placeholder = 'Wert';
	textareaElement.cols = '30';
	textareaElement.rows = '1';

	divElement.appendChild(inputElement);
	divElement.appendChild(spanElement);
	divElement.appendChild(textareaElement);

	var zielDiv = document.getElementById('variablenContainer');

	zielDiv.appendChild(divElement);
}
function createVariables() {
	let keys = Object.keys(variables);
	let values = Object.values(variables);
	for (i = 0; i < keys.length - 1; i++) {
		addVariable();
	}
	let keysDiv = document.getElementsByName('variableName');
	let valuesDiv = document.getElementsByName('variableValue');

	for (i = 0; i < keys.length; i++) {
		keysDiv[i].value = keys[i];
		valuesDiv[i].value = values[i];
	}
}
function saveVariableSettings() {
	let keys = document.getElementsByName('variableName');
	let values = document.getElementsByName('variableValue');

	let saveVariableSettings = {};

	for (let i = 0; i < keys.length; i++) {
		let key = keys[i].value;
		let value = values[i].value;
		if (key == '' && value == '') {
			continue;
		}
		saveVariableSettings[key] = value;
	}

	jQuery(document).ready(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			method: 'POST',
			data: {
				action: 'save_variable_settings',
				variables: saveVariableSettings,
			},
			success: function (response) {
				console.log(response);
				updateVariablesOption();
			},
			error: function (error) {
				console.log(error);
			},
		});
	});
}
function alertDone() {
	alert('Die Einstellungen wurden erfolgreich gespeichert.');
}
function importJson(type) {
	if (type == 'settings') {
		var file = document.getElementById('settingsFile').files[0];
	}
	if (type == 'variables') {
		var file = document.getElementById('variablesFile').files[0];
	}
	if (type == 'templates') {
		var file = document.getElementById('templatesFile').files[0];
	}
	if (!file) {
		alert('Bitte wählen Sie eine Datei aus.');
		return;
	}
	var reader = new FileReader();
	reader.readAsText(file);
	reader.onload = function (e) {
		var json = JSON.parse(e.target.result);
		console.log(json);
		if (type == 'settings') {
			jQuery(document).ready(function ($) {
				$.ajax({
					url: myAjax.ajaxurl,
					method: 'POST',
					data: {
						action: 'save_main_settings',
						apiKey: json['apiKey'],
						firmenname: json['firmenname'],
						adresse: json['adresse'],
						Gewerbe: json['Gewerbe'],
						whyUs: json['warumWir'],
						usps: json['usps'],
						cta: json['cta'],
						shortcode: json['shortcode'],
					},
					success: function (response) {
						console.log(response);
						updateSettingsOption();
					},
					error: function (error) {
						console.log(error);
					},
				});
			});
		}
		if (type == 'variables') {
			jQuery(document).ready(function ($) {
				$.ajax({
					url: myAjax.ajaxurl,
					method: 'POST',
					data: {
						action: 'save_variable_settings',
						variables: json,
					},
					success: function (response) {
						console.log(response);
						updateVariablesOption();
					},
					error: function (error) {
						console.log(error);
					},
				});
			});
		}
		if (type == 'templates') {
			jQuery(document).ready(function ($) {
				$.ajax({
					url: myAjax.ajaxurl,
					method: 'POST',
					data: {
						action: 'import_seocontent_template_action',
						templates: json,
					},
					success: function (response) {
						console.log(response);
						alertDone();
					},
					error: function (error) {
						console.log(error);
					},
				});
			});
		}
	};
}

function addButtonsToContainer() {
	// Finde das Container-Div
	var container = document.getElementById('exportButtonContainer');

	// Erstelle den ersten Button für Einstellungen und füge ihn in ein <a> Tag ein
	var settingsButton = document.createElement('a');
	settingsButton.href = homeUrl + '/wp-content/plugins/SEOContent/src/scripts/php/settings.json';
	settingsButton.appendChild(document.createTextNode('Einstellungen Exportieren'));
	settingsButton.className = 'exportButton';
	settingsButton.download = 'settings.json';
	container.appendChild(settingsButton);

	// Erstelle den zweiten Button für Vorlagen und füge ihn in ein <a> Tag ein
	var templatesButton = document.createElement('a');
	templatesButton.href = homeUrl + '/wp-content/plugins/SEOContent/src/scripts/php/templateTest.json'; // Setze den Link auf "#" oder ein anderes Ziel
	templatesButton.appendChild(document.createTextNode('Vorlagen Exportieren'));
	templatesButton.className = 'exportButton';
	templatesButton.download = 'templates.json';
	container.appendChild(templatesButton);

	// Erstelle den dritten Button für Variablen und füge ihn in ein <a> Tag ein
	var variablesButton = document.createElement('a');
	variablesButton.href = homeUrl + '/wp-content/plugins/SEOContent/src/scripts/php/variables.json'; // Setze den Link auf "#" oder ein anderes Ziel
	variablesButton.appendChild(document.createTextNode('Variablen Exportieren'));
	variablesButton.download = 'variables.json';
	variablesButton.className = 'exportButton';
	container.appendChild(variablesButton);
}

function handleDrop(e) {
	const dt = e.dataTransfer;
	const files = dt.files;

	// Handle the dropped files here
	if (files.length > 0) {
		// You can trigger your JavaScript event or function here
		// For example, you can access the first dropped file with files[0]
		// files[0] will contain the File object, and you can perform further actions with it.
		// For instance, you can read the file contents or validate the file type.
		console.log('File dropped:', files[0].name);
		document.getElementById('settingsFileText').textContent = files[0].name;
	}
}
async function handleDropTemplates(e) {
	const dt = e.dataTransfer;
	const files = dt.files;

	// Handle the dropped files here
	if (files.length > 0) {
		// You can trigger your JavaScript event or function here
		// For example, you can access the first dropped file with files[0]
		// files[0] will contain the File object, and you can perform further actions with it.
		// For instance, you can read the file contents or validate the file type.
		console.log('File dropped:', files[0].name);
		document.getElementById('templatesFileText').textContent = files[0].name;
	}
}
function handleDropVariables(e) {
	const dt = e.dataTransfer;
	const files = dt.files;

	// Handle the dropped files here
	if (files.length > 0) {
		// You can trigger your JavaScript event or function here
		// For example, you can access the first dropped file with files[0]
		// files[0] will contain the File object, and you can perform further actions with it.
		// For instance, you can read the file contents or validate the file type.
		console.log('File dropped:', files[0].name);
		document.getElementById('variablesFileText').textContent = files[0].name;
	}
}
function handleFileSelect(event) {
	const selectedFile = event.target.files[0];

	if (selectedFile) {
		// You can now access the selected file and perform actions with it
		console.log('Selected file:', selectedFile.name);
		document.getElementById('settingsFileText').textContent = selectedFile.name;
	}
}

function handleFileSelectTemplates(event) {
	const selectedFile = event.target.files[0];

	if (selectedFile) {
		// You can now access the selected file and perform actions with it
		console.log('Selected file:', selectedFile.name);
		document.getElementById('templatesFileText').textContent = selectedFile.name;
	}
}
function handleFileSelectVariables(event) {
	const selectedFile = event.target.files[0];

	if (selectedFile) {
		// You can now access the selected file and perform actions with it
		console.log('Selected file:', selectedFile.name);
		document.getElementById('variablesFileText').textContent = selectedFile.name;
	}
}
