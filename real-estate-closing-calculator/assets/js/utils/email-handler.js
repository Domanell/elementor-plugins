/**
 * Email Handler for Real Estate Closing Calculator
 * Handles email validation and UI interactions
 */
class EmailHandler {
	constructor($container, emailMessageHandler) {
		this.email = '';
		this.$input = null;
		this.$error = null;
		this.emailMessageHandler = emailMessageHandler;

		if ($container && $container.length) {
			this._initializeElements($container);
			this._bindEvents();
		} else {
			console.error('EmailHandler: No valid container provided');
		}
	}

	get isValid() {
		return this.validateValue().isValid;
	}

	_initializeElements($container) {
		this.$input = $container.find('input[name="email"]');
		// Find the error message element within the email action
		const $emailAction = $container.find('.recc-action').has('.recc-button--send');
		this.$error = $emailAction.find('.recc-action__message');

		if (this.$input.length) {
			this.email = this.$input.val().trim();
		} else {
			console.error('EmailHandler: Email input not found');
		}

		if (!this.$error.length) {
			console.error('EmailHandler: Error display element not found');
		}
	}

	_bindEvents() {
		this.$input
			.on('input', (event) => {
				this.email = event.target.value.trim();

				// Clear error on valid input
				if (this.$input.hasClass('recc-input--error') && this.isValid) {
					this.hideError();
				}
			})
			.on('blur', () => {
				this.validate();
			});
	}

	getEmail() {
		return this.email;
	}

	validateValue() {
		if (!this.email || this.email.trim() === '') {
			return { isValid: false, message: 'Email is required' };
		}

		// Format check using more robust regex
		const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		const isValid = emailRegex.test(this.email.toLowerCase());

		return {
			isValid,
			message: isValid ? '' : 'Please enter a valid email address',
		};
	}

	validate() {
		const { isValid, message } = this.validateValue();
		isValid ? this.hideError() : this.showError(message);

		return isValid;
	}

	showError(message) {
		this.emailMessageHandler.showError(message);
	}

	hideError() {
		this.emailMessageHandler.hide();
	}

	reset() {
		this.email = '';
		this.$input.val('');
	}
}
