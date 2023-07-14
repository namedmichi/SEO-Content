function ask_gpt() {
	var art = document.getElementById('nmd_form_dienstleistung').value;
	var firmenname = document.getElementById('nmd_form_firmenname').value;
	var jahr = document.getElementById('nmd_form_jahr').value;
	var service1 = document.getElementById('nmd_form_service1').value;
	var service2 = document.getElementById('nmd_form_service2').value;
	var service3 = document.getElementById('nmd_form_service3').value;
	var service4 = document.getElementById('nmd_form_service4').value;
	var service5 = document.getElementById('nmd_form_service5').value;
	var services = [service1, service2, service3, service4, service5];
	var ort = document.getElementById('nmd_form_ort').value;
	var stil = document.getElementById('nmd_form_stil').value;

	var result;
	const model = 'text-davinci-003';
	var prompt =
		'Schreibe in HTML code. Erstelle eine ' +
		stil +
		' H2-Überschrift zum Thema ' +
		art +
		' fÜr den Raum ' +
		ort +
		'. benutze die passenden Tags';
	//Nach der ersten Überschrift soll ein kurzer Einleitungssatz erscheinen, der den Betrieb " + firmenname + " vorstellt und auf das Gründungsjahr " + jahr + " eingeht.
	const url = 'https://api.openai.com/v1/engines/' + model + '/completions';

	fetch(url, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			Authorization: 'Bearer ' + apiKey,
		},
		body: JSON.stringify({
			prompt: prompt,
			max_tokens: 60,
		}),
	})
		.then((response) => response.json())
		.then((data) => (result = data.choices[0].text))
		.then((data) => console.log(result))
		.then((data) => {
			document.getElementById('gpt_result').innerHTML = result;
			if (jahr.length <= 1) {
				console.log('if');
				prompt = 'Schreibe ein kurzen ' + stil + ' Text , der den Betrieb ' + firmenname + ' vorstellt.';
			} else {
				console.log('else');
				prompt =
					'Schreibe ein kurzen ' +
					stil +
					' Text der den Betrieb ' +
					firmenname +
					' vorstellt und auf das Gründungsjahr ' +
					jahr +
					' eingeht.';
			}
			fetch(url, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					Authorization: 'Bearer ' + apiKey,
				},
				body: JSON.stringify({
					prompt: prompt,
					max_tokens: 90,
				}),
			})
				.then((response) => response.json())
				.then((data) => (result = data.choices[0].text))
				.then((data) => console.log(result))
				.then((data) => {
					document.getElementById('gpt_result_text').innerHTML = result;
					for (let index = 1; index <= service_count; index++) {
						prompt =
							'Schreibe eine ' +
							stil +
							' H2 Überschrift für einen Text über ' +
							services[index - 1] +
							' für eine Firma. Benutze den <h2></h2> tag';
						fetch(url, {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
								Authorization: 'Bearer ' + apiKey,
							},
							body: JSON.stringify({
								prompt: prompt,
								max_tokens: 60,
							}),
						})
							.then((response) => response.json())
							.then((data) => (result = data.choices[0].text))
							.then((data) => console.log(result))
							.then((data) => {
								document.getElementById('gpt_service' + index + '_header').innerHTML = result;
							})
							.catch((error) => console.error(error));
						prompt =
							'Benutze <p></p> tags. Schreibe einen ' +
							stil +
							' gewerblichen Text über ' +
							services[index - 1] +
							' für eine Firma im Ort' +
							ort +
							'. Es müssen genau 2 Absätze sein mit exact 2 Sätzen pro Ansatz. ';
						fetch(url, {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
								Authorization: 'Bearer ' + apiKey,
							},
							body: JSON.stringify({
								prompt: prompt,
								max_tokens: 225,
							}),
						})
							.then((response) => response.json())
							.then((data) => (result = data.choices[0].text))
							.then((data) => console.log(result))
							.then((data) => {
								document.getElementById('gpt_service' + index + '_text').innerHTML = result;
							})
							.catch((error) =>
								alert(
									'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dan erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
								)
							);
					}
				})
				.catch((error) =>
					alert(
						'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dan erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
					)
				);
		})
		.catch((error) =>
			alert(
				'Es ist ein Fehler aufgetreten. Bitte warten Sie ein paar Sekunden und versuchen es dan erneut. Bei weiteren Probleme kontaktieren Sie bitte den Support'
			)
		);
}
