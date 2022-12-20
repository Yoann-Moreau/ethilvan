import Autocomplete from './classes/Autocomplete.js';
import BetterMultiSelect from './classes/BetterMultiSelect.js';

// ============================================================================
// Variables
// ============================================================================

const autocompleteInputs = document.getElementsByClassName('autocomplete');
const multiSelects = document.querySelectorAll('select[multiple]');


// ============================================================================
// Functions
// ============================================================================


// ============================================================================
// Code to execute
// ============================================================================

for (const autocompleteInput of autocompleteInputs) {
	new Autocomplete(autocompleteInput);
}
for (const multiSelect of multiSelects) {
	new BetterMultiSelect(multiSelect);
}


// ============================================================================
// Event listeners
// ============================================================================
