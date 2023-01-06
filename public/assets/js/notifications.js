import {ajax} from './tools/functions.js';

// ============================================================================
// Variables
// ============================================================================

const bells = document.querySelectorAll('.notification i');


// ============================================================================
// Functions
// ============================================================================

async function toggleNotification(bell) {
	const notification = bell.parentElement;
	const id = parseInt(notification.dataset.id);
	let seen = notification.classList.contains('seen');
	const pathname = new URL(window.location.href).pathname;
	const area = pathname.split('/')[1];

	const parameters = {
		id    : id,
		toggle: seen ? 'on' : 'off',
		area  : area,
	}

	if (seen) {
		notification.classList.remove('seen');
		bell.classList.remove('fa-bell-slash');
		bell.classList.add('fa-bell');
	}
	else {
		notification.classList.add('seen');
		bell.classList.remove('fa-bell');
		bell.classList.add('fa-bell-slash');
	}

	const response = await ajax('/ajax/toggle_notification', parameters);

	if (response !== 'ok') {
		console.log(response);
	}
}


// ============================================================================
// Code to execute
// ============================================================================


// ============================================================================
// Event listeners
// ============================================================================

for (const bell of bells) {
	bell.addEventListener('click', () => {
		toggleNotification(bell);
	});
}
