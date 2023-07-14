function ask_gpt_content_page() {
	setValues();

	var result;

	var prompt = document.getElementById('title_prompt').innerHTML;
	prompt = prompt.replace('{topic}', topic);
	prompt = prompt.replace('{stil}', stil);
	prompt = prompt.replace('{ton}', ton);
	console.log(prompt);
	fetch(url, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			Authorization: 'Bearer ' + apiKey,
		},
		body: JSON.stringify({
			prompt: prompt,
			max_tokens: 50,
			temperature: 0.7,
		}),
	})
		.then((response) => response.json())
		.then((data) => (result = data.choices[0].text.split('\n').slice(1).slice(1).join('\n')))
		.then((data) => console.log(result))
		.then((data) => {
			document.getElementById('nmd_title_input').innerHTML = result;
			document.getElementById('nmd_title_input').value = result;
		})
		.then((data) => {
			ask_gpt_content_page_title();
		})
		.catch((error) =>
			alert(
				'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dan erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
			)
		);
}

function ask_gpt_content_page_title() {
	setValues();
	console.log(abschnitte);

	var prompt = document.getElementById('abschnitte_prompt').innerHTML;
	prompt = prompt.replace('{title}', title);
	prompt = prompt.replace('{stil}', stil);
	prompt = prompt.replace('{ton}', ton);
	prompt = prompt.replace('{abschnitte}', abschnitte);

	fetch(url, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			Authorization: 'Bearer ' + apiKey,
		},
		body: JSON.stringify({
			prompt: prompt,
			max_tokens: 30 * abschnitte,
			temperature: 0.7,
		}),
	})
		.then((response) => response.json())
		.then((data) => (result = data.choices[0].text.split('\n').slice(1).slice(1).join('\n')))
		.then((data) => console.log(result))
		.then((data) => {
			document.getElementById('nmd_abschnitte_input').innerHTML = result;
			document.getElementById('nmd_abschnitte_input').value = result;
		})
		.then((data) => {
			ask_gpt_content_page_ueberschriften();
		})
		.catch((error) =>
			alert(
				error +
					'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dan erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
			)
		);
}

function ask_gpt_content_page_ueberschriften() {
	setValues();
	console.log(inhaltCount);

	var words = document.getElementById('nmd_words_count').value;
	var tokens = words * 3;
	var prompt = document.getElementById('inhalt_prompt').innerHTML;
	prompt = prompt.replace('{title}', title);
	prompt = prompt.replace('{stil}', stil);
	prompt = prompt.replace('{ton}', ton);
	prompt = prompt.replace('{ueberschriften}', ueberschriften);
	prompt = prompt.replace('{ueberschriftenAnzahl}', inhaltCount);
	prompt = prompt.replace('{woerter}', words);
	var keywordInputs = document.getElementsByName('keyword');
	keywordString = '';
	for (var i = 0; i < keywordInputs.length; i++) {
		var keyword = keywordInputs[i].value;

		if (keyword) {
			keywordString += 'Keyword' + i + '. :' + keyword + ',';
		}
	}
	prompt = prompt.replace('{keywords}', keywordString);

	console.log(prompt);
	console.log('Tokens: ' + tokens * abschnitte * inhaltCount);
	fetch(url, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			Authorization: 'Bearer ' + apiKey,
		},
		body: JSON.stringify({
			prompt: prompt,
			max_tokens: tokens * abschnitte * inhaltCount,
			temperature: 0.2,
		}),
	})
		.then((response) => response.json())
		.then((data) => (result = data.choices[0].text))
		.then((data) => console.log(result))
		.then((data) => {
			document.getElementById('nmd_inhalt_input').innerHTML = result;
			document.getElementById('nmd_inhalt_input').value = result;
		})
		.then((data) => {
			ask_gpt_content_page_excerp();
		})
		.catch((error) =>
			alert(
				'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dan erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
			)
		);
}

function ask_gpt_content_page_excerp() {
	setValues();
	console.log(inhaltCount);

	var prompt = document.getElementById('excerp_prompt').innerHTML;
	prompt = prompt.replace('{title}', title);
	prompt = prompt.replace('{stil}', stil);
	prompt = prompt.replace('{ton}', ton);

	fetch(url, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			Authorization: 'Bearer ' + apiKey,
		},
		body: JSON.stringify({
			prompt: prompt,
			max_tokens: 100,
			temperature: 0.7,
		}),
	})
		.then((response) => response.json())
		.then((data) => (result = data.choices[0].text.split('\n').slice(1).slice(1).join('\n')))
		.then((data) => console.log(result))
		.then((data) => {
			document.getElementById('nmd_excerp_input').innerHTML = result;
			document.getElementById('nmd_excerp_input').value = result;
			document.getElementById('overlay').style.display = 'none';
			document.body.classList.remove('blurred');
			document.body.classList.remove('no-scroll');
			document.getElementsByTagName('html')[0].style.paddingTop = '32px';
		})
		.catch((error) => {
			alert(
				'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dan erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
			);
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
	ton = document.getElementById('nmd_ton_select').value;
	stil = document.getElementById('nmd_stil_select').value;
	topic = document.getElementById('nmd_topic_input').value;
	title = document.getElementById('nmd_title_input').value;
	abschnitte = document.getElementById('nmd_abschnitte_select').value;
	ueberschriften = document.getElementById('nmd_abschnitte_input').value;
	inhaltCount = document.getElementById('nmd_inhalt_select').value;
}
