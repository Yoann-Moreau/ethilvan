
// ============================================================================
// Variables
// ============================================================================

const loader = document.getElementById('submit-loader');
const submitButton = document.getElementById('copy-submit');
const form = document.getElementsByName('copy-challenges-form')[0];
const deleteForm = document.querySelector('.delete-challenges form');


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


function confirmDelete(e) {
	e.preventDefault();
	if (!confirm('Êtes-vous sur(e) de vouloir supprimer les défis pour cette période ?')) {
		return;
	}
	deleteForm.submit();
}


// ============================================================================
// Code to execute
// ============================================================================


// ============================================================================
// Event listeners
// ============================================================================

form.addEventListener('submit', hideSubmit);
deleteForm.addEventListener('submit', confirmDelete);
