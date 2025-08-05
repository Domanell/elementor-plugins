/**
 * Utility functions
 */
const RECCUtils = (function ($) {
	'use strict';

	const sanitizeNumber = (value) => {
		if (value === null || value === undefined || value === '') return 0;
		const num = parseFloat(String(value).replace(/[^0-9.-]+/g, ''));
		return isNaN(num) ? 0 : num;
	};

	return {
		// Number formatting
		formatCurrency: (number) => {
			const sanitized = sanitizeNumber(number);
			return `$${sanitized.toLocaleString('en-US', {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2,
			})}`;
		},

		formatPercentage: (number) => {
			const sanitized = sanitizeNumber(number);
			return `${sanitized.toLocaleString('en-US', {
				minimumFractionDigits: 1,
				maximumFractionDigits: 2,
			})}%`;
		},

		// Value parsing with input sanitization
		parseValue: (value) => sanitizeNumber(value),

		parseInputValue: ($input) => {
			const min = $input.attr('min');
			const max = $input.attr('max');
			const value = sanitizeNumber($input.val());

			if (min && value < sanitizeNumber(min)) {
				return sanitizeNumber(min);
			}
			if (max && value > sanitizeNumber(max)) {
				return sanitizeNumber(max);
			}

			return value;
		},

		// DOM helpers
		updateCalculatedField: ($calculator, field, value = 0) => {
			$calculator.find(`.recc-input[data-field="${field}"]`).val(RECCUtils.formatCurrency(value));
		},

		updateTextOutput: ($calculator, dataSelector, value) => {
			$calculator.find(`[data-field="${dataSelector}"]`).text(RECCUtils.formatCurrency(value));
		},

		// Debounce function for performance
		debounce: (func, wait, immediate) => {
			let timeout;
			return function executedFunction(...args) {
				const later = () => {
					timeout = null;
					if (!immediate) func.apply(this, args);
				};
				const callNow = immediate && !timeout;
				clearTimeout(timeout);
				timeout = setTimeout(later, wait);
				if (callNow) func.apply(this, args);
			};
		},
	};
})(jQuery);
