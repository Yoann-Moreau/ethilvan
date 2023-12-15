
// ============================================================================
// Variables
// ============================================================================

const loader = document.getElementById('submit-loader');
const submitButton = document.getElementById('copy-submit');
const form = document.getElementsByName('copy-challenges-form')[0];


// ============================================================================
// Functions
// ============================================================================

function hideSubmit(e) {
	e.preventDefault();
	if (!confirm('Êtes-vous sûr(e) de vouloir copier les défis ?')) {
		return;
	}
	submitButton.classList.add('hidden');
	loader.classList.remove('hidden');
	form.submit();
}


// ============================================================================
// Code to execute
// ============================================================================


// ============================================================================
// Event listeners
// ============================================================================

form.addEventListener('submit', hideSubmit);
