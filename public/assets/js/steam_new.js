import {ajax} from './tools/functions.js';

// ============================================================================
// Variables
// ============================================================================

const appNameInput = document.getElementById('steam_game_name');
const appIdInput = document.getElementById('steam_game_app_id');


// ============================================================================
// Functions
// ============================================================================

function fetchSteamGameNames() {
	const parameters = {
		name: appNameInput.value,
	};

	ajax('/ajax/fetch_steam_game_names', parameters).then(data => {
		console.log(data);
	});
}


// ============================================================================
// Code to execute
// ============================================================================


// ============================================================================
// Event lisnteners
// ============================================================================

appNameInput.addEventListener('input', fetchSteamGameNames);
