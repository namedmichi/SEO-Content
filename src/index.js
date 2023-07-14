import App from './app';
import { render } from '@wordpress/element';
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
import Download from '../src/components/toolbar_button';
import ai_block from '../src/components/ai_block';
/**
 * Import the stylesheet for the plugin.
 */
import './style/main.scss';

// Render the App component into the DOM
render(<App />, document.getElementById('jobplace'));
