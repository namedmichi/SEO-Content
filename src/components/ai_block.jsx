const { registerBlockType } = wp.blocks;
const { RichText, InspectorControls, BlockControls, AlignmentToolbar, useBlockProps } = wp.blockEditor;
const { ToggleControl, PanelBody, PanelRow, CheckboxControl, SelectControl, ColorPicker } = wp.components;
const model = 'text-davinci-003';

function getHomeUrl() {
	var href = window.location.href;
	var index = href.indexOf('/wp-admin');
	var homeUrl = href.substring(0, index);
	return homeUrl;
}
var homeUrl = getHomeUrl();
const API_ENDPOINT = 'https://api.openai.com/v1/chat/completions';
import { __experimentalInputControl as InputControl } from '@wordpress/components';
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
registerBlockType('namedmichi/seocontentblock', {
	title: 'SEOContent KI Block',
	category: 'common',
	icon: 'seologo',
	description: 'Learning in progress',
	keywords: ['example', 'test'],
	attributes: {
		myRichHeading: {
			type: 'string',
		},
		myRichText: {
			type: 'string',
			source: 'html',
			selector: 'p',
		},
		textAlignment: {
			type: 'string',
		},
	},
	supports: {
		align: ['wide', 'full'],
	},
	edit: (props) => {
		const { attributes, setAttributes } = props;

		const alignmentClass = attributes.textAlignment != null ? 'has-text-align-' + attributes.textAlignment : '';

		return (
			<div {...useBlockProps()}>
				<BlockControls></BlockControls>
				<InputControl
					label="Thema eingeben"
					id="nmd_thema_input"
					labelPosition="top"
					value=""
					type="string"
					isPressEnterToChange
					onChange={(nextValue) => {
						gpt_call();
					}}
				/>
				<div id="nmdBlockLoaderDiv">
					<div class="lds-roller">
						<div></div>
						<div></div>
						<div></div>
						<div></div>
						<div></div>
						<div></div>
						<div></div>
						<div></div>
					</div>
				</div>
			</div>
		);
		async function gpt_call() {
			var promptList;

			var href = window.location.href;
			var index = href.indexOf('/wp-admin');
			var homeUrl = href.substring(0, index);

			const request = new XMLHttpRequest();
			const jsonUrl = homeUrl + '/wp-content/plugins/SEOContent/src/scripts/php/prompts.json'; // Replace with the actual URL of your JSON file

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
			document.getElementById('nmdBlockLoaderDiv').style.display = 'block';
			document.getElementById('nmd_thema_input').style.display = 'none';
			let premium = false;
			let tokens;

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
					},
					error: function (error) {
						console.log(error);
					},
				});
			});

			await new Promise((resolve) => setTimeout(resolve, 2000));
			var result;
			var thema = document.getElementById('nmd_thema_input').value;
			console.log('function run');
			var prompt = promptList['aiBlockPrompt'];
			prompt = prompt.replace('{thema}', thema);
			let systemprompt = promptList['systemRoleAiBlock'];
			let chat = [
				{
					role: 'system',
					content: systemprompt,
				},
			];

			chat.push({ role: 'user', content: prompt });
			if (!premium) {
				fetch(API_ENDPOINT, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						Authorization: 'Bearer ' + apiKey,
					},
					body: JSON.stringify({
						messages: chat,
						temperature: 0.2,
						model: 'gpt-4-1106-preview',
					}),
				})
					.then((response) => response.json())
					.then((data) => (result = data.choices[0]['message']['content'].split('\n').slice(1).slice(1).join('\n')))
					.then((data) => console.log(result))
					.then((data) => {
						let block = wp.blocks.createBlock('core/paragraph', { content: result });
						wp.data.dispatch('core/editor').insertBlocks(block);
						document.getElementById('nmdBlockLoaderDiv').style.display = 'none';
						wp.data.dispatch('core/editor').removeBlock(props.clientId);
					});
			} else {
				new Promise((resolve, reject) => {
					jQuery.ajax({
						url: myAjax.ajaxurl,
						method: 'POST',
						data: {
							action: 'ask_gpt',
							chat: chat,
							temperature: 0.2,
							model: 'gpt-4-1106-preview',
						},
						success: async function (response) {
							let finished = false;
							let task_id = response.split('"')[3];
							task_id = task_id.split('"')[0];
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

											let result = response;
											let block = wp.blocks.createBlock('core/paragraph', { content: result });
											wp.data.dispatch('core/editor').insertBlocks(block);
											document.getElementById('nmdBlockLoaderDiv').style.display = 'none';
											wp.data.dispatch('core/editor').removeBlock(props.clientId);

											resolve();
										} else {
											console.log("Task still processing. Let's wait 1 seconds and try again.");
											console.log(response);
										}
									},
									error: function (error) {
										console.log(error);
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
		}
	},
	save: (props) => {
		const { attributes } = props;

		const alignmentClass = attributes.textAlignment != null ? 'has-text-align-' + attributes.textAlignment : '';

		return (
			<div className={alignmentClass}>
				<RichText.Content tagName="h2" value={attributes.myRichHeading} />
				<RichText.Content tagName="p" value={attributes.myRichText} />
				{attributes.activateLasers && <div className="lasers">Lasers activated</div>}
			</div>
		);
	},
});
