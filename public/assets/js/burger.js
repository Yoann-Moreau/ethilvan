// ============================================================================
// Variables
// ============================================================================
const mainBurger = document.getElementById('main-burger');
const headerNav = document.getElementById('header-nav');


// ============================================================================
// Functions
// ============================================================================
function toggleBurgerMenu(mode) {
	if (mode === 'toggle') {
		mainBurger.classList.toggle('active');
		headerNav.classList.toggle('active');
	}
	else if (mode === 'close') {
		mainBurger.classList.remove('active');
		headerNav.classList.remove('active');
	}
}


// ============================================================================
// Code to execute
// ============================================================================


// ============================================================================
// Event listeners
// ============================================================================
mainBurger.addEventListener('click', () => {
	toggleBurgerMenu('toggle');
});
window.addEventListener('resize', () => {
	toggleBurgerMenu('close');
});
