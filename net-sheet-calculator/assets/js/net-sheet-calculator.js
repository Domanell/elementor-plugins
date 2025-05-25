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
	let emailHandler; // Instance of EmailHandler
	let downloadMessageHandler; // Instance for download messages
	let emailMessageHandler; // Instance for email messages
	let siteLogo = null; // Store site logo information

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

	// Update calculated field with formatted value
	const updateCalculatedField = (field, value = 0) => {
		NSCUtils.updateCalculatedField($calculator, field, value);
	};

	const updateTextOutput = (dataSelector, value) => {
		NSCUtils.updateTextOutput($calculator, dataSelector, value);
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

	const debounceCalculate = NSCUtils.debounce(calculate, 300);

	const handleDownload = async (e) => {
		e.preventDefault();

		// Create simple PDF data object with values and labels
		const pdfData = { values, labels, siteLogo };
		const $downloadBtn = $(e.currentTarget);

		// Disable button to prevent multiple clicks
		$downloadBtn.prop('disabled', true);

		try {
			if (typeof PDFGenerator === 'undefined') {
				console.error('PDF generator not available.');
			}

			// Generate PDF using PDFGenerator
			await PDFGenerator.downloadPDF(pdfData, 'net-sheet-calculator-results.pdf');
		} catch (error) {
			downloadMessageHandler.showError('PDF generation error. Please try again.');
		} finally {
			// Enable button after operation
			$downloadBtn.prop('disabled', false);
		}
	};

	const handleSendEmail = async (e) => {
		e.preventDefault();

		const validation = emailHandler.validateValue();

		if (!validation.isValid) {
			emailHandler.showError(validation.message);
			return;
		}

		// Create PDF data object with values and labels
		const pdfData = { values, labels, siteLogo };
		// Show loading state
		const $sendBtn = $(e.currentTarget);
		const originalBtnText = $sendBtn.text();
		$sendBtn.prop('disabled', true).text('Sending...');

		try {
			// Generate the PDF as base64
			const pdfBase64 = await PDFGenerator.getPDFAsBase64(pdfData);

			// Send the PDF to the server
			await $.ajax({
				url: nscEmailData.ajaxUrl,
				type: 'POST',
				data: {
					action: 'nsc_send_email',
					nonce: nscEmailData.nonce,
					email: emailHandler.getEmail(),
					pdfBase64: pdfBase64,
					pdfData: pdfData,
				},
				dataType: 'json',
				timeout: 30000, // 30 second timeout
				success: (response) => {
					if (response.success) {
						emailMessageHandler.showSuccess('Email has been sent');
						emailHandler.reset();
					} else {
						emailMessageHandler.showError('There was an error sending your email. Please try again.');
					}
				},
				error: (xhr, status, error) => {
					let errorMessage = 'There was an error sending your email. Please try again.';
					if (xhr.status === 0) {
						errorMessage = 'Network error. Please check your connection and try again.';
					} else if (xhr.status >= 500) {
						errorMessage = 'Server error. Please try again in a few moments.';
					}

					emailMessageHandler.showError(errorMessage);
					console.error('Email send error:', errorMessage);
				},
			});
		} catch (error) {
			console.error('Error generating PDF:', error);
			// Show error message if PDF generation fails
			emailMessageHandler.showError('PDF generation failed. Please refresh and try again.');
		} finally {
			// Reset button text after operation
			$sendBtn.prop('disabled', false).text(originalBtnText);
		}
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

		// Initialize message handlers for download and email
		downloadMessageHandler = new MessageHandler($calculator.find('#nsc-download-action .nsc-action__message'));
		emailMessageHandler = new MessageHandler($calculator.find('#nsc-email-action .nsc-action__message'));

		// Initialize email handler with email message handler
		emailHandler = new EmailHandler($calculator, emailMessageHandler);
	};

	const initValues = () => {
		// Initialize all input values
		$inputs.each((index, element) => {
			const $input = $(element);
			const field = $input.data('field');
			if (field) {
				values[field] = NSCUtils.parseInputValue($input);
			}
		});
	};

	// !!!!!! test
	// const initFields = () => {
	// 	$inputs.each((index, element) => {
	// 		const $input = $(element);
	// 		const field = $input.data('field');
	// 		if (field) {
	// 			fields.set(field, {
	// 				value: parseInputValue($input),
	// 				isPercentage: $input.hasClass('nsc-input--percentage'),
	// 				isCurrency: $input.hasClass('nsc-input--currency'),
	// 				label: labels[field],
	// 				$input: $input,
	// 				setValue(value) {
	// 					this.value = value;
	// 				},
	// 				setInputValue(value) {
	// 					this.$input.val(value);
	// 				},
	// 				setFormattedValue(value) {
	// 					this.$input.val(this.isPercentage ? formatPercentage(value) : this.isCurrency ? formatCurrency(value) : value);
	// 				},
	// 			});
	// 		}
	// 	});
	// };

	const initEventHandlers = () => {
		$inputs.on('input change', (e) => {
			const $input = $(e.currentTarget);
			const field = $input.data('field');

			if (field) {
				const value = NSCUtils.parseInputValue($input);
				// Update values object based on input type
				values[field] = value;
				// Update input value to make sure that it is in min-max range
				$input.val(value || '');
				// Calculate with debounce
				debounceCalculate();
			}
		});

		// Special handling for currency fields
		$currencyInputs
			.on('blur', (e) => {
				const $input = $(e.currentTarget);
				const field = $input.data('field');

				// Format the value as currency on blur
				if (field) {
					$input.val(NSCUtils.formatCurrency(values[field]));
				}
			})
			.on('focus', (e) => {
				const $input = $(e.currentTarget);
				$input.val(NSCUtils.parseValue($input.val()) || '');
			});

		// Special handling for percentage fields
		$percentageInputs
			.on('blur', (e) => {
				const $input = $(e.currentTarget);
				const field = $input.data('field');
				// Format the value as percentage on blur
				if (field) {
					$input.val(NSCUtils.formatPercentage(values[field]));
				}
			})
			.on('focus', (e) => {
				const $input = $(e.currentTarget);
				$input.val(NSCUtils.parseValue($input.val()) || '');
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

	const initSiteLogo = () => {
		// Get logo data directly from the localized script data
		siteLogo = nscEmailData?.siteLogo || null;
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
		initSiteLogo(); // Initialize site logo from localized data
		calculate();
	};

	// Initialize calculator when Elementor is ready
	$(window).on('elementor/frontend/init', () => {
		elementorFrontend.hooks.addAction('frontend/element_ready/net_sheet_calculator_widget.default', ($calculator) => init($calculator));
	});
})(jQuery);
