(function ($) {
	'use strict';
	class BCCalculator extends RECalculator {
		constructor($calculator, labels, pdfConfig) {
			// Call parent constructor with only the required element
			super($calculator);

			// Initialize child-specific properties
			this.labels = labels;
			this.pdfConfig = pdfConfig;
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
			{ title: 'Purchase Information', fields: ['purchase_price', 'other_credits', 'gross_proceeds'] },
			{ title: 'Mortgage Payoffs', fields: ['mortgage_payoff', 'other_mortgage_payoff', 'special_assessment_payoff', 'lien_release_tracking_fee'] },
			{ title: 'Taxes', fields: ['property_taxes_due', 'michigan_transfer_tax', 'revenue_stamps'] },
			{ title: 'Title Fees', fields: ['settlement_fee', 'security_fee', 'title_insurance_policy'] },
			{ title: 'Commission Fees', fields: ['commission_realtor', 'commission_realtor_extra'] },
			{ title: 'Other Fees', fields: ['current_water', 'hoa_assessment', 'water_escrow', 'home_warranty', 'fha', 'misc_cost_seller', 'seller_attorney_fee'] },
			{ title: 'Totals', fields: ['total_closing_costs', 'estimated_net_proceeds'] },
		],
	};

	const createLabelDefinitions = (settings) => {
		return {
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

	const init = ($calculator) => {
		try {
			const settings = $calculator.data('settings');
			const labels = createLabelDefinitions(settings);
			const calculator = new BCCalculator($calculator, labels, pdfConfig);

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
