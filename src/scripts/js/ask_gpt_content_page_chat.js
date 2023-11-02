const API_ENDPOINT = 'https://api.openai.com/v1/chat/completions';

var ton;
var stil;
var topic;
var title;
var abschnitte;
var ueberschriften;
var inhaltCount;
let templateList;
var var_prompt = '';
var promptList;
let loadingText = document.getElementById('loadingText');
var chat;

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
				try {
					let array = JSON.parse(response);
					tokens = array['tokens'];
					premium = true;
				} catch (error) {
					premium = false;
				}
				setPremiumfields();
			},
			error: function (error) {
				console.log(error);
				setPremiumfields();
			},
		});
	});
}

checkpremium();

function setPremiumfields() {
	if (premium == true) {
	} else {
		document.getElementById('keywordRechercheButton').style.backgroundColor = 'gray';
		document.getElementById('keywordRechercheButton').disabled = true;
		document.getElementById('keywordRechercheButton').ariaLabel = 'Premium benötigt';
		document.getElementById('keywordRechercheButton').title = 'Premium benötigt';
		document.getElementById('keywordRechercheButton').style.color = '#d0d0d0';
		document.getElementById('keywordRechercheButton').style.cursor = 'not-allowed';
		document.getElementById('keywordRechercheButton').style.border = 'none';
	}
}

function getHomeUrl() {
	var href = window.location.href;
	var index = href.indexOf('/wp-admin');
	var homeUrl = href.substring(0, index);
	return homeUrl;
}
homeUrl = getHomeUrl();
const request = new XMLHttpRequest();
var jsonUrl = homeUrl + '/wp-content/plugins/SEOContent/src/scripts/php/prompts.json'; // Replace with the actual URL of your JSON file

request.open('GET', jsonUrl, true);
request.onreadystatechange = function () {
	if (request.readyState === 4 && request.status === 200) {
		// Parse the JSON response
		const json = JSON.parse(request.responseText);

		// Save the JSON data to a variable
		const jsonData = json;
		promptList = jsonData;
		document.getElementById('title_prompt').value = promptList['titlePrompt'];
		document.getElementById('abschnitte_prompt').value = promptList['ueberschriftenPrompt'];
		document.getElementById('inhalt_prompt').value = promptList['inhaltPrompt'];
		document.getElementById('excerp_prompt').value = promptList['excerpPrompt'];
		// Use the jsonData variable as needed
		console.log(jsonData);
		let systemprompt = promptList['systemRole'];

		chat = [
			{
				role: 'system',
				content: systemprompt,
			},
		];
	}
};
request.send();

const request3 = new XMLHttpRequest();

var jsonUrl = homeUrl + '/wp-content/plugins/SEOContent/src/scripts/php/templateTest.json'; // Replace with the actual URL of your JSON file
request3.open('GET', jsonUrl, true);
request3.onreadystatechange = function () {
	if (request3.readyState === 4 && request3.status === 200) {
		// Parse the JSON response
		const json = JSON.parse(request3.responseText);

		// Save the JSON data to a variable
		const jsonData = json;
		templateList = jsonData;

		// Use the jsonData variable as needed
		console.log(jsonData);
	}
};

request3.send();

var settingsArray;
const request2 = new XMLHttpRequest();
var jsonUrl = homeUrl + '/wp-content/plugins/SEOContent/src/scripts/php/settings.json'; // Replace with the actual URL of your JSON file
var apiKey = '';
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
getCustomVariables();
request2.send();

function get_template(folder, subFolder, name) {
	console.log(name);
	document.getElementById('template_name').value = name;
	document.getElementById('template_description').value = templateList[folder][subFolder][name][0];
	document.getElementById('title_prompt').value = templateList[folder][subFolder][name][1];
	document.getElementById('abschnitte_prompt').value = templateList[folder][subFolder][name][2];
	document.getElementById('inhalt_prompt').value = templateList[folder][subFolder][name][3];
	document.getElementById('excerp_prompt').value = templateList[folder][subFolder][name][4];
	document.getElementById('nmd_stil_select').value = templateList[folder][subFolder][name][5];
	document.getElementById('nmd_typ_select').value = templateList[folder][subFolder][name][7];
}

function save_template() {
	var template_name = document.getElementById('template_name').value;
	var template_description = document.getElementById('template_description').value;
	var prompt1 = document.getElementById('title_prompt').value;
	var prompt2 = document.getElementById('abschnitte_prompt').value;
	var prompt3 = document.getElementById('inhalt_prompt').value;
	var prompt4 = document.getElementById('excerp_prompt').value;
	var stil = document.getElementById('nmd_stil_select').value;
	var typ = document.getElementById('nmd_typ_select').value;
	var folder = document.getElementById('unterordner_select').value;
	var folderArray = folder.split(',');
	jQuery(document).ready(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			method: 'POST',
			data: {
				action: 'save_template',
				template_name: template_name,
				template_description: template_description,
				prompt1: prompt1,
				prompt2: prompt2,
				prompt3: prompt3,
				prompt4: prompt4,
				stil: stil,
				ton: ton,
				typ: typ,
				subFolder: folderArray[0],
				folder: folderArray[1],
			},
			success: async function (response) {
				console.log(response);
				await updateTemplateOption();
				location.reload();
			},
			error: function (error) {
				console.log(error);
			},
		});
	});
}

function delete_template(folder, subFolder, index) {
	let confirm = prompt('Bitte geben Sie "Bestätigen" ein um diesen Prompt wirklich zu Löschen');
	if (confirm != 'Bestätigen') {
		alert('Abgebrochen');
		return;
	}
	jQuery(document).ready(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			method: 'POST',
			data: {
				action: 'delete_template',
				folder: folder,
				subFolder: subFolder,
				index: index,
				typ: 'prompt',
			},
			success: async function (response) {
				console.log(response);
				await updateTemplateOption();
				location.reload();
			},
			error: function (error) {
				console.log(error);
			},
		});
	});
}
function delete_template_subFolder(folder, subFolder) {
	let confirm = prompt('Bitte geben Sie "Bestätigen" ein um diesen Ordner wirklich zu Löschen');
	if (confirm != 'Bestätigen') {
		alert('Abgebrochen');
		return;
	}
	jQuery(document).ready(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			method: 'POST',
			data: {
				action: 'delete_template',
				folder: folder,
				subFolder: subFolder,
				typ: 'sub',
			},
			success: async function (response) {
				console.log(response);
				await updateTemplateOption();
				location.reload();
			},
			error: function (error) {
				console.log(error);
			},
		});
	});
}
function delete_template_Folder(folder) {
	let confirm = prompt('Bitte geben Sie "Bestätigen" ein um diesen Ordner wirklich zu Löschen');
	if (confirm != 'Bestätigen') {
		alert('Abgebrochen');
		return;
	}
	jQuery(document).ready(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			method: 'POST',
			data: {
				action: 'delete_template',
				folder: folder,
				typ: 'folder',
			},
			success: async function (response) {
				console.log(response);
				await updateTemplateOption();
				location.reload();
			},
			error: function (error) {
				console.log(error);
			},
		});
	});
}

function createFolder() {
	let name = document.getElementById('folder_name').value;
	jQuery(document).ready(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			method: 'POST',
			data: {
				action: 'create_folder',
				name: name,
			},
			success: async function (response) {
				console.log(response);
				await updateTemplateOption();
				location.reload();
			},
		});
	});
}
function createSubFolder() {
	let name = document.getElementById('folder_select').value;
	let subName = document.getElementById('subFolder_name').value;
	jQuery(document).ready(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			method: 'POST',
			data: {
				action: 'create_sub_folder',
				name: name,
				subName: subName,
			},
			success: async function (response) {
				console.log(response);
				await updateTemplateOption();
				location.reload();
			},
		});
	});
}
function editFolder(folder) {
	let newName = prompt('Bitte den neuen Namen eingeben', folder);

	if (newName == null || newName == '') {
		return;
	}
	jQuery(document).ready(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			method: 'POST',
			data: {
				action: 'edit_folder',
				folder: folder,
				newName: newName,
			},
			success: async function (response) {
				console.log(response);
				await updateTemplateOption();
				location.reload();
			},
		});
	});
}

async function updateTemplateOption() {
	jQuery.ajax({
		url: myAjax.ajaxurl,
		type: 'POST',
		data: {
			action: 'update_seocontent_templates_action',
		},
		success: async function (response) {
			console.log('SEO-Content-Einstellungen wurden aktualisiert.');
		},
		error: function (error) {
			console.error('Fehler beim Aktualisieren der SEO-Content-Einstellungen: ' + error.responseText);
		},
	});
	await new Promise((r) => setTimeout(r, 1000));
}

let customVarList;
function getCustomVariables() {
	fetch(homeUrl + '/wp-content/plugins/SEOContent/src/scripts/php/variables.json')
		.then((response) => response.json())
		.then((json) => {
			console.log(json);
			customVarList = json;
		});
}

async function askGpt(prompt, tokens) {
	let temp = 0.6;
	console.log('Prompt: ' + prompt);
	console.trace();
	function getStackLines() {
		const err = new Error();
		return err.stack.split('\n');
	}

	let lines = getStackLines();
	let systemprompt = promptList['systemRole'];
	console.log(lines);
	if (lines.length > 3 && lines[3].includes('ask_gpt_content_page_ueberschriften') && document.getElementById('templatePrompt').checked) {
		console.log('Condition met on second line of trace.');
		temp = 0.3;
		systemprompt = promptList['templatePrompt'];
		chat = [
			{
				role: 'system',
				content: systemprompt,
			},
		];
	}
	chat.push({ role: 'user', content: prompt });
	console.log(chat);
	try {
		if (premium == false) {
			const response = await axios.post(
				API_ENDPOINT,
				{
					messages: chat,

					temperature: temp,
					model: 'gpt-4',
					n: 1,
				},
				{
					headers: {
						'Content-Type': 'application/json',
						Authorization: `Bearer ${settingsArray['apiKey']}`,
					},
				}
			);

			if (response.status === 200) {
				console.log(response.data);
				const { choices } = response.data;
				if (choices && choices.length > 0) {
					console.log(choices);
					const { message } = choices[0];
					const { content } = message;
					console.log(content);
					chat.push({ role: 'assistant', content: content });
					return content.trim();
				}
			}
			throw new Error('Chat completion request failed.');
		} else {
			return new Promise((resolve, reject) => {
				jQuery.ajax({
					url: myAjax.ajaxurl,
					method: 'POST',
					data: {
						action: 'ask_gpt',
						chat: chat,
						temperature: temp,
						model: 'gpt-4',
					},
					success: function (response) {
						try {
							response = JSON.parse(response);
							console.log(response);
							let content = response['answer'];
							resolve(content.trim()); // Resolve the promise with the response content
						} catch (e) {
							reject(e); // Reject the promise if there is an error (e.g., in parsing the response)
						}
					},
					error: function (error) {
						reject(error); // Reject the promise if the AJAX request fails
					},
				});
			});
		}
	} catch (error) {
		console.error('Error:', error.message);
		alert(
			'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dan erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
		);
		removeLoadingScreen();
		throw error;
	}
}

function ask_gpt_content_page() {
	setValues();
	loadingText.innerHTML = 'Generiere Titel...';
	var result;

	var prompt = document.getElementById('title_prompt').value;
	prompt = prompt.replace('{topic}', topic);
	prompt = prompt.replace('{stil}', stil);
	prompt = prompt.replace('{ton}', ton);

	prompt = setKeywords(prompt, 'title');
	prompt = replaceCustomVars(prompt);

	askGpt(prompt, 100).then((result) => {
		document.getElementById('nmd_title_input').innerHTML = result.replace(/^"(.*)"$/, '$1');
		document.getElementById('nmd_title_input').value = result.replace(/^"(.*)"$/, '$1');
		ask_gpt_content_page_title();
	});
}
function ask_gpt_content_page_title() {
	setValues();
	loadingText.innerHTML = 'Generiere Überschriften...';
	var prompt = document.getElementById('abschnitte_prompt').value;
	prompt = prompt.replace('{title}', title);
	prompt = prompt.replace('{stil}', stil);
	prompt = prompt.replace('{ton}', ton);
	prompt = prompt.replace('{ueberschriftenAnzahl}', abschnitte);
	prompt = prompt.replace('{ueberschriftenAnzahl}', abschnitte);

	prompt = setKeywords(prompt, 'ueberschriften');
	prompt = replaceCustomVars(prompt);

	askGpt(prompt, 27 * abschnitte).then((result) => {
		document.getElementById('nmd_abschnitte_input').innerHTML = result.replace(/^"(.*)"$/, '$1');
		document.getElementById('nmd_abschnitte_input').value = result.replace(/^"(.*)"$/, '$1');
		ask_gpt_content_page_ueberschriften();
	});
}
function ask_gpt_content_page_ueberschriften() {
	setValues();
	loadingText.innerHTML = 'Generiere Inhalt...';
	var words = document.getElementById('nmd_words_count').value;
	var tokens = words * 3;
	var prompt = document.getElementById('inhalt_prompt').value;
	prompt = prompt.replace('{title}', title);
	prompt = prompt.replace('{stil}', stil);
	prompt = prompt.replace('{ton}', ton);
	prompt = prompt.replace('{ueberschriften}', ueberschriften);
	prompt = prompt.replace('{absaetzeAnzahl}', inhaltCount);
	prompt = prompt.replace('{woerter}', words);
	var includeInfos = document.getElementById('includeInfos').checked;

	if (includeInfos) {
		var address = settingsArray['adresse'];
		var Gewerbe = settingsArray['Gewerbe'];
		var firmenname = settingsArray['firmenname'];
		var warumWir = settingsArray['warumWir'];
		prompt += promptList['unternehmensInfoPrompt'];
		prompt = prompt.replace('{adresse}', address);
		prompt = prompt.replace('{Gewerbe}', Gewerbe);
		prompt = prompt.replace('{firmenname}', firmenname);
		prompt = prompt.replace('{warumWir}', warumWir);
	}

	prompt = setKeywords(prompt, 'inhalt');
	prompt = replaceCustomVars(prompt);

	tokens = tokens * abschnitte * inhaltCount;
	askGpt(prompt, tokens).then((result) => {
		document.getElementById('nmd_inhalt_input').innerHTML = result.replace(/^"(.*)"$/, '$1');
		document.getElementById('nmd_inhalt_input').value = result.replace(/^"(.*)"$/, '$1');
		ask_gpt_content_page_excerp();
	});
}

function ask_gpt_content_page_excerp() {
	setValues();
	loadingText.innerHTML = 'Generiere Meta-Daten...';
	var prompt = document.getElementById('excerp_prompt').value;
	prompt = prompt.replace('{title}', title);
	prompt = prompt.replace('{stil}', stil);
	prompt = prompt.replace('{ton}', ton);

	prompt = setKeywords(prompt, 'excerp');
	prompt = replaceCustomVars(prompt);
	askGpt(prompt, 100)
		.then((result) => {
			document.getElementById('nmd_excerp_input').innerHTML = result.replace(/^"(.*)"$/, '$1');
			document.getElementById('nmd_excerp_input').value = result.replace(/^"(.*)"$/, '$1');
			removeLoadingScreen();
			let systemprompt = promptList['systemRole'];
			chat = [
				{
					role: 'system',
					content: systemprompt,
				},
			];
		})
		.catch((error) => {
			removeLoadingScreen();
		});
}

function replaceCustomVars(prompt) {
	for (let key in customVarList) {
		let value = customVarList[key];
		prompt = prompt.replace('{' + key + '}', value);
	}
	return prompt;
}

function setKeywords(prompt, step) {
	var keywordList = document.getElementsByName('keyword');
	var keywordAnzahlList = document.getElementsByName('keywordAnzahl');
	var beschreibungList = document.getElementsByName('beschreibung');
	var synonymList = document.getElementsByName('synonym');
	var keywordInputs = [];
	var keywordAnzahlInputs = [];
	var beschreibungInputs = [];
	var synonymInputs = [];

	for (var i = 0; i < keywordList.length; i++) {
		keywordInputs.push(keywordList[i].value);
		keywordAnzahlInputs.push(keywordAnzahlList[i].value);
		beschreibungInputs.push(beschreibungList[i].value);
		synonymInputs.push(synonymList[i].value);
	}

	var keywordReihenfolgeArray = [];
	if (headingCounter == 1) {
		let keywordString = '';
		var ones = document.getElementsByClassName('keywordWhereId1Value');
		for (var i = 0; i < ones.length; i++) {
			if (ones[i].checked) {
				keywordString = '(Überschrift 1)';
			}
			keywordReihenfolgeArray.push(keywordString);
			keywordString = '(Überschrift 1)';
		}
	} else if (headingCounter == 2) {
		let keywordString = '(Überschrift 1, Überschrift 2)';
		var ones = document.getElementsByClassName('keywordWhereId1Value');
		var twos = document.getElementsByClassName('keywordWhereId2Value');
		for (var i = 0; i < ones.length; i++) {
			if (!ones[i].checked) {
				keywordString = keywordString.replace('Überschrift 1, ', '');
			}
			if (!twos[i].checked) {
				keywordString = keywordString.replace('Überschrift 2', '');
			}
			keywordReihenfolgeArray.push(keywordString);
			keywordString = '(Überschrift 1, Überschrift 2)';
		}
	} else if (headingCounter == 3) {
		let keywordString = '(Überschrift 1, Überschrift 2, Überschrift 3)';
		var ones = document.getElementsByClassName('keywordWhereId1Value');
		var twos = document.getElementsByClassName('keywordWhereId2Value');
		var threes = document.getElementsByClassName('keywordWhereId3Value');
		for (var i = 0; i < ones.length; i++) {
			if (!ones[i].checked) {
				keywordString = keywordString.replace('Überschrift 1, ', '');
			}
			if (!twos[i].checked) {
				keywordString = keywordString.replace('Überschrift 2, ', '');
			}
			if (!threes[i].checked) {
				keywordString = keywordString.replace('Überschrift 3', '');
			}
			keywordReihenfolgeArray.push(keywordString);
			keywordString = '(Überschrift 1, Überschrift 2, Überschrift 3)';
		}
	}
	console.log(keywordReihenfolgeArray);
	var keywordStringFinal = '';
	for (var i = 0; i < keywordInputs.length; i++) {
		var keyword = keywordInputs[i];
		var anzahl = keywordAnzahlInputs[i];
		let tempPrompt = '';
		if (step == 'title') {
			tempPrompt = promptList['keywordPromptTitel'];
		} else if (step == 'inhalt') {
			tempPrompt = promptList['keywordPromptInhalt'];
		} else if (step == 'ueberschriften') {
			tempPrompt = promptList['keywordPromptUeberschrift'];
		} else {
			tempPrompt = promptList['keywordPromptTitel'];
		}
		tempPrompt = tempPrompt.replace('{keyword}', keyword);
		tempPrompt = tempPrompt.replace('{anzahl}', anzahl);
		tempPrompt = tempPrompt.replace('{i}', i);
		tempPrompt = tempPrompt.replace('{reihenfolge}', keywordReihenfolgeArray[i]);
		tempPrompt = tempPrompt.replace('{beschreibung}', beschreibungInputs[i]);
		tempPrompt = tempPrompt.replace('{Synonyme}', synonymInputs[i]);
		keywordStringFinal += tempPrompt;
	}
	prompt = prompt.replace('{keywords}', keywordStringFinal);
	return prompt;
}
function setValues() {
	setLoadingScreen();
	ton = '';
	stil = document.getElementById('nmd_stil_select').value;
	topic = document.getElementById('nmd_topic_input').value;
	title = document.getElementById('nmd_title_input').value;
	abschnitte = document.getElementById('nmd_abschnitte_select').value;
	ueberschriften = document.getElementById('nmd_abschnitte_input').value;
	inhaltCount = document.getElementById('nmd_inhalt_select').value;
}
let faqCount = 1;

function create_content_page() {
	setLoadingScreen();
	loadingText.innerHTML = 'Erstelle Seite...';
	generateMarkup();
	topic = document.getElementById('nmd_topic_input').value;
	var title_output = document.getElementById('nmd_title_input').value;
	var inhalt = document.getElementById('nmd_inhalt_input').value;
	inhalt += '\n\n';
	var excerpt = document.getElementById('nmd_excerp_input').value;
	var questionInputs = document.getElementsByName('question');
	var answerInputs = document.getElementsByName('answer');
	var typ = document.getElementById('nmd_typ_select').value;
	var faqBool = false;

	if (document.getElementById('includeShortcode').checked) {
		console.log('includeShortcode');
		inhalt += '<!-- wp:shortcode --> \n ' + settingsArray['shortcode'] + '\n	<!-- /wp:shortcode -->';
	}

	if (document.getElementById('question').value !== '') {
		faqBool = true;
		for (var i = 0; i < questionInputs.length; i++) {
			if (i == 0) {
				inhalt += '<h2>Häufige Fragen zum Thema: ' + topic + ' </h2>\n\n';
			}
			var question = questionInputs[i].value;
			var answer = answerInputs[i].value;
			if (question && answer) {
				inhalt += '<h3> ' + question + '</h3>\n';
				inhalt += '<p> ' + answer + '</p>\n';
			}
		}
	}
	jQuery(document).ready(function ($) {
		console.log(topic);
		$.ajax({
			url: myAjax.ajaxurl,
			type: 'POST',
			data: {
				action: 'gpt_create_post',
				thema: topic,
				title: title_output,
				inhalt: inhalt,
				excerpt: excerpt,
				typ: typ,
			},
			success: function (response) {
				console.log(response);
				if (!faqBool) {
					removeLoadingScreen();
					loadingText.innerHTML = 'Füge Metadaten hinzu...';
					if (typ == 'page') {
						alert('Die Seite wurde erfolgreich erstellt. Sie können die Seite nun unter dem Menüpunkt "Seiten" finden.');
					} else {
						alert('Der Beitrag wurde erfolgreich erstellt. Sie können den Beitrag nun unter dem Menüpunkt "Beiträge" finden.');
					}
					return;
				}

				$.ajax({
					url: myAjax.ajaxurl,
					type: 'POST',
					data: {
						action: 'my_ajax_request2',
						response: response,
						faq: faqOutput,
					},
					success: function (response) {
						console.log(response);
						removeLoadingScreen();

						if (typ == 'page') {
							alert('Die Seite wurde erfolgreich erstellt. Sie können die Seite nun unter dem Menüpunkt "Seiten" finden.');
						} else {
							alert(
								'Der Beitrag wurde erfolgreich erstellt. Sie können den Beitrag nun unter dem Menüpunkt "Beiträge" finden.'
							);
						}
					},
					error: function (error) {
						console.log(error);
						removeLoadingScreen();

						alert('Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.');
					},
				});
			},
			error: function (error) {
				console.log(error);
				removeLoadingScreen();
			},
		});
	});
}
// Frage/Antwort Eingaben
function addFAQ() {
	faqCount++;
	var div = document.createElement('div');
	div.innerHTML =
		'<label for="question' +
		'">Frage ' +
		faqCount +
		':</label>' +
		'<br>' +
		'<input type="text" id="question' +
		'" name="question' +
		'" class="eingabe"> <br>' +
		'<label for="answer' +
		'">Antwort ' +
		':</label> <br> ' +
		'<input type="text" id="answer' +
		'" name="answer' +
		'" class="eingabe">';
	document.getElementById('faq').appendChild(div);
}
async function generateFAQ() {
	loadingText.innerHTML = 'Generiere FAQ...';
	setLoadingScreen();
	for (let index = 0; faqCount < 5; index++) {
		addFAQ();
	}
	var faqThema = document.getElementById('nmd_topic_input').value;

	var prompt = promptList['faqPrompt'];
	prompt = prompt.replace('{faqThema}', faqThema);
	let output = await askGpt(prompt, 390);
	loadingText.innerHTML = 'FAQ generiert. Fülle die Felder aus...';
	outputArray = output.split('\n');
	for (let i = outputArray.length - 1; i >= 0; i--) {
		if ((i + 1) % 3 === 0) {
			outputArray.splice(i, 1);
		}
	}

	console.log(outputArray);
	var questionInputs = document.getElementsByName('question');
	var answerInputs = document.getElementsByName('answer');
	for (let index = 0; index < 5; index++) {
		questionInputs[index].value = outputArray[index * 2].replace('Frage: ', '');
		answerInputs[index].value = outputArray[index * 2 + 1].replace('Antwort: ', '');
	}
	removeLoadingScreen();
}
//FAQ-Ausgabe generieren
var faqOutput;
function generateMarkup() {
	var faqs = [];
	var questionInputs = document.getElementsByName('question');
	var answerInputs = document.getElementsByName('answer');
	var output = '';
	for (var i = 0; i < questionInputs.length; i++) {
		var question = questionInputs[i].value;
		var answer = answerInputs[i].value;

		if (question && answer) {
			question = question.replace(/"/g, "'");
			answer = answer.replace(/"/g, "'");
			faqs.push({
				'@type': 'Question',
				name: question,
				acceptedAnswer: {
					'@type': 'Answer',
					text: answer,
				},
			});
			output += '<p><strong>Frage:</strong> ' + question + '</p>';
			output += '<p><strong>Antwort:</strong> ' + answer + '</p>';
		}
	}
	var jsonld = {
		'@context': 'https://schema.org',
		'@type': 'FAQPage',
		mainEntity: faqs,
	};

	var markup = JSON.stringify(jsonld, null);

	faqOutput = markup;
	console.log(markup);
}
async function generateAnswers() {
	var questionInputs = document.getElementsByName('question');
	var answerInputs = document.getElementsByName('answer');
	setLoadingScreen();
	loadingText.innerHTML = 'Generiere Antworten...';
	for (var i = 0; i < questionInputs.length; i++) {
		let question = questionInputs[i].value;
		let answer = answerInputs[i];
		let prompt = promptList['answerPrompt'];
		prompt = prompt.replace('{frage}', question);
		let output = await askGpt(prompt, 100);
		console.log(output);
		answer.value = output;
	}
	removeLoadingScreen();
}
function showTab(tabName, n) {
	var tab = document.getElementById(tabName + 'Container');
	if (tabName == 'keyword') {
		if (headingCounter == 0) {
			return;
		}
	}
	if (tab.style.display == 'block') {
		tab.style.display = 'none';
		document.getElementById('arrowUp' + n).style.display = 'block';
		document.getElementById('arrowDown' + n).style.display = 'none';
	} else {
		tab.style.display = 'block';
		document.getElementById('arrowUp' + n).style.display = 'none';
		document.getElementById('arrowDown' + n).style.display = 'block';
	}
}
var headingCounter = 0;
function headingCount(number) {
	document.getElementById('keywordTab').title = '';
	document.getElementById('keywordTab').style.background = '';
	if (number == 1) {
		var twos = document.getElementsByClassName('keywordWhereId2');
		var thress = document.getElementsByClassName('keywordWhereId3');
		for (let index = 0; index < twos.length; index++) {
			twos[index].style.display = 'none';
		}
		for (let index = 0; index < thress.length; index++) {
			thress[index].style.display = 'none';
		}
	}
	if (number == 2) {
		var twos = document.getElementsByClassName('keywordWhereId2');
		var thress = document.getElementsByClassName('keywordWhereId3');
		for (let index = 0; index < twos.length; index++) {
			twos[index].style.display = 'inline-block';
		}
		for (let index = 0; index < thress.length; index++) {
			thress[index].style.display = 'none';
		}
	}
	if (number == 3) {
		var twos = document.getElementsByClassName('keywordWhereId2');
		var thress = document.getElementsByClassName('keywordWhereId3');
		for (let index = 0; index < twos.length; index++) {
			twos[index].style.display = 'inline-block';
		}
		for (let index = 0; index < thress.length; index++) {
			thress[index].style.display = 'inline-block';
		}
	}
	headingCounter = number;
}
keywordCounter = 1;
function addKeyword() {
	var div = document.createElement('div');
	div.classList.add('keywordDiv');
	var oneHeading =
		'<div class="flexCenter"><label class="keywordWhereId1" for="1">1</label><input type="checkbox" name="1" class="keywordWhereId1 keywordWhereId1Value" id="1" value="1">	<label style="display: none" class="keywordWhereId2" for="2">2</label><input style="display: none" type="checkbox" name="2" class="keywordWhereId2 keywordWhereId2Value" id="2" value="2"><label style="display: none" class="keywordWhereId3" for="3">3</label><input style="display: none" type="checkbox" name="3" class="keywordWhereId3 keywordWhereId3Value" id="3" value="3"></div>';
	var twoHeading =
		'<div class="flexCenter"><label class="keywordWhereId1" for="1">1</label><input type="checkbox" name="1" class="keywordWhereId1 keywordWhereId1Value" id="1" value="1"><label class="keywordWhereId2" for="2">2</label><input type="checkbox" name="2" class="keywordWhereId2 keywordWhereId2Value" id="2" value="2"><label style="display: none" class="keywordWhereId3" for="3">3</label><input style="display: none" type="checkbox" name="3" class="keywordWhereId3 keywordWhereId3Value" id="3" value="3">	</div>';
	var threeHeading =
		'<div class="flexCenter"> <label class="keywordWhereId1" for="1">1</label><input type="checkbox" name="1" class="keywordWhereId1 keywordWhereId1Value" id="1" value="1"><label class="keywordWhereId2" for="2">2</label><input type="checkbox" name="2" class="keywordWhereId2 keywordWhereId2Value" id="2" value="2"><label class="keywordWhereId3" for="3">3</label><input type="checkbox" name="3" class="keywordWhereId3 keywordWhereId3Value" id="3" value="3"></div>';

	var actualHeadingtext = '';

	if (headingCounter == 1) {
		actualHeadingtext = oneHeading;
	}
	if (headingCounter == 2) {
		actualHeadingtext = twoHeading;
	}
	if (headingCounter == 3) {
		actualHeadingtext = threeHeading;
	}

	div.innerHTML =
		'<svg class="removeKeywordDiv" onclick="removeKeywordDiv(this)" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->		<path d="M376.6 84.5c11.3-13.6 9.5-33.8-4.1-45.1s-33.8-9.5-45.1 4.1L192 206 56.6 43.5C45.3 29.9 25.1 28.1 11.5 39.4S-3.9 70.9 7.4 84.5L150.3 256 7.4 427.5c-11.3 13.6-9.5 33.8 4.1 45.1s33.8 9.5 45.1-4.1L192 306 327.4 468.5c11.3 13.6 31.5 15.4 45.1 4.1s15.4-31.5 4.1-45.1L233.7 256 376.6 84.5z" />	</svg>' +
		'<label for="keyword">Keyword: </label><br>' +
		'<input type="text" id="keyword' +
		'" name="keyword' +
		'" class="eingabe"> <br>	<label for="keywordAnzahl">Vorkommen im Text:</label> <br><input type="number" name="keywordAnzahl" id="keywordAnzahl" style="width: 8ch;"> <br>  <label for="keywordWhere">Vorkommen in:</label>		<p>Überschrift inkl. Absätze</p>		' +
		actualHeadingtext +
		' <br> <br><label for="synonym">Synonyme(optional):</label><br><input name="synonym" id="synonym" type="text" placeholder="Synonym1, Synonym2, ...">' +
		'<br><label for="beschreibung">Beschreibung(Optional):</label><br><input type="text" name="beschreibung" id="beschreibung"><br>';
	document.getElementById('keywordsAddContainer').appendChild(div);
}
let firstRemoveTempBool = true;
function removeKeyword() {
	var div = document.getElementById('keywordsAddContainer');
	div.removeChild(div.lastChild);
	if (firstRemoveTempBool) {
		firstRemoveTempBool = false;
		div.removeChild(div.lastChild);
	}
}
function getKeywordsLocation() {
	var ones = '';
	var twos = '';
	var threes = '';
}
function showFolder(n) {
	var tab = document.getElementById('folderContainer' + n);

	if (tab.style.display == 'block') {
		tab.style.display = 'none';
		document.getElementById('folderArrowUp' + n).style.display = 'block';
		document.getElementById('folderArrowDown' + n).style.display = 'none';
	} else {
		tab.style.display = 'block';
		document.getElementById('folderArrowUp' + n).style.display = 'none';
		document.getElementById('folderArrowDown' + n).style.display = 'block';
	}
}
function showSubFolder(n) {
	var tab = document.getElementById('subFolderContainer' + n);

	if (tab.style.display == 'block') {
		tab.style.display = 'none';
		document.getElementById('subFolderArrowUp' + n).style.display = 'block';
		document.getElementById('subFolderArrowDown' + n).style.display = 'none';
	} else {
		tab.style.display = 'block';
		document.getElementById('subFolderArrowUp' + n).style.display = 'none';
		document.getElementById('subFolderArrowDown' + n).style.display = 'block';
	}
}
function testfunction() {
	jQuery(document).ready(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			method: 'POST',
			data: {
				action: 'my_action',
			},
			success: function (response) {
				console.log(response);
			},
			error: function (error) {
				console.log(error);
			},
		});
	});
}
function setLoadingTest() {
	document.getElementById('overlay').style.display = 'flex';
	document.body.classList.add('blurred');
}
function get_keywords() {
	let topic = document.getElementById('nmd_topic_input').value;
	setLoadingScreen();
	loadingText.innerHTML = 'Gute Keywords werden gesucht...';
	jQuery(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			method: 'POST',
			data: {
				topic: encodeURIComponent(topic),
				action: 'get_keyword_api',
			},
			success: function (response) {
				let domainArray = [];

				for (i = 0; i < 5; i++) {
					try {
						let domain = 'www.' + encodeURIComponent(response.answer[0].result[i].domain);
						domainArray.push(domain);
					} catch (error) {
						continue;
					}
				}
				loadingText.innerHTML = 'Keywords werden gefiltert...';
				$.ajax({
					url: myAjax.ajaxurl,
					method: 'POST',
					data: {
						urlArray: domainArray,
						action: 'get_best_keyword_api',
					},
					success: function (response) {
						let kwArray = [];
						loadingText.innerHTML = 'Keywords werden mit Hilfe von KI verbessert...';
						for (i = 0; i < 5; i++) {
							if (response[i] == '' || JSON.parse(response[i]).status == 'fail') {
								continue;
							}
							let jsonData = JSON.parse(response[i]);

							for (j = 0; j < jsonData.answer[0].result.length; j++) {
								let kw = jsonData.answer[0].result[j].kw;
								kwArray.push(kw);
							}
						}
						const uniqueStringsArray = [...new Set(kwArray)];
						console.log(uniqueStringsArray);

						let filterPrompt = promptList['filterKeywordsPrompt'];
						filterPrompt = filterPrompt.replace('{thema}', topic);
						filterPrompt = filterPrompt.replace('{keywordArray}', uniqueStringsArray);
						askGpt(filterPrompt, 100).then((result) => {
							try {
								resultArry = JSON.parse(result);
							} catch (error) {
								removeLoadingScreen();
							}
							console.log(resultArry['Keywords']);
							for (i = 0; i < resultArry['Keywords'].length; i++) {
								addKeywordDiv(resultArry['Keywords'][i]);
							}
							removeLoadingScreen();
						});
					},
					error: function (error) {
						console.log(error);
						removeLoadingScreen();
					},
				});
			},
			error: function (error) {
				console.log(error);
				removeLoadingScreen();
			},
		});
	});
}

function setLoadingScreen() {
	document.getElementById('overlay').style.display = 'flex';
	document.body.classList.add('blurred');
}
function removeLoadingScreen() {
	document.getElementById('overlay').style.display = 'none';
	document.body.classList.remove('blurred');
}
function removeKeywordDiv(element) {
	var parentDiv = element.parentElement;
	parentDiv.remove();
}
function addKeywordDiv(keyword) {
	var div = document.createElement('div');
	div.classList.add('keywordDiv');
	var oneHeading =
		'<div class="flexCenter"><label class="keywordWhereId1" for="1">1</label><input type="checkbox" name="1" class="keywordWhereId1 keywordWhereId1Value" id="1" value="1">	<label style="display: none" class="keywordWhereId2" for="2">2</label><input style="display: none" type="checkbox" name="2" class="keywordWhereId2 keywordWhereId2Value" id="2" value="2"><label style="display: none" class="keywordWhereId3" for="3">3</label><input style="display: none" type="checkbox" name="3" class="keywordWhereId3 keywordWhereId3Value" id="3" value="3"></div>';
	var twoHeading =
		'<div class="flexCenter"><label class="keywordWhereId1" for="1">1</label><input type="checkbox" name="1" class="keywordWhereId1 keywordWhereId1Value" id="1" value="1"><label class="keywordWhereId2" for="2">2</label><input type="checkbox" name="2" class="keywordWhereId2 keywordWhereId2Value" id="2" value="2"><label style="display: none" class="keywordWhereId3" for="3">3</label><input style="display: none" type="checkbox" name="3" class="keywordWhereId3 keywordWhereId3Value" id="3" value="3">	</div>';
	var threeHeading =
		'<div class="flexCenter"> <label class="keywordWhereId1" for="1">1</label><input type="checkbox" name="1" class="keywordWhereId1 keywordWhereId1Value" id="1" value="1"><label class="keywordWhereId2" for="2">2</label><input type="checkbox" name="2" class="keywordWhereId2 keywordWhereId2Value" id="2" value="2"><label class="keywordWhereId3" for="3">3</label><input type="checkbox" name="3" class="keywordWhereId3 keywordWhereId3Value" id="3" value="3"></div>';

	var actualHeadingtext = '';

	if (headingCounter == 1) {
		actualHeadingtext = oneHeading;
	}
	if (headingCounter == 2) {
		actualHeadingtext = twoHeading;
	}
	if (headingCounter == 3) {
		actualHeadingtext = threeHeading;
	}

	div.innerHTML =
		'<svg class="removeKeywordDiv" onclick="removeKeywordDiv(this)" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->		<path d="M376.6 84.5c11.3-13.6 9.5-33.8-4.1-45.1s-33.8-9.5-45.1 4.1L192 206 56.6 43.5C45.3 29.9 25.1 28.1 11.5 39.4S-3.9 70.9 7.4 84.5L150.3 256 7.4 427.5c-11.3 13.6-9.5 33.8 4.1 45.1s33.8 9.5 45.1-4.1L192 306 327.4 468.5c11.3 13.6 31.5 15.4 45.1 4.1s15.4-31.5 4.1-45.1L233.7 256 376.6 84.5z" />	</svg>' +
		'<label for="keyword">Keyword: </label><br>' +
		'<input type="text" id="keyword' +
		'" value="' +
		keyword +
		'"  name="keyword' +
		'" class="eingabe"> <br>	<label for="keywordAnzahl">Vorkommen im Text:</label> <br><input type="number" name="keywordAnzahl" id="keywordAnzahl" style="width: 8ch;"> <br>  <label for="keywordWhere">Vorkommen in:</label>		<p>Überschrift inkl. Absätze</p>		' +
		actualHeadingtext +
		' <br> <br><label for="synonym">Synonyme(optional):</label><br><input name="synonym" id="synonym" type="text" placeholder="Synonym1, Synonym2, ...">' +
		'<br><label for="beschreibung">Beschreibung(Optional):</label><br><input type="text" name="beschreibung" id="beschreibung"><br>';
	document.getElementById('keywordsAddContainer').appendChild(div);
}
