function saveSettings() {
	var apiKey = document.getElementById('apiKey').value;
	var firmenname = document.getElementById('firmenname').value;
	var adresse = document.getElementById('adresse').value;
	var gewerbe = document.getElementById('Gewerbe').value;
	var whyUs = document.getElementById('whyUs').value;
	var usps = document.getElementById('usps').value;
	var cta = document.getElementById('cta').value;
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
function getHomeUrl() {
	var href = window.location.href;
	var index = href.indexOf('/wp-admin');
	var homeUrl = href.substring(0, index);
	return homeUrl;
}
var homeUrl = getHomeUrl();

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
	});
}

function closePopup() {
	setCookie('popup', 'closed', 7);
	document.getElementById('topText').style.display = 'none';
}
getSettings();
