import { addFilter } from '@wordpress/hooks';
import { BlockControls } from '@wordpress/block-editor';
import { ToolbarButton } from '@wordpress/components';
import { Toolbar, ToolbarDropdownMenu } from '@wordpress/components';
import { more, arrowLeft, arrowRight, arrowUp, arrowDown } from '@wordpress/icons';
import { select } from '@wordpress/data';
import { dispatch } from '@wordpress/data';
import Modal from 'react-modal';
import axios from 'axios';
const Download = (BlockEdit) => {
	return (props) => {
		if (props.name == 'core/table') {
			return <BlockEdit {...props} />;
		}
		console.log(props);
		const [modalIsOpen, setIsOpen] = React.useState(false);
		function openModal() {
			setIsOpen(true);
		}
		function afterOpenModal() {
			// references are now sync'd and can be accessed.
			subtitle.style.color = '#f00';
		}

		function closeModal() {
			setIsOpen(false);
		}

		const [modalIsOpen2, setIsOpen2] = React.useState(false);
		function openModal2() {
			setIsOpen2(true);
		}
		function afterOpenModal2() {
			// references are now sync'd and can be accessed.
			subtitle.style.color = '#f00';
		}

		function closeModal2() {
			setIsOpen2(false);
		}

		//function that gets the keyword and count variable from POST
		async function handleSubmit() {
			var keyword = document.getElementById('keyword').value;
			var count = document.getElementById('count').value;
			console.log(keyword + ' ' + count);
			closeModal();
			var test = await gpt_toolbar_keyword(props.attributes.content, props, keyword, count);
			// console.log(test + 'after await');
			// props.isSelected = false;
		}
		async function handleSubmit2() {
			var sprache = document.getElementById('sprache').value;

			closeModal2();
			var test = await gpt_language_keyword(props.attributes.content, props, sprache);
			// console.log(test + 'after await');
			// props.isSelected = false;
		}

		let subtitle;
		const customStyles = {
			content: {
				top: '50%',
				left: '50%',
				right: 'auto',
				bottom: 'auto',
				marginRight: '-50%',
				transform: 'translate(-50%, -50%)',
				fontSize: '1.2rem',
			},
			button: {
				marginTop: '1rem !important',
				marginRight: '1rem !important nmdButtonForm',
			},
		};
		Modal.setAppElement('body');
		return (
			<>
				<BlockControls>
					<Modal
						isOpen={modalIsOpen}
						onAfterOpen={afterOpenModal}
						onRequestClose={closeModal}
						style={customStyles}
						contentLabel="Keyword Optimierung"
					>
						<h2>Keyword Optimierung</h2>

						<form action="" method="post" id="seo_keyword_form">
							<label htmlFor="keyword">Keyword</label>
							<input name="keyword" id="keyword" type="text" />
							<label htmlFor="count">Anzahl im Text</label>
							<input type="number" name="count" id="count" />
						</form>
						<div id="seo_keyword_button_container">
							<button class="button button-primary nmdButtonForm" type="submit" onClick={handleSubmit}>
								{' '}
								Absenden
							</button>
							<button class="button button-primary nmdButtonForm" onClick={closeModal}>
								Schließen
							</button>
						</div>
					</Modal>
					<Modal
						isOpen={modalIsOpen2}
						onAfterOpen={afterOpenModal2}
						onRequestClose={closeModal2}
						style={customStyles}
						contentLabel="Übersetzen"
					>
						<h2>Übersetzen</h2>

						<form action="" method="post" id="seo_translate_form">
							<label htmlFor="sprache">Sprache:</label>
							<input name="sprache" id="sprache" type="text" />
						</form>
						<div id="seo_keyword_button_container">
							<button class="button button-primary nmdButtonForm" type="submit" onClick={handleSubmit2}>
								{' '}
								Absenden
							</button>
							<button class="button button-primary nmdButtonForm" onClick={closeModal2}>
								Schließen
							</button>
						</div>
					</Modal>
					<ToolbarDropdownMenu
						icon="seologo"
						label="Verbessere deinen Text"
						controls={[
							{
								title: 'Auf Keyword optimieren',

								onClick: () => {
									let content = props.attributes.content;
									openModal();
									// var test = await gpt_toolbar(content, 'correct', props);
									// console.log(test + 'after await');
									// props.isSelected = false;
								},
							},
							{
								title: 'Text korrigieren',

								onClick: async () => {
									let content = props.attributes.content;
									var test = await gpt_toolbar(content, 'correct', props);
									console.log(test + 'after await');
									props.isSelected = false;
								},
							},
							{
								title: 'Übersetzen',

								onClick: async () => {
									let content = props.attributes.content;
									openModal2();
								},
							},
							{
								title: 'Lesbarkeit verbessern',

								onClick: async () => {
									let content = props.attributes.content;
									var test = await gpt_toolbar(content, 'readability', props);
									console.log(test + 'after await');
									props.isSelected = false;
								},
							},
							{
								title: 'Umschreiben',

								onClick: async () => {
									let content = props.attributes.content;
									var test = await gpt_toolbar(content, 'rewrite', props);
									console.log(test + 'after await');
									props.isSelected = false;
								},
							},
							{
								title: 'Verlängern',

								onClick: async () => {
									let content = props.attributes.content;
									var test = await gpt_toolbar(content, 'longer', props);
									console.log(test + 'after await');
									props.isSelected = false;
								},
							},
							{
								title: 'Verkürzen',

								onClick: async () => {
									let content = props.attributes.content;
									var test = await gpt_toolbar(content, 'shorter', props);
									console.log(test + 'after await');
									props.isSelected = false;
								},
							},
						]}
					/>
				</BlockControls>
				<BlockEdit {...props} />
			</>
		);
	};
};
addFilter('editor.BlockEdit', 'Nmd_Form', Download);
let listItemClientIdsArray;
async function gpt_toolbar(content, selection, props) {
	await set_global_settings();

	if (selection == 'correct') {
		var prompt = promptList['toolbarCorrect'];
		prompt = prompt.replace('{content}', content);
		if (props.name == 'core/list') {
			let { listItems, listItemsClientIds } = testListItems(props.clientId);
			listItemClientIdsArray = listItemsClientIds;
			prompt = promptList['toolbarCorrectList'];
			prompt = prompt.replace('{content}', listItems);
		}
	}
	if (selection == 'readability') {
		var prompt = promptList['toolbarReadability'];
		prompt = prompt.replace('{content}', content);
		if (props.name == 'core/list') {
			let { listItems, listItemsClientIds } = testListItems(props.clientId);
			listItemClientIdsArray = listItemsClientIds;
			prompt = promptList['toolbarReadabilityList'];
			prompt = prompt.replace('{content}', listItems);
		}
	}
	if (selection == 'longer') {
		var prompt = promptList['toolbarLonger'];
		prompt = prompt.replace('{content}', content);
		if (props.name == 'core/list') {
			let { listItems, listItemsClientIds } = testListItems(props.clientId);
			listItemClientIdsArray = listItemsClientIds;
			prompt = promptList['toolbarLongerList'];
			prompt = prompt.replace('{content}', listItems);
		}
	}
	if (selection == 'shorter') {
		var prompt = promptList['toolbarShorter'];
		prompt = prompt.replace('{content}', content);
		if (props.name == 'core/list') {
			let { listItems, listItemsClientIds } = testListItems(props.clientId);
			listItemClientIdsArray = listItemsClientIds;
			prompt = promptList['toolbarShorterList'];
			prompt = prompt.replace('{content}', listItems);
		}
	}
	if (selection == 'rewrite') {
		var prompt = promptList['toolbarRewrite'];
		prompt = prompt.replace('{content}', content);
		if (props.name == 'core/list') {
			let { listItems, listItemsClientIds } = testListItems(props.clientId);
			listItemClientIdsArray = listItemsClientIds;
			prompt = promptList['toolbarRewriteList'];
			prompt = prompt.replace('{content}', listItems);
		}
	}
	if (selection == 'keyword') {
	}

	await gpt_make_request(content, props, prompt);
}

async function gpt_toolbar_keyword(content, props, keyword, count) {
	await set_global_settings();

	var prompt = promptList['toolbarKeyword'];
	prompt = prompt.replace('{content}', content);
	if (props.name == 'core/list') {
		let { listItems, listItemsClientIds } = testListItems(props.clientId);
		listItemClientIdsArray = listItemsClientIds;
		prompt = promptList['toolbarKeywordList'];
		prompt = prompt.replace('{content}', listItems);
	}
	prompt = prompt.replace('{keyword}', keyword);
	prompt = prompt.replace('{anzahl}', count);

	await gpt_make_request(content, props, prompt);
}
async function gpt_language_keyword(content, props, sprache) {
	await set_global_settings();

	var prompt = promptList['toolbarTranslate'];
	prompt = prompt.replace('{content}', content);
	if (props.name == 'core/list') {
		let { listItems, listItemsClientIds } = testListItems(props.clientId);
		listItemClientIdsArray = listItemsClientIds;
		prompt = promptList['toolbarTranslateList'];
		prompt = prompt.replace('{content}', listItems);
	}
	prompt = prompt.replace('{sprache}', sprache);

	await gpt_make_request(content, props, prompt);
}

let apiKey = '';
let href = window.location.href;
let index = href.indexOf('/wp-admin');
let homeUrl = href.substring(0, index);
var settingsArray = '';
let promptList = '';
let settingsSet = false;
async function set_global_settings() {
	console.log('settingsSet');
	const request2 = new XMLHttpRequest();
	var jsonUrl = homeUrl + '/wp-content/plugins/SEOContent/src/scripts/php/settings.json'; // Replace with the actual URL of your JSON file
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
	await new Promise((resolve) => setTimeout(resolve, 100));
	settingsSet = true;
}
async function test_content(content, props) {
	if (props.name == 'core/list') {
		document.getElementById('block-' + props.clientId).style.animation = '1.3s linear 0s infinite normal none running nmd-fading';
		return content;
	}
	if (content == undefined) {
		try {
			content = props.attributes.text;
			if (content == undefined) {
				alert('Es konnte kein Text gefunden werden. Bitte wählen Sie einen anderen Block aus oder wenden Sie sich an den Support');
				return;
			}
		} catch {
			alert('Es konnte kein Text gefunden werden. Bitte wählen Sie einen anderen Block aus oder wenden Sie sich an den Support');
			return;
		}
	}
	document.getElementById('block-' + props.clientId).style.animation = '1.3s linear 0s infinite normal none running nmd-fading';
	return content;
}

function testListItems(clientId) {
	const blockElement = document.querySelector(`[data-block="${clientId}"]`);

	const listItems = [];
	const listItemsClientIds = [];

	if (blockElement) {
		const listItemElements = blockElement.querySelectorAll('li[data-type="core/list-item"]');
		listItemElements.forEach((item) => {
			listItems.push(item.textContent.trim());

			// Extract clientId from the id attribute
			const itemId = item.getAttribute('id');
			if (itemId && itemId.startsWith('block-')) {
				listItemsClientIds.push(itemId.replace('block-', ''));
			}
		});
	}

	return { listItems, listItemsClientIds };
}
async function gpt_make_request(content, props, prompt) {
	content = test_content(content, props);

	const API_ENDPOINT = 'https://api.openai.com/v1/chat/completions';
	var chat = [
		{
			role: 'system',
			content: promptList['systemRoleZauberstab'],
		},
	];

	chat.push({ role: 'user', content: prompt });
	try {
		const response = await axios.post(
			API_ENDPOINT,
			{
				messages: chat,
				temperature: 0.6,
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
				var blockId = props.clientId;
				console.log('BLock	ID: ' + blockId);

				if (props.name == 'core/list') {
					let contentJsonArray = JSON.parse(content);
					console.log(contentJsonArray);
					for (let i = 0; i < contentJsonArray.length; i++) {
						select('core/block-editor').getBlock(listItemClientIdsArray[i]).attributes.content = contentJsonArray[i]
							.trim()
							.replace(/^"(.*)"$/, '$1');
						var updatedAttributes = select('core/block-editor').getBlock(listItemClientIdsArray[i]).attributes;
						dispatch('core/block-editor').updateBlock(listItemClientIdsArray[i], updatedAttributes);
					}
					document.getElementById('block-' + props.clientId).style.animation = '';
					return;
				}

				if (select('core/block-editor').getBlock(blockId).attributes.content != undefined) {
					select('core/block-editor').getBlock(blockId).attributes.content = content.trim().replace(/^"(.*)"$/, '$1');
				} else {
					select('core/block-editor').getBlock(blockId).attributes.text = content.trim().replace(/^"(.*)"$/, '$1');
				}

				var updatedAttributes = select('core/block-editor').getBlock(blockId).attributes;

				dispatch('core/block-editor').updateBlock(blockId, updatedAttributes);
				document.getElementById('block-' + props.clientId).style.animation = '';
				console.log(updatedAttributes);
				return 'test';
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

export default Download;
