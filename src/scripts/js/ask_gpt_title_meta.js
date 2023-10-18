const API_ENDPOINT = 'https://api.openai.com/v1/chat/completions';

var promptList;
let loadingText = document.getElementById('loadingText');

function getHomeUrl() {
	var href = window.location.href;
	var index = href.indexOf('/wp-admin');
	var homeUrl = href.substring(0, index);
	return homeUrl;
}
homeUrl = getHomeUrl();
const request = new XMLHttpRequest();
var jsonUrl = homeUrl + '/wp-content/plugins/SEOContent/src/scripts/php/prompts.json'; // Replace with the actual URL of your JSON file
let metaTemplateList;
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
			},
			error: function (error) {
				console.log(error);
			},
		});
	});
}

checkpremium();

request.open('GET', jsonUrl, true);
request.onreadystatechange = function () {
	if (request.readyState === 4 && request.status === 200) {
		// Parse the JSON response
		const json = JSON.parse(request.responseText);

		// Save the JSON data to a variable
		const jsonData = json;
		promptList = jsonData;
		document.getElementById('promtTitel').value = promptList['newTitlePrompt'];
		document.getElementById('promtMeta').value = promptList['newMetaPrompt'];

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

const request3 = new XMLHttpRequest();

var jsonUrl = homeUrl + '/wp-content/plugins/SEOContent/src/scripts/php/metaPromptTemplates.json'; // Replace with the actual URL of your JSON file
request3.open('GET', jsonUrl, true);
request3.onreadystatechange = function () {
	if (request3.readyState === 4 && request3.status === 200) {
		// Parse the JSON response
		const json = JSON.parse(request3.responseText);

		// Save the JSON data to a variable
		const jsonData = json;
		metaTemplateList = jsonData;

		// Use the jsonData variable as needed
		console.log(jsonData);
	}
};

request3.send();

async function getAllPages(type) {
	setLoadingScreen();
	loadingText.innerHTML = 'Lade Seiten...';
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
	removeLoadingScreen();
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
			loadingText.innerHTML = 'Lade Text...';
			var title = page.title.rendered.replace(/(<([^>]+)>)/gi, '');
			if (title == '' || title == undefined) {
				title = 'Kein Titel vorhanden';
			}
			titlesArray.push(title);
			try {
				var meta = page.yoast_head_json.description;
				if (meta == undefined) {
					meta = page.excerpt.rendered;
				}
			} catch (error) {
				var meta = page.excerpt.rendered;
			}
			if (meta == undefined || meta == '') {
				meta = 'Keine Metabeschreibung vorhanden';
			} else {
				meta = meta.replace(/(<([^>]+)>)/gi, '');
			}
			exceptArray.push(meta);
			idArray.push(page.id);
			urlArray.push(page.link);
		} catch (error) {
			console.log(error);
		}
	}
	for (var i = 0 + printetTitles; i < titlesArray.length; i++) {
		loadingText.innerHTML = 'Lade Snippet...';
		addBlock(titlesArray[i], exceptArray[i], idArray[i]);
		printetTitles++;
		shortenText();
	}

	let paragraphs = document.querySelectorAll('.titleDesktop');
	paragraphs.forEach(function (para, index) {
		para.addEventListener('input', function (event) {
			console.log('Content edited in paragraph at index', index);
			console.log('New content: ', event.target.textContent.trim());
			titlesArray[index] = event.target.textContent.trim();
		});
	});
	paragraphs = document.querySelectorAll('.metaDesktop');
	paragraphs.forEach(function (para, index) {
		para.addEventListener('input', function (event) {
			console.log('Content edited in paragraph at index', index);
			console.log('New content: ', event.target.textContent.trim());
			exceptArray[index] = event.target.textContent.trim();
		});
	});
	paragraphs = document.querySelectorAll('.titleMobile');
	paragraphs.forEach(function (para, index) {
		para.addEventListener('input', function (event) {
			console.log('Content edited in paragraph at index', index);
			console.log('New content: ', event.target.textContent.trim());
			titlesArray[index] = event.target.textContent.trim();
		});
	});
	paragraphs = document.querySelectorAll('.metaMobile');
	paragraphs.forEach(function (para, index) {
		para.addEventListener('input', function (event) {
			console.log('Content edited in paragraph at index', index);
			console.log('New content: ', event.target.textContent.trim());
			exceptArray[index] = event.target.textContent.trim();
		});
	});

	var boxes = document.getElementsByName('selectBox');

	for (let index = 0; index < boxes.length; index++) {
		boxes[index].checked = true;
	}
	document.getElementById('cb-select-all').checked = true;
	console.log(titlesArray);
	document.getElementById('buttonBar').style.display = 'flex';
	removeLoadingScreen();
}

var sonderzeichen = '✅,✓,►,';
var alreadyPrintet = 0;
let firstTry = true;
let finishedCounter = 0;
async function generateNewSnippets() {
	if (alreadyPrintet == titlesArray.length) {
		multipleAction();
		return;
	}

	setLoadingScreen();
	loadingText.innerHTML = 'Generiere Snippets...';
	var stil = document.getElementById('nmd_style').value;
	var metas = document.getElementsByClassName('metaShorted');
	let boxes = document.getElementsByName('selectBox');
	var table = document.getElementById('table_body');
	var rows = table.getElementsByTagName('tr');
	let boxesChecked = 0;
	for (let i = 0; i < boxes.length; i++) {
		if (boxes[i].checked) {
			boxesChecked++;
		}
	}
	let trueI = 0;
	for (let i = 0 + alreadyPrintet; i < titlesArray.length; i++) {
		let checkedIndex = i;
		if (!firstTry) {
			let subButtons = document.getElementsByName('selectBox');

			for (let j = 0; j < subButtons.length; j++) {
				if (rows[j].getElementsByTagName('td').length == 3) continue;
				if (subButtons[j].checked) {
					checkedIndex = j;
					subButtons[j].checked = false;
					finishedCounter++;
					metas[(checkedIndex + 1) * 2 - 1].style.animation = '1.3s linear 0s infinite normal none running nmd-fading';
					metas[(checkedIndex + 1) * 2 - 2].style.animation = '1.3s linear 0s infinite normal none running nmd-fading';
					generateNewSnippetsSubFunction(
						stil,
						trueI,
						checkedIndex,
						metas[(checkedIndex + 1) * 2 - 1],
						metas[(checkedIndex + 1) * 2 - 2]
					);
					await new Promise((r) => setTimeout(r, 800));
					trueI++;
					alreadyPrintet++;
					break;
				}
			}
		}
		if (!boxes[checkedIndex].checked) continue;
		finishedCounter++;
		metas[(checkedIndex + 1) * 2 - 1].style.animation = '1.3s linear 0s infinite normal none running nmd-fading';
		metas[(checkedIndex + 1) * 2 - 2].style.animation = '1.3s linear 0s infinite normal none running nmd-fading';
		generateNewSnippetsSubFunction(stil, trueI, checkedIndex, metas[(checkedIndex + 1) * 2 - 1], metas[(checkedIndex + 1) * 2 - 2]);
		await new Promise((r) => setTimeout(r, 800));
		trueI++;
		alreadyPrintet++;
	}
	if (alreadyPrintet == titlesArray.length) {
		multipleAction();
	}
	firstTry = false;
	removeLoadingScreen();
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
	loadingText.innerHTML = 'Frage KI...';
	// Get the new meta description from GPT.
	let newMeta = await askGpt(prompt, 160);
	loadingText.innerHTML = 'Füge Snippet hinzu...';
	// Add the new snippet to the page.
	await addNewBlock(newTitle, newMeta, idArray[i], i);
	asyncCoounter++;
	shortenText();
	element.style.animation = 'none';
	element2.style.animation = 'none';
	if (asyncCoounter == finishedCounter) {
		await new Promise((r) => setTimeout(r, 1500));
		var newTitles = document.getElementsByClassName('newTitleDesktop');
		var newMetas = document.getElementsByClassName('newMetaDesktop');
		newTitlesArray = [];
		newExceptArray = [];
		for (let i = 0; i < newTitles.length; i++) {
			newTitlesArray.push(newTitles[i].innerHTML);
			newExceptArray.push(newMetas[i].innerHTML);
		}
		let paragraphs = document.querySelectorAll('.newTitleDesktop');
		paragraphs.forEach(function (para, index) {
			para.addEventListener('input', function (event) {
				console.log('Content edited in paragraph at index', index);
				console.log('New content: ', event.target.textContent.trim());
				newTitlesArray[index] = event.target.textContent.trim();
			});
		});
		paragraphs = document.querySelectorAll('.newMetaDesktop');
		paragraphs.forEach(function (para, index) {
			para.addEventListener('input', function (event) {
				console.log('Content edited in paragraph at index', index);
				console.log('New content: ', event.target.textContent.trim());
				newExceptArray[index] = event.target.textContent.trim();
			});
		});
		paragraphs = document.querySelectorAll('.newTitleMobile');
		paragraphs.forEach(function (para, index) {
			para.addEventListener('input', function (event) {
				console.log('Content edited in paragraph at index', index);
				console.log('New content: ', event.target.textContent.trim());
				newTitlesArray[index] = event.target.textContent.trim();
			});
		});
		paragraphs = document.querySelectorAll('.newMetaMobile');
		paragraphs.forEach(function (para, index) {
			para.addEventListener('input', function (event) {
				console.log('Content edited in paragraph at index', index);
				console.log('New content: ', event.target.textContent.trim());
				newExceptArray[index] = event.target.textContent.trim();
			});
		});
	}
}

var blockCount = 0;
async function addBlock(title, meta, id) {
	blockCount++;
	var tr = document.createElement('tr');
	tr.classList.add('title-meta-block-body');
	tr.innerHTML = `
    <td style="width: 1%;margin-left: 30px;">
        <input name="selectBox" id="cb-select-${id}" type="checkbox" name="post[]" value="${blockCount - 1}">
    </td>
    <td class="firstTd">
        <div id="desktop-snippet" class="desktop-snippet">
            <div class="urlHeader">
                <div id="url-ausgabe">${homeUrl}</div>
                <span class="statusText" style="color: orange">Aktueller Inhalt</span>
            </div>
			<div style="display:flex; margin-bottom:6px">
				<p contenteditable="true" id="ausgabeTitel" class="titleShorted titleDesktop">
					${title}
				</p>
				<a href="${urlArray[blockCount - 1]}" target="_blank" rel="noopener" style="cursor: pointer; margin-left: auto">
					<span>
						<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
							<path d="M320 0c-17.7 0-32 14.3-32 32s14.3 32 32 32h82.7L201.4 265.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L448 109.3V192c0 17.7 14.3 32 32 32s32-14.3 32-32V32c0-17.7-14.3-32-32-32H320zM80 32C35.8 32 0 67.8 0 112V432c0 44.2 35.8 80 80 80H400c44.2 0 80-35.8 80-80V320c0-17.7-14.3-32-32-32s-32 14.3-32 32V432c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16H192c17.7 0 32-14.3 32-32s-14.3-32-32-32H80z" />
						</svg>
					</span>
				</a>
			</div>
            <span class="metaShorted metaDesktop" contenteditable="true" id="ausgabeMetaBeschreibung">${meta}</span>
            <p class="empty"></p>
        </div>
        <div id="mobile-snippet" class="mobile-snippet">
            <div class="urlHeader">
                <div id="url-ausgabe-mobile">${homeUrl}</div>
                <span class="statusText" style="color: orange">Aktueller Inhalt</span>
            </div>
			<div style="display:flex; margin-bottom:6px">
				<p class="titleShorted titleMobile" id="ausgabeTitel-mobile">
					${title}
				</p>
				<a href="${urlArray[blockCount - 1]}" target="_blank" rel="noopener" style="cursor: pointer;margin-left: auto">
					<span>
						<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
							<path d="M320 0c-17.7 0-32 14.3-32 32s14.3 32 32 32h82.7L201.4 265.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L448 109.3V192c0 17.7 14.3 32 32 32s32-14.3 32-32V32c0-17.7-14.3-32-32-32H320zM80 32C35.8 32 0 67.8 0 112V432c0 44.2 35.8 80 80 80H400c44.2 0 80-35.8 80-80V320c0-17.7-14.3-32-32-32s-32 14.3-32 32V432c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16H192c17.7 0 32-14.3 32-32s-14.3-32-32-32H80z" />
						</svg>
					</span>
				</a>
			</div>
            <span class="metaShorted metaMobile" contenteditable id="ausgabeMetaBeschreibung-mobile">${meta}</span>
            <p class="empty"></p>
        </div>
    </td>
`;

	document.getElementById('table_body').appendChild(tr);
}

var newBlockCount = 0;
async function addNewBlock(newTitle, newMeta, id, i) {
	newBlockCount++;
	var td = document.createElement('td');
	td.classList.add('secondTd');
	td.innerHTML = `
	<div id="desktop-snippet" class="desktop-snippet">
		<div class="urlHeader">
			<div id="url-ausgabe">${homeUrl}</div>
			<span class="statusText" style="color: #afcb08">Neuer Inhalt</span>
		</div>
		<div style="display:flex; margin-bottom:6px">
			<p class="newTitleDesktop newTitleShorted" id="ausgabeTitel" contenteditable="true">${newTitle}</p>
			<a href="${urlArray[i]}" target="_blank" rel="noopener" style="cursor: pointer; margin-left: auto">
				<span>
					<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
						<path d="M320 0c-17.7 0-32 14.3-32 32s14.3 32 32 32h82.7L201.4 265.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L448 109.3V192c0 17.7 14.3 32 32 32s32-14.3 32-32V32c0-17.7-14.3-32-32-32H320zM80 32C35.8 32 0 67.8 0 112V432c0 44.2 35.8 80 80 80H400c44.2 0 80-35.8 80-80V320c0-17.7-14.3-32-32-32s-32 14.3-32 32V432c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16H192c17.7 0 32-14.3 32-32s-14.3-32-32-32H80z" />
					</svg>
				</span>
			</a>
		</div>
		<p>
			<span class="newMetaDesktop newMetaShorted" id="ausgabeMetaBeschreibung" contenteditable="true">${newMeta}</span>
		</p>
		<p class="empty"></p>
	</div>
	<div id="mobile-snippet" class="mobile-snippet">
		<div class="urlHeader">
			<div id="url-ausgabe-mobile">${homeUrl}</div>
			<span class="statusText" style="color: #afcb08">Neuer Inhalt</span>
		</div>
		<div style="display:flex; margin-bottom:6px">
			<p id="ausgabeTitel-mobile" class="newTitleShorted newTitleMobile" contenteditable="true">${newTitle}</p>
			<a  href="${urlArray[i]}" target="_blank" rel="noopener" style="cursor: pointer;margin-left: auto">
				<span>
					<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
						<path d="M320 0c-17.7 0-32 14.3-32 32s14.3 32 32 32h82.7L201.4 265.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L448 109.3V192c0 17.7 14.3 32 32 32s32-14.3 32-32V32c0-17.7-14.3-32-32-32H320zM80 32C35.8 32 0 67.8 0 112V432c0 44.2 35.8 80 80 80H400c44.2 0 80-35.8 80-80V320c0-17.7-14.3-32-32-32s-32 14.3-32 32V432c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16H192c17.7 0 32-14.3 32-32s-14.3-32-32-32H80z" />
					</svg>
				</span>
			</a>
		</div>
		<p>
			<span class="newMetaShorted newMetaMobile" id="ausgabeMetaBeschreibung-mobile" contenteditable="true">${newMeta}</span>
		</p>
		<p class="empty"></p>
	</div>
	<div class="buttonDiv">
		<button type="button" class="button action" onclick="retry(${i}, ${id})">Neu generieren</button><br>
		<button style="margin-top:8px" class="button action submitChanges" type="button" onclick="submitChanges(${i}, ${id}, this)">Vorschlag übernehmen</button><br>
	</div>
	`;

	var table = document.getElementById('table_body');
	var rows = table.getElementsByTagName('tr');
	rows[i].appendChild(td);
}

var newTitlesAll = '';
var newMetasAll = '';
async function retry(index, id, index) {
	let stil = document.getElementById('nmd_style').value;
	let newTitles = document.getElementsByClassName('newTitleDesktop');
	let newMetas = document.getElementsByClassName('newMetaDesktop');
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

function submitChanges(blockCount, id, element) {
	setLoadingScreen();

	let subButtons = document.getElementsByClassName('submitChanges');
	let buttonIndex = Array.from(subButtons).indexOf(element);

	loadingText.innerHTML = 'Speichere Änderungen...';

	updatePage(id, newTitlesArray[buttonIndex], newExceptArray[buttonIndex], blockCount);
	console.log(id, newTitlesArray[buttonIndex], newExceptArray[buttonIndex], blockCount);

	removeLoadingScreen();
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
		if (premium == false) {
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
		} else {
			return new Promise((resolve, reject) => {
				jQuery.ajax({
					url: myAjax.ajaxurl,
					method: 'POST',
					data: {
						action: 'ask_gpt',
						chat: chat,
						temperature: 0.5,
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
	if (document.getElementById('nmd_multiple_action').value == 'submit') {
		setLoadingScreen();
		loadingText.innerHTML = 'Speichere Änderungen...';
		for (let index = 0; index < boxes.length; index++) {
			if (boxes[index].checked) {
				blockCounter = boxes[index].value;
				updatePage(idArray[blockCounter], newTitlesArray[blockCounter], newExceptArray[blockCounter], blockCounter);
			}
		}
		removeLoadingScreen();
		alert('Metadaten wurden erfolgreich geändert');
		return;
	}
	if (document.getElementById('nmd_multiple_action').value == 'retry' || !firstTry) {
		setLoadingScreen();
		loadingText.innerHTML = 'Generiere Snippets...';
		for (let index = 0; index < boxes.length; index++) {
			if (boxes[index].checked) {
				retry(boxes[index].value, idArray[boxes[index].value], index);
			}
		}
		removeLoadingScreen();
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

function get_template(folder, subFolder, name) {
	console.log(name);
	jQuery(document).ready(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			method: 'POST',
			data: {
				action: 'get_template_meta',
			},
			success: function (response) {
				response = response.substring(0, response.length - 1);

				console.log(response);
				const prompts = JSON.parse(response);

				document.getElementById('template_name').value = name;
				document.getElementById('template_description').value = metaTemplateList[folder][subFolder][name][0];
				document.getElementById('promtTitel').value = metaTemplateList[folder][subFolder][name][1];
				document.getElementById('promtMeta').value = metaTemplateList[folder][subFolder][name][2];
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
	var prompt1 = document.getElementById('promtTitel').value;
	var prompt2 = document.getElementById('promtMeta').value;
	var folder = document.getElementById('unterordner_select').value;
	var folderArray = folder.split(',');
	jQuery(document).ready(function ($) {
		$.ajax({
			url: myAjax.ajaxurl,
			method: 'POST',
			data: {
				action: 'save_template_meta',
				template_name: template_name,
				template_description: template_description,
				prompt1: prompt1,
				prompt2: prompt2,
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
				action: 'delete_template_meta',
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
				action: 'delete_template_meta',
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
				action: 'delete_template_meta',
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
				action: 'create_folder_meta',
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
				action: 'create_sub_folder_meta',
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
				action: 'edit_folder_meta',
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
			action: 'update_seocontent_templates_meta_action',
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
function setLoadingScreen() {
	document.getElementById('overlay').style.display = 'flex';
	document.body.classList.add('blurred');
}
function removeLoadingScreen() {
	document.getElementById('overlay').style.display = 'none';
	document.body.classList.remove('blurred');
}
function showSettings() {
	document.getElementById('settingsOverlay').style.display = 'block';
	document.getElementById('overlaySettings').style.display = 'block';
}
function closeSettings() {
	document.getElementById('settingsOverlay').style.display = 'none';
	document.getElementById('overlaySettings').style.display = 'none';
}
