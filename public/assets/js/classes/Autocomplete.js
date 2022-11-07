/**
 * Creates an autocomplete for the provided element based on the data provided in the choicesReceived event
 */
export default class Autocomplete {

	constructor(element) {
		this.element = element;
		this.parentElement = element.parentElement;

		this.hoveredChoice = -1;

		this.init();
	}


	/**
	 * Initiates the input and add the event listeners
	 */
	init() {
		this.createInputHtml();
		this.addUpdateChoicesListener();
		this.addControlsListener();
	}


	/**
	 * Replaces the HTML of the input by a container with the input and another container inside
	 */
	createInputHtml() {
		const inputHtml = this.element.outerHTML;

		this.element.outerHTML = `
			<div class="autocomplete-container">
				${inputHtml}
				<div class="choices-container"></div>
			</div>
		`;

		this.element = this.parentElement.querySelector('.autocomplete');
		this.container = this.element.parentElement;
		this.choicesContainer = this.container.querySelector('.choices-container');
	}


	/**
	 * Append a child element to the choices container for each choice
	 * @param choices An array of objects where name is the value of the choice
	 */
	addSuggestions(choices) {
		for (const choice of choices) {
			let domChoice = document.createElement('div');
			domChoice.classList.add('choice');

			// Add dataset for each entry in the object
			for (const [key, value] of Object.entries(choice)) {
				domChoice.dataset[key] = String(value);
			}

			domChoice.innerHTML = choice.name;

			this.choicesContainer.appendChild(domChoice);
		}
	}


	/**
	 * Clear the content of the choices container
	 */
	clearSuggestions() {
		this.choicesContainer.innerHTML = '';
	}


	/**
	 * Add event listener for choicesReceived
	 */
	addUpdateChoicesListener() {
		this.element.addEventListener('choicesReceived', (e) => {
			this.clearSuggestions();
			if (this.element.value !== '') {
				this.addSuggestions(e.detail.choices);
			}
		});
	}


	/**
	 * Increment hoveredChoice if possible
	 */
	incrementHoveredChoice() {
		const choices = this.choicesContainer.getElementsByClassName('choice');

		if (this.hoveredChoice + 1 < choices.length) {
			this.hoveredChoice++;
		}
		else if (choices.length > 0) {
			this.hoveredChoice = 0;
		}
	}


	/**
	 * Decrement hoveredChoice if possible
	 */
	decrementHoveredChoice() {
		const choices = this.choicesContainer.getElementsByClassName('choice');

		if (this.hoveredChoice - 1 >= 0) {
			this.hoveredChoice--;
		}
		else if (choices.length > 0) {
			this.hoveredChoice = choices.length - 1;
		}
	}


	/**
	 * Add the class 'hovered' to the currently hovered choice
	 */
	displayHoveredChoice() {
		const choices = this.choicesContainer.getElementsByClassName('choice');

		for (const choice of choices) {
			choice.classList.remove('hovered');
		}
		if (this.hoveredChoice > -1) {
			choices[this.hoveredChoice].classList.add('hovered');
		}
	}


	/**
	 * Update the input value accrding to the selected choice
	 */
	updateInputValue() {
		const choices = this.choicesContainer.getElementsByClassName('choice');

		if (this.hoveredChoice > -1) {
			this.element.value = choices[this.hoveredChoice].dataset.name;
			this.element.dispatchEvent(new Event('input'));
		}
	}


	/**
	 * Add controls
	 */
	addControlsListener() {
		this.element.addEventListener('keydown', (e) => {
			if (e.key === 'ArrowDown') {
				e.preventDefault();
				this.incrementHoveredChoice();
				this.displayHoveredChoice();
			}
			else if (e.key === 'ArrowUp') {
				e.preventDefault();
				this.decrementHoveredChoice();
				this.displayHoveredChoice();
			}
			else if (e.key === 'Enter') {
				e.preventDefault();
				this.updateInputValue();
				this.hoveredChoice = -1;
				this.displayHoveredChoice();
			}
			else if (e.location === 0 || e.location === 3) { // letter, space or number
				this.hoveredChoice = -1;
				this.displayHoveredChoice();
			}
		});
	}

}
