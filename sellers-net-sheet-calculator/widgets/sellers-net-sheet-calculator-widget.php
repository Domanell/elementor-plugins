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
        $settlement_fee = $settings['default_settlement_fee'] ?? 350;
        $lien_release_tracking_fee = $settings['default_lien_release_tracking_fee'] ?? 45;
        $security_fee = $settings['default_security_fee'] ?? 55;
        $water_escrow = $settings['default_water_escrow'] ?? 300;

        // Unique ID for this instance
        $calculator_id = 'snsc-' . uniqid();
        
        ?>
        <div class="seller-net-sheet-calculator" id="<?php echo esc_attr( $calculator_id ); ?>">
            <form class="snsc-form">
                <!-- Seller Credits Section -->
                <div class="snsc-section">
                    <h3 class="snsc-section-title"><?php echo esc_html__( 'Transaction Summary', 'sellers-net-sheet-calculator' ); ?></h3>
                    
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
                            <?php echo esc_html__( 'Total Estimated Gross Proceeds', 'sellers-net-sheet-calculator' ); ?>
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
                <!-- Mortgage & Liens Section -->
                <div class="snsc-section">
                    <h3 class="snsc-section-title"><?php echo esc_html__( 'Mortgage & Liens', 'sellers-net-sheet-calculator' ); ?></h3>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-mortgage-payoff">
                            <?php echo esc_html__( 'Mortgage Payoff', 'sellers-net-sheet-calculator' ); ?>
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
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-other-mortgage-payoff">
                            <?php echo esc_html__( 'Second Mortgage Payoff ', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-other-mortgage-payoff" 
                                name="other_mortgage_payoff" 
                                data-field="other_mortgage_payoff"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-special-assessment-payoff">
                            <?php echo esc_html__( 'Special Assessment Payoff', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-calculated-field" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-special-assessment-payoff" 
                                name="special_assessment_payoff" 
                                data-field="special_assessment_payoff"  
                            >
                        </div>
                    </div>
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-lien-release-tracking-fee">
                            <?php echo esc_html__( 'Lien Release Tracking Fee', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-calculated-field" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-lien-release-tracking-fee" 
                                name="lien_release_tracking_fee" 
                                data-field="lien_release_tracking_fee"
                                value="<?php echo esc_attr( $lien_release_tracking_fee ); ?>"
                            >
                        </div>
                    </div>
                </div>
                <!-- Taxes & Government Fees Section -->
                <div class="snsc-section">
                    <h3 class="snsc-section-title"><?php echo esc_html__( 'Taxes & Government Fees', 'sellers-net-sheet-calculator' ); ?></h3>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-property-taxes-due">
                            <?php echo esc_html__( 'Property Taxes Due', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-property-taxes-due" 
                                name="property_taxes_due" 
                                data-field="property_taxes_due"
                                placeholder="0"
                                readonly
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-michigan-transfer-tax">
                            <?php echo esc_html__( 'Michigan Transfer Tax ', 'sellers-net-sheet-calculator' ); ?>
                            <!-- Formula: (purchase price/500) * 3.75  -->
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-michigan-transfer-tax" 
                                name="michigan_transfer_tax" 
                                data-field="michigan_transfer_tax"
                                placeholder="0"
                                readonly
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-revenue-stamps">
                            <?php echo esc_html__( 'Revenue Stamps', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <!-- Formula: (purchase price/500) * 0.55  -->
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-calculated-field" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-revenue-stamps" 
                                name="revenue_stamps" 
                                data-field="revenue_stamps"  
                            >
                        </div>
                    </div>
                </div>
                <!-- Title & Closing Fees Section -->
                <div class="snsc-section">
                    <h3 class="snsc-section-title"><?php echo esc_html__( 'Title & Closing Fees', 'sellers-net-sheet-calculator' ); ?></h3>
                
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-settlement-fee">
                            <?php echo esc_html__( 'Settlement Fee', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-settlement-fee" 
                                name="settlement_fee" 
                                data-field="settlement_fee"
                                placeholder="0"
                                value="<?php echo esc_attr( $settlement_fee ); ?>"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-security-fee">
                            <?php echo esc_html__( 'Security Fee', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-security-fee" 
                                name="security_fee" 
                                data-field="security_fee"
                                placeholder="0"
                                readonly
                                value="<?php echo esc_attr( $security_fee ); ?>"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-title-insurance-policy">
                            <?php echo esc_html__( 'Title Insurance Policy', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <!-- add field with description here, to insert URL -->

                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-calculated-field" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-title-insurance-policy" 
                                name="title_insurance_policy" 
                                data-field="title_insurance_policy"  
                            >
                        </div>
                    </div>
                </div>
                <!-- Agent Commissions Section -->
                <div class="snsc-section">
                    <h3 class="snsc-section-title"><?php echo esc_html__( 'Agent Commissions', 'sellers-net-sheet-calculator' ); ?></h3>
                
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-comission-realtor">
                            <?php echo esc_html__( 'Commission Due Realtor (6% standard)', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-comission-realtor" 
                                name="comission_realtor" 
                                data-field="comission_realtor"
                                placeholder="6"
                                readonly
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-comission-realtor-extra">
                            <?php echo esc_html__( 'Commission Due Realtor', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-comission-realtor-extra" 
                                name="comission_realtor_extra" 
                                data-field="comission_realtor_extra"
                                placeholder="0"
                            >
                        </div>
                    </div>
                </div>
                <!-- Utilities & HOA Section -->
                <div class="snsc-section">
                    <h3 class="snsc-section-title"><?php echo esc_html__( 'Utilities & HOA', 'sellers-net-sheet-calculator' ); ?></h3>
                
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-current-water">
                            <?php echo esc_html__( 'Current Water/Sewer', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-current-water" 
                                name="current_water" 
                                data-field="current_water"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-hoa-assessment">
                            <?php echo esc_html__( 'HOA Assessment', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-hoa-assessment" 
                                name="hoa_assessment" 
                                data-field="hoa_assessment"
                                placeholder="0"
                            >
                        </div>
                    </div>

                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-water-escrow">
                            <?php echo esc_html__( 'Water Escrow (Minimum $300)', 'sellers-net-sheet-calculator' ); ?>
                            <!-- Minimum value is $300 -->
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-water-escrow" 
                                name="water_escrow" 
                                data-field="water_escrow"
                                placeholder="300"
                                min="300"
                                step="1"
                                value ="<?php echo esc_attr( $water_escrow ); ?>"
                            >
                        </div>
                    </div>
                </div>
                
                <!-- Miscellaneous Seller Costs Section -->
                <div class="snsc-section">
                    <h3 class="snsc-section-title"><?php echo esc_html__( 'Miscellaneous Seller Costs', 'sellers-net-sheet-calculator' ); ?></h3>
                    
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
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-fha">
                            <?php echo esc_html__( 'FHA or VA Seller Paid Fees', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-currency" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-fha" 
                                name="fha" 
                                data-field="fha"
                                placeholder="0"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-misc-cost-seller">
                            <?php echo esc_html__( 'Misc Cost to Seller', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="number" 
                                class="snsc-input snsc-number" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-misc-cost-seller" 
                                name="misc_cost_seller" 
                                data-field="misc_cost_seller"
                                value="<?php echo esc_attr( $commission_rate ); ?>"
                            >
                        </div>
                    </div>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-seller-attorney-fee">
                            <?php echo esc_html__( 'Seller\'s Attorney Fee', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-calculated-field" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-seller-attorney-fee" 
                                name="seller_attorney_fee" 
                                data-field="seller_attorney_fee"
                            >
                        </div>
                    </div>
                </div>
                
                <!-- Final Totals Section -->
                <div class="snsc-section">
                    <h3 class="snsc-section-title"><?php echo esc_html__( 'Final Totals', 'sellers-net-sheet-calculator' ); ?></h3>
                    
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-total-closing-costs">
                            <?php echo esc_html__( 'Total Closing Costs', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-calculated-field" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-total-closing-costs" 
                                name="total_closing_costs" 
                                data-field="total_closing_costs"
                                readonly
                            >
                        </div>
                    </div>
                    <div class="snsc-field">
                        <label class="snsc-field-label" for="<?php echo esc_attr( $calculator_id ); ?>-estimated-net-proceeds">
                            <?php echo esc_html__( 'Estimated Net Proceeds', 'sellers-net-sheet-calculator' ); ?>
                        </label>
                        <div class="snsc-input-wrap">
                            <input type="text" 
                                class="snsc-input snsc-calculated-field" 
                                id="<?php echo esc_attr( $calculator_id ); ?>-estimated-net-proceeds" 
                                name="estimated_net_proceeds" 
                                data-field="estimated_net_proceeds"
                                readonly
                            >
                        </div>
                    </div>
                </div>
                <!-- Buttons for frontend -->
                    <button>Download PDF</button>
                    <input type="email" id="email" name="email" placeholder="Enter your email address">     
                    <button>Send via email</button>
                <!-- Buttons for frontend -->
            </form>
        </div>
        <?php
    }
}