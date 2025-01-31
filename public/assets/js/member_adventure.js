// ============================================================================
// Variables
// ============================================================================

const openTabletButton = document.querySelector(".icon.tablet");
const tabletMenu = document.querySelector('.tablet-menu');
const tabletContainer = document.querySelector('.tablet-container');
const closeTabletButton = document.querySelector('.tablet .button-container');


// ============================================================================
// Functions
// ============================================================================

function displayTablet() {
	tabletContainer.classList.remove('hidden');
}


function hideTablet() {
	tabletContainer.classList.add('hidden');
}


// ============================================================================
// Code to execute
// ============================================================================


// ============================================================================
// Event listeners
// ============================================================================

openTabletButton.addEventListener('click', displayTablet);
tabletMenu.addEventListener('click', displayTablet);
closeTabletButton.addEventListener('click', hideTablet);
