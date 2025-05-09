/**
 * Net Sheet Calculator JavaScript
 */
(function ($) {
	'use strict';

	$(window).on('elementor/frontend/init', () => {
		elementorFrontend.hooks.addAction('frontend/element_ready/net_sheet_calculator_widget.default', initCalculator);
	});

	// Format number as currency
	const formatCurrency = (number) =>
		`$${(isNaN(number) ? 0 : number).toLocaleString('en-US', {
			minimumFractionDigits: 2,
			maximumFractionDigits: 2,
		})}`;

	// Parse currency string to float
	const parseCurrency = (value) => parseFloat(String(value).replace(/[^0-9.-]+/g, '')) || 0;

	// Validate email format
	const validateEmail = (email) =>
		/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(
			String(email).toLowerCase()
		);

	function initCalculator($calculator) {
		const $inputs = $calculator.find('.nsc-input');

		initCurrencyInputs($calculator);
		bindButtons($calculator);
		$inputs.on('input change', () => calculate($calculator));
		calculate($calculator);
	}

	function initCurrencyInputs($container) {
		$container.find('.nsc-input--currency').each(function () {
			const $input = $(this);
			if ($input.val()) {
				$input.val(formatCurrency(parseCurrency($input.val())));
			}

			$input.on('blur', function () {
				const value = parseCurrency($(this).val());
				$(this).val(value ? formatCurrency(value) : '');
			});

			$input.on('focus', function () {
				$(this).val(parseCurrency($(this).val()) || '');
			});
		});
	}

	function calculate($calc) {
		const getVal = (field) => parseCurrency($calc.find(`[data-field="${field}"]`).val());

		const getRate = (field, fallback) => parseFloat($calc.find(`[data-field="${field}"]`).val()) || fallback;

		// Input values
		const purchasePrice = getVal('purchase_price');
		const otherCredits = getVal('other_credits');

		const mortgagePayoff = getVal('mortgage_payoff');
		const secondMortgagePayoff = getVal('other_mortgage_payoff');
		const specialAssessmentPayoff = getVal('special_assessment_payoff');
		const lienReleaseTrackingFee = getVal('lien_release_tracking_fee');

		const propertyTaxesDue = getVal('property_taxes_due');
		const michiganTransferTaxRate = getRate('michigan_transfer_tax_rate', 3.75);
		const revenueStampsRate = getRate('revenue_stamps_rate', 0.55);

		const settlementFee = getVal('settlement_fee');
		const securityFee = getVal('security_fee');
		const titleInsurancePolicy = getVal('title_insurance_policy');

		const commissionRate = getRate('comission_rate', 6); // typo preserved from original
		const commissionDueRealtor = purchasePrice * (commissionRate / 100);
		const commissionDueRealtorExtra = getVal('comission_realtor_extra');

		const currentWaterSewer = getVal('current_water');
		const hoaAssessment = getVal('hoa_assessment');
		const waterEscrow = getVal('water_escrow');

		const homeWarranty = getVal('home_warranty');
		const fhaVaFees = getVal('fha');
		const miscCostSeller = getVal('misc_cost_seller');
		const sellerAttorneyFee = getVal('seller_attorney_fee');

		// Calculations
		const michiganTransferTax = (purchasePrice / 500) * michiganTransferTaxRate;
		const revenueStamps = (purchasePrice / 500) * revenueStampsRate;
		const grossProceeds = purchasePrice + otherCredits;

		const totalClosingCosts =
			mortgagePayoff +
			secondMortgagePayoff +
			specialAssessmentPayoff +
			lienReleaseTrackingFee +
			propertyTaxesDue +
			michiganTransferTax +
			revenueStamps +
			settlementFee +
			securityFee +
			titleInsurancePolicy +
			commissionDueRealtor +
			commissionDueRealtorExtra +
			currentWaterSewer +
			hoaAssessment +
			waterEscrow +
			homeWarranty +
			fhaVaFees +
			miscCostSeller +
			sellerAttorneyFee;

		const netProceeds = grossProceeds - totalClosingCosts;

		// Output fields
		const setField = (field, value) => $calc.find(`[data-field="${field}"]`).val(formatCurrency(value));

		setField('michigan_transfer_tax', michiganTransferTax);
		setField('revenue_stamps', revenueStamps);
		setField('comission_realtor', commissionDueRealtor);
		setField('gross_proceeds', grossProceeds);
		setField('total_closing_costs', totalClosingCosts);
		setField('estimated_net_proceeds', netProceeds);
	}

	function bindButtons($calc) {
		$calc.find('.nsc-button--download').on('click', (e) => {
			e.preventDefault();
			alert('PDF download functionality will be implemented here.');
		});

		$calc.find('.nsc-button--send').on('click', (e) => {
			e.preventDefault();
			const email = $calc.find('input[name="email"]').val();
			if (!email || !validateEmail(email)) {
				alert('Please enter a valid email address.');
				return;
			}
			alert(`Email would be sent to: ${email}`);
		});
	}
})(jQuery);
