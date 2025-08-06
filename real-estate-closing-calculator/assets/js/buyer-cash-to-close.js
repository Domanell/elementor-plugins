(function ($) {
	'use strict';
	class BCCalculator extends RECalculator {
		constructor($calculator, pdfConfig, emailTemplate) {
			// Call parent constructor with only the required element
			super($calculator, pdfConfig);

			// Initialize child-specific properties
			this.emailTemplate = emailTemplate;
			this.loanPolicyRates = this._defineLoanPolicyRates();
		}

		_defineLoanPolicyRates() {
			const rates = this.settings.loan_policy_rates || [];
			return rates.map(({ insurance_is_fixed, insurance_range_from, insurance_range_to, insurance_rate }) => ({
				isFixed: insurance_is_fixed === 'yes',
				min: insurance_range_from || 0,
				max: insurance_range_to || Infinity,
				rate: insurance_rate || 0,
			}));
		}

		calculate() {
			// Perform all calculations
			this.values.loan_insurance_policy = this.calculateTieredRate(this.values.purchase_price, this.loanPolicyRates);
			const [countyName, mortgageFee, warrantyFee] = (this.values.county_rates || '').split('_');
			this.values.county = countyName || '';
			this.values.mortgage_recording_fee = parseFloat(mortgageFee) || 0;
			this.values.warranty_deed_recording_fee = parseFloat(warrantyFee) || 0;

			// Calculate total closing costs using the formula:
			// ( Purchase Price − Loan Amount ) + (Loan Policy + Settlement Fee + Security Fee + eRecording Fee + Mortgage Recording Fee + Warranty Deed Recording Fee + Tax Proration Estimate + Realtor Compliance Fee + HOA Transfer Fee + HOA Prepaid Assessment + Other ) − (Earnest Money Deposit + Seller Credit + Agent Credit + Transaction Credits)

			const downPayment = (this.values.purchase_price || 0) - (this.values.loan_amount || 0);

			const additionalCosts = [
				this.values.loan_insurance_policy,
				this.values.settlement_fee,
				this.values.security_fee,
				this.values.erecording_fee,
				this.values.mortgage_recording_fee,
				this.values.warranty_deed_recording_fee,
				this.values.tax_proration_estimate,
				this.values.realtor_compliance_fee,
				this.values.hoa_transfer_fee,
				this.values.hoa_prepaid_assessment,
				this.values.other_fees,
			].reduce((sum, val) => sum + (val || 0), 0);

			const credits = [this.values.earnest_money_deposit, this.values.seller_credit, this.values.agent_credit].reduce((sum, val) => sum + (val || 0), 0);

			this.values.total_closing_costs = downPayment + additionalCosts - credits;

			this.values.estimated_net_proceeds = this.values.total_closing_costs < 0 ? 0 : this.values.total_closing_costs;

			// Update input display for calculated fields (readonly fields)
			['loan_insurance_policy', 'mortgage_recording_fee', 'warranty_deed_recording_fee'].forEach((field) => {
				this.updateCalculatedField(field, this.values[field]);
			});

			// Update text output fields
			this.updateTextOutput('total_closing_costs', this.values.total_closing_costs);
			this.updateTextOutput('estimated_net_proceeds', this.values.estimated_net_proceeds);
		}
	}

	const pdfConfig = {
		documentTitle: 'Buyer Cash to Close Calculator Results',
		filename: 'buyer_cash_to_close_results.pdf',
		sections: [
			{
				title: 'Transaction Summary',
				fields: [
					{ name: 'purchase_price', label: 'purchase_price_label', type: 'currency' },
					{ name: 'county', label: 'county_label', type: 'text' },
				],
			},
			{
				title: 'Transaction Credits',
				fields: [
					{ name: 'loan_amount', label: 'loan_amount_label', type: 'currency' },
					{ name: 'earnest_money_deposit', label: 'earnest_money_deposit_label', type: 'currency' },
					{ name: 'seller_credit', label: 'seller_credit_label', type: 'currency' },
					{ name: 'agent_credit', label: 'agent_credit_label', type: 'currency' },
				],
			},
			{
				title: 'Title/Settlement costs',
				fields: [
					{ name: 'loan_insurance_policy', label: 'loan_insurance_policy_label', type: 'currency' },
					{ name: 'settlement_fee', label: 'settlement_fee_label', type: 'currency' },
					{ name: 'security_fee', label: 'security_fee_label', type: 'currency' },
				],
			},
			{
				title: 'Recording Costs',
				fields: [
					{ name: 'erecording_fee', label: 'erecording_fee_label', type: 'currency' },
					{ name: 'mortgage_recording_fee', label: 'mortgage_recording_fee_label', type: 'currency' },
					{ name: 'warranty_deed_recording_fee', label: 'warranty_deed_recording_fee_label', type: 'currency' },
				],
			},
			{
				title: 'Other closing costs',
				fields: [
					{ name: 'tax_proration_estimate', label: 'tax_proration_estimate_label', type: 'currency' },
					{ name: 'realtor_compliance_fee', label: 'realtor_compliance_fee_label', type: 'currency' },
					{ name: 'hoa_transfer_fee', label: 'hoa_transfer_fee_label', type: 'currency' },
					{ name: 'hoa_prepaid_assessment', label: 'hoa_prepaid_assessment_label', type: 'currency' },
					{ name: 'other_fees', label: 'other_fees_label', type: 'currency' },
				],
			},
			{
				title: 'Totals',
				fields: [{ name: 'total_closing_costs', label: 'total_closing_costs_label', type: 'currency' }],
			},
		],
	};

	const init = ($calculator) => {
		try {
			const emailTemplate = 'buyer-cash-template';
			const calculator = new BCCalculator($calculator, pdfConfig, emailTemplate);

			// Perform initial calculation
			calculator.calculate();
		} catch (error) {
			console.error('BCCalculator initialization failed:', error);
		}
	};
	// Initialize calculator when Elementor is ready
	$(window).on('elementor/frontend/init', () => {
		elementorFrontend.hooks.addAction('frontend/element_ready/buyer_cash_to_close_widget.default', ($calculator) => init($calculator));
	});
})(jQuery);
