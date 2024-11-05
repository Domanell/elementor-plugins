(() => {
	const ACTION_ADD = 'add-item';
	const ACTION_REMOVE = 'remove-item';
	const ACTION_UPDATE = 'update-item';
	const currencySymbol = '$';

	const store = {};

	class PriceCalculatorItem {
		constructor(item) {
			this.id = item.id;
			this.name = item.name;
			this.price = item.price;
			this.isPricePerItemEnabled = item.enable_price_per_item === 'yes';
			this.pricePerItem = item.price_per_item || 0;
			this.min = item.min_quantity || 0;
			this.max = item.max_quantity || 1;
			this.includeQuantity = item.included_quantity || 0;
			this.quantity = item.min_quantity || 0;
			this.$switcher = item.$switcher;
			this.$rangeInput = item.$rangeInput;
			this.$numberInput = item.$numberInput;
			this.$priceText = item.$priceText;
			this.$itemPriceText = item.$itemPriceText;
			this.isEditMode = item.isEditMode;

			this.initializeEventHandlers();
		}

		get additionQuantity() {
			return this.quantity > this.includeQuantity ? this.quantity - this.includeQuantity : 0;
		}

		get totalPrice() {
			return this.price + (this.additionQuantity * this.pricePerItem || 0);
		}

		initializeEventHandlers() {
			if (!this.isEditMode) {
				this.$switcher.on('change', this.toggleItemSelection.bind(this));
				this.$rangeInput.on('input', this.handleInputChange.bind(this));
				this.$numberInput.on('input', this.handleInputChange.bind(this));
			}
		}

		dispatch(type) {
			const payload = { id: this.id, name: this.name, quantity: this.quantity, totalPrice: this.totalPrice };
			const event = new CustomEvent('updateTotalCalc', { detail: { type, payload } });
			window.dispatchEvent(event);
		}

		setQuantity(value) {
			this.quantity = value;
			this.updateInputValue(value);
			this.updatePriceText();
			this.dispatch(ACTION_UPDATE);
		}

		add() {
			this.setSwitcherState(true);
			this.setInputDisabledState(false);
			this.dispatch(ACTION_ADD);
		}

		remove() {
			this.setSwitcherState(false);
			this.setInputDisabledState(true);
			this.dispatch(ACTION_REMOVE);
		}

		toggleItemSelection(event) {
			const { checked } = event.target;

			if (checked) {
				this.add();
			} else {
				this.remove();
			}
		}

		handleInputChange(event) {
			const { value } = event.target;
			const parsedValue = parseInt(value, 10) || 0;
			const quantity = this.min > parsedValue ? this.min : this.max < parsedValue ? this.max : parsedValue;

			this.setQuantity(quantity);
		}

		setInputDisabledState(isDisabled) {
			this.$rangeInput.prop('disabled', isDisabled);
			this.$numberInput.prop('disabled', isDisabled);
		}

		setSwitcherState(isChecked) {
			this.$switcher.prop('checked', isChecked);
		}

		updateInputValue(value) {
			this.$rangeInput.val(value);
			this.$numberInput.val(value);
		}

		updatePriceText() {
			this.$priceText.text(`${currencySymbol}${this.totalPrice}`);
			this.$itemPriceText?.text(`${currencySymbol}${this.pricePerItem}`);
		}
	}

	const addWidgetDataToStore = ($element) => {
		// $element is a jQuery object that contains the widget element
		const $switcher = $element.find('.calc-item__checkbox');
		const $rangeInput = $element.find('.calc-item__range-input');
		const $numberInput = $element.find('.calc-item__number-input');
		const $priceText = $element.find('.calc-item__price p');
		const $itemPriceText = $element.find('.calc-item__item-price span');

		const elementData = {
			id: $element.data('id'),
			...$element.data('settings'),
			$switcher,
			$rangeInput,
			$numberInput,
			$priceText,
			$itemPriceText,
			isEditMode: elementorFrontend.isEditMode(),
		};

		store[elementData.id] = new PriceCalculatorItem(elementData);
	};

	window.addEventListener('elementor/frontend/init', () => {
		elementorFrontend.hooks.addAction('frontend/element_ready/price-calculator-item.default', addWidgetDataToStore);
	});
})();
