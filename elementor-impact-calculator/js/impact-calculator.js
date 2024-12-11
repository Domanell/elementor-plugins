(() => {
	const standardMultiplier = 15.33;
	const sustainableMultiplier = 3.51;
	let quantity;
	let $rangeInput;
	let $numberInput;
	let $standardValueText;
	let $sustainableValueText;
	let min;
	let max;
	let isEditMode;

	const initializeEventHandlers = () => {
		if (!isEditMode) {
			$rangeInput.on('input', handleInputChange);
			$numberInput.on('input', handleInputChange);
		}
	};

	const handleInputChange = (event) => {
		const { value } = event.target;
		const parsedValue = parseInt(value, 10) || 0;
		quantity = min > parsedValue ? min : max < parsedValue ? max : parsedValue;

		updateInputValues(value);
		updatePriceText(value);
	};

	const updateInputValues = (value) => {
		$rangeInput.val(value);
		$numberInput.val(value);
	};

	const updatePriceText = () => {
		$standardValueText.text(calculateImpactValue(standardMultiplier));
		$sustainableValueText?.text(calculateImpactValue(sustainableMultiplier));
	};

	const calculateImpactValue = (multiplier) => {
		const number = Math.round(quantity * multiplier);
		return new Intl.NumberFormat(_, {
			maximumFractionDigits: 0,
		}).format(number);
	};

	const init = ($element) => {
		const settings = $element.data('settings');
		// $element is a jQuery object that contains the widget element
		$rangeInput = $element.find('.calc__range-input');
		$numberInput = $element.find('.calc__number-input');
		$standardValueText = $element.find('.impact-value_standard');
		$sustainableValueText = $element.find('.impact-value_sustainable');
		min = settings.min_quantity || 0;
		max = settings.max_quantity || 10000;
		quantity = settings.default_quantity || 0;
		isEditMode = elementorFrontend.isEditMode();

		initializeEventHandlers();
		updatePriceText();
	};

	window.addEventListener('elementor/frontend/init', () => {
		elementorFrontend.hooks.addAction('frontend/element_ready/impact-calculator.default', init);
	});
})();
