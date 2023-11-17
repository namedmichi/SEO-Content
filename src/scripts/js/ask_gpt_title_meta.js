const API_ENDPOINT = 'https://api.openai.com/v1/chat/completions';

let promptList;
let loadingText;

let div = document.createElement('div');
div.id = 'overlaySettings';
div.style.display = 'none';
div.innerHTML = '';

document.getElementById('wpwrap').appendChild(div);

function getHomeUrl() {
	let href = window.location.href;
	let index = href.indexOf('/wp-admin');
	let homeUrl = href.substring(0, index);
	return homeUrl;
}
homeUrl = getHomeUrl();
const request = new XMLHttpRequest();
let jsonUrl = homeUrl + '/wp-content/plugins/SEOContent/src/scripts/php/prompts.json'; // Replace with the actual URL of your JSON file
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
let settingsArray;
const request2 = new XMLHttpRequest();
jsonUrl = homeUrl + '/wp-content/plugins/SEOContent/src/scripts/php/settings.json'; // Replace with the actual URL of your JSON file
let apiKey = '';
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

jsonUrl = homeUrl + '/wp-content/plugins/SEOContent/src/scripts/php/metaPromptTemplates.json'; // Replace with the actual URL of your JSON file
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
	for (let page = 1; page <= Math.ceil(totalPages / 100); page++) {
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

let titlesArray = [];
let exceptArray = [];
let idArray = [];
let newTitlesArray = [];
let newExceptArray = [];
let urlArray = [];
let inhaltArray = [];

let printetTitles = 0;
async function getInfos(type) {
	let pagesArray = [];
	await getAllPages(type)
		.then((pages) => {
			console.log(pages);
			pagesArray = pages;
		})
		.catch((error) => {
			console.error(error);
		});

	for (let i = 0; i < pagesArray.length; i++) {
		let page = pagesArray[i];
		let title;
		let inhalt;
		let meta;
		console.log(page);
		try {
			//sanitize the title  from html tags
			title = page.title.rendered.replace(/(<([^>]+)>)/gi, '');
			if (title == '' || title == undefined) {
				title = 'Kein Titel vorhanden';
			}
			titlesArray.push(title);
			try {
				meta = page.yoast_head_json.description;
				if (meta == undefined) {
					meta = page.excerpt.rendered;
				}
			} catch (error) {
				meta = page.excerpt.rendered;
			}
			if (meta == undefined || meta == '') {
				meta = 'Keine Metabeschreibung vorhanden';
			} else {
				meta = meta.replace(/(<([^>]+)>)/gi, '');
			}
			try {
				inhalt = page.content.rendered;
				if (inhalt == undefined || inhalt == '') {
					throw 'Kein Inhalt vorhanden';
				}
				inhaltArray.push(inhalt);
			} catch (error) {
				inhaltArray.push('');
			}
			exceptArray.push(meta);
			idArray.push(page.id);
			urlArray.push(page.link);
		} catch (error) {
			console.log(error);
		}
	}
	console.log(inhaltArray);
	keywordArray = [];
	console.log(idArray);
	keywordArray = Array.from(Array(idArray.length).keys());
	for (let i = 0; i < idArray.length; i++) {
		jQuery(document).ready(function ($) {
			$.ajax({
				url: myAjax.ajaxurl,
				method: 'POST',
				data: {
					action: 'get_page_keyword',
					pageId: idArray[i],
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
					keywordArray[i] = keyword;
				},
				error: function (error) {
					console.log(error);
				},
			});
		});
	}
	for (let i = 0; i < titlesArray.length; i++) {
		let tr = document.createElement('tr');
		tr.classList.add('title-meta-block-body');
		document.getElementById('table_body').appendChild(tr);
	}
	for (let i = 0 + printetTitles; i < titlesArray.length; i++) {
		addBlock(titlesArray[i], exceptArray[i], idArray[i], i);
	}
}

let sonderzeichen = '✅,✓,►,';
let alreadyPrintet = 0;
let firstTry = true;
let finishedCounter = 0;
async function generateNewSnippets() {
	if (alreadyPrintet == titlesArray.length) {
		multipleAction();
		return;
	}

	setLoadingScreen();
	let stil = document.getElementById('nmd_style').value;
	let metas = document.getElementsByClassName('metaShorted');
	let boxes = document.getElementsByName('selectBox');
	let table = document.getElementById('table_body');
	let rows = table.getElementsByTagName('tr');
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
}
let asyncCoounter = 0;
async function generateNewSnippetsSubFunction(stil, trueI, i, element, element2) {
	let title = titlesArray[i];
	let meta = exceptArray[i];
	let keyword = keywordArray[i];
	// Build the prompt for the title.
	let prompt = document.getElementById('promtTitel').value;
	prompt = prompt.replace('{title}', title);
	prompt = prompt.replace('{stil}', stil);
	prompt = prompt.replace('{keyword}', keyword);

	// Get the new title from GPT.
	let newTitle = await askGpt(prompt, 60);

	let ctas = settingsArray['cta'];
	let usps = settingsArray['usps'];

	// Build the prompt for the meta description.
	prompt = document.getElementById('promtMeta').value;
	prompt = prompt.replace('{title}', title);
	prompt = prompt.replace('{meta}', meta);
	prompt = prompt.replace('{stil}', stil);
	prompt = prompt.replace('{keyword}', keyword);
	prompt = prompt.replace('{inhalt}', inhaltArray[i]);
	if (document.getElementById('marketing').checked) {
		prompt = prompt.replace('{marketingInfo}', '.Mögliche: {usps}. Mögliche Call to Action Sätze: {ctas}.');
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
	if (asyncCoounter == finishedCounter) {
		await new Promise((r) => setTimeout(r, 1500));
		let newTitles = document.getElementsByClassName('newTitleDesktop');
		let newMetas = document.getElementsByClassName('newMetaDesktop');
		newTitlesArray = Array.from(Array(titlesArray.length).keys());
		newExceptArray = Array.from(Array(titlesArray.length).keys());
		for (let i = 0; i < newTitles.length; i++) {
			newTitlesArray[i] = newTitles[i].innerHTML;
			newExceptArray[i] = newMetas[i].innerHTML;
		}
		let paragraphs = document.querySelectorAll('.newTitleDesktop');
		paragraphs.forEach(function (para, index) {
			para.addEventListener('input', function (event) {
				newTitlesArray[index] = event.target.textContent.trim();
			});
		});
		paragraphs = document.querySelectorAll('.newMetaDesktop');
		paragraphs.forEach(function (para, index) {
			para.addEventListener('input', function (event) {
				newExceptArray[index] = event.target.textContent.trim();
			});
		});
		paragraphs = document.querySelectorAll('.newTitleMobile');
		paragraphs.forEach(function (para, index) {
			para.addEventListener('input', function (event) {
				newTitlesArray[index] = event.target.textContent.trim();
			});
		});
		paragraphs = document.querySelectorAll('.newMetaMobile');
		paragraphs.forEach(function (para, index) {
			para.addEventListener('input', function (event) {
				newExceptArray[index] = event.target.textContent.trim();
			});
		});
	}
}

function checkReady() {
	if (printetTitles == titlesArray.length) {
		shortenText();
		removeLoadingScreen();
		addEventListenerToParagraphs('.titleDesktop', function (event, index) {
			titlesArray[index] = event.target.textContent.trim();
		});
		addEventListenerToParagraphs('.metaDesktop', function (event, index) {
			exceptArray[index] = event.target.textContent.trim();
		});
		addEventListenerToParagraphs('.titleMobile', function (event, index) {
			titlesArray[index] = event.target.textContent.trim();
		});
		addEventListenerToParagraphs('.metaMobile', function (event, index) {
			exceptArray[index] = event.target.textContent.trim();
		});

		function addEventListenerToParagraphs(selector, callback) {
			let paragraphs = document.querySelectorAll(selector);
			paragraphs.forEach(function (para, index) {
				para.addEventListener('input', function (event) {
					callback(event, index);
				});
			});
		}

		let boxes = document.getElementsByName('selectBox');

		for (let index = 0; index < boxes.length; index++) {
			boxes[index].checked = true;
		}
		document.getElementById('cb-select-all').checked = true;
		console.log(titlesArray);
		document.getElementById('buttonBar').style.display = 'flex';
	}
}

let blockCount = 0;
async function addBlock(title, meta, id, i) {
	blockCount++;
	let classNameKeyword = 'keywordSet';
	let keywordText = 'Keyword ändern';
	let asyncNumber = blockCount;
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
				if (keyword == undefined || keyword == null || keyword == '') {
					classNameKeyword = 'keywordUnset';
					keywordText = 'Keyword setzen';
					keyword = '';
				}
				let tr = document.getElementsByTagName('tr')[i + 1];
				tr.innerHTML =
					`
				<td style="width: 1%;margin-left: 30px;">
					<input name="selectBox" id="cb-select-${id}" type="checkbox" name="post[]" value="${asyncNumber - 1}">
				</td>
				<td class="firstTd">
					<div id="desktop-snippet" class="desktop-snippet">
						<div class="urlHeader">
							<div id="url-ausgabe">${urlArray[asyncNumber - 1]}</div>
							<span style="margin-left:auto;" class="` +
					classNameKeyword +
					`" onclick="openChangeKeyword(` +
					id +
					` , '` +
					keyword +
					`')">` +
					keywordText +
					`</span>
							<span class="statusText" style="color: orange">Aktueller Inhalt</span>
						</div>
						<div style="display:flex; margin-bottom:6px">
							<p contenteditable="true" id="ausgabeTitel" class="titleShorted titleDesktop">
								${title}
							</p>
							<a href="${urlArray[asyncNumber - 1]}" target="_blank" rel="noopener" style="cursor: pointer; margin-left: auto">
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
							<div id="url-ausgabe-mobile">${urlArray[asyncNumber - 1]}</div>
							<span class="statusText" style="color: orange">Aktueller Inhalt</span>
						</div>
						<div style="display:flex; margin-bottom:6px">
							<p class="titleShorted titleMobile" id="ausgabeTitel-mobile">
								${title}
							</p>
							<a href="${urlArray[asyncNumber - 1]}" target="_blank" rel="noopener" style="cursor: pointer;margin-left: auto">
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

				printetTitles++;
				checkReady();
				return Promise.resolve();
			},
			error: function (error) {
				console.log(error);
				return Promise.resolve();
			},
		});
	});
}

let newBlockCount = 0;
async function addNewBlock(newTitle, newMeta, id, i) {
	newBlockCount++;
	let currentTr = document.getElementsByTagName('tr')[i + 1];
	let td = document.createElement('td');
	td.classList.add('secondTd');
	td.innerHTML = `
	<div id="desktop-snippet" class="desktop-snippet">
		<div class="urlHeader">
			<div id="url-ausgabe">${urlArray[i]}</div>
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
			<div id="url-ausgabe-mobile">${urlArray[i]}</div>
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

	let table = document.getElementById('table_body');
	let rows = table.getElementsByTagName('tr');
	rows[i].appendChild(td);
}

let newTitlesAll = '';
let newMetasAll = '';
async function retry(index, id) {
	let stil = document.getElementById('nmd_style').value;
	let newTitles = document.getElementsByClassName('newTitleDesktop');
	let newMetas = document.getElementsByClassName('newMetaDesktop');
	let keyword = keywordArray[index];
	console.log('Size of newMetasAll: ', newMetasAll.length);
	console.log('Accessing index: ', (index + 1) * 2 - 2);
	let index1 = (parseInt(index) + 1) * 2 - 2;
	let index2 = (parseInt(index) + 1) * 2 - 1;
	newMetasAll[index1].style.animation = '1.3s linear 0s infinite normal none running nmd-fading';
	newMetasAll[index2].style.animation = '1.3s linear 0s infinite normal none running nmd-fading';

	let title = newTitles[index].innerHTML;
	let meta = newMetas[index].innerHTML;
	let prompt = promptList['newTitlePrompt'];
	prompt = prompt.replace('{title}', title);
	prompt = prompt.replace('{stil}', stil);
	prompt = prompt.replace('{keyword}', keyword);

	let newTitle = await askGpt(prompt, 60);

	let ctas = settingsArray['cta'];
	let usps = settingsArray['usps'];
	newTitlesArray.push(newTitle);
	prompt = promptList['newMetaPrompt'];
	prompt = prompt.replace('{title}', title);
	prompt = prompt.replace('{meta}', meta);
	prompt = prompt.replace('{keyword}', keyword);
	prompt = prompt.replace('{inhalt}', inhaltArray[index]);
	if (document.getElementById('marketing').checked) {
		prompt = prompt.replace('{usps}', usps);
		prompt = prompt.replace('{ctas}', ctas);
	}
	prompt = prompt.replace('{stil}', stil);
	prompt = prompt.replace('{sonderzeichen}', sonderzeichen);

	let newMeta = await askGpt(prompt, 160);

	newTitlesArray[index] = newTitle;
	newExceptArray[index] = newMeta;
	newTitlesAll[(parseInt(index) + 1) * 2 - 1].innerHTML = newTitle;
	newMetasAll[(parseInt(index) + 1) * 2 - 1].innerHTML = newMeta;
	newTitlesAll[(parseInt(index) + 1) * 2 - 2].innerHTML = newTitle;
	newMetasAll[(parseInt(index) + 1) * 2 - 2].innerHTML = newMeta;

	newMetasAll[(parseInt(index) + 1) * 2 - 1].style.animation = 'none';
	newMetasAll[(parseInt(index) + 1) * 2 - 2].style.animation = 'none';

	shortenText();
}

function editBlock(blockCount, id) {
	let title = newTitlesArray[blockCount];
	let meta = newExceptArray[blockCount];

	let titlesTempArray = document.getElementsByClassName('newTitleShorted');
	let metaTempArray = document.getElementsByClassName('newMetaShorted');

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
	let chat = [
		{
			role: 'system',
			content:
				'You are a helpful assistant speaking German. You are a creativ Textwriter that helps with SEO and Text optimization.Do not explain yourself. Complete my Promts:',
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
					model: 'gpt-4-1106-preview',
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
						model: 'gpt-4-1106-preview',
					},
					success: async function (response) {
						let task_id = response.split('"')[3];
						task_id = task_id.split('"')[0];
						let finished = false;
						while (!finished) {
							jQuery.ajax({
								url: myAjax.ajaxurl,
								method: 'POST',
								data: {
									action: 'check_task_status',
									task_id: task_id,
								},
								success: function (response) {
									console.log(response);
									if (!response.includes('Task still processing.')) {
										console.log(response);
										finished = true;
										try {
											let content = response.replace(/^"(.*)"$/, '$1');

											resolve(content.trim()); // Resolve the promise with the response content
										} catch (e) {
											reject(e); // Reject the promise if there is an error (e.g., in parsing the response)
										}
									} else {
										console.log("Task still processing. Let's wait 1 seconds and try again.");
										console.log(response);
									}
								},
								error: function (error) {
									console.log(error);
									reject(e);
								},
							});
							await new Promise((r) => setTimeout(r, 3000));
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
			'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dann erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
		);
		throw error;
	}
}

function switchSnippets(typ) {
	if (typ == 'mobile') {
		let desktopSnippets = document.getElementsByClassName('desktop-snippet');
		for (let index = 0; index < desktopSnippets.length; index++) {
			desktopSnippets[index].style.display = 'none';
		}
		let mobileSnippet = document.getElementsByClassName('mobile-snippet');
		for (let index = 0; index < mobileSnippet.length; index++) {
			mobileSnippet[index].style.display = 'inline-block';
		}
	}
	if (typ == 'pc') {
		let desktopSnippets = document.getElementsByClassName('desktop-snippet');
		for (let index = 0; index < desktopSnippets.length; index++) {
			desktopSnippets[index].style.display = 'inline-block';
		}
		let mobileSnippet = document.getElementsByClassName('mobile-snippet');
		for (let index = 0; index < mobileSnippet.length; index++) {
			mobileSnippet[index].style.display = 'none';
		}
	}
}
function selectAll() {
	let boxes = document.getElementsByName('selectBox');
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
	newTitlesAll = document.getElementsByClassName('newTitleShorted');
	newMetasAll = document.getElementsByClassName('newMetaShorted');
	let boxes = document.getElementsByName('selectBox');
	if (!firstTry) {
		setLoadingScreen();
		for (let index = 0; index < boxes.length; index++) {
			if (boxes[index].checked) {
				retry(boxes[index].value, idArray[boxes[index].value]);
			}
		}
		removeLoadingScreen();
	}
	removeLoadingScreen();
}

function setAllSelected() {
	let boxes = document.getElementsByName('selectBox');
	newTitlesAll = document.getElementsByClassName('newTitleShorted');
	newMetasAll = document.getElementsByClassName('newMetaShorted');
	setLoadingScreen();
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

function updateTextWidth(text, type) {
	let maxCharacters;
	if (type == 'title') {
		maxCharacters = 70;
	} else {
		maxCharacters = 160;
	}

	if (text.length < maxCharacters) {
		return text;
	}
	shortenedText = text.substring(0, maxCharacters) + '...';
	return shortenedText;
}
function shortenText() {
	let titles = document.getElementsByClassName('titleShorted');
	for (let index = 0; index < titles.length; index++) {
		titles[index].innerHTML = updateTextWidth(titles[index].innerHTML, 'title');
	}
	let metas = document.getElementsByClassName('metaShorted');
	for (let index = 0; index < metas.length; index++) {
		metas[index].innerHTML = updateTextWidth(metas[index].innerHTML, 'meta');
	}
	let newTitles = document.getElementsByClassName('newTitleShorted');
	for (let index = 0; index < newTitles.length; index++) {
		newTitles[index].innerHTML = updateTextWidth(newTitles[index].innerHTML, 'title');
	}
	let newMetas = document.getElementsByClassName('newMetaShorted');
	for (let index = 0; index < newMetas.length; index++) {
		newMetas[index].innerHTML = updateTextWidth(newMetas[index].innerHTML, 'meta');
	}
}
function showTab(tabName, n) {
	let tab = document.getElementById(tabName + 'Container');

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
	let template_name = document.getElementById('template_name').value;
	let template_description = document.getElementById('template_description').value;
	let prompt1 = document.getElementById('promtTitel').value;
	let prompt2 = document.getElementById('promtMeta').value;
	let folder = document.getElementById('unterordner_select').value;
	let folderArray = folder.split(',');
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
			action: 'update_seocontent_template',
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
	let tab = document.getElementById('folderContainer' + n);

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
	let tab = document.getElementById('subFolderContainer' + n);

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
let first = true;
let startText = true;
function setLoadingScreen() {
	// Create a new div element
	if (first) {
		first = false;
		let overlayDiv = document.createElement('div');
		overlayDiv.id = 'overlay';
		overlayDiv.style.display = 'none';

		// Set the inner HTML of the new div
		overlayDiv.innerHTML = `
			<div class="lds-roller">
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<p style="position: absolute;top: 16px;left: 16px;">Loading</p>
				<span class="overlayBackground">
					<p id="loadingText" style="margin-bottom: 82px;">some Text</p>
				</span>
			</div>`;

		// Find the wp-wrap element and append the new div
		let wpWrap = document.getElementById('wpwrap');
		if (wpWrap) {
			wpWrap.appendChild(overlayDiv);
			loadingText = document.getElementById('loadingText');
		} else {
			console.error('Element with id "wpwrap" not found.');
		}
	}

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
	'Deine Website wird gerade mit maßgeschneiderten Meta-Daten ausgestattet.',
	'Unsere SEO-Zauberkünstler arbeiten daran, deine Meta-Daten zu optimieren.',
	'Die Kunst der Meta-Daten-Generierung erfordert ihre Zeit – wir sind gleich fertig!',
	'Optimierte Meta-Daten sind der Schlüssel zum Online-Erfolg – wir kümmern uns darum!',
	'Während wir hier arbeiten, entstehen Meta-Daten, die deine Website in den Suchergebnissen hervorheben werden.',
	'Wir formen Meta-Daten in effektive Werkzeuge für dein digitales Marketing um.',
	'Die Meta-Daten-Generierung ist im Gange – bald wird deine Website besser gefunden werden!',
	'Du kannst entspannt weiter diese Sprüche lesen, während wir deine Meta-Daten optimieren.',
	'Diese kurze Ladezeit ist nix im Vergleich zu dem, was die manuelle Meta-Daten-Generierung erfordert hätte.',
	'20 Meta-Daten auf einmal zu generieren schafft kein Mensch – zum Glück gibt es uns!',
	'Während die Datenstrategen tüfteln, werden deine Meta-Daten optimiert.',
	'Geduld zahlt sich aus – bald werden deine Meta-Daten für Aufmerksamkeit sorgen.',
	'Deine Website wird mit maßgeschneiderten Meta-Daten ausgestattet – der Schlüssel zu höheren Rankings!',
	'Während wir hier laden, verbessern sich deine Chancen, online gefunden zu werden.',
	'Gib uns diese kurze Zeit, um deine Website mit optimierten Meta-Daten auszustatten.',
	'Während wir hier arbeiten, werden deine Konkurrenten noch darüber nachdenken, wie sie ihre Meta-Daten optimieren können.',
];

async function startCycleText() {
	let loadingTextElement = document.getElementById('loadingText');
	loadingTextElement.innerHTML = 'Dieser Vorgang kann einige Minuten dauern. Bitte warten Sie einen Moment...';
	await new Promise((r) => setTimeout(r, 5000));
	while (!startText) {
		randInt = Math.floor(Math.random() * sprueche.length);
		loadingTextElement.innerHTML = sprueche[randInt];
		randTImeout = Math.floor(Math.random() * 1000) + 8000;
		await new Promise((r) => setTimeout(r, randTImeout));
	}
}

function showSettings() {
	document.getElementById('settingsOverlay').style.display = 'block';
	document.getElementById('overlaySettings').style.display = 'block';
	document.body.style.height = '200%';
}
function closeSettings() {
	document.getElementById('settingsOverlay').style.display = 'none';
	document.getElementById('overlaySettings').style.display = 'none';
}
function createFolderStructure() {
	let data = metaTemplateList;

	const container = document.getElementById('templateContainer');
	let htmlContent = '';
	let folderCount = 0;
	let subFolderCount = 0;

	let arrowUp =
		'<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><path d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z" /></svg>';
	let arrowDown =
		'<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><path d="M201.4 342.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 274.7 86.6 137.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z" /></svg>';
	let editPen =
		'<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/></svg>';
	let deletIcon =
		'<svg xmlns="http://www.w3.org/2000/svg" height="1.3em" viewBox="0 0 448 512"><path d="M170.5 51.6L151.5 80h145l-19-28.4c-1.5-2.2-4-3.6-6.7-3.6H177.1c-2.7 0-5.2 1.3-6.7 3.6zm147-26.6L354.2 80H368h48 8c13.3 0 24 10.7 24 24s-10.7 24-24 24h-8V432c0 44.2-35.8 80-80 80H112c-44.2 0-80-35.8-80-80V128H24c-13.3 0-24-10.7-24-24S10.7 80 24 80h8H80 93.8l36.7-55.1C140.9 9.4 158.4 0 177.1 0h93.7c18.7 0 36.2 9.4 46.6 24.9zM80 128V432c0 17.7 14.3 32 32 32H336c17.7 0 32-14.3 32-32V128H80zm80 64V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16z"/></svg>';

	Object.keys(data).forEach((folder) => {
		htmlContent += `<div class="folderTab">`;
		htmlContent += `<div class="folderHeaderFlex" onclick="showFolder(${folderCount})">`;
		htmlContent += `<span id="folderArrowUp${folderCount}" style="margin-right: 1rem;">` + arrowUp + `</span>`;
		htmlContent += `<span id="folderArrowDown${folderCount}" style="margin-right: 1rem; display: none;">` + arrowDown + `</span>`;
		htmlContent += `<h2 style='margin-top: 0px'>${folder}</h2>`;
		htmlContent += `<span class="editPen" onclick="editFolder('${folder}')">` + editPen + `</span>`;
		htmlContent += `<span onclick="delete_template_Folder('${folder}')">` + deletIcon + `</span>`;
		htmlContent += `</div>`;
		htmlContent += `<div id='folderContainer${folderCount}' class='folderContainer'>`;

		Object.keys(data[folder]).forEach((subFolder) => {
			htmlContent += `<div class='subFolderTab'>`;
			htmlContent += `<div class='folderHeaderFlex' onclick='showSubFolder(${subFolderCount})'>`;
			htmlContent += `<span id="subFolderArrowUp${subFolderCount}" style="margin-right: 1rem;">` + arrowUp + `</span>`;
			htmlContent +=
				`<span id="subFolderArrowDown${subFolderCount}" style="margin-right: 1rem; display: none;">` + arrowDown + `</span>`;
			htmlContent += `<h3 class='subFolderHeader'>${subFolder}</h3>`;
			htmlContent += `<span class="editPen" onclick="editFolder('${subFolder}')">` + editPen + `</span>`;
			htmlContent += `<span onclick="delete_template_subFolder('${folder},${subFolder}')">` + deletIcon + `</span>`;
			htmlContent += `</div>`;
			htmlContent += `<div id='subFolderContainer${subFolderCount}' class='subFolderContainer'>`;

			Object.keys(data[folder][subFolder]).forEach((template) => {
				htmlContent += `<div class="template_card" onclick="get_template('${folder}', '${subFolder}', '${template}')">`;
				htmlContent += `<div class="template_left">`;
				htmlContent += `<span title="${data[folder][subFolder][template][0]}" style="margin-right:6px">${template}</span>`;
				htmlContent += `<span class="editPen" onclick="editFolder('${template}')">` + editPen + `</span>`;
				htmlContent += `</div>`;
				htmlContent += `<span onclick="delete_template('${folder}', '${subFolder}', '${template}')">` + deletIcon + `</span>`;
				htmlContent += `</div>`;
			});

			htmlContent += `</div>`; // Close subFolderContainer
			htmlContent += `</div>`; // Close subFolderTab
			subFolderCount++;
		});

		htmlContent += `</div>`; // Close folderContainer
		htmlContent += `</div>`; // Close folderTab
		folderCount++;
	});

	container.innerHTML = htmlContent + container.innerHTML;
}

function setFolderOptions() {
	let folderOptions = document.getElementById('unterordner_select');
	let data = metaTemplateList;
	let htmlContent = '';

	for (const folder in data) {
		for (const subFolder in data[folder]) {
			htmlContent += `<option value="${folder},${subFolder}">${folder}: ${subFolder}</option>`;
		}
	}

	folderOptions.innerHTML = htmlContent;
}

document.addEventListener('DOMContentLoaded', async () => {
	await new Promise((r) => setTimeout(r, 1000));
	createFolderStructure();
	setFolderOptions();
});

function addMouseOverAndOutEventListeners(id) {
	document.getElementById(id).addEventListener('mouseover', function () {
		document.getElementById(id + 'Text').style.display = 'flex';
	});
	document.getElementById(id).addEventListener('mouseout', function () {
		document.getElementById(id + 'Text').style.display = 'none';
	});
}

function addMouseOverandOutInfoIconText(elementId) {
	document.getElementById(elementId).addEventListener('mouseover', function () {
		document.getElementById(elementId).style.display = 'flex';
	});
	document.getElementById(elementId).addEventListener('mouseout', function () {
		document.getElementById(elementId).style.display = 'none';
	});
}
addMouseOverAndOutEventListeners('infoIconUnternehmensinfo');

function openChangeKeyword(id, keyword) {
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
	overlayFormInput.value = keyword;

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
}
