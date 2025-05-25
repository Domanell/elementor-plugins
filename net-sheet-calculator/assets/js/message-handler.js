/**
 * Message Handler for Net Sheet Calculator
 * Handles success and error messages for a single action button
 */

class MessageHandler {
	constructor($element) {
		if (!$element || !$element.length) {
			throw new Error('MessageHandler: No valid element provided');
		}
		this.$element = $element;
	}

	showMessage(message, type) {
		this.$element.empty().append(`<span class="nsc-message nsc-message--${type}">${message}</span>`);
	}

	showSuccess(message) {
		this.showMessage(message, 'success');
	}

	showError(message) {
		this.showMessage(message, 'error');
	}

	hide() {
		this.$element.empty();
	}
}
