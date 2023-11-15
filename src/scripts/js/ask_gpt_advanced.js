//Generiere 5 „[Suchintention]“ Keywords zum Thema „[Thema]“ für den Raum „[Ort]“.
function ask_gpt_advanced() {
	var thema = document.getElementById('nmd_form_adv_thema').value;
	var ort = document.getElementById('nmd_form_adv_ort').value;
	var intention = document.getElementById('nmd_form_adv_intention').value;

	console.log(ort);
	var result;
	const model = 'text-davinci-003';
	var prompt =
		' Generiere 5 ' +
		intention +
		' Keywords zum Thema  ' +
		thema +
		' fÜr den Raum ' +
		ort +
		' füge nach jedem Keyword ein <br /> tag ein und nummerier jedes Keyword';

	const url = 'https://api.openai.com/v1/engines/' + model + '/completions';

	fetch(url, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			Authorization: 'Bearer ' + apiKey,
		},
		body: JSON.stringify({
			prompt: prompt,
			max_tokens: 70,
		}),
	})
		.then((response) => response.json())
		.then((data) => (result = data.choices[0].text))
		.then((data) => console.log(result))
		.then((data) => {
			document.getElementById('gpt_adv_keywords').innerHTML = result;
		})
		.catch((error) =>
			alert(
				'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dann erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
			)
		);
}
