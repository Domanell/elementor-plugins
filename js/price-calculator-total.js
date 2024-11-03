(() => {
	const ACTION_ADD = 'add-item';
	const ACTION_REMOVE = 'remove-item';
	const ACTION_UPDATE = 'update-item';
	const currencySymbol = '$';
	let id;
	let items = [];
	let basePrice = 0;

	const getParentNode = () => {
		return document.querySelector(`[data-id="${id}"]`);
	};

	const getTotalPrice = () => {
		return items.reduce((total, item) => total + item.totalPrice, basePrice);
	};

	const destroy = () => {
		const listNode = getParentNode().querySelector('.calc-total__list');

		if (listNode) {
			listNode.remove();
		}
	};

	const rerender = () => {
		// Remove the existing list
		destroy();
		// Update the total price
		getParentNode().querySelector('.calc-total__price').textContent = `${currencySymbol}${getTotalPrice()}`;

		if (items.length === 0) {
			return;
		}

		// Create a new list
		const listNode = document.createElement('ul');
		listNode.classList.add('calc-total__list');

		items.forEach((item) => {
			const li = document.createElement('li');
			const name = document.createElement('span');
			const price = document.createElement('span');

			li.classList.add('calc-total-item');
			name.classList.add('calc-total-item__name');
			price.classList.add('calc-total-item__price');
			name.textContent = item.name;
			price.textContent = `${currencySymbol}${item.totalPrice}`;

			li.append(name, price);
			listNode.append(li);
		});

		// Append the new list
		getParentNode().querySelector('.calc-total__details').append(listNode);
	};

	const processMessage = (event) => {
		const { type, payload } = event.detail;
		switch (type) {
			case ACTION_ADD:
				items.push(payload);
				break;
			case ACTION_REMOVE:
				items = items.filter((item) => item.id !== payload.id);
				break;
			case ACTION_UPDATE:
				items[items.findIndex((item) => item.id === payload.id)] = payload;
				break;
			default:
				break;
		}

		rerender();
	};

	const subscribeToMessages = () => {
		window.addEventListener('updateTotalCalc', processMessage);
	};

	const init = ($element) => {
		const settings = { ...$element.data('settings') };
		id = $element.data('id');
		basePrice = settings.base_price || 0;

		subscribeToMessages();
	};

	window.addEventListener('elementor/frontend/init', () => {
		elementorFrontend.hooks.addAction('frontend/element_ready/price-calculator-total.default', init);
	});
})();
