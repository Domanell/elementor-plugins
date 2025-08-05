(function ($) {
	'use strict';

	let $calculator;
	let values = {};
	let labels = {};
	let loanPolicyRates = [];
	let $inputs;
	let $currencyInputs;
	let $percentageInputs;
	let $downloadBtn;
	let $sendBtn;
	let emailHandler; // Instance of EmailHandler
	let downloadMessageHandler; // Instance for download messages
	let emailMessageHandler; // Instance for email messages
	let companyInfo = {}; // Store company information

	const calculateHomeownersRate = (amount) => {
		let total = 0;

		for (const { min, max, rate, isFixed } of loanPolicyRates) {
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
		RECCUtils.updateCalculatedField($calculator, field, value);
	};

	const updateTextOutput = (dataSelector, value) => {
		RECCUtils.updateTextOutput($calculator, dataSelector, value);
	};

	// Calculate all values and update fields
	const calculate = () => {
		// Perform all calculations

		values.loan_insurance_policy = calculateHomeownersRate(values.purchase_price);

		values.total_closing_costs = [
			values.loan_amount,
			values.loan_insurance_policy,
			values.earnest_money_deposit,
			values.seller_credit,
			values.agent_credit,
			values.settlement_fee,
			values.security_fee,
			values.erecording_fee,
			values.mortgage_recording_fee, //based on county
			values.warranty_deed_recording_fee, //based on county
			values.tax_proration_estimate,
			values.realtor_compliance_fee,
			values.hoa_transfer_fee,
			values.hoa_prepaid_assessment,
			values.other_fees,
		].reduce((sum, val) => sum + (val || 0), 0);

		values.estimated_net_proceeds = values.total_closing_costs < 0 ? 0 : values.total_closing_costs;

		// Update input display for calculated fields (readonly fields)
		['loan_insurance_policy', 'mortgage_recording_fee', 'warranty_deed_recording_fee'].forEach((field) => {
			updateCalculatedField(field, values[field]);
		});

		// Update text output fields
		updateTextOutput('total_closing_costs', values.total_closing_costs);
		updateTextOutput('estimated_net_proceeds', values.estimated_net_proceeds);
	};

	const debounceCalculate = RECCUtils.debounce(calculate, 300);

	const handleDownload = async (e) => {
		e.preventDefault();

		// Create simple PDF data object with values and labels
		const pdfData = { values, labels, companyInfo };
		const $downloadBtn = $(e.currentTarget);

		// Disable button to prevent multiple clicks
		$downloadBtn.prop('disabled', true);

		try {
			if (typeof PDFGenerator === 'undefined') {
				console.error('PDF generator not available.');
			}

			// Generate PDF using PDFGenerator
			await PDFGenerator.downloadPDF(pdfData, 'buyer-cash-to-close-results.pdf');
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
		const pdfData = { values, labels, companyInfo };
		// Show loading state
		const $sendBtn = $(e.currentTarget);
		const originalBtnText = $sendBtn.text();
		$sendBtn.prop('disabled', true).text('Sending...');

		try {
			// Generate the PDF as base64
			const pdfBase64 = await PDFGenerator.getPDFAsBase64(pdfData);

			// Send the PDF to the server
			await $.ajax({
				url: reccEmailData.ajaxUrl,
				type: 'POST',
				data: {
					action: 'recc_send_email',
					nonce: reccEmailData.nonce,
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
		$inputs = $calculator.find('.recc-fields-wrap input');
		$currencyInputs = $calculator.find('.recc-input--currency');
		$percentageInputs = $calculator.find('.recc-input--percentage');
		$downloadBtn = $calculator.find('.recc-button--download');
		$sendBtn = $calculator.find('.recc-button--send');

		// Initialize message handlers for download and email
		downloadMessageHandler = new MessageHandler($calculator.find('#recc-download-action .recc-action__message'));
		emailMessageHandler = new MessageHandler($calculator.find('#recc-email-action .recc-action__message'));

		// Initialize email handler with email message handler
		emailHandler = new EmailHandler($calculator, emailMessageHandler);
	};

	const initValues = () => {
		// Initialize all input values
		$inputs.each((index, element) => {
			const $input = $(element);
			const field = $input.data('field');
			if (field) {
				values[field] = RECCUtils.parseInputValue($input);
			}
		});
	};

	const initEventHandlers = () => {
		$inputs.on('input change', (e) => {
			const $input = $(e.currentTarget);
			const field = $input.data('field');

			if (field) {
				const value = RECCUtils.parseInputValue($input);
				// Update values object based on input type
				values[field] = value;
				// Update input value to make sure that it is in min-max range
				// $input.val(value || '');
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
					$input.val(RECCUtils.formatCurrency(values[field]));
				}
			})
			.on('focus', (e) => {
				const $input = $(e.currentTarget);
				$input.val(RECCUtils.parseValue($input.val()) || '');
			});

		// Special handling for percentage fields
		$percentageInputs
			.on('blur', (e) => {
				const $input = $(e.currentTarget);
				const field = $input.data('field');
				// Format the value as percentage on blur
				if (field) {
					$input.val(RECCUtils.formatPercentage(values[field]));
				}
			})
			.on('focus', (e) => {
				const $input = $(e.currentTarget);
				$input.val(RECCUtils.parseValue($input.val()) || '');
			});

		// Button handlers
		$downloadBtn.on('click', handleDownload);
		$sendBtn.on('click', handleSendEmail);
	};

	const initLabels = (settings) => {
		labels = {
			purchase_price: settings.purchase_price_label,
			county: settings.county_label,
			loan_amount: settings.loan_amount_label,
			earnest_money_deposit: settings.earnest_money_deposit_label,
			seller_credit: settings.seller_credit_label,
			agent_credit: settings.agent_credit_label,
			loan_policy: settings.loan_insurance_policy_label,
			settlement_fee: settings.settlement_fee_label,
			security_fee: settings.security_fee_label,
			erecording_fee: settings.erecording_fee_label,
			mortgage_recording_fee: settings.mortgage_recording_fee_label,
			warranty_deed_recording_fee: settings.warranty_deed_recording_fee_label,
			tax_proration_estimate: settings.tax_proration_estimate_label,
			realtor_compliance_fee: settings.realtor_compliance_fee_label,
			hoa_transfer_fee: settings.hoa_transfer_fee_label,
			hoa_prepaid_assessment: settings.hoa_prepaid_assessment_label,
			other_fees: settings.other_fees_label,
			total_closing_costs: settings.total_closing_costs_label,
			estimated_net_proceeds: settings.estimated_net_proceeds_label,
		};
	};

	const initLoanPolicyRates = (settings) => {
		const rates = settings.loan_policy_rates || [];
		loanPolicyRates = rates.map(({ insurance_is_fixed, insurance_range_from, insurance_range_to, insurance_rate }) => ({
			isFixed: insurance_is_fixed === 'yes',
			min: insurance_range_from || 0,
			max: insurance_range_to || Infinity,
			rate: insurance_rate || 0,
		}));
	};

	const initCompanyInfo = (settings) => {
		companyInfo = {
			logo: reccEmailData?.siteLogo || null, // object with logo URL
			address1: settings.address_line1,
			address2: settings.address_line2,
			phone: settings.phone_number,
		};
	};

	const init = (calculatorElement) => {
		$calculator = calculatorElement;

		// Get settings directly from Elementor widget
		const settings = { ...$calculator.data('settings') };
		initLabels(settings);
		initLoanPolicyRates(settings);
		initElements();
		initValues();
		initEventHandlers();
		initCompanyInfo(settings);
		calculate();
	};

	// Initialize calculator when Elementor is ready
	$(window).on('elementor/frontend/init', () => {
		elementorFrontend.hooks.addAction('frontend/element_ready/buyer_cash_to_close_widget.default', ($calculator) => init($calculator));
	});
})(jQuery);
