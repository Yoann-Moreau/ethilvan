// ============================================================================
// Variables
// ============================================================================

const openTabletButton = document.querySelector(".icon.tablet");
const tabletMenu = document.querySelector('.tablet-menu');
const tabletContainer = document.querySelector('.tablet-container');
const closeTabletButton = document.querySelector('.tablet .button-container');

const textTitles = document.querySelectorAll('.text-title');


// ============================================================================
// Functions
// ============================================================================

function displayTablet() {
	tabletContainer.classList.remove('hidden');
}


function hideTablet() {
	tabletContainer.classList.add('hidden');
}


function toggleText(title) {
	const textContainer = title.closest('.text');
	const text = textContainer.querySelector('.text-text');
	title.classList.toggle('closed');
	text.classList.toggle('hidden');
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

for (const textTitle of textTitles) {
	textTitle.addEventListener('click', () => {
		toggleText(textTitle);
	});
}
