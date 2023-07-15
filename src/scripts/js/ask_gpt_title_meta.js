const API_ENDPOINT = 'https://api.openai.com/v1/chat/completions';

var promptList;

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
async function getAllPages(type) {
	const baseURL = homeUrl + '/wp-json/wp/v2';
	const perPage = 100; // Number of pages to retrieve per request
	let totalPages = 1;
	let allPages = [];

	// Retrieve the total number of pages
	const response = await fetch(`${baseURL}/${type}?status=any&per_page=1`, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json',
			'X-WP-Nonce': nmd.nonce,
		},
	});
	const headers = response.headers;
	if (headers.has('x-wp-totalpages')) {
		totalPages = parseInt(headers.get('x-wp-totalpages'));
	}

	// Fetch pages from each page until all are retrieved
	for (let page = 1; page <= totalPages; page++) {
		const pagesResponse = await fetch(`${baseURL}/${type}?status=any&per_page=${perPage}&page=${page}`, {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': nmd.nonce,
			},
		});
		const pages = await pagesResponse.json();
		allPages = allPages.concat(pages);
	}

	return allPages;
}

var titlesArray = [];
var exceptArray = [];
var idArray = [];
var newTitlesArray = [];
var newExceptArray = [];
var urlArray = [];

var printetTitles = 0;
async function getInfos(type) {
	var pagesArray = [];
	await getAllPages(type)
		.then((pages) => {
			console.log(pages);
			pagesArray = pages;
		})
		.catch((error) => {
			console.error(error);
		});

	for (var i = 0; i < pagesArray.length; i++) {
		var page = pagesArray[i];
		console.log(page);
		try {
			//sanitize the title  from html tags
			var title = page.title.rendered.replace(/(<([^>]+)>)/gi, '');
			titlesArray.push(title);
			try {
				var meta = page.yoast_head_json.description;
				if (meta == undefined) {
					meta = page.excerpt.rendered;
				}
			} catch (error) {
				var meta = page.excerpt.rendered;
			}
			meta = meta.replace(/(<([^>]+)>)/gi, '');
			exceptArray.push(meta);
			idArray.push(page.id);
			urlArray.push(page.link);
		} catch (error) {
			console.log(error);
		}
	}
	for (var i = 0 + printetTitles; i < titlesArray.length; i++) {
		addBlock(titlesArray[i], exceptArray[i], idArray[i]);
		printetTitles++;
		shortenText();
	}

	var boxes = document.getElementsByName('selectBox');

	for (let index = 0; index < boxes.length; index++) {
		boxes[index].checked = true;
	}
	document.getElementById('cb-select-all').checked = true;
	console.log(titlesArray);
	document.getElementById('buttonBar').style.display = 'flex';
}

var sonderzeichen = '';
var alreadyPrintet = 0;

async function generateNewSnippets() {
	var stil = document.getElementById('nmd_style').value;
	var metas = document.getElementsByClassName('metaShorted');
	let boxes = document.getElementsByName('selectBox');
	let trueI = 0;
	for (let i = 0 + alreadyPrintet; i < titlesArray.length; i++) {
		if (!boxes[i].checked) continue;
		metas[(i + 1) * 2 - 1].style.animation = '1.3s linear 0s infinite normal none running nmd-fading';
		metas[(i + 1) * 2 - 2].style.animation = '1.3s linear 0s infinite normal none running nmd-fading';
		generateNewSnippetsSubFunction(stil, trueI, i, metas[(i + 1) * 2 - 1], metas[(i + 1) * 2 - 2]);
		trueI++;
		alreadyPrintet++;
	}
}
var asyncCoounter = 0;
async function generateNewSnippetsSubFunction(stil, trueI, i, element, element2) {
	let title = titlesArray[i];
	let meta = exceptArray[i];
	// Build the prompt for the title.
	let prompt = document.getElementById('promtTitel').value;
	prompt = prompt.replace('{title}', title);
	prompt = prompt.replace('{stil}', stil);

	// Get the new title from GPT.
	let newTitle = await askGpt(prompt, 60);

	let ctas = settingsArray['cta'];
	let usps = settingsArray['usps'];

	// Build the prompt for the meta description.
	prompt = document.getElementById('promtMeta').value;
	prompt = prompt.replace('{title}', title);
	prompt = prompt.replace('{meta}', meta);
	prompt = prompt.replace('{stil}', stil);
	if (document.getElementById('marketing').checked) {
		prompt = prompt.replace('{usps}', usps);
		prompt = prompt.replace('{ctas}', ctas);
	}

	prompt = prompt.replace('{sonderzeichen}', sonderzeichen);

	// Get the new meta description from GPT.
	let newMeta = await askGpt(prompt, 160);

	// Add the new snippet to the page.
	await addNewBlock(newTitle, newMeta, idArray[i], i);
	asyncCoounter++;
	shortenText();
	element.style.animation = 'none';
	element2.style.animation = 'none';
	if (asyncCoounter == titlesArray.length - 1) {
		await new Promise((r) => setTimeout(r, 1500));
		var newTitles = document.getElementsByClassName('newTitle');
		var newMetas = document.getElementsByClassName('newMeta');
		newTitlesArray = [];
		newExceptArray = [];
		for (let i = 0; i < titlesArray.length; i++) {
			newTitlesArray.push(newTitles[i].innerHTML);
			newExceptArray.push(newMetas[i].innerHTML);
		}
	}
}

var blockCount = 0;
async function addBlock(title, meta, id) {
	blockCount++;
	var tr = document.createElement('tr');
	tr.classList.add('title-meta-block-body');
	tr.innerHTML =
		'    <td style="width: 1%;margin-left: 30px;"><input name="selectBox" id="cb-select-' +
		id +
		'" type="checkbox" name="post[]" value="' +
		(blockCount - 1) +
		'"></td> <td class="firstTd"> <div id="desktop-snippet" class="desktop-snippet"> <div class="urlHeader">	<div id="url-ausgabe">' +
		homeUrl +
		'</div> <span class="statusText" style="color: orange">Aktueller Inhalt</span>   </div><p><a id="ausgabeTitel" class="titleShorted" href=" ' +
		urlArray[blockCount - 1] +
		' " target="_blank" rel="noopener" style="cursor: pointer;">' +
		title +
		'</a></p>	<span class="metaShorted"  id="ausgabeMetaBeschreibung"> ' +
		meta +
		' </span>	<p class="empty"></p></div><div id="mobile-snippet" class="mobile-snippet">	<div class="urlHeader">	<div id="url-ausgabe-mobile">' +
		homeUrl +
		'</div>	<span class="statusText" style="color: orange">Aktueller Inhalt</span>   </div>	<p><a class="titleShorted"  id="ausgabeTitel-mobile" href="' +
		urlArray[blockCount - 1] +
		'" target="_blank" rel="noopener" style="cursor: pointer;">' +
		title +
		'</a></p>		<span  class="metaShorted" id="ausgabeMetaBeschreibung-mobile"> ' +
		meta +
		'</span>	<p class="empty"></p></div> </td> ';

	document.getElementById('table_body').appendChild(tr);
}

var newBlockCount = 0;
async function addNewBlock(newTitle, newMeta, id, i) {
	newBlockCount++;
	var td = document.createElement('td');
	td.classList.add('secondTd');
	td.innerHTML =
		' <div id="desktop-snippet" class="desktop-snippet"> <div class="urlHeader">	<div id="url-ausgabe">' +
		homeUrl +
		'</div><span class="statusText" style="color: #afcb08">Neuer Inhalt</span>   </div><p><a class="newTitle newTitleShorted" id="ausgabeTitel" href="' +
		urlArray[i] +
		'" target="_blank" rel="noopener" style="cursor: pointer;">' +
		newTitle +
		'</a></p>	<p><span class="newMeta newMetaShorted" id="ausgabeMetaBeschreibung"> ' +
		newMeta +
		' </span></p>	<p class="empty"></p></div>      <div id="mobile-snippet" class="mobile-snippet">	<div class="urlHeader">	<div id="url-ausgabe-mobile">' +
		homeUrl +
		'</div>	<span class="statusText" style="color: #afcb08">Neuer Inhalt</span>   </div>	<p><a id="ausgabeTitel-mobile" class="newTitleShorted" href="' +
		urlArray[i] +
		'" target="_blank" rel="noopener" style="cursor: pointer;">' +
		newTitle +
		'</a></p>		<p><span class="newMetaShorted" id="ausgabeMetaBeschreibung-mobile"> ' +
		newMeta +
		'</span></p>		<p class="empty"></p></div> <div class="buttonDiv"> <button type="button" class="button action" onclick="retry(' +
		i +
		', ' +
		id +
		')">Neu generieren</button> <br> <button style="margin-top:8px" class="button action" type="button" onclick="submitChanges(' +
		i +
		',' +
		id +
		')">Vorschlag übernehmen</button><br> <button style="margin-top:8px" class="button action" type="button" onclick="editBlock(' +
		i +
		',' +
		id +
		')">Vorschlag bearbeiten</button>  </div>  ';

	var table = document.getElementById('table_body');
	var rows = table.getElementsByTagName('tr');
	rows[i].appendChild(td);
}

var newTitlesAll = '';
var newMetasAll = '';
async function retry(index, id, index) {
	let stil = document.getElementById('nmd_style').value;
	let newTitles = document.getElementsByClassName('newTitle');
	let newMetas = document.getElementsByClassName('newMeta');
	console.log('Size of newMetasAll: ', newMetasAll.length);
	console.log('Accessing index: ', (index + 1) * 2 - 2);
	newMetasAll[(index + 1) * 2 - 2].style.animation = '1.3s linear 0s infinite normal none running nmd-fading';
	newMetasAll[(index + 1) * 2 - 1].style.animation = '1.3s linear 0s infinite normal none running nmd-fading';

	let title = newTitles[index].innerHTML;
	let meta = newMetas[index].innerHTML;
	let prompt = promptList['newTitlePrompt'];
	prompt = prompt.replace('{title}', title);
	prompt = prompt.replace('{stil}', stil);

	let newTitle = await askGpt(prompt, 60);

	let ctas = settingsArray['cta'];
	let usps = settingsArray['usps'];
	newTitlesArray.push(newTitle);
	prompt = promptList['newMetaPrompt'];
	prompt = prompt.replace('{title}', title);
	prompt = prompt.replace('{meta}', meta);
	if (document.getElementById('marketing').checked) {
		prompt = prompt.replace('{usps}', usps);
		prompt = prompt.replace('{ctas}', ctas);
	}
	prompt = prompt.replace('{stil}', stil);
	prompt = prompt.replace('{sonderzeichen}', sonderzeichen);

	let newMeta = await askGpt(prompt, 160);

	newTitlesArray[index] = newTitle;
	newExceptArray[index] = newMeta;
	newTitlesAll[(index + 1) * 2 - 1].innerHTML = newTitle;
	newMetasAll[(index + 1) * 2 - 1].innerHTML = newMeta;
	newTitlesAll[(index + 1) * 2 - 2].innerHTML = newTitle;
	newMetasAll[(index + 1) * 2 - 2].innerHTML = newMeta;

	newMetasAll[(index + 1) * 2 - 1].style.animation = 'none';
	newMetasAll[(index + 1) * 2 - 2].style.animation = 'none';

	shortenText();
}

function editBlock(blockCount, id) {
	var title = newTitlesArray[blockCount];
	var meta = newExceptArray[blockCount];

	var titlesTempArray = document.getElementsByClassName('newTitleShorted');
	var metaTempArray = document.getElementsByClassName('newMetaShorted');

	let newTitle = prompt('Ändern sie den Title', title);
	if (newTitle == null || newTitle == '') {
		text = 'User cancelled the prompt.';
		return;
	} else {
	}

	let newMeta = prompt('Ändern Sie die Metabeschreibung', meta);
	if (newMeta == null || newMeta == '') {
		text = 'User cancelled the prompt.';
		return;
	} else {
	}

	titlesTempArray[(blockCount + 1) * 2 - 1].innerHTML = newTitle;
	metaTempArray[(blockCount + 1) * 2 - 1].innerHTML = newMeta;
	titlesTempArray[(blockCount + 1) * 2 - 2].innerHTML = newTitle;
	metaTempArray[(blockCount + 1) * 2 - 2].innerHTML = newMeta;

	updatePage(id, newTitle, newMeta, blockCount);
	shortenText();
	alert('Metadaten wurden erfolgreich geändert');
}

function submitChanges(blockCount, id) {
	updatePage(id, newTitlesArray[blockCount], newExceptArray[blockCount], blockCount);
	console.log(id, newTitlesArray[blockCount], newExceptArray[blockCount], blockCount);
	alert('Metadaten wurden erfolgreich geändert');
}
// function updatePage(pageId, newTitle, newExcerpt, blockCount) {
// 	jQuery(document).ready(function ($) {
// 		$.ajax({
// 			url: myAjax.ajaxurl,
// 			method: 'POST',
// 			data: {
// 				action: 'nmd_save_title_meta',
// 			},
// 			success: function (response) {
// 				console.log(response);
// 			},
// 			error: function (error) {
// 				console.log(error);
// 			},
// 		});
// 	});
// }
function addSpecialCharacter(ele, char) {
	if (ele.style.border == '1px solid black') {
		ele.style.border = 'none';
		sonderzeichen = sonderzeichen.replace(char + ',', '');
	} else {
		sonderzeichen += char + ',';
		ele.style.border = '1px solid black';
	}
	console.log(sonderzeichen);
}
function updatePage(pageId, newTitle, newExcerpt, blockCount) {
	jQuery(document).ready(function ($) {
		$.post({
			url: myAjax.ajaxurl,
			method: 'POST',

			data: {
				action: 'update_meta_page',
				pageId: pageId,
				newTitle: newTitle,
				newExcerpt: newExcerpt,
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
function getPages(type) {
	getInfos(type);
}
async function askGpt(prompt, tokens) {
	console.log(prompt);
	var chat = [
		{
			role: 'system',
			content:
				'You are a helpful assistant speaking German. You are a creativ Textwriter that helps with SEO and Text optimization. Complete my Promts:',
		},
	];
	chat.push({ role: 'user', content: prompt });
	try {
		const response = await axios.post(
			API_ENDPOINT,
			{
				messages: chat,
				max_tokens: tokens,
				temperature: 0.5,
				model: 'gpt-4',
				n: 1,
			},
			{
				headers: {
					'Content-Type': 'application/json',
					Authorization: `Bearer ${apiKey}`,
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
				return content.trim().replace(/^"(.*)"$/, '$1');
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

function switchSnippets(typ) {
	if (typ == 'mobile') {
		var desktopSnippets = document.getElementsByClassName('desktop-snippet');
		for (let index = 0; index < desktopSnippets.length; index++) {
			desktopSnippets[index].style.display = 'none';
		}
		var mobileSnippet = document.getElementsByClassName('mobile-snippet');
		for (let index = 0; index < mobileSnippet.length; index++) {
			mobileSnippet[index].style.display = 'inline-block';
		}
	}
	if (typ == 'pc') {
		var desktopSnippets = document.getElementsByClassName('desktop-snippet');
		for (let index = 0; index < desktopSnippets.length; index++) {
			desktopSnippets[index].style.display = 'inline-block';
		}
		var mobileSnippet = document.getElementsByClassName('mobile-snippet');
		for (let index = 0; index < mobileSnippet.length; index++) {
			mobileSnippet[index].style.display = 'none';
		}
	}
}
function selectAll() {
	var boxes = document.getElementsByName('selectBox');
	if (document.getElementById('cb-select-all').checked) {
		for (let index = 0; index < boxes.length; index++) {
			boxes[index].checked = true;
		}
	} else {
		for (let index = 0; index < boxes.length; index++) {
			boxes[index].checked = false;
		}
	}
}
async function multipleAction() {
	var boxes = document.getElementsByName('selectBox');
	newTitlesAll = document.getElementsByClassName('newTitleShorted');
	newMetasAll = document.getElementsByClassName('newMetaShorted');
	if (document.getElementById('nmd_multiple_action').value == 'retry') {
		for (let index = 0; index < boxes.length; index++) {
			if (boxes[index].checked) {
				retry(boxes[index].value, idArray[boxes[index].value], index);
			}
		}
	}
	if (document.getElementById('nmd_multiple_action').value == 'submit') {
		for (let index = 0; index < boxes.length; index++) {
			if (boxes[index].checked) {
				blockCounter = boxes[index].value;
				updatePage(idArray[blockCounter], newTitlesArray[blockCounter], newExceptArray[blockCounter], blockCounter);
			}
		}
		alert('Metadaten wurden erfolgreich geändert');
	}
}

function updateTextWidth(text, type) {
	if (type == 'title') {
		var maxCharacters = 70;
	} else {
		var maxCharacters = 160;
	}

	if (text.length < maxCharacters) {
		return text;
	}
	shortenedText = text.substring(0, maxCharacters) + '...';
	return shortenedText;
}
function shortenText() {
	var titles = document.getElementsByClassName('titleShorted');
	for (let index = 0; index < titles.length; index++) {
		titles[index].innerHTML = updateTextWidth(titles[index].innerHTML, 'title');
	}
	var metas = document.getElementsByClassName('metaShorted');
	for (let index = 0; index < metas.length; index++) {
		metas[index].innerHTML = updateTextWidth(metas[index].innerHTML, 'meta');
	}
	var newTitles = document.getElementsByClassName('newTitleShorted');
	for (let index = 0; index < newTitles.length; index++) {
		newTitles[index].innerHTML = updateTextWidth(newTitles[index].innerHTML, 'title');
	}
	var metas = document.getElementsByClassName('newMetaShorted');
	for (let index = 0; index < metas.length; index++) {
		metas[index].innerHTML = updateTextWidth(metas[index].innerHTML, 'meta');
	}
}
function showTab(tabName, n) {
	var tab = document.getElementById(tabName + 'Container');

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
