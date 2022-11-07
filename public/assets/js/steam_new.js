import {ajax} from './tools/functions.js';

// ============================================================================
// Variables
// ============================================================================

const appNameInput = document.getElementById('steam_game_name');
const appIdInput = document.getElementById('steam_game_app_id');


// ============================================================================
// Functions
// ============================================================================

/**
 * Fetch steam game names from backend and dispatch 'autocomplete-choices-received' event on success
 */
function fetchSteamGameNames() {
	appIdInput.value = 0;

	const parameters = {
		name: appNameInput.value,
	};

	ajax('/ajax/fetch_steam_game_names', parameters).then(data => {
		if (typeof data === 'object') {
			appNameInput.dispatchEvent(new CustomEvent('autocomplete-choices-received', {
				detail: {
					choices: data,
				},
			}));
		}
		else {
			console.log('Error fetching steam game names');
		}
	});
}


/**
 * Update app id input value
 */
function updateAppId(e) {
	appIdInput.value = e.detail.choice.dataset.app_id;
}

// ============================================================================
// Code to execute
// ============================================================================


// ============================================================================
// Event lisnteners
// ============================================================================

appNameInput.addEventListener('input', fetchSteamGameNames);
appNameInput.addEventListener('autocomplete-choice-made', updateAppId);
