class RECalculator {
	$calculator;
	settings = {};
	values = {};
	$inputs;
	$selects;
	$currencyInputs;
	$percentageInputs;
	$downloadBtn;
	$sendBtn;
	emailHandler; // Instance of EmailHandler
	downloadMessageHandler; // Instance for download messages
	emailMessageHandler; // Instance for email messages
	emailTemplate = 'net-sheet-template'; // Default email template

	pdfConfig = {
		documentTitle: '',
		filename: '',
		sections: [],
	};

	constructor($calculator, pdfConfig) {
		// Validate required parameter
		if (!$calculator) {
			throw new Error('RECalculator: $calculator is required');
		}

		// Initialize core properties
		this.$calculator = $calculator;
		this.settings = $calculator.data('settings') || {};
		this.pdfConfig = pdfConfig;

		// Initialize the calculator
		this.init();

		// Create debounced calculate method after initialization
		this.debounceCalculate = RECCUtils.debounce(this.calculate.bind(this), 300);
	}

	get companyInfo() {
		return {
			logo: reccEmailData?.siteLogo || null, // object with logo URL
			address1: this.settings.address_line1,
			address2: this.settings.address_line2,
			phone: this.settings.phone_number,
		};
	}

	get pdfData() {
		return {
			companyInfo: this.companyInfo,
			documentTitle: this.pdfConfig.documentTitle,
			sections: this.configurePdfSections(),
			filename: this.pdfConfig.filename,
		};
	}

	// Calculate all values and update fields
	calculate() {
		console.error('RECalculator: calculate method should be overridden in subclasses');
	}

	calculateTieredRate(amount, rates) {
		let total = 0;

		for (const { min, max, rate, isFixed } of rates) {
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
	}

	configurePdfSections() {
		const sections = this.pdfConfig.sections.map((section) => {
			const fields = section.fields.map((field) => {
				const label = this.settings[field.label] || field.label;
				let value = this.values[field.name] || '';

				if (field.name === 'commission_realtor') {
					value = `${RECCUtils.formatCurrency(values.commission_realtor_amount)} (${RECCUtils.formatPercentage(value)})`;
				} else if (field.type === 'currency') {
					value = RECCUtils.formatCurrency(value);
				} else if (field.type === 'percentage') {
					value = RECCUtils.formatPercentage(value);
				}

				return { label, value };
			});
			return { ...section, fields };
		});

		return sections;
	}

	// Update calculated field with formatted value
	updateCalculatedField(field, value = 0) {
		RECCUtils.updateCalculatedField(this.$calculator, field, value);
	}

	updateTextOutput(dataSelector, value) {
		RECCUtils.updateTextOutput(this.$calculator, dataSelector, value);
	}

	async handleDownload(e) {
		e.preventDefault();

		const $downloadBtn = jQuery(e.currentTarget);

		// Disable button to prevent multiple clicks
		$downloadBtn.prop('disabled', true);

		try {
			if (typeof PDFGenerator === 'undefined') {
				console.error('PDF generator not available.');
			}

			// Generate PDF using PDFGenerator
			await PDFGenerator.downloadPDF(this.pdfData);
		} catch (error) {
			console.error(error);
			this.downloadMessageHandler.showError('PDF generation error. Please try again.');
		} finally {
			// Enable button after operation
			$downloadBtn.prop('disabled', false);
		}
	}

	async handleSendEmail(e) {
		e.preventDefault();

		const validation = this.emailHandler.validateValue();

		if (!validation.isValid) {
			this.emailHandler.showError(validation.message);
			return;
		}

		// Show loading state
		const $sendBtn = jQuery(e.currentTarget);
		const originalBtnText = $sendBtn.text();
		$sendBtn.prop('disabled', true).text('Sending...');

		try {
			// Generate the PDF as base64
			const pdfBase64 = await PDFGenerator.getPDFAsBase64(this.pdfData);

			// Send the PDF to the server
			await jQuery.ajax({
				url: reccEmailData.ajaxUrl,
				type: 'POST',
				data: {
					action: 'recc_send_email',
					nonce: reccEmailData.nonce,
					email: this.emailHandler.getEmail(),
					pdfBase64: pdfBase64,
					pdfData: this.pdfData,
					templateName: this.emailTemplate,
				},
				dataType: 'json',
				timeout: 30000, // 30 second timeout
				success: (response) => {
					if (response.success) {
						this.emailMessageHandler.showSuccess('Email has been sent');
						this.emailHandler.reset();
					} else {
						console.error(response.data);
						this.emailMessageHandler.showError('There was an error sending your email. Please try again.');
					}
				},
				error: (xhr, status, error) => {
					let errorMessage = 'There was an error sending your email. Please try again.';
					if (xhr.status === 0) {
						errorMessage = 'Network error. Please check your connection and try again.';
					} else if (xhr.status >= 500) {
						errorMessage = 'Server error. Please try again in a few moments.';
					}

					this.emailMessageHandler.showError(errorMessage);
					console.error('Email send error:', errorMessage);
				},
			});
		} catch (error) {
			console.error('Error generating PDF:', error);
			// Show error message if PDF generation fails
			this.emailMessageHandler.showError('PDF generation failed. Please refresh and try again.');
		} finally {
			// Reset button text after operation
			$sendBtn.prop('disabled', false).text(originalBtnText);
		}
	}

	//===============
	// Initialization
	//===============
	initElements() {
		this.$inputs = this.$calculator.find('.recc-fields-wrap input');
		this.$selects = this.$calculator.find('.recc-fields-wrap select');
		this.$currencyInputs = this.$calculator.find('.recc-input--currency');
		this.$percentageInputs = this.$calculator.find('.recc-input--percentage');
		this.$downloadBtn = this.$calculator.find('.recc-button--download');
		this.$sendBtn = this.$calculator.find('.recc-button--send');

		// Initialize message handlers for download and email
		this.downloadMessageHandler = new MessageHandler(this.$calculator.find('#recc-download-action .recc-action__message'));
		this.emailMessageHandler = new MessageHandler(this.$calculator.find('#recc-email-action .recc-action__message'));

		// Initialize email handler with email message handler
		this.emailHandler = new EmailHandler(this.$calculator, this.emailMessageHandler);
	}

	initValues() {
		// Initialize all input values
		this.$inputs.each((index, element) => {
			const $input = jQuery(element);
			const field = $input.data('field');
			if (field) {
				this.values[field] = RECCUtils.parseInputValue($input);
			}
		});
		// Initialize all select values
		this.$selects.each((index, element) => {
			const $select = jQuery(element);
			const field = $select.data('field');
			if (field) {
				this.values[field] = $select.val();
			}
		});
	}

	initEventHandlers() {
		this.$inputs.on('input change', (e) => {
			const $input = jQuery(e.currentTarget);
			const field = $input.data('field');

			if (field) {
				const value = RECCUtils.parseInputValue($input);
				// Update values object based on input type
				this.values[field] = value;
				// Update input value to make sure that it is in min-max range
				// $input.val(value || '');
				// Calculate with debounce
				this.debounceCalculate();
			}
		});

		// Handle select changes
		this.$selects.on('change', (e) => {
			const $select = jQuery(e.currentTarget);
			const field = $select.data('field');

			if (field) {
				// Get selected value and update values object
				const value = $select.val();
				this.values[field] = value;

				// Calculate with debounce
				this.debounceCalculate();
			}
		});

		// Special handling for currency fields
		this.$currencyInputs
			.on('blur', (e) => {
				const $input = jQuery(e.currentTarget);
				const field = $input.data('field');

				// Format the value as currency on blur
				if (field) {
					$input.val(RECCUtils.formatCurrency(this.values[field]));
				}
			})
			.on('focus', (e) => {
				const $input = jQuery(e.currentTarget);
				$input.val(RECCUtils.parseValue($input.val()) || '');
			});

		// Special handling for percentage fields
		this.$percentageInputs
			.on('blur', (e) => {
				const $input = jQuery(e.currentTarget);
				const field = $input.data('field');
				// Format the value as percentage on blur
				if (field) {
					$input.val(RECCUtils.formatPercentage(this.values[field]));
				}
			})
			.on('focus', (e) => {
				const $input = jQuery(e.currentTarget);
				$input.val(RECCUtils.parseValue($input.val()) || '');
			});

		// Button handlers - bind context properly
		this.$downloadBtn.on('click', this.handleDownload.bind(this));
		this.$sendBtn.on('click', this.handleSendEmail.bind(this));
	}

	init() {
		// Get settings directly from Elementor widget
		this.settings = this.$calculator.data('settings');
		this.initElements();
		this.initValues();
		this.initEventHandlers();
	}
}
