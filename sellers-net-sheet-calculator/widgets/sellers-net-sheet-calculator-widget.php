<?php
/**
 * Seller's Net Sheet Calculator Widget
 *
 * @package SellerNetSheetCalculator
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Seller's Net Sheet Calculator Widget.
 */
class Sellers_Net_Sheet_Calculator_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name.
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'sellers_net_sheet_calculator';
    }

    /**
     * Get widget title.
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Seller\'s Net Sheet Calculator', 'sellers-net-sheet-calculator' );
    }

    /**
     * Get widget icon.
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-calculator';
    }

    /**
     * Get widget categories.
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return [ 'general' ];
    }

    /**
     * Get widget keywords.
     *
     * @return array Widget keywords.
     */
    public function get_keywords() {
        return [ 'real estate', 'calculator', 'seller', 'net sheet' ];
    }

    /**
     * Register widget controls.
     */
    protected function register_controls() {
        // Section for Default Values
        $this->start_controls_section(
            'section_defaults',
            [
                'label' => esc_html__( 'Default Values', 'sellers-net-sheet-calculator' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'default_commission_rate',
            [
                'label'       => esc_html__( 'Default Commission Rate (%)', 'sellers-net-sheet-calculator' ),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 6,
                'min'         => 0,
                'max'         => 10,
                'step'        => 0.1,
                'description' => esc_html__( 'Set the default commission percentage.', 'sellers-net-sheet-calculator' ),
            ]
        );

        $this->add_control(
            'default_closing_fee',
            [
                'label'       => esc_html__( 'Default Closing Fee', 'sellers-net-sheet-calculator' ),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 495,
                'description' => esc_html__( 'Set the default closing fee.', 'sellers-net-sheet-calculator' ),
            ]
        );

        $this->add_control(
            'default_doc_prep_fee',
            [
                'label'       => esc_html__( 'Default Document Prep Fee', 'sellers-net-sheet-calculator' ),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 250,
                'description' => esc_html__( 'Set the default document preparation fee.', 'sellers-net-sheet-calculator' ),
            ]
        );

        $this->add_control(
            'default_county_fee',
            [
                'label'       => esc_html__( 'Default County Conservation Fee', 'sellers-net-sheet-calculator' ),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 5,
                'description' => esc_html__( 'Set the default county conservation fee.', 'sellers-net-sheet-calculator' ),
            ]
        );

        $this->add_control(
            'default_courier_fee',
            [
                'label'       => esc_html__( 'Default Courier/Payoff Fee', 'sellers-net-sheet-calculator' ),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 0,
                'description' => esc_html__( 'Set the default courier/payoff processing fee.', 'sellers-net-sheet-calculator' ),
            ]
        );

        $this->add_control(
            'state_deed_tax_rate',
            [
                'label'       => esc_html__( 'State Deed Tax Rate (%)', 'sellers-net-sheet-calculator' ),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 0.34,
                'min'         => 0,
                'max'         => 5,
                'step'        => 0.01,
                'description' => esc_html__( 'Set the state deed tax rate (as percentage).', 'sellers-net-sheet-calculator' ),
            ]
        );

        $this->end_controls_section();

        // Section for Form Styling
        $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__( 'Form Style', 'sellers-net-sheet-calculator' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'form_typography',
                'selector' => '{{WRAPPER}} .seller-net-sheet-calculator',
            ]
        );

        $this->add_control(
            'section_heading_color',
            [
                'label'     => esc_html__( 'Section Heading Color', 'sellers-net-sheet-calculator' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .snsc-section-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'label_color',
            [
                'label'     => esc_html__( 'Field Label Color', 'sellers-net-sheet-calculator' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#555555',
                'selectors' => [
                    '{{WRAPPER}} .snsc-field-label' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'input_background_color',
            [
                'label'     => esc_html__( 'Input Background Color', 'sellers-net-sheet-calculator' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .snsc-input' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'calculated_field_background',
            [
                'label'     => esc_html__( 'Calculated Field Background', 'sellers-net-sheet-calculator' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#f5f5f5',
                'selectors' => [
                    '{{WRAPPER}} .snsc-calculated-field' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output on the frontend.
     */
    protected function render() {
        $settings = $this->get_settings_for_display();

        // Get default values from settings
        $commission_rate = $settings['default_commission_rate'] ?? 6;
        $closing_fee = $settings['default_closing_fee'] ?? 495;
        $doc_prep_fee = $settings['default_doc_prep_fee'] ?? 250;
        $county_fee = $settings['default_county_fee'] ?? 5;
        $courier_fee = $settings['default_courier_fee'] ?? 100;
        $state_deed_tax_rate = $settings['state_deed_tax_rate'] ?? 0.34;

        // Unique ID for this instance
        $calculator_id = 'snsc-' . uniqid();
        
        ?>
        <div class="seller-net-sheet-calculator" id="<?php echo esc_attr( $calculator_id ); ?>">
            <form class="snsc-form">
                <!-- Seller Credits Section -->
                <div class="snsc-section">
                    <h3 class="snsc-section-title"><?php echo esc_html__( 'Seller Credits', 'sellers-net-sheet-calculator' ); ?></h3>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-purchase-price">
                            <?php echo esc_html__( 'Purchase Price', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-purchase-price" 
                                name="purchase_price" 
                                data-field="purchase_price"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-other-credits">
                            <?php echo esc_html__( 'Other Credits to Seller', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-other-credits" 
                                name="other_credits" 
                                data-field="other_credits"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-gross-proceeds">
                            <?php echo esc_html__( 'Total Estimated Gross Proceeds Due to Seller', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-calculated-field" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-gross-proceeds" 
                                name="gross_proceeds" 
                                data-field="gross_proceeds"
                                readonly
                            >
                        </div>
                    </div>
                </div>
                
                <!-- Selling Costs Section -->
                <div class="snsc-section">
                    <h3 class="snsc-section-title"><?php echo esc_html__( 'Less Selling Costs', 'sellers-net-sheet-calculator' ); ?></h3>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-mortgage-payoff">
                            <?php echo esc_html__( 'Mortgage Payoff (with interest)', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-mortgage-payoff" 
                                name="mortgage_payoff" 
                                data-field="mortgage_payoff"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-second-mortgage">
                            <?php echo esc_html__( '2nd Mortgage Payoff (with interest)', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-second-mortgage" 
                                name="second_mortgage" 
                                data-field="second_mortgage"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-commission-rate">
                            <?php echo esc_html__( 'Realtor Commission %', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="number" 
                                class="snsc-input snsc-number" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-commission-rate" 
                                name="commission_rate" 
                                data-field="commission_rate"
                                value="<?php echo esc_attr( $commission_rate ); ?>"
                                step="0.1"
                                min="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-commission-total">
                            <?php echo esc_html__( 'Realtor Commission Total', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-calculated-field" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-commission-total" 
                                name="commission_total" 
                                data-field="commission_total"
                                readonly
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-real-estate-taxes">
                            <?php echo esc_html__( 'Real Estate Taxes Due at closing (proration)', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-real-estate-taxes" 
                                name="real_estate_taxes" 
                                data-field="real_estate_taxes"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-special-assessments">
                            <?php echo esc_html__( 'Special Assessments due at closing', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-special-assessments" 
                                name="special_assessments" 
                                data-field="special_assessments"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-closing-fee">
                            <?php echo esc_html__( 'Closing Fee ($495-$600)', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-closing-fee" 
                                name="closing_fee" 
                                data-field="closing_fee"
                                value="<?php echo esc_attr( $closing_fee ); ?>"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-broker-fee">
                            <?php echo esc_html__( 'Broker Administration Fee', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-broker-fee" 
                                name="broker_fee" 
                                data-field="broker_fee"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-doc-prep-fee">
                            <?php echo esc_html__( 'Document Preparation Fees ($200-$500)', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-doc-prep-fee" 
                                name="doc_prep_fee" 
                                data-field="doc_prep_fee"
                                value="<?php echo esc_attr( $doc_prep_fee ); ?>"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-state-deed-tax">
                            <?php echo esc_html__( 'State Deed Tax', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-calculated-field" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-state-deed-tax" 
                                name="state_deed_tax" 
                                data-field="state_deed_tax"
                                readonly
                                data-tax-rate="<?php echo esc_attr( $state_deed_tax_rate ); ?>"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-seller-paid-costs">
                            <?php echo esc_html__( 'Seller Paid Closing Costs for Buyer', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-seller-paid-costs" 
                                name="seller_paid_costs" 
                                data-field="seller_paid_costs"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-county-fee">
                            <?php echo esc_html__( 'County Conservation Fee', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-county-fee" 
                                name="county_fee" 
                                data-field="county_fee"
                                value="<?php echo esc_attr( $county_fee ); ?>"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-home-warranty">
                            <?php echo esc_html__( 'Home Warranty', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-home-warranty" 
                                name="home_warranty" 
                                data-field="home_warranty"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-courier-fees">
                            <?php echo esc_html__( 'Courier Fees/Payoff Processing ($50 per payoff)', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-courier-fees" 
                                name="courier_fees" 
                                data-field="courier_fees"
                                value="<?php echo esc_attr( $courier_fee ); ?>"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-work-orders">
                            <?php echo esc_html__( 'Work Orders', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-work-orders" 
                                name="work_orders" 
                                data-field="work_orders"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-association-dues">
                            <?php echo esc_html__( 'Association Dues owing at closing', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-association-dues" 
                                name="association_dues" 
                                data-field="association_dues"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-association-disclosure">
                            <?php echo esc_html__( 'Association Disclosure/Dues Letter', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-association-disclosure" 
                                name="association_disclosure" 
                                data-field="association_disclosure"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-misc-costs">
                            <?php echo esc_html__( 'Misc. Costs to Seller', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-misc-costs" 
                                name="misc_costs" 
                                data-field="misc_costs"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-misc-expenses-1">
                            <?php echo esc_html__( 'Miscellaneous Expenses 1', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-misc-expenses-1" 
                                name="misc_expenses_1" 
                                data-field="misc_expenses_1"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-misc-expenses-2">
                            <?php echo esc_html__( 'Miscellaneous Expenses 2', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-misc-expenses-2" 
                                name="misc_expenses_2" 
                                data-field="misc_expenses_2"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-misc-expenses-3">
                            <?php echo esc_html__( 'Miscellaneous Expenses 3', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-misc-expenses-3" 
                                name="misc_expenses_3" 
                                data-field="misc_expenses_3"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-misc-expenses-4">
                            <?php echo esc_html__( 'Miscellaneous Expenses 4', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-misc-expenses-4" 
                                name="misc_expenses_4" 
                                data-field="misc_expenses_4"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-total-selling-costs">
                            <?php echo esc_html__( 'Total Selling Costs', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-calculated-field" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-total-selling-costs" 
                                name="total_selling_costs" 
                                data-field="total_selling_costs"
                                readonly
                            >
                        </div>
                    </div>
                </div>
                
                <!-- Estimated Proceeds Section -->
                <div class="snsc-section">
                    <h3 class="snsc-section-title"><?php echo esc_html__( 'Estimated Proceeds', 'sellers-net-sheet-calculator' ); ?></h3>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-net-proceeds">
                            <?php echo esc_html__( 'Estimated Net Proceeds', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-calculated-field" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-net-proceeds" 
                                name="net_proceeds" 
                                data-field="net_proceeds"
                                readonly
                            >
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
}