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
		labels = {}, // Store field labels
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
		values.comission_realtor = values.purchase_price * ((values.comission_rate || 0) / 100);
		values.michigan_transfer_tax = Math.ceil(values.purchase_price / 500) * (values.michigan_transfer_tax_rate || 3.75);
		values.revenue_stamps = Math.ceil(values.purchase_price / 500) * (values.revenue_stamps_rate || 0.55);
		values.gross_proceeds = values.purchase_price + (values.other_credits || 0);

		const totalClosingCosts = [
			values.comission_realtor,
			values.michigan_transfer_tax,
			values.revenue_stamps,
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

		const netProceeds = values.gross_proceeds - totalClosingCosts < 0 ? 0 : values.gross_proceeds - totalClosingCosts;

		// Update input display for calculated fields
		['gross_proceeds', 'michigan_transfer_tax', 'revenue_stamps', 'comission_realtor'].forEach((field) => {
			updateCalculatedField(field, values[field]);
		});

		// Update text output fields
		$calculator.find(`[data-field="total_closing_costs"]`).text(formatCurrency(totalClosingCosts));
		$calculator.find(`[data-field="estimated_net_proceeds"]`).text(formatCurrency(netProceeds));
	};
	const handleDownload = (e) => {
		e.preventDefault();

		// Add calculated totals to values
		values.total_closing_costs = parseCurrency($calculator.find(`[data-field="total_closing_costs"]`).text());
		values.estimated_net_proceeds = parseCurrency($calculator.find(`[data-field="estimated_net_proceeds"]`).text());

		// Create simple PDF data object with values and labels
		const pdfData = { values, labels };
		// Generate PDF using PDFGenerator
		PDFGenerator.downloadPDF(pdfData, 'net-sheet-calculator-results.pdf')
			.then((success) => {
				if (!success) {
					alert('There was an error generating the PDF. Please try again.');
				}
			})
			.catch((error) => {
				console.error('PDF generation error:', error);
				alert('There was an error generating the PDF. Please try again.');
			});
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
		$inputs = $calculator.find('.nsc-fields-wrap input');
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

	const initLabels = (settings) => {
		labels = {
			purchase_price: settings.purchase_price_label,
			other_credits: settings.other_credits_label,
			gross_proceeds: settings.gross_proceeds_label,
			mortgage_payoff: settings.mortgage_payoff_label,
			other_mortgage_payoff: settings.other_mortgage_payoff_label,
			special_assessment_payoff: settings.special_assessment_payoff_label,
			lien_release_tracking_fee: settings.lien_release_tracking_fee_label,
			property_taxes_due: settings.property_taxes_due_label,
			michigan_transfer_tax: settings.michigan_transfer_tax_label,
			revenue_stamps: settings.revenue_stamps_label,
			settlement_fee: settings.settlement_fee_label,
			security_fee: settings.security_fee_label,
			title_insurance_policy: settings.title_insurance_policy_label,
			comission_realtor: settings.comission_realtor_label,
			comission_realtor_extra: settings.comission_realtor_extra_label,
			current_water: settings.current_water_label,
			hoa_assessment: settings.hoa_assessment_label,
			water_escrow: settings.water_escrow_label,
			home_warranty: settings.home_warranty_label,
			fha: settings.fha_label,
			misc_cost_seller: settings.misc_cost_seller_label,
			seller_attorney_fee: settings.seller_attorney_fee_label,
			total_closing_costs: settings.total_closing_costs_label,
			estimated_net_proceeds: settings.estimated_net_proceeds_label,
		};
	};

	const init = (calculatorElement) => {
		$calculator = calculatorElement;

		// Get settings directly from Elementor widget
		const settings = { ...$calculator.data('settings') };
		initLabels(settings);

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
