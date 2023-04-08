// ============================================================================
// Variables
// ============================================================================

const games = document.getElementsByClassName('game');
const gamesInfo = document.getElementsByClassName('single-game-challenges');


// ============================================================================
// Functions
// ============================================================================

function displayGameInfo(game) {
	for (const gameInfo of gamesInfo) {
		if (game.dataset.id === gameInfo.dataset.id) {
			gameInfo.classList.remove('hidden');
		}
		else {
			gameInfo.classList.add('hidden');
		}
	}
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
