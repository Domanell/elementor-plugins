/**
 * Seller's Net Sheet Calculator JavaScript
 */
(function ($) {
	'use strict';

	/**
	 * Format a number as currency
	 */
	function formatCurrency(number) {
		if (isNaN(number)) {
			number = 0;
		}
		return (
			'$' +
			number.toLocaleString('en-US', {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2,
			})
		);
	}

	/**
	 * Parse currency value to number
	 */
	function parseCurrency(value) {
		if (!value) return 0;
		return parseFloat(value.replace(/[^0-9.-]+/g, '')) || 0;
	}

	/**
	 * Initialize calculator
	 */
	function initCalculator(calculator) {
		const $calculator = $(calculator);
		const calculatorId = $calculator.attr('id');
		const $inputs = $calculator.find('.snsc-input');

		// Initialize currency inputs
		$calculator.find('.snsc-currency').each(function () {
			const $this = $(this);
			if ($this.val()) {
				$this.val(formatCurrency(parseCurrency($this.val())));
			}

			// Format currency on blur
			$this.on('blur', function () {
				const value = parseCurrency($(this).val());
				$(this).val(value ? formatCurrency(value) : '');
			});

			// Clear formatting on focus
			$this.on('focus', function () {
				const value = parseCurrency($(this).val());
				$(this).val(value || '');
			});
		});

		// Calculate function
		function calculate() {
			// Get values
			const purchasePrice = parseCurrency($calculator.find('[data-field="purchase_price"]').val());
			const otherCredits = parseCurrency($calculator.find('[data-field="other_credits"]').val());
			const mortgagePayoff = parseCurrency($calculator.find('[data-field="mortgage_payoff"]').val());
			const secondMortgage = parseCurrency($calculator.find('[data-field="second_mortgage"]').val());
			const commissionRate = parseFloat($calculator.find('[data-field="commission_rate"]').val()) || 0;
			const realEstateTaxes = parseCurrency($calculator.find('[data-field="real_estate_taxes"]').val());
			const specialAssessments = parseCurrency($calculator.find('[data-field="special_assessments"]').val());
			const closingFee = parseCurrency($calculator.find('[data-field="closing_fee"]').val());
			const brokerFee = parseCurrency($calculator.find('[data-field="broker_fee"]').val());
			const docPrepFee = parseCurrency($calculator.find('[data-field="doc_prep_fee"]').val());
			const stateDeedTaxRate = parseFloat($calculator.find('[data-field="state_deed_tax"]').data('tax-rate')) || 0.34;
			const sellerPaidCosts = parseCurrency($calculator.find('[data-field="seller_paid_costs"]').val());
			const countyFee = parseCurrency($calculator.find('[data-field="county_fee"]').val());
			const homeWarranty = parseCurrency($calculator.find('[data-field="home_warranty"]').val());
			const courierFees = parseCurrency($calculator.find('[data-field="courier_fees"]').val());
			const workOrders = parseCurrency($calculator.find('[data-field="work_orders"]').val());
			const associationDues = parseCurrency($calculator.find('[data-field="association_dues"]').val());
			const associationDisclosure = parseCurrency($calculator.find('[data-field="association_disclosure"]').val());
			const miscCosts = parseCurrency($calculator.find('[data-field="misc_costs"]').val());
			const miscExpenses1 = parseCurrency($calculator.find('[data-field="misc_expenses_1"]').val());
			const miscExpenses2 = parseCurrency($calculator.find('[data-field="misc_expenses_2"]').val());
			const miscExpenses3 = parseCurrency($calculator.find('[data-field="misc_expenses_3"]').val());
			const miscExpenses4 = parseCurrency($calculator.find('[data-field="misc_expenses_4"]').val());

			// Calculate gross proceeds
			const grossProceeds = purchasePrice + otherCredits;
			$calculator.find('[data-field="gross_proceeds"]').val(formatCurrency(grossProceeds));

			// Calculate commission
			const commissionTotal = purchasePrice * (commissionRate / 100);
			$calculator.find('[data-field="commission_total"]').val(formatCurrency(commissionTotal));

			// Calculate state deed tax
			const stateDeedTax = purchasePrice * (stateDeedTaxRate / 100);
			$calculator.find('[data-field="state_deed_tax"]').val(formatCurrency(stateDeedTax));

			// Calculate total selling costs
			const totalSellingCosts =
				mortgagePayoff +
				secondMortgage +
				commissionTotal +
				realEstateTaxes +
				specialAssessments +
				closingFee +
				brokerFee +
				docPrepFee +
				stateDeedTax +
				sellerPaidCosts +
				countyFee +
				homeWarranty +
				courierFees +
				workOrders +
				associationDues +
				associationDisclosure +
				miscCosts +
				miscExpenses1 +
				miscExpenses2 +
				miscExpenses3 +
				miscExpenses4;

			$calculator.find('[data-field="total_selling_costs"]').val(formatCurrency(totalSellingCosts));

			// Calculate net proceeds
			const netProceeds = grossProceeds - totalSellingCosts;
			$calculator.find('[data-field="net_proceeds"]').val(formatCurrency(netProceeds));
		}

		// Calculate on input change
		$inputs.on('input change', function () {
			calculate();
		});

		// Initial calculation
		calculate();
	}

	// Initialize all calculators on page
	$(document).ready(function () {
		$('.seller-net-sheet-calculator').each(function () {
			initCalculator(this);
		});
	});
})(jQuery);
