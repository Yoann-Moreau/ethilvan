// ============================================================================
// Variables
// ============================================================================

const openShipButton = document.querySelector('.icon.ship-icon');
const shipMenu = document.querySelector('.ship-menu');
const closeShipButton = document.querySelector('.ship .close-button');
const toggleShipButton = document.querySelector('.toggle-ship');
const shipContainer = document.querySelector('.ship');
const shipInside = document.querySelector('.ship-inside');
const shipOutside = document.querySelector('.ship-outside');

const openMapButton = document.querySelector('.icon.map-icon');
const mapMenu = document.querySelector('.map-menu');
const closeMapButton = document.querySelector('.map .close-button');
const mapImage = document.querySelector('.map');

const openTabletButton = document.querySelector(".icon.tablet-icon");
const tabletMenu = document.querySelector('.tablet-menu');
const tabletContainer = document.querySelector('.tablet-container');
const tabletButton = document.querySelector('.tablet .button-container');
const closeTabletButton = document.querySelector('.tablet-container .close-button');

const textTitles = document.querySelectorAll('.text-title');

const menus = document.querySelector('.menus');


// ============================================================================
// Functions
// ============================================================================

function displayShip() {
	hideTablet();
	hideMap();
	hideMenus();
	shipContainer.classList.remove('hidden');
}


function hideShip() {
	displayMenus();
	shipContainer.classList.add('hidden');
}


function toggleShip() {
	shipInside.classList.toggle('hidden');
	shipOutside.classList.toggle('hidden');
}


function displayMap() {
	hideTablet();
	hideShip();
	hideMenus();
	mapImage.classList.remove('hidden');
}


function hideMap() {
	displayMenus();
	mapImage.classList.add('hidden');
}


function displayTablet() {
	hideMap();
	hideShip();
	hideMenus();
	tabletContainer.classList.remove('hidden');
}


function hideTablet() {
	displayMenus();
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
// Code to execute on load
// ============================================================================


// ============================================================================
// Event listeners
// ============================================================================

openShipButton.addEventListener('click', displayShip);
shipMenu.addEventListener('click', displayShip);
toggleShipButton.addEventListener('click', toggleShip);
closeShipButton.addEventListener('click', hideShip);

openMapButton.addEventListener('click', displayMap);
mapMenu.addEventListener('click', displayMap);
closeMapButton.addEventListener('click', hideMap);

openTabletButton.addEventListener('click', displayTablet);
tabletMenu.addEventListener('click', displayTablet);
tabletButton.addEventListener('click', hideTablet);
closeTabletButton.addEventListener('click', hideTablet);

for (const textTitle of textTitles) {
	textTitle.addEventListener('click', () => {
		toggleText(textTitle);
	});
}
