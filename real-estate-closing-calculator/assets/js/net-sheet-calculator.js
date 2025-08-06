(function ($) {
	'use strict';
	class NSCCalculator extends RECalculator {
		constructor($calculator, pdfConfig, emailTemplate) {
			// Call parent constructor with only the required element
			super($calculator, pdfConfig);

			// Initialize child-specific properties
			this.emailTemplate = emailTemplate;
			this.insuranceRates = this._defineInsuranceRates();
		}

		_defineInsuranceRates() {
			const rates = this.settings.insurance_rates || [];
			return rates.map(({ insurance_is_fixed, insurance_range_from, insurance_range_to, insurance_rate }) => ({
				isFixed: insurance_is_fixed === 'yes',
				min: insurance_range_from || 0,
				max: insurance_range_to || Infinity,
				rate: insurance_rate || 0,
			}));
		}

		calculate() {
			// Perform all calculations
			this.values.commission_realtor_amount = this.values.purchase_price * ((this.values.commission_realtor || 0) / 100);
			this.values.michigan_transfer_tax = Math.ceil(this.values.purchase_price / 500) * (this.values.michigan_transfer_tax_rate || 3.75);
			this.values.revenue_stamps = Math.ceil(this.values.purchase_price / 500) * (this.values.revenue_stamps_rate || 0.55);
			this.values.gross_proceeds = this.values.purchase_price + (this.values.other_credits || 0);
			this.values.title_insurance_policy = this.calculateTieredRate(this.values.purchase_price, this.insuranceRates);

			this.values.total_closing_costs = [
				this.values.commission_realtor_amount,
				this.values.michigan_transfer_tax,
				this.values.revenue_stamps,
				this.values.mortgage_payoff,
				this.values.other_mortgage_payoff,
				this.values.special_assessment_payoff,
				this.values.lien_release_tracking_fee,
				this.values.property_taxes_due,
				this.values.settlement_fee,
				this.values.security_fee,
				this.values.title_insurance_policy,
				this.values.commission_realtor_extra,
				this.values.current_water,
				this.values.hoa_assessment,
				this.values.water_escrow,
				this.values.home_warranty,
				this.values.fha,
				this.values.misc_cost_seller,
				this.values.seller_attorney_fee,
			].reduce((sum, val) => sum + (val || 0), 0);

			this.values.estimated_net_proceeds =
				this.values.gross_proceeds - this.values.total_closing_costs < 0 ? 0 : this.values.gross_proceeds - this.values.total_closing_costs;

			// Update input display for calculated fields (readonly fields)
			['gross_proceeds', 'michigan_transfer_tax', 'revenue_stamps', 'title_insurance_policy', 'commission_realtor_amount'].forEach((field) => {
				this.updateCalculatedField(field, this.values[field]);
			});

			// Update text output fields
			this.updateTextOutput('total_closing_costs', this.values.total_closing_costs);
			this.updateTextOutput('estimated_net_proceeds', this.values.estimated_net_proceeds);
		}
	}

	const pdfConfig = {
		documentTitle: 'Net Sheet Calculator Results',
		filename: 'net-sheet-calculator-results.pdf',
		sections: [
			{
				title: 'Purchase Information',
				fields: [
					{ name: 'purchase_price', label: 'purchase_price_label', type: 'currency' },
					{ name: 'other_credits', label: 'other_credits_label', type: 'currency' },
					{ name: 'gross_proceeds', label: 'gross_proceeds_label', type: 'currency' },
				],
			},
			{
				title: 'Mortgage Payoffs',
				fields: [
					{ name: 'mortgage_payoff', label: 'mortgage_payoff_label', type: 'currency' },
					{ name: 'other_mortgage_payoff', label: 'other_mortgage_payoff_label', type: 'currency' },
					{ name: 'special_assessment_payoff', label: 'special_assessment_payoff_label', type: 'currency' },
					{ name: 'lien_release_tracking_fee', label: 'lien_release_tracking_fee_label', type: 'currency' },
				],
			},
			{
				title: 'Taxes',
				fields: [
					{ name: 'property_taxes_due', label: 'property_taxes_due_label', type: 'currency' },
					{ name: 'michigan_transfer_tax', label: 'michigan_transfer_tax_label', type: 'currency' },
					{ name: 'revenue_stamps', label: 'revenue_stamps_label', type: 'currency' },
				],
			},
			{
				title: 'Title Fees',
				fields: [
					{ name: 'settlement_fee', label: 'settlement_fee_label', type: 'currency' },
					{ name: 'security_fee', label: 'security_fee_label', type: 'currency' },
					{ name: 'title_insurance_policy', label: 'title_insurance_policy_label', type: 'currency' },
				],
			},
			{
				title: 'Commission Fees',
				fields: [
					{ name: 'commission_realtor', label: 'commission_realtor_label', type: 'percentage' },
					{ name: 'commission_realtor_extra', label: 'commission_realtor_extra_label', type: 'currency' },
				],
			},
			{
				title: 'Other Fees',
				fields: [
					{ name: 'current_water', label: 'current_water_label', type: 'currency' },
					{ name: 'hoa_assessment', label: 'hoa_assessment_label', type: 'currency' },
					{ name: 'water_escrow', label: 'water_escrow_label', type: 'currency' },
					{ name: 'home_warranty', label: 'home_warranty_label', type: 'currency' },
					{ name: 'fha', label: 'fha_label', type: 'currency' },
					{ name: 'misc_cost_seller', label: 'misc_cost_seller_label', type: 'currency' },
					{ name: 'seller_attorney_fee', label: 'seller_attorney_fee_label', type: 'currency' },
				],
			},
			{
				title: 'Totals',
				fields: [
					{ name: 'total_closing_costs', label: 'total_closing_costs_label', type: 'currency' },
					{ name: 'estimated_net_proceeds', label: 'estimated_net_proceeds_label', type: 'currency' },
				],
			},
		],
	};

	const init = ($calculator) => {
		try {
			const emailTemplate = 'net-sheet-template';
			const calculator = new NSCCalculator($calculator, pdfConfig, emailTemplate);

			// Perform initial calculation
			calculator.calculate();
		} catch (error) {
			console.error('NSCCalculator initialization failed:', error);
		}
	};

	// Initialize calculator when Elementor is ready
	$(window).on('elementor/frontend/init', () => {
		elementorFrontend.hooks.addAction('frontend/element_ready/net_sheet_calculator_widget.default', ($calculator) => init($calculator));
	});
})(jQuery);
