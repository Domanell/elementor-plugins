(() => {
	const ACTION_ADD = 'add-item';
	const ACTION_REMOVE = 'remove-item';
	const ACTION_UPDATE = 'update-item';
	const DISCOUNT_ID = 'discount';
	const BASE_PRICE_ID = 'basePrice';
	const currencyCode = 'USD';
	let id;
	let items = [
		{ id: BASE_PRICE_ID, name: 'Base price', totalPrice: 0 },
		{ id: DISCOUNT_ID, name: 'Discount', totalPrice: 0 },
	];

	const getParentNode = () => {
		return document.querySelector(`[data-id="${id}"]`);
	};

	const getTotalPrice = () => {
		return items.reduce((total, item) => total + item.totalPrice, 0);
	};

	function formatCurrency(amount, locale = 'en-US') {
		return new Intl.NumberFormat(locale, { style: 'currency', currency: currencyCode, minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(amount);
	}

	const rerender = () => {
		const parent = getParentNode();
		const listContainerNode = parent.querySelector('.calc-total__details');
		const priceNode = parent.querySelector('.calc-total__price');

		// Update the total price
		priceNode.textContent = formatCurrency(getTotalPrice());

		// Clear and rebuild the list
		listContainerNode.innerHTML = '';
		if (items.length > 0) {
			// Create a new list
			const listNode = document.createElement('ul');
			listNode.classList.add('calc-total__list');

			items.forEach((item) => {
				if (item.id === DISCOUNT_ID && item.totalPrice === 0) return;

				const li = document.createElement('li');
				li.classList.add('calc-total-item');

				const name = document.createElement('span');
				name.classList.add('calc-total-item__name');
				name.textContent = item.name;

				const price = document.createElement('span');
				price.classList.add('calc-total-item__price');
				price.textContent = formatCurrency(item.totalPrice);

				li.append(name, price);
				listNode.append(li);
			});

			listContainerNode.append(listNode);
		}
	};

	const processMessage = (event) => {
		const { type, payload } = event.detail;

		switch (type) {
			case ACTION_ADD:
				items.push(payload);
				// Move the discount item to the end of the list
				items = items.filter((item) => item.id !== DISCOUNT_ID).concat(items.find((item) => item.id === DISCOUNT_ID) || []);
				break;
			case ACTION_REMOVE:
				items = items.filter((item) => item.id !== payload.id);
				break;
			case ACTION_UPDATE:
				updateItem(payload, true);
				return; // Skip rerender
		}

		rerender();
	};

	const updateItem = (updatedItem) => {
		const targetItem = items.find((item) => item.id === updatedItem.id);

		if (targetItem) {
			Object.assign(targetItem, updatedItem);
		}

		rerender();
	};

	const subscribeToMessages = () => {
		const parent = getParentNode();
		window.addEventListener('updateTotalCalc', processMessage);

		if (parent.querySelector('.calc-total__info')?.dataset.loggedIn === 'true') {
			const discountForm = parent.querySelector('.calc-total__discount-form');
			const discountFormTrigger = parent.querySelector('.calc-total__info');

			discountForm?.addEventListener('submit', handleSubmitDiscountForm);
			discountFormTrigger?.addEventListener('click', handleToggleDiscountFormVisibility);
		}
	};

	const handleSubmitDiscountForm = (event) => {
		event.preventDefault();
		const discountAmount = new FormData(event.target).get('discount-amount') || 0;
		updateItem({ id: DISCOUNT_ID, totalPrice: -discountAmount });
		handleToggleDiscountFormVisibility();
	};

	const handleToggleDiscountFormVisibility = () => {
		getParentNode().querySelector('.calc-total__discount-form')?.classList.toggle('hidden');
	};

	const init = ($element) => {
		const settings = { ...$element.data('settings') };
		id = $element.data('id');

		// Set the base price
		updateItem({ id: BASE_PRICE_ID, totalPrice: settings.base_price || 0 });
		subscribeToMessages();
	};

	window.addEventListener('elementor/frontend/init', () => {
		elementorFrontend.hooks.addAction('frontend/element_ready/price-calculator-total.default', init);
	});
})();
