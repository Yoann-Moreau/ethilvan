import {ajax} from './tools/functions.js';

// ============================================================================
// Variables
// ============================================================================

const maxToggles = 3;

const toggles = document.getElementsByClassName('toggle');


// ============================================================================
// Functions
// ============================================================================

async function toggleToggle(element) {
	const activeToggles = document.querySelectorAll('.toggle.active');

	if (!element.classList.contains('active') && activeToggles.length >= maxToggles) {
		alert('Seuls ' + maxToggles + ' jeux peuvent être jeux du moment.')
		return;
	}

	const parameters = {
		id: parseInt(element.dataset.id),
	}

	if (element.classList.contains('active')) {
		parameters.mode = 'toggle-off';
	}
	else {
		parameters.mode = 'toggle-on';
	}

	const response = await ajax('/ajax/toggle_current_game', parameters);

	if (response === 'ok') {
		element.classList.toggle('active');
	}
	else {
		console.log(response);
	}
}


// ============================================================================
// Code to execute
// ============================================================================


// ============================================================================
// Event listeners
// ============================================================================
for (const toggle of toggles) {
	toggle.addEventListener('click', () => {
		toggleToggle(toggle).then();
	})
}
