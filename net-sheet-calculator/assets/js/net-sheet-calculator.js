(function ($) {
	'use strict';

	let $calculator;
	let values = {};
	let labels = {};
	let insuranceRates = [];
	let $inputs;
	let $currencyInputs;
	let $percentageInputs;
	let $downloadBtn;
	let $sendBtn;

	//==========
	// Utilities
	//==========
	const formatCurrency = (number) =>
		`$${(isNaN(number) ? 0 : number).toLocaleString('en-US', {
			minimumFractionDigits: 2,
			maximumFractionDigits: 2,
		})}`;

	const formatPercentage = (number) =>
		`${(isNaN(number) ? 0 : number).toLocaleString('en-US', {
			minimumFractionDigits: 1,
			maximumFractionDigits: 2,
		})}%`;

	const parseValue = (value) => parseFloat(String(value).replace(/[^0-9.-]+/g, '')) || 0;

	const parseInputValue = ($input) => {
		// check if input has min and max attributes
		const min = $input.attr('min');
		const max = $input.attr('max');
		const value = parseValue($input.val());

		// check if value is less than min or greater than max
		if (min && value < parseValue(min)) {
			return parseValue(min);
		}
		if (max && value > parseValue(max)) {
			return parseValue(max);
		}

		return parseValue($input.val());
	};

	const calculateHomeownersRate = (amount) => {
		let total = 0;

		for (const { min, max, rate, isFixed } of insuranceRates) {
			if (amount >= min) {
				if (isFixed) {
					total += rate;
				} else {
					const upperBound = Math.min(amount, max);
					const thousands = Math.ceil((upperBound - min) / 1000);
					total += thousands * rate;
				}
			}
		}

		return Math.ceil(total);
	};

	//=========
	// Handlers
	//=========
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

	// Update calculated field with formatted value
	const updateCalculatedField = (field, value = 0) => {
		$calculator.find(`.nsc-input[data-field="${field}"]`).val(formatCurrency(value));
	};

	const updateTextOutput = (dataSelector, value) => {
		$calculator.find(`[data-field="${dataSelector}"]`).text(formatCurrency(value));
	};

	// Calculate all values and update fields
	const calculate = () => {
		// Perform all calculations
		values.commission_realtor_amount = values.purchase_price * ((values.commission_realtor || 0) / 100);
		values.michigan_transfer_tax = Math.ceil(values.purchase_price / 500) * (values.michigan_transfer_tax_rate || 3.75);
		values.revenue_stamps = Math.ceil(values.purchase_price / 500) * (values.revenue_stamps_rate || 0.55);
		values.gross_proceeds = values.purchase_price + (values.other_credits || 0);
		values.homeowners_rate = calculateHomeownersRate(values.title_insurance_policy);

		values.total_closing_costs = [
			values.commission_realtor_amount,
			values.michigan_transfer_tax,
			values.revenue_stamps,
			values.mortgage_payoff,
			values.other_mortgage_payoff,
			values.special_assessment_payoff,
			values.lien_release_tracking_fee,
			values.property_taxes_due,
			values.settlement_fee,
			values.security_fee,
			values.homeowners_rate,
			values.commission_realtor_extra,
			values.current_water,
			values.hoa_assessment,
			values.water_escrow,
			values.home_warranty,
			values.fha,
			values.misc_cost_seller,
			values.seller_attorney_fee,
		].reduce((sum, val) => sum + (val || 0), 0);

		values.estimated_net_proceeds = values.gross_proceeds - values.total_closing_costs < 0 ? 0 : values.gross_proceeds - values.total_closing_costs;

		// Update input display for calculated fields
		['gross_proceeds', 'michigan_transfer_tax', 'revenue_stamps'].forEach((field) => {
			updateCalculatedField(field, values[field]);
		});

		// Update text output fields
		updateTextOutput('total_closing_costs', values.total_closing_costs);
		updateTextOutput('estimated_net_proceeds', values.estimated_net_proceeds);
	};

	const handleDownload = (e) => {
		e.preventDefault();

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

	//===============
	// Initialization
	//===============
	const initElements = () => {
		$inputs = $calculator.find('.nsc-fields-wrap input');
		$currencyInputs = $calculator.find('.nsc-input--currency');
		$percentageInputs = $calculator.find('.nsc-input--percentage');
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
				const value = parseInputValue($input);
				// Update values object based on input type
				values[field] = value;
				// Update input value to make sure that it is in min-max range
				$input.val(value || '');
				// Update calculated fields
				calculate();
			}
		});

		// Special handling for currency fields
		$currencyInputs
			.on('blur', (e) => {
				const $input = $(e.currentTarget);
				const field = $input.data('field');
				values[field] = parseValue($input.val());
				$input.val(formatCurrency(values[field]));
			})
			.on('focus', (e) => {
				const $input = $(e.currentTarget);
				$input.val(parseValue($input.val()) || '');
			});

		// Special handling for percentage fields
		$percentageInputs
			.on('blur', (e) => {
				const $input = $(e.currentTarget);
				const field = $input.data('field');
				values[field] = parseValue($input.val());
				$input.val(formatPercentage(values[field]));
			})
			.on('focus', (e) => {
				const $input = $(e.currentTarget);
				$input.val(parseValue($input.val()) || '');
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
			commission_realtor: settings.commission_realtor_label,
			commission_realtor_extra: settings.commission_realtor_extra_label,
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

	const initInsuranceRates = (settings) => {
		const rates = settings.insurance_rates || [];
		insuranceRates = rates.map(({ insurance_is_fixed, insurance_range_from, insurance_range_to, insurance_rate }) => ({
			isFixed: insurance_is_fixed === 'yes',
			min: insurance_range_from || 0,
			max: insurance_range_to || Infinity,
			rate: insurance_rate || 0,
		}));
	};

	const init = (calculatorElement) => {
		$calculator = calculatorElement;

		// Get settings directly from Elementor widget
		const settings = { ...$calculator.data('settings') };

		initLabels(settings);
		initInsuranceRates(settings);
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
