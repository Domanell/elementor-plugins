(function ($) {
	'use strict';
	class NSCCalculator extends RECalculator {
		constructor($calculator, labels, pdfConfig) {
			// Call parent constructor with only the required element
			super($calculator);

			// Initialize child-specific properties
			this.labels = labels;
			this.pdfConfig = pdfConfig;
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

	const init = ($calculator) => {
		try {
			const settings = $calculator.data('settings');
			const labels = createLabelDefinitions(settings);
			const calculator = new NSCCalculator($calculator, labels, pdfConfig);

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
