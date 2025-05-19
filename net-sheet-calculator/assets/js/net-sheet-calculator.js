(function ($) {
	'use strict';

	// Utility functions
	const formatCurrency = (number) =>
		`$${(isNaN(number) ? 0 : number).toLocaleString('en-US', {
			minimumFractionDigits: 2,
			maximumFractionDigits: 2,
		})}`;

	const parseCurrency = (value) => parseFloat(String(value).replace(/[^0-9.-]+/g, '')) || 0;
	const parseInputValue = ($input) => ($input.hasClass('nsc-input--currency') ? parseCurrency($input.val()) : parseFloat($input.val()) || 0);

	// EmailHandler for validation and error handling
	const EmailHandler = {
		email: '',
		init($container) {
			this.$input = $container.find('input[name="email"]');
			this.$error = $container.find('.nsc-email-error');
			this.email = this.$input.val().trim();

			// Bind events
			this.$input
				.on('input', (e) => {
					// Update email property
					this.email = this.$input.val().trim();

					// Clear error on valid input if email is valid
					if (this.$input.hasClass('nsc-input--error') && this.validateValue().isValid) {
						this.$error.hide();
						this.$input.removeClass('nsc-input--error');
					}
				})
				.on('blur', (e) => {
					const { isValid, message } = this.validateValue();
					isValid ? this.hideError() : this.showError(message);
				});

			return this;
		},

		getEmail() {
			return this.email;
		},

		validateValue(emailToValidate = null) {
			// Use provided email or the email property
			const email = emailToValidate !== null ? emailToValidate : this.email;

			// Required check
			if (!email || email.trim() === '') {
				return { isValid: false, message: 'Email is required' };
			}

			// Format check
			const isValid =
				/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(
					email.toLowerCase()
				);

			return {
				isValid,
				message: isValid ? '' : 'Please enter a valid email address',
			};
		},

		validate() {
			const { isValid } = this.validateValue();

			if (!isValid) {
				this.showError(message);
			}

			return isValid;
		},

		showError(message) {
			this.$error.text(message).show();
		},

		hideError() {
			this.$error.hide();
			this.$input.removeClass('nsc-input--error');
		},
	};
	// Main calculator state object
	let $calculator,
		values = {},
		$inputs,
		$currencyInputs,
		$downloadBtn,
		$sendBtn;

	// Update calculated field with formatted value
	const updateCalculatedField = (field, value = 0) => {
		$calculator.find(`.nsc-input[data-field="${field}"]`).val(formatCurrency(value));
	};
	// Calculate all values and update fields
	const calculate = () => {
		// Perform all calculations
		const commission = values.purchase_price * ((values.comission_rate || 0) / 100);
		console.log(commission);
		const grossProceeds = values.purchase_price + (values.other_credits || 0);
		const michiganTransferTax = Math.ceil(values.purchase_price / 500) * (values.michigan_transfer_tax_rate || 3.75);
		const revenueStamps = Math.ceil(values.purchase_price / 500) * (values.revenue_stamps_rate || 0.55);

		const totalClosingCosts = [
			commission,
			michiganTransferTax,
			revenueStamps,
			values.mortgage_payoff,
			values.other_mortgage_payoff,
			values.special_assessment_payoff,
			values.lien_release_tracking_fee,
			values.property_taxes_due,
			values.settlement_fee,
			values.security_fee,
			values.title_insurance_policy,
			values.comission_realtor_extra,
			values.current_water,
			values.hoa_assessment,
			values.water_escrow,
			values.home_warranty,
			values.fha,
			values.misc_cost_seller,
			values.seller_attorney_fee,
		].reduce((sum, val) => sum + (val || 0), 0);

		const netProceeds = grossProceeds - totalClosingCosts < 0 ? 0 : grossProceeds - totalClosingCosts;

		// Update calculated fields
		updateCalculatedField('gross_proceeds', grossProceeds);
		updateCalculatedField('michigan_transfer_tax', michiganTransferTax);
		updateCalculatedField('revenue_stamps', revenueStamps);
		updateCalculatedField('comission_realtor', commission);

		// Update text output fields
		$calculator.find(`[data-field="total_closing_costs"]`).text(formatCurrency(totalClosingCosts));
		$calculator.find(`[data-field="estimated_net_proceeds"]`).text(formatCurrency(netProceeds));
	};

	const handleDownload = (e) => {
		e.preventDefault();
		alert('PDF download functionality will be implemented here.');
	};
	const handleSendEmail = (e) => {
		e.preventDefault();

		// Use the email handler to validate
		const validation = EmailHandler.validate();

		if (!validation.isValid) {
			return;
		}

		// Proceed with sending the email
		alert(`Email would be sent to: ${EmailHandler.email}`);
	};
	const initElements = () => {
		$inputs = $calculator.find('input');
		$currencyInputs = $calculator.find('.nsc-input--currency');
		$downloadBtn = $calculator.find('.nsc-button--download');
		$sendBtn = $calculator.find('.nsc-button--send');

		// Initialize email handler
		EmailHandler.init($calculator);
	};

	const initValues = () => {
		// Initialize all input values
		$inputs.each((index, element) => {
			const $input = $(element);
			const field = $input.data('field');

			if (field) {
				values[field] = parseInputValue($input);
			} else {
				console.error(`Input field "${$input.attr('name')}" is missing a data-field attribute.`);
			}
		});
	};
	const initEventHandlers = () => {
		// Handle all input changes
		$inputs.on('input change', (e) => {
			const $input = $(e.currentTarget);
			const field = $input.data('field');

			if (field) {
				// Update values object based on input type
				values[field] = parseInputValue($input);
				calculate();
			}
		});

		// Special handling for currency fields
		$currencyInputs
			.on('blur', (e) => {
				const $input = $(e.currentTarget);
				const field = $input.data('field');
				values[field] = parseCurrency($input.val());
				$input.val(formatCurrency(values[field]));
			})
			.on('focus', (e) => {
				const $input = $(e.currentTarget);
				$input.val(values[$input.data('field')] || '');
			});

		// Button handlers
		$downloadBtn.on('click', handleDownload);
		$sendBtn.on('click', handleSendEmail);
	};

	const init = (calculatorElement) => {
		$calculator = calculatorElement;
		initElements();
		initValues();
		initEventHandlers();
		calculate();
	};

	// Initialize calculator when Elementor is ready
	$(window).on('elementor/frontend/init', () => {
		elementorFrontend.hooks.addAction('frontend/element_ready/net_sheet_calculator_widget.default', ($calculator) => init($calculator));
	});
})(jQuery);
