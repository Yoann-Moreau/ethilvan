// ============================================================================
// Variables
// ============================================================================

const games = document.getElementsByClassName('game');
const gamesInfo = document.getElementsByClassName('single-game-challenges');


// ============================================================================
// Functions
// ============================================================================

function displayGameInfo(selectedGame) {
	for (const game of games) {
		game.classList.remove('current');
	}
	selectedGame.classList.add('current');

	for (const gameInfo of gamesInfo) {
		if (selectedGame.dataset.id === gameInfo.dataset.id) {
			gameInfo.classList.remove('hidden');
		}
		else {
			gameInfo.classList.add('hidden');
		}
	}
	window.scrollTo(0, 0);
}


// ============================================================================
// Code to execute
// ============================================================================


// ============================================================================
// Event listeners
// ============================================================================

for (const game of games) {
	game.addEventListener('click', () => {
		displayGameInfo(game);
	});
}
