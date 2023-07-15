const API_ENDPOINT = 'https://api.openai.com/v1/chat/completions';

var ton;
var stil;
var topic;
var title;
var abschnitte;
var ueberschriften;
var inhaltCount;
var var_prompt = '';
var promptList;

var chat = [
	{
		role: 'system',
		content:
			'You are a helpful assistant speaking German. You are a creativ Textwriter that helps with SEO and Text optimization. Complete my Promts:',
	},
];

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

		// Use the jsonData variable as needed
		console.log(jsonData);
	}
};

request.send();
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

request2.send();

function get_template(folder, subFolder, name) {
	console.log(name);
	jQuery(document).ready(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			method: 'POST',
			data: {
				action: 'get_template',
			},
			success: function (response) {
				response = response.substring(0, response.length - 1);

				console.log(response);
				const prompts = JSON.parse(response);

				document.getElementById('template_name').value = name;
				document.getElementById('template_description').value = prompts[folder][subFolder][name][0];
				document.getElementById('title_prompt').value = prompts[folder][subFolder][name][1];
				document.getElementById('abschnitte_prompt').value = prompts[folder][subFolder][name][2];
				document.getElementById('inhalt_prompt').value = prompts[folder][subFolder][name][3];
				document.getElementById('excerp_prompt').value = prompts[folder][subFolder][name][4];
				document.getElementById('nmd_stil_select').value = prompts[folder][subFolder][name][5];
				document.getElementById('nmd_ton_select').value = prompts[folder][subFolder][name][6];
				document.getElementById('nmd_typ_select').value = prompts[folder][subFolder][name][7];
			},
			error: function (error) {
				console.log(error);
			},
		});
	});
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
			success: function (response) {
				console.log(response);

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
			success: function (response) {
				console.log(response);
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
			success: function (response) {
				console.log(response);
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
			success: function (response) {
				console.log(response);
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
			success: function (response) {
				console.log(response);
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
			success: function (response) {
				console.log(response);
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
			success: function (response) {
				console.log(response);
				location.reload();
			},
		});
	});
}

async function askGpt(prompt, tokens) {
	console.log('Prompt: ' + prompt);
	tokens = tokens * 1.5;
	tokens = Math.round(tokens);
	chat.push({ role: 'user', content: prompt });
	try {
		const response = await axios.post(
			API_ENDPOINT,
			{
				messages: chat,
				max_tokens: tokens,
				temperature: 0.6,
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
	} catch (error) {
		console.error('Error:', error.message);
		alert(
			'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dan erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
		);
		throw error;
	}
}

function ask_gpt_content_page() {
	setValues();

	var result;

	var prompt = document.getElementById('title_prompt').value;
	prompt = prompt.replace('{topic}', topic);
	prompt = prompt.replace('{stil}', stil);
	prompt = prompt.replace('{ton}', ton);

	askGpt(prompt, 100).then((result) => {
		document.getElementById('nmd_title_input').innerHTML = result.replace(/^"(.*)"$/, '$1');
		document.getElementById('nmd_title_input').value = result.replace(/^"(.*)"$/, '$1');
		ask_gpt_content_page_title();
	});
}
function ask_gpt_content_page_title() {
	setValues();

	var prompt = document.getElementById('abschnitte_prompt').value;
	prompt = prompt.replace('{title}', title);
	prompt = prompt.replace('{stil}', stil);
	prompt = prompt.replace('{ton}', ton);
	prompt = prompt.replace('{ueberschriftenAnzahl}', abschnitte);
	prompt = prompt.replace('{ueberschriftenAnzahl}', abschnitte);

	askGpt(prompt, 27 * abschnitte).then((result) => {
		document.getElementById('nmd_abschnitte_input').innerHTML = result;
		document.getElementById('nmd_abschnitte_input').value = result;
		ask_gpt_content_page_ueberschriften();
	});
}
function ask_gpt_content_page_ueberschriften() {
	setValues();

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
	var keywordList = document.getElementsByName('keyword');
	var keywordAnzahlList = document.getElementsByName('keywordAnzahl');
	var beschreibungList = document.getElementsByName('beschreibung');
	var synonymList = document.getElementsByName('synonym');
	var keywordInputs = [];
	var keywordAnzahlInputs = [];
	var beschreibungInputs = [];
	var synonymInputs = [];
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
				keywordString += ones[i].value + ',';
			}
			keywordReihenfolgeArray.push(keywordString);
			keywordString = '';
		}
	} else if (headingCounter == 2) {
		let keywordString = '';
		var ones = document.getElementsByClassName('keywordWhereId1Value');
		var twos = document.getElementsByClassName('keywordWhereId2Value');
		for (var i = 0; i < ones.length; i++) {
			if (ones[i].checked) {
				keywordString += ones[i].value + ',';
			}
			if (twos[i].checked) {
				keywordString += twos[i].value + ',';
			}
			keywordReihenfolgeArray.push(keywordString);
			keywordString = '';
		}
	} else if (headingCounter == 3) {
		let keywordString = '';
		var ones = document.getElementsByClassName('keywordWhereId1Value');
		var twos = document.getElementsByClassName('keywordWhereId2Value');
		var threes = document.getElementsByClassName('keywordWhereId3Value');
		for (var i = 0; i < ones.length; i++) {
			if (ones[i].checked) {
				keywordString += ones[i].value + ',';
			}
			if (twos[i].checked) {
				keywordString += twos[i].value + ',';
			}
			if (threes[i].checked) {
				keywordString += threes[i].value + ',';
			}
			keywordReihenfolgeArray.push(keywordString);
			keywordString = '';
		}
	}
	var keywordStringFinal = '';
	for (var i = 0; i < keywordInputs.length; i++) {
		var keyword = keywordInputs[i];
		var anzahl = keywordAnzahlInputs[i];

		let tempPrompt = promptList['keywordPrompt'];
		tempPrompt = tempPrompt.replace('{keyword}', keyword);
		tempPrompt = tempPrompt.replace('{anzahl}', anzahl);
		tempPrompt = tempPrompt.replace('{i}', i);
		tempPrompt = tempPrompt.replace('reihenfolge', keywordReihenfolgeArray[i]);
		tempPrompt = tempPrompt.replace('{beschreibung}', beschreibungInputs[i]);
		tempPrompt = tempPrompt.replace('{Synonyme}', synonymInputs[i]);
		keywordStringFinal += tempPrompt;
	}
	prompt = prompt.replace('{keywords}', keywordStringFinal);

	tokens = tokens * abschnitte * inhaltCount;
	askGpt(prompt, tokens).then((result) => {
		document.getElementById('nmd_inhalt_input').innerHTML = result;
		document.getElementById('nmd_inhalt_input').value = result;
		ask_gpt_content_page_excerp();
	});
}

function ask_gpt_content_page_excerp() {
	setValues();

	var prompt = document.getElementById('excerp_prompt').value;
	prompt = prompt.replace('{title}', title);
	prompt = prompt.replace('{stil}', stil);
	prompt = prompt.replace('{ton}', ton);

	askGpt(prompt, 100)
		.then((result) => {
			document.getElementById('nmd_excerp_input').innerHTML = result;
			document.getElementById('nmd_excerp_input').value = result;
			document.getElementById('overlay').style.display = 'none';
			document.body.classList.remove('blurred');
			document.body.classList.remove('no-scroll');
			document.getElementsByTagName('html')[0].style.paddingTop = '32px';
			chat = [
				{
					role: 'system',
					content:
						'You are a helpful assistant speaking German. You are a creativ Textwriter that helps with SEO and Text optimization. Complete my Promts:',
				},
			];
		})
		.catch((error) => {
			document.getElementById('overlay').style.display = 'none';
			document.body.classList.remove('blurred');
			document.body.classList.remove('no-scroll');
		});
}

function setValues() {
	document.getElementsByTagName('html')[0].style.paddingTop = '0';
	document.body.classList.add('no-scroll');
	document.getElementById('overlay').style.display = 'flex';
	document.body.classList.add('blurred');
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
	document.getElementsByTagName('html')[0].style.paddingTop = '0';
	document.body.classList.add('no-scroll');
	document.getElementById('overlay').style.display = 'flex';
	document.body.classList.add('blurred');
	generateMarkup();
	var title_output = document.getElementById('nmd_title_input').value;
	var inhalt = document.getElementById('nmd_inhalt_input').value;
	inhalt += '\n\n';
	var excerpt = document.getElementById('nmd_excerp_input').value;
	var questionInputs = document.getElementsByName('question');
	var answerInputs = document.getElementsByName('answer');
	var typ = document.getElementById('nmd_typ_select').value;
	var faqBool = false;

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
		$.ajax({
			url: myAjax.ajaxurl,
			type: 'POST',
			data: {
				action: 'my_ajax_request',
				title: title_output,
				inhalt: inhalt,
				excerpt: excerpt,
				typ: typ,
			},
			success: function (response) {
				console.log(response);
				if (!faqBool) {
					document.getElementById('overlay').style.display = 'none';
					document.body.classList.remove('blurred');
					document.body.classList.remove('no-scroll');
					document.getElementsByTagName('html')[0].style.paddingTop = '32px';
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
						document.getElementById('overlay').style.display = 'none';
						document.body.classList.remove('blurred');
						document.body.classList.remove('no-scroll');
						document.getElementsByTagName('html')[0].style.paddingTop = '32px';
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
						document.getElementById('overlay').style.display = 'none';
						document.body.classList.remove('blurred');
						document.body.classList.remove('no-scroll');
						document.getElementsByTagName('html')[0].style.paddingTop = '32px';
						alert('Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.');
					},
				});
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
	document.getElementsByTagName('html')[0].style.paddingTop = '0';
	document.body.classList.add('no-scroll');
	document.getElementById('overlay').style.display = 'flex';
	document.body.classList.add('blurred');
	for (let index = 0; faqCount < 5; index++) {
		addFAQ();
	}
	var faqThema = document.getElementById('nmd_topic_input').value;

	var prompt = promptList['faqPrompt'];
	prompt = prompt.replace('{faqThema}', faqThema);
	let output = await askGpt(prompt, 390);
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
	document.getElementById('overlay').style.display = 'none';
	document.body.classList.remove('blurred');
	document.body.classList.remove('no-scroll');
	document.getElementsByTagName('html')[0].style.paddingTop = '32px';
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
	document.body.classList.add('no-scroll');
	document.getElementById('overlay').style.display = 'flex';
	document.body.classList.add('blurred');

	for (var i = 0; i < questionInputs.length; i++) {
		let question = questionInputs[i].value;
		let answer = answerInputs[i];
		let prompt = promptList['answerPrompt'];
		prompt = prompt.replace('{frage}', question);
		let output = await askGpt(prompt, 100);
		console.log(output);
		answer.value = output;
	}
	document.getElementById('overlay').style.display = 'none';
	document.body.classList.remove('blurred');
	document.body.classList.remove('no-scroll');
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
function addKeyword() {
	var div = document.createElement('div');
	div.classList.add('keywordDiv');
	var oneHeading =
		'<label class="keywordWhereId1" for="1">1</label><input type="checkbox" name="1" class="keywordWhereId1 keywordWhereId1Value" id="1" value="1">	<label style="display: none" class="keywordWhereId2" for="2">2</label><input style="display: none" type="checkbox" name="2" class="keywordWhereId2 keywordWhereId2Value" id="2" value="2"><label style="display: none" class="keywordWhereId3" for="3">3</label><input style="display: none" type="checkbox" name="3" class="keywordWhereId3 keywordWhereId3Value" id="3" value="3">';
	var twoHeading =
		'<label class="keywordWhereId1" for="1">1</label><input type="checkbox" name="1" class="keywordWhereId1 keywordWhereId1Value" id="1" value="1"><label class="keywordWhereId2" for="2">2</label><input type="checkbox" name="2" class="keywordWhereId2 keywordWhereId2Value" id="2" value="2"><label style="display: none" class="keywordWhereId3" for="3">3</label><input style="display: none" type="checkbox" name="3" class="keywordWhereId3 keywordWhereId3Value" id="3" value="3">	';
	var threeHeading =
		' <label class="keywordWhereId1" for="1">1</label><input type="checkbox" name="1" class="keywordWhereId1 keywordWhereId1Value" id="1" value="1"><label class="keywordWhereId2" for="2">2</label><input type="checkbox" name="2" class="keywordWhereId2 keywordWhereId2Value" id="2" value="2"><label class="keywordWhereId3" for="3">3</label><input type="checkbox" name="3" class="keywordWhereId3 keywordWhereId3Value" id="3" value="3">';

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
		'<label for="keyword">Keyword: </label><br>' +
		'<input type="text" id="keyword' +
		'" name="keyword' +
		'" class="eingabe"> <br>	<label for="keywordAnzahl">Vorkommen im Text:</label> <br><input type="number" name="keywordAnzahl" id="keywordAnzahl" style="width: 8ch;"> <br>  <label for="keywordWhere">Vorkommen in:</label>		<p>Überschrift inkl. Absätze</p>		' +
		actualHeadingtext +
		' <br> <br><label for="synonym">Synonyme(optional):</label><br><input name="synonym" id="synonym" type="text" placeholder="Synonym1, Synonym2, ...">' +
		'<br><label for="beschreibung">Beschreibung(Optional):</label><br><input type="text" name="beschreibung" id="beschreibung"><br>';
	document.getElementById('keywordsAddContainer').appendChild(div);
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
