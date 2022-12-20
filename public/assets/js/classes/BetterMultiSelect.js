/**
 * Better multiselect
 */
export default class BetterMultiSelect {

	constructor(element) {
		this.element = element;
		this.options = this.element.querySelectorAll('option');

		this.init();
	}

	init() {
		this.addOptionsContainer();
		this.addOptions();
		this.addSelectedsContainer();
		this.addSelecteds();
	}


	/**
	 * Add a selected-container after the multiselect
	 */
	addSelectedsContainer() {
		const selectedContainer = document.createElement('div');
		selectedContainer.classList.add('selecteds-container');
		selectedContainer.addEventListener('click', (e) => {
			this.toggleSelectableOptions(e);
		});
		this.element.after(selectedContainer);

		// set property for later use
		this.selectedContainer = selectedContainer;
	}

	/**
	 * Add selected options to selected-container
	 */
	addSelecteds() {
		for (const option of this.options) {
			if (option.selected === true) {
				const selected = document.createElement('div');
				selected.classList.add('selected');
				selected.dataset.value = option.value;
				selected.innerHTML = option.innerText;
				selected.addEventListener('click', (e) => {
					this.deselectOption(e.target.dataset.value);
					this.showOption(e.target.dataset.value);
					this.deleteFromDom(e.target);
				});
				this.selectedContainer.appendChild(selected);
			}
		}
	}


	/**
	 * Add a container for selectable options after the multiselect
	 */
	addOptionsContainer() {
		const optionsContainer = document.createElement('div');
		optionsContainer.classList.add('options-container', 'hidden');
		this.element.after(optionsContainer);

		// set property for later use
		this.optionsContainer = optionsContainer;
	}


	/**
	 * Add the selectable options to the container
	 */
	addOptions() {
		this.selectableOptions = [];
		for (const option of this.options) {
			const domOption = document.createElement('div');
			domOption.classList.add('option');
			if (option.selected === true) {
				domOption.classList.add('hidden');
			}
			domOption.innerHTML = option.innerText;
			domOption.dataset.value = option.value;
			domOption.addEventListener('click', (e) => {
				this.selectOption(e.target.dataset.value);
			})
			this.optionsContainer.appendChild(domOption);
			this.selectableOptions.push(domOption);
		}
	}


	/**
	 * Select the option corresponding to the value
	 * @param value Value of the option to select
	 */
	selectOption(value) {
		for (const option of this.options) {
			if (option.value === value) {
				option.selected = true;
				const selected = document.createElement('div');
				selected.classList.add('selected');
				selected.innerHTML = option.innerText;
				selected.dataset.value = option.value;
				selected.addEventListener('click', (e) => {
					this.deselectOption(e.target.dataset.value);
					this.showOption(e.target.dataset.value);
					this.deleteFromDom(e.target);
				})
				this.selectedContainer.appendChild(selected);
				this.hideOption(value);
				this.hideSelectableOptions();
			}
		}
	}


	/**
	 * Deselect option from the multiselect
	 * @param value The value of the option to deselect
	 */
	deselectOption(value) {
		for (const option of this.options) {
			if (option.value === value) {
				option.selected = false;
			}
		}
	}


	/**
	 * Hide the selectable option of the corresponding value
	 * @param value The dataset value of the selectable option to hide
	 */
	hideOption(value) {
		for (const option of this.selectableOptions) {
			if (option.dataset.value === value) {
				option.classList.add('hidden');
			}
		}
	}


	/**
	 * Show the selectable option of the corresponding value
	 * @param value The dataset value of the selectable option to show
	 */
	showOption(value) {
		for (const option of this.selectableOptions) {
			if (option.dataset.value === value) {
				option.classList.remove('hidden');
			}
		}
	}


	/**
	 * Toggle the visibility of the selectable options container
	 * @param e
	 */
	toggleSelectableOptions(e) {
		if (e.target.matches('.selecteds-container')) {
			this.element.parentNode.querySelector('.selecteds-container').classList.toggle('active');
			this.element.parentNode.querySelector('.options-container').classList.toggle('hidden');
		}
	}


	/**
	 * Hide the selectable options container
	 */
	hideSelectableOptions() {
		this.element.parentNode.querySelector('.selecteds-container').classList.remove('active');
		this.element.parentNode.querySelector('.options-container').classList.add('hidden');
	}


	/**
	 * Remove element from DOM
	 * @param element The DOM element to remove
	 */
	deleteFromDom(element) {
		element.remove();
	}
}
