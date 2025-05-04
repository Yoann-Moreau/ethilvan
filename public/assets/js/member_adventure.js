// ============================================================================
// Variables
// ============================================================================

const openMapButton = document.querySelector('.icon.map-icon');
const mapMenu = document.querySelector('.map-menu');
const closeMapButton = document.querySelector('.map .close-button');
const mapImage = document.querySelector('.map');

const openTabletButton = document.querySelector(".icon.tablet-icon");
const tabletMenu = document.querySelector('.tablet-menu');
const tabletContainer = document.querySelector('.tablet-container');
const closeTabletButton = document.querySelector('.tablet .button-container');

const textTitles = document.querySelectorAll('.text-title');

const menus = document.querySelector('.menus');


// ============================================================================
// Functions
// ============================================================================

function displayMap() {
	hideTablet();
	hideMenus();
	mapImage.classList.remove('hidden');
}


function hideMap() {
	displayMenus();
	mapImage.classList.add('hidden');
}


function displayTablet() {
	hideMap();
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


function hideMenus() {
	menus.classList.add('hidden');
}


function displayMenus() {
	menus.classList.remove('hidden');
}


// ============================================================================
// Code to execute
// ============================================================================


// ============================================================================
// Event listeners
// ============================================================================

openMapButton.addEventListener('click', displayMap);
mapMenu.addEventListener('click', displayMap);
closeMapButton.addEventListener('click', hideMap);

openTabletButton.addEventListener('click', displayTablet);
tabletMenu.addEventListener('click', displayTablet);
closeTabletButton.addEventListener('click', hideTablet);

for (const textTitle of textTitles) {
	textTitle.addEventListener('click', () => {
		toggleText(textTitle);
	});
}
