
// ============================================================================
// Variables
// ============================================================================

const loader = document.getElementById('submit-loader');
const submitButton = document.getElementById('challenge-submit');
const form = document.getElementsByName('submission_message')[0];


// ============================================================================
// Functions
// ============================================================================

function hideSubmit() {
	submitButton.classList.add('hidden');
	loader.classList.remove('hidden');
}


// ============================================================================
// Code to execute
// ============================================================================


// ============================================================================
// Event listeners
// ============================================================================

form.addEventListener('submit', hideSubmit);
