<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Net Sheet Calculator Widget.
 */
class Net_Sheet_Calculator_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'net_sheet_calculator_widget';
    }

    public function get_title() {
        return esc_html__('Net Sheet Calculator', 'net-sheet-calculator');
    }

    public function get_icon() {
        return 'eicon-price-list';
    }

    public function get_categories() {
        return ['general'];
    }

    public function get_keywords() {
        return ['real estate', 'calculator', 'seller', 'net sheet'];
    }

    public function get_script_depends() {
        return ['net-sheet-calculator-script'];
    }

    public function get_style_depends() {
        return ['net-sheet-calculator-style'];
    }

    protected function register_controls() {
        // Section for Default Values
        $this->start_controls_section(
            'section_defaults',
            [
                'label' => esc_html__('Default Values', 'net-sheet-calculator'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'commissions_defaults_heading',
            [
                'label'       => esc_html__('Commissions', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'default_commission_rate',
            [
                'label'       => esc_html__('Default Commission Rate (%)', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 6,
                'min'         => 0,
                'max'         => 10,
                'step'        => 0.1,
                'description' => esc_html__('Set the default commission percentage.', 'net-sheet-calculator'),
            ]
        );

        $this->add_control(
            'title_closing_defaults_heading',
            [
                'label'       => esc_html__('Title & Closing Fees', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'default_settlement_fee',
            [
                'label'       => esc_html__('Default Settlement Fee', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 350,
                'description' => esc_html__('Set the default settlement fee.', 'net-sheet-calculator'),
            ]
        );

        $this->add_control(
            'default_lien_release_tracking_fee',
            [
                'label'       => esc_html__('Default Lien Release Tracking Fee', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 45,
                'description' => esc_html__('Set the default lien release tracking fee.', 'net-sheet-calculator'),
            ]
        );

        $this->add_control(
            'default_security_fee',
            [
                'label'       => esc_html__('Default Security Fee', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 55,
                'description' => esc_html__('Set the default security fee.', 'net-sheet-calculator'),
            ]
        );

        $this->add_control(
            'utilities_defaults_heading',
            [
                'label'       => esc_html__('Utilities', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'default_water_escrow',
            [
                'label'       => esc_html__('Default Water Escrow', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 300,
                'min'         => 300,
                'description' => esc_html__('Set the default water escrow (minimum $300).', 'net-sheet-calculator'),
            ]
        );

        $this->add_control(
            'michigan_transfer_tax_rate',
            [
                'label'       => esc_html__('Michigan Transfer Tax Rate', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 3.75,
                'min'         => 0,
                'step'        => 0.01,
                'description' => esc_html__('Michigan transfer tax calculation: (Purchase Price/500) * Rate', 'net-sheet-calculator'),
            ]
        );

        $this->add_control(
            'revenue_stamps_rate',
            [
                'label'       => esc_html__('Revenue Stamps Rate', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 0.55,
                'min'         => 0,
                'step'        => 0.01,
                'description' => esc_html__('Revenue stamps calculation: (Purchase Price/500) * Rate', 'net-sheet-calculator'),
            ]
        );

        $this->end_controls_section();

        // Transaction Summary Fields
        $this->start_controls_section(
            'section_transaction_summary_fields',
            [
                'label' => esc_html__('Transaction Summary Fields', 'net-sheet-calculator'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'purchase_price_heading',
            [
                'label'       => esc_html__('Purchase Price', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'purchase_price_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Purchase Price', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'purchase_price_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );

        $this->add_control(
            'other_credits_heading',
            [
                'label'       => esc_html__('Other Credits', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'other_credits_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Other Credits to Seller', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'other_credits_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );

        $this->add_control(
            'gross_proceeds_heading',
            [
                'label'       => esc_html__('Gross Proceeds', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'gross_proceeds_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Total Estimated Gross Proceeds', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'gross_proceeds_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );

        $this->end_controls_section();

        // Mortgage & Liens Fields
        $this->start_controls_section(
            'section_mortgage_liens_fields',
            [
                'label' => esc_html__('Mortgage & Liens Fields', 'net-sheet-calculator'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'mortgage_payoff_heading',
            [
                'label'       => esc_html__('Mortgage Payoff', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'mortgage_payoff_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Mortgage Payoff', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'mortgage_payoff_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );

        $this->add_control(
            'other_mortgage_payoff_heading',
            [
                'label'       => esc_html__('Second Mortgage Payoff', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'other_mortgage_payoff_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Second Mortgage Payoff', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'other_mortgage_payoff_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );

        $this->add_control(
            'special_assessment_payoff_heading',
            [
                'label'       => esc_html__('Special Assessment Payoff', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'special_assessment_payoff_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Special Assessment Payoff', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'special_assessment_payoff_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );

        $this->add_control(
            'lien_release_tracking_fee_heading',
            [
                'label'       => esc_html__('Lien Release Tracking Fee', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'lien_release_tracking_fee_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Lien Release Tracking Fee', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'lien_release_tracking_fee_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );        
        $this->end_controls_section();

        // Taxes & Government Fees Fields
        $this->start_controls_section(
            'section_taxes_govt_fees_fields',
            [
                'label' => esc_html__('Taxes & Government Fees Fields', 'net-sheet-calculator'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'property_taxes_due_heading',
            [
                'label'       => esc_html__('Property Taxes Due', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'property_taxes_due_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Property Taxes Due', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'property_taxes_due_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );

        $this->add_control(
            'michigan_transfer_tax_heading',
            [
                'label'       => esc_html__('Michigan Transfer Tax', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'michigan_transfer_tax_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Michigan Transfer Tax', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'michigan_transfer_tax_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('(Purchase price/500) * 3.75', 'net-sheet-calculator'),
            ]
        );

        $this->add_control(
            'revenue_stamps_heading',
            [
                'label'       => esc_html__('Revenue Stamps', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'revenue_stamps_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Revenue Stamps', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'revenue_stamps_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('(Purchase price/500) * 0.55', 'net-sheet-calculator'),
            ]
        );        
        $this->end_controls_section();
        
        // Title & Closing Fees Fields
        $this->start_controls_section(
            'section_title_closing_fields',
            [
                'label' => esc_html__('Title & Closing Fees Fields', 'net-sheet-calculator'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'settlement_fee_heading',
            [
                'label'       => esc_html__('Settlement Fee', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'settlement_fee_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Settlement Fee', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'settlement_fee_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );

        $this->add_control(
            'security_fee_heading',
            [
                'label'       => esc_html__('Security Fee', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'security_fee_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Security Fee', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'security_fee_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );

        $this->add_control(
            'title_insurance_policy_heading',
            [
                'label'       => esc_html__('Title Insurance Policy', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'title_insurance_policy_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Title Insurance Policy', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'title_insurance_policy_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );        
        $this->end_controls_section();

        // Agent Commissions Fields
        $this->start_controls_section(
            'section_agent_commissions_fields',
            [
                'label' => esc_html__('Agent Commissions Fields', 'net-sheet-calculator'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'comission_realtor_heading',
            [
                'label'       => esc_html__('Commission Due Realtor', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'comission_realtor_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Commission Due Realtor (6% standard)', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'comission_realtor_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );

        $this->add_control(
            'comission_realtor_extra_heading',
            [
                'label'       => esc_html__('Commission Due Realtor Extra', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'comission_realtor_extra_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Commission Due Realtor (Extra $)', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'comission_realtor_extra_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );

        $this->end_controls_section();

        // Utilities & HOA Fields
        $this->start_controls_section(
            'section_utilities_hoa_fields',
            [
                'label' => esc_html__('Utilities & HOA Fields', 'net-sheet-calculator'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'current_water_heading',
            [
                'label'       => esc_html__('Current Water/Sewer', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'current_water_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Current Water/Sewer', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'current_water_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );

        $this->add_control(
            'hoa_assessment_heading',
            [
                'label'       => esc_html__('HOA Assessment', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'hoa_assessment_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('HOA Assessment', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'hoa_assessment_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );

        $this->add_control(
            'water_escrow_heading',
            [
                'label'       => esc_html__('Water Escrow', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'water_escrow_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Water Escrow (Minimum $300)', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'water_escrow_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );        
        $this->end_controls_section();

        // Miscellaneous Seller Costs Fields
        $this->start_controls_section(
            'section_misc_seller_costs_fields',
            [
                'label' => esc_html__('Miscellaneous Seller Costs Fields', 'net-sheet-calculator'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'home_warranty_heading',
            [
                'label'       => esc_html__('Home Warranty', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'home_warranty_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Home Warranty', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'home_warranty_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );

        $this->add_control(
            'fha_heading',
            [
                'label'       => esc_html__('FHA or VA Seller Paid Fees', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'fha_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('FHA or VA Seller Paid Fees', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'fha_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );

        $this->add_control(
            'misc_cost_seller_heading',
            [
                'label'       => esc_html__('Misc Cost to Seller', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'misc_cost_seller_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Misc Cost to Seller', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'misc_cost_seller_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );

        $this->add_control(
            'seller_attorney_fee_heading',
            [
                'label'       => esc_html__('Seller\'s Attorney Fee', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'seller_attorney_fee_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Seller\'s Attorney Fee', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'seller_attorney_fee_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );

        $this->end_controls_section();

        // Final Totals Fields
        $this->start_controls_section(
            'section_final_totals_fields',
            [
                'label' => esc_html__('Final Totals Fields', 'net-sheet-calculator'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'total_closing_costs_heading',
            [
                'label'       => esc_html__('Total Closing Costs', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'total_closing_costs_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Total Closing Costs', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'total_closing_costs_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );

        $this->add_control(
            'estimated_net_proceeds_heading',
            [
                'label'       => esc_html__('Estimated Net Proceeds', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'estimated_net_proceeds_label',
            [
                'label'       => esc_html__('Label', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Estimated Net Proceeds', 'net-sheet-calculator'),
            ]
        );
        $this->add_control(
            'estimated_net_proceeds_description',
            [
                'label'       => esc_html__('Description', 'net-sheet-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );

        $this->end_controls_section();

        // Section for Form Styling
        $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__('Form Style', 'net-sheet-calculator'),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'form_typography',
                'selector' => '{{WRAPPER}} .net-sheet-calculator',
            ]
        );

        $this->add_control(
            'section_heading_color',
            [
                'label'     => esc_html__('Section Heading Color', 'net-sheet-calculator'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .nsc-section__title' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'label_color',
            [
                'label'     => esc_html__('Field Label Color', 'net-sheet-calculator'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#555555',
                'selectors' => [
                    '{{WRAPPER}} .nsc-field__label' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'input_background_color',
            [
                'label'     => esc_html__('Input Background Color', 'net-sheet-calculator'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .nsc-input' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'calculated_field_background',
            [
                'label'     => esc_html__('Calculated Field Background', 'net-sheet-calculator'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#f5f5f5',
                'selectors' => [
                    '{{WRAPPER}} .nsc-input--calculated' => 'background-color: {{VALUE}}',
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
        $michigan_transfer_tax_rate = $settings['michigan_transfer_tax_rate'] ?? 3.75;        $revenue_stamps_rate = $settings['revenue_stamps_rate'] ?? 0.55;
        
        // Unique ID for this instance
        $calculator_id = 'nsc-' . uniqid();?>
        <div class="nsc-calculator" id="<?php echo esc_attr($calculator_id); ?>">
            <form class="nsc-form">
                <!-- Hidden fields for calculations -->
                <input type="hidden" data-field="comission_rate" value="<?php echo esc_attr($commission_rate); ?>">
                <input type="hidden" data-field="michigan_transfer_tax_rate" value="<?php echo esc_attr($michigan_transfer_tax_rate); ?>">
                <input type="hidden" data-field="revenue_stamps_rate" value="<?php echo esc_attr($revenue_stamps_rate); ?>">

                <!-- Transaction Summary Section -->
                <div class="nsc-section">
                    <h3 class="nsc-section__title"><?php echo esc_html__('Transaction Summary', 'net-sheet-calculator'); ?></h3>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-purchase-price">
                            <?php echo esc_html($settings['purchase_price_label'] ?? esc_html__('Purchase Price', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['purchase_price_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['purchase_price_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--currency"
                                id="<?php echo esc_attr($calculator_id); ?>-purchase-price"
                                name="purchase_price"
                                data-field="purchase_price"
                                placeholder="0">
                        </div>
                    </div>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-other-credits">
                            <?php echo esc_html($settings['other_credits_label'] ?? esc_html__('Other Credits to Seller', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['other_credits_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['other_credits_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--currency"
                                id="<?php echo esc_attr($calculator_id); ?>-other-credits"
                                name="other_credits"
                                data-field="other_credits"
                                placeholder="0">
                        </div>
                    </div>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-gross-proceeds">
                            <?php echo esc_html($settings['gross_proceeds_label'] ?? esc_html__('Total Estimated Gross Proceeds', 'net-sheet-calculator')); ?> <?php if (!empty($settings['gross_proceeds_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['gross_proceeds_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--calculated"
                                id="<?php echo esc_attr($calculator_id); ?>-gross-proceeds"
                                name="gross_proceeds"
                                data-field="gross_proceeds"
                                readonly>
                        </div>
                    </div>
                </div>

                <!-- Mortgage & Liens Section -->
                <div class="nsc-section">
                    <h3 class="nsc-section__title"><?php echo esc_html__('Mortgage & Liens', 'net-sheet-calculator'); ?></h3>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-mortgage-payoff">
                            <?php echo esc_html($settings['mortgage_payoff_label'] ?? esc_html__('Mortgage Payoff', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['mortgage_payoff_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['mortgage_payoff_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--currency"
                                id="<?php echo esc_attr($calculator_id); ?>-mortgage-payoff"
                                name="mortgage_payoff"
                                data-field="mortgage_payoff"
                                placeholder="0">
                        </div>
                    </div>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-other-mortgage-payoff">
                            <?php echo esc_html($settings['other_mortgage_payoff_label'] ?? esc_html__('Second Mortgage Payoff', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['other_mortgage_payoff_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['other_mortgage_payoff_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--currency"
                                id="<?php echo esc_attr($calculator_id); ?>-other-mortgage-payoff"
                                name="other_mortgage_payoff"
                                data-field="other_mortgage_payoff"
                                placeholder="0">
                        </div>
                    </div>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-special-assessment-payoff">
                            <?php echo esc_html($settings['special_assessment_payoff_label'] ?? esc_html__('Special Assessment Payoff', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['special_assessment_payoff_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['special_assessment_payoff_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--currency"
                                id="<?php echo esc_attr($calculator_id); ?>-special-assessment-payoff"
                                name="special_assessment_payoff"
                                data-field="special_assessment_payoff"
                                placeholder="0">
                        </div>
                    </div>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-lien-release-tracking-fee">
                            <?php echo esc_html($settings['lien_release_tracking_fee_label'] ?? esc_html__('Lien Release Tracking Fee', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['lien_release_tracking_fee_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['lien_release_tracking_fee_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--currency"
                                id="<?php echo esc_attr($calculator_id); ?>-lien-release-tracking-fee"
                                name="lien_release_tracking_fee"
                                data-field="lien_release_tracking_fee"
                                value="<?php echo esc_attr($lien_release_tracking_fee); ?>">
                        </div>
                    </div>
                </div>

                <!-- Taxes & Government Fees Section -->
                <div class="nsc-section">
                    <h3 class="nsc-section__title"><?php echo esc_html__('Taxes & Government Fees', 'net-sheet-calculator'); ?></h3>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-property-taxes-due">
                            <?php echo esc_html($settings['property_taxes_due_label'] ?? esc_html__('Property Taxes Due', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['property_taxes_due_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['property_taxes_due_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--currency"
                                id="<?php echo esc_attr($calculator_id); ?>-property-taxes-due"
                                name="property_taxes_due"
                                data-field="property_taxes_due"
                                placeholder="0">
                        </div>
                    </div>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-michigan-transfer-tax">
                            <?php echo esc_html($settings['michigan_transfer_tax_label'] ?? esc_html__('Michigan Transfer Tax', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['michigan_transfer_tax_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['michigan_transfer_tax_description']); ?></div>
                            <?php else: ?>
                                <small><?php echo esc_html__('(Purchase price/500) * 3.75', 'net-sheet-calculator'); ?></small>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--calculated"
                                id="<?php echo esc_attr($calculator_id); ?>-michigan-transfer-tax"
                                name="michigan_transfer_tax"
                                data-field="michigan_transfer_tax"
                                readonly>
                        </div>
                    </div>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-revenue-stamps">
                            <?php echo esc_html($settings['revenue_stamps_label'] ?? esc_html__('Revenue Stamps', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['revenue_stamps_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['revenue_stamps_description']); ?></div>
                            <?php else: ?>
                                <small><?php echo esc_html__('(Purchase price/500) * 0.55', 'net-sheet-calculator'); ?></small>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--calculated"
                                id="<?php echo esc_attr($calculator_id); ?>-revenue-stamps"
                                name="revenue_stamps"
                                data-field="revenue_stamps"
                                readonly>
                        </div>
                    </div>
                </div>

                <!-- Title & Closing Fees Section -->
                <div class="nsc-section">
                    <h3 class="nsc-section__title"><?php echo esc_html__('Title & Closing Fees', 'net-sheet-calculator'); ?></h3>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-settlement-fee">
                            <?php echo esc_html($settings['settlement_fee_label'] ?? esc_html__('Settlement Fee', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['settlement_fee_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['settlement_fee_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--currency"
                                id="<?php echo esc_attr($calculator_id); ?>-settlement-fee"
                                name="settlement_fee"
                                data-field="settlement_fee"
                                value="<?php echo esc_attr($settlement_fee); ?>">
                        </div>
                    </div>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-security-fee">
                            <?php echo esc_html($settings['security_fee_label'] ?? esc_html__('Security Fee', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['security_fee_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['security_fee_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--currency"
                                id="<?php echo esc_attr($calculator_id); ?>-security-fee"
                                name="security_fee"
                                data-field="security_fee"
                                value="<?php echo esc_attr($security_fee); ?>">
                        </div>
                    </div>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-title-insurance-policy">
                            <?php echo esc_html($settings['title_insurance_policy_label'] ?? esc_html__('Title Insurance Policy', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['title_insurance_policy_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['title_insurance_policy_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--currency"
                                id="<?php echo esc_attr($calculator_id); ?>-title-insurance-policy"
                                name="title_insurance_policy"
                                data-field="title_insurance_policy"
                                placeholder="0">
                        </div>
                    </div>
                </div>

                <!-- Agent Commissions Section -->
                <div class="nsc-section">
                    <h3 class="nsc-section__title"><?php echo esc_html__('Agent Commissions', 'net-sheet-calculator'); ?></h3>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-comission-realtor">
                            <?php echo esc_html($settings['comission_realtor_label'] ?? esc_html__('Commission Due Realtor (6% standard)', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['comission_realtor_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['comission_realtor_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--calculated"
                                id="<?php echo esc_attr($calculator_id); ?>-comission-realtor"
                                name="comission_realtor"
                                data-field="comission_realtor"
                                readonly>
                        </div>
                    </div>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-comission-realtor-extra">
                            <?php echo esc_html($settings['comission_realtor_extra_label'] ?? esc_html__('Commission Due Realtor (Extra $)', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['comission_realtor_extra_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['comission_realtor_extra_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--currency"
                                id="<?php echo esc_attr($calculator_id); ?>-comission-realtor-extra"
                                name="comission_realtor_extra"
                                data-field="comission_realtor_extra"
                                placeholder="0">
                        </div>
                    </div>
                </div>

                <!-- Utilities & HOA Section -->
                <div class="nsc-section">
                    <h3 class="nsc-section__title"><?php echo esc_html__('Utilities & HOA', 'net-sheet-calculator'); ?></h3>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-current-water">
                            <?php echo esc_html($settings['current_water_label'] ?? esc_html__('Current Water/Sewer', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['current_water_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['current_water_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--currency"
                                id="<?php echo esc_attr($calculator_id); ?>-current-water"
                                name="current_water"
                                data-field="current_water"
                                placeholder="0">
                        </div>
                    </div>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-hoa-assessment">
                            <?php echo esc_html($settings['hoa_assessment_label'] ?? esc_html__('HOA Assessment', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['hoa_assessment_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['hoa_assessment_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--currency"
                                id="<?php echo esc_attr($calculator_id); ?>-hoa-assessment"
                                name="hoa_assessment"
                                data-field="hoa_assessment"
                                placeholder="0">
                        </div>
                    </div>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-water-escrow">
                            <?php echo esc_html($settings['water_escrow_label'] ?? esc_html__('Water Escrow (Minimum $300)', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['water_escrow_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['water_escrow_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--currency"
                                id="<?php echo esc_attr($calculator_id); ?>-water-escrow"
                                name="water_escrow"
                                data-field="water_escrow"
                                min="300"
                                value="<?php echo esc_attr($water_escrow); ?>">
                        </div>
                    </div>
                </div>

                <!-- Miscellaneous Seller Costs Section -->
                <div class="nsc-section">
                    <h3 class="nsc-section__title"><?php echo esc_html__('Miscellaneous Seller Costs', 'net-sheet-calculator'); ?></h3>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-home-warranty">
                            <?php echo esc_html($settings['home_warranty_label'] ?? esc_html__('Home Warranty', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['home_warranty_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['home_warranty_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--currency"
                                id="<?php echo esc_attr($calculator_id); ?>-home-warranty"
                                name="home_warranty"
                                data-field="home_warranty"
                                placeholder="0">
                        </div>
                    </div>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-fha">
                            <?php echo esc_html($settings['fha_label'] ?? esc_html__('FHA or VA Seller Paid Fees', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['fha_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['fha_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--currency"
                                id="<?php echo esc_attr($calculator_id); ?>-fha"
                                name="fha"
                                data-field="fha"
                                placeholder="0">
                        </div>
                    </div>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-misc-cost-seller">
                            <?php echo esc_html($settings['misc_cost_seller_label'] ?? esc_html__('Misc Cost to Seller', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['misc_cost_seller_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['misc_cost_seller_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--currency"
                                id="<?php echo esc_attr($calculator_id); ?>-misc-cost-seller"
                                name="misc_cost_seller"
                                data-field="misc_cost_seller"
                                placeholder="0">
                        </div>
                    </div>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-seller-attorney-fee">
                            <?php echo esc_html($settings['seller_attorney_fee_label'] ?? esc_html__('Seller\'s Attorney Fee', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['seller_attorney_fee_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['seller_attorney_fee_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--currency"
                                id="<?php echo esc_attr($calculator_id); ?>-seller-attorney-fee"
                                name="seller_attorney_fee"
                                data-field="seller_attorney_fee"
                                placeholder="0">
                        </div>
                    </div>
                </div>

                <!-- Final Totals Section -->
                <div class="nsc-section">
                    <h3 class="nsc-section__title"><?php echo esc_html__('Final Totals', 'net-sheet-calculator'); ?></h3>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-total-closing-costs">
                            <?php echo esc_html($settings['total_closing_costs_label'] ?? esc_html__('Total Closing Costs', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['total_closing_costs_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['total_closing_costs_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--calculated"
                                id="<?php echo esc_attr($calculator_id); ?>-total-closing-costs"
                                name="total_closing_costs"
                                data-field="total_closing_costs"
                                readonly>
                        </div>
                    </div>
                    <div class="nsc-field">
                        <label class="nsc-field__label" for="<?php echo esc_attr($calculator_id); ?>-estimated-net-proceeds">
                            <?php echo esc_html($settings['estimated_net_proceeds_label'] ?? esc_html__('Estimated Net Proceeds', 'net-sheet-calculator')); ?>
                            <?php if (!empty($settings['estimated_net_proceeds_description'])): ?>
                                <div class="nsc-field__description"><?php echo esc_html($settings['estimated_net_proceeds_description']); ?></div>
                            <?php endif; ?>
                        </label>
                        <div class="nsc-field__input-wrap">
                            <input type="text"
                                class="nsc-input nsc-input--calculated"
                                id="<?php echo esc_attr($calculator_id); ?>-estimated-net-proceeds"
                                name="estimated_net_proceeds"
                                data-field="estimated_net_proceeds"
                                readonly>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="nsc-actions">
                    <button type="button" class="nsc-button nsc-button--download"><?php echo esc_html__('Download PDF', 'net-sheet-calculator'); ?></button>
                    <div class="nsc-email-form">
                        <input type="email" id="<?php echo esc_attr($calculator_id); ?>-email" name="email" placeholder="<?php echo esc_attr__('Enter your email address', 'net-sheet-calculator'); ?>">
                        <button type="button" class="nsc-button nsc-button--send"><?php echo esc_html__('Send via email', 'net-sheet-calculator'); ?></button>
                    </div>
                </div>
            </form>
        </div>
<?php
    }
}
