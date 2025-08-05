<?php
/**
 * Buyer Estimated Cash to Close Calculator Widget
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Buyer_Cash_To_Close_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'buyer_cash_to_close_widget';
    }

    public function get_title() {
        return esc_html__('Buyer Cash To Close Calculator', 'real-estate-closing-calculator');
    }

    public function get_icon() {
        return 'eicon-price-list';
    }

    public function get_categories() {
        return ['general'];
    }

    public function get_keywords() {
        return ['real estate', 'calculator', 'buyer'];
    }   
    
    public function get_script_depends() {
        return ['recc-utils', 'recc-pdf-generator', 'recc-email-handler', 'recc-message-handler', 'buyer-cash-to-close-script'];
    }

    public function get_style_depends() {
        return ['recc-style'];
    }

    protected function register_controls() {
        // Section for Default Values
        $this->start_controls_section(
            'section_company_info',
            [
                'label' => esc_html__('Company Information', 'real-estate-closing-calculator'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'address_line1',
            [
                'label'       => esc_html__('Address Line 1', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '3250 W Big Beaver Rd #312,',
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'address_line2',
            [
                'label'       => esc_html__('Address Line 2', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => 'Troy, MI 48084',
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'phone_number',
            [
                'label'       => esc_html__('Phone Number', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '(248) 792-2096',
                'frontend_available' => true,
            ]
        );

        $this->end_controls_section();

        // Section for Default Values
        $this->start_controls_section(
            'section_defaults',
            [
                'label' => esc_html__('Default Values', 'real-estate-closing-calculator'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'title_closing_defaults_heading',
            [
                'label'       => esc_html__('Default fee costs', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );
        $this->add_control(
            'default_settlement_fee',
            [
                'label'       => esc_html__('Settlement Fee', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 350,
            ]
        );
        $this->add_control(
            'default_security_fee',
            [
                'label'       => esc_html__('Security Fee', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 55,
            ]
        );
        $this->add_control(
            'default_erecording_fee',
            [
                'label'       => esc_html__('eRecording Fee', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 50,
            ]
        );

        $this->end_controls_section();

        // Loan Policy Rates Section
        $this->start_controls_section(
            'section_loan_policy',
            [
                'label' => esc_html__('Loan Policy Rates', 'real-estate-closing-calculator'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control('insurance_range_from', [
            'label' => 'From ($)',
            'type' => \Elementor\Controls_Manager::NUMBER,
            'min' => 0,
            'step' => 1,
            'default' => 0,
        ]);

        $repeater->add_control('insurance_range_to', [
            'label' => 'To ($)',
            'type' => \Elementor\Controls_Manager::NUMBER,
            'min' => 0,
            'step' => 1,
            'default' => 100000,
        ]);

        $repeater->add_control('insurance_rate', [
            'label' => 'Rate',
            'type' => \Elementor\Controls_Manager::NUMBER,
            'step' => 0.01,
            'default' => 0,
        ]);

        $repeater->add_control('insurance_is_fixed', [
            'label' => 'Fixed Rate or Per $1000',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => 'Fixed',
            'label_off' => '$1000',
            'return_value' => 'yes',
            'default' => 'no',
        ]);       
        $this->add_control('loan_policy_rates', [
            'label' => '',
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'title_field' => '{{ insurance_range_from ? insurance_range_from : "0" }} - {{ insurance_range_to ? insurance_range_to : " Infinity" }}: ${{ insurance_rate ? insurance_rate : "0" }}{{ insurance_is_fixed === "yes" ? " fixed" : " per $1000" }}',
            'prevent_empty' => true,
            'frontend_available' => true,
            'default' => [
                [
                    'insurance_range_from' => 0,
                    'insurance_range_to' => 20000,
                    'insurance_rate' => 375,
                    'insurance_is_fixed' => 'yes',
                ],
                [
                    'insurance_range_from' => 20001,
                    'insurance_range_to' => 100000,
                    'insurance_rate' => 3.75,
                    'insurance_is_fixed' => 'no',
                ],
                [
                    'insurance_range_from' => 100001,
                    'insurance_range_to' => 200000,
                    'insurance_rate' => 2.68,
                    'insurance_is_fixed' => 'no',
                ],
                [
                    'insurance_range_from' => 200001,
                    'insurance_range_to' => 300000,
                    'insurance_rate' => 2.41,
                    'insurance_is_fixed' => 'no',
                ],
                [
                    'insurance_range_from' => 300001,
                    'insurance_range_to' => 1000000,
                    'insurance_rate' => 1.88,
                    'insurance_is_fixed' => 'no',
                ],
                [
                    'insurance_range_from' => 1000001,
                    'insurance_range_to' => '',
                    'insurance_rate' => 1.50,
                    'insurance_is_fixed' => 'no',
                ],
            ],
        ]);
        $this->end_controls_section();


        // Transaction Summary Fields
        $this->start_controls_section(
            'section_transaction_summary_fields',
            [
                'label' => esc_html__('Transaction Summary Fields', 'real-estate-closing-calculator'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'purchase_price_heading',
            [
                'label'       => esc_html__('Purchase Price', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );
        $this->add_control(
            'purchase_price_label',
            [
                'label'       => esc_html__('Label', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Purchase Price', 'real-estate-closing-calculator'),
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'purchase_price_description',
            [
                'label'       => esc_html__('Description', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );
        $this->add_control(
            'county_heading',
            [
                'label'       => esc_html__('County', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );
        $this->add_control(
            'county_label',
            [
                'label'       => esc_html__('Label', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('County', 'real-estate-closing-calculator'),
                'frontend_available' => true,
            ]
        );

        $this->end_controls_section();

        // Transaction Credits Fields
        $this->start_controls_section(
            'section_transaction_credits_fields ',
            [
                'label' => esc_html__('Transaction Credits Fields', 'real-estate-closing-calculator'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'loan_amount_heading',
            [
                'label'       => esc_html__('Loan Amount', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );
        $this->add_control(
            'loan_amount_label',
            [
                'label'       => esc_html__('Label', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Loan Amount', 'real-estate-closing-calculator'),
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'loan_amount_description',
            [
                'label'       => esc_html__('Description', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );
        $this->add_control(
            'earnest_money_deposit_heading',
            [
                'label'       => esc_html__('Earnest Money Deposit', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );
        $this->add_control(
            'earnest_money_deposit_label',
            [
                'label'       => esc_html__('Label', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Earnest Money Deposit', 'real-estate-closing-calculator'),
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'earnest_money_deposit_description',
            [
                'label'       => esc_html__('Description', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );
        $this->add_control(
            'seller_credit_heading',
            [
                'label'       => esc_html__('Seller Credit', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );
        $this->add_control(
            'seller_credit_label',
            [
                'label'       => esc_html__('Label', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Seller Credit', 'real-estate-closing-calculator'),
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'seller_credit_description',
            [
                'label'       => esc_html__('Description', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );
        $this->add_control(
            'agent_credit_heading',
            [
                'label'       => esc_html__('Agent Credit', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );
        $this->add_control(
            'agent_credit_label',
            [
                'label'       => esc_html__('Label', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Agent Credit', 'real-estate-closing-calculator'),
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'agent_credit_description',
            [
                'label'       => esc_html__('Description', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );

        $this->end_controls_section();

        
        // Title/settlement costs Fields
        $this->start_controls_section(
            'section_title_closing_fields',
            [
                'label' => esc_html__('Title/settlement Costs Fields', 'real-estate-closing-calculator'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'loan_insurance_policy_heading',
            [
                'label'       => esc_html__('Loan Policy', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );
        $this->add_control(
            'loan_insurance_policy_label',
            [
                'label'       => esc_html__('Label', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Loan Policy', 'real-estate-closing-calculator'),
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'loan_insurance_policy_description',
            [
                'label'       => esc_html__('Description', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );      
        $this->add_control(
            'settlement_fee_heading',
            [
                'label'       => esc_html__('Settlement Fee', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );
        $this->add_control(
            'settlement_fee_label',
            [
                'label'       => esc_html__('Label', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Title/settlement costs', 'real-estate-closing-calculator'),
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'settlement_fee_description',
            [
                'label'       => esc_html__('Description', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'security_fee_heading',
            [
                'label'       => esc_html__('Security Fee', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );
        $this->add_control(
            'security_fee_label',
            [
                'label'       => esc_html__('Label', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Security Fee', 'real-estate-closing-calculator'),
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'security_fee_description',
            [
                'label'       => esc_html__('Description', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );
        $this->end_controls_section();

        // Recording CostsFields
        $this->start_controls_section(
            'section_recording_costs_fields',
            [
                'label' => esc_html__('Recording CostsFields', 'real-estate-closing-calculator'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'erecording_fee_heading',
            [
                'label'       => esc_html__('eRrecording Fee', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );
        $this->add_control(
            'erecording_fee_label',
            [
                'label'       => esc_html__('Label', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('eRrecording Fee', 'real-estate-closing-calculator'),
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'erecording_fee_description',
            [
                'label'       => esc_html__('Description', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );
        $this->add_control(
            'mortgage_recording_fee_heading',
            [
                'label'       => esc_html__('Mortgage Recording Fee', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );
        $this->add_control(
            'mortgage_recording_fee_label',
            [
                'label'       => esc_html__('Label', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Mortgage Recording Fee', 'real-estate-closing-calculator'),
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'mortgage_recording_fee_description',
            [
                'label'       => esc_html__('Description', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );
        $this->add_control(
            'warranty_deed_recording_fee_heading',
            [
                'label'       => esc_html__('Warranty Deed Recording Fee', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );
        $this->add_control(
            'warranty_deed_recording_fee_label',
            [
                'label'       => esc_html__('Label', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Warranty Deed Recording Fee', 'real-estate-closing-calculator'),
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'warranty_deed_recording_fee_description',
            [
                'label'       => esc_html__('Description', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );        
        $this->end_controls_section();

        // Other Costs Fields
        $this->start_controls_section(
            'section_other_closing_costs_fields',
            [
                'label' => esc_html__('Other Closing costs', 'real-estate-closing-calculator'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'tax_proration_estimate_heading',
            [
                'label'       => esc_html__('Tax Proration Estimate', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );
        $this->add_control(
            'tax_proration_estimate_label',
            [
                'label'       => esc_html__('Label', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Tax Proration Estimate', 'real-estate-closing-calculator'),
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'tax_proration_estimate_description',
            [
                'label'       => esc_html__('Description', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );
        $this->add_control(
            'realtor_compliance_fee_heading',
            [
                'label'       => esc_html__('Realtor Compliance Fee', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );
        $this->add_control(
            'realtor_compliance_fee_label',
            [
                'label'       => esc_html__('Label', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Realtor Compliance Fee', 'real-estate-closing-calculator'),
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'realtor_compliance_fee_description',
            [
                'label'       => esc_html__('Description', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );
        $this->add_control(
            'hoa_transfer_fee_heading',
            [
                'label'       => esc_html__('HOA Transfer Fee', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );
        $this->add_control(
            'hoa_transfer_fee_label',
            [
                'label'       => esc_html__('Label', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('HOA Transfer Fee', 'real-estate-closing-calculator'),
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'hoa_transfer_fee_description',
            [
                'label'       => esc_html__('Description', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );        
        $this->add_control(
            'hoa_prepaid_assessment_heading',
            [
                'label'       => esc_html__('HOA Prepaid Assessment', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );
        $this->add_control(
            'hoa_prepaid_assessment_label',
            [
                'label'       => esc_html__('Label', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('HOA Prepaid Assessment', 'real-estate-closing-calculator'),
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'hoa_prepaid_assessment_description',
            [
                'label'       => esc_html__('Description', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );        
        $this->add_control(
            'other_fees_heading',
            [
                'label'       => esc_html__('Other', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );
        $this->add_control(
            'other_fees_label',
            [
                'label'       => esc_html__('Label', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Other', 'real-estate-closing-calculator'),
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'other_fees_description',
            [
                'label'       => esc_html__('Description', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
            ]
        );        
        $this->end_controls_section();


        // Final Totals Fields
        $this->start_controls_section(
            'section_final_totals_fields',
            [
                'label' => esc_html__('Final Totals Fields', 'real-estate-closing-calculator'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'total_closing_costs_heading',
            [
                'label'       => esc_html__('Total Closing Costs', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'total_closing_costs_label',
            [
                'label'       => esc_html__('Label', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Total Closing Costs', 'real-estate-closing-calculator'),
                'frontend_available' => true,
            ]
        );


        $this->add_control(
            'estimated_net_proceeds_heading',
            [
                'label'       => esc_html__('Estimated Net Proceeds', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'estimated_net_proceeds_label',
            [
                'label'       => esc_html__('Label', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Estimated Net Proceeds', 'real-estate-closing-calculator'),
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'total_disclaimer_text_heading',
            [
                'label'       => esc_html__('Disclaimer Text', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::HEADING,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'total_disclaimer_text',
            [
                'label'       => esc_html__('Add disclaimer text', 'real-estate-closing-calculator'),
                'type'        => \Elementor\Controls_Manager::WYSIWYG,
                'default'     => esc_html__('Disclaimer:
                                                Lorem ipsum...', 'real-estate-closing-calculator'),
            ]
        );
    

        $this->end_controls_section();

        // Section for Form Styling
        $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__('Form Style', 'real-estate-closing-calculator'),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'form_typography',
                'selector' => '{{WRAPPER}} .real-estate-closing-calculator',
            ]
        );

        $this->add_control(
            'section_heading_color',
            [
                'label'     => esc_html__('Section Heading Color', 'real-estate-closing-calculator'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .recc-section__title' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'label_color',
            [
                'label'     => esc_html__('Field Label Color', 'real-estate-closing-calculator'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#555555',
                'selectors' => [
                    '{{WRAPPER}} .recc-field__label' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'input_background_color',
            [
                'label'     => esc_html__('Input Background Color', 'real-estate-closing-calculator'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .recc-input' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'calculated_field_background',
            [
                'label'     => esc_html__('Calculated Field Background', 'real-estate-closing-calculator'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#f5f5f5',
                'selectors' => [
                    '{{WRAPPER}} .recc-input--calculated' => 'background-color: {{VALUE}}',
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
        $settlement_fee = $settings['default_settlement_fee'];
        $security_fee = $settings['default_security_fee'];
        $erecording_fee = $settings['default_erecording_fee'];
        
        // Unique ID for this instance
        $buyer_calculator_id = 'recc-' . uniqid();?>
        <div class="recc-calculator" id="<?php echo esc_attr($buyer_calculator_id); ?>">
            <form class="recc-form">
                <div class="recc-fields-wrap">
                    <!-- Hidden fields for calculations -->
                    <!-- <input type="hidden" data-field="michigan_transfer_tax_rate" value="<?php //echo esc_attr($michigan_transfer_tax_rate); ?>"> -->
                    <!-- <input type="hidden" data-field="revenue_stamps_rate" value="<?php //echo esc_attr($revenue_stamps_rate); ?>"> -->
                    <!-- Transaction Summary Section -->
                    <div class="recc-section">
                        <h5 class="recc-section__title"><?php echo esc_html__('Transaction Summary', 'real-estate-closing-calculator'); ?></h5>
                        <div class="recc-field">
                            <label class="recc-field__label" for="<?php echo esc_attr($buyer_calculator_id); ?>-purchase-price">
                                <?php echo esc_html($settings['purchase_price_label']); ?>
                                <?php if (!empty($settings['purchase_price_description'])): ?>
                                    <div class="recc-field__description"><?php echo esc_html($settings['purchase_price_description']); ?></div>
                                <?php endif; ?>
                            </label>
                            <div class="recc-field__input-wrap">                   
                                <input type="text"
                                    class="recc-input recc-input--currency"
                                    id="<?php echo esc_attr($buyer_calculator_id); ?>-purchase-price"
                                    name="purchase_price"
                                    data-field="purchase_price"
                                    placeholder="$0"
                                    max="9999999999">
                            </div>
                        </div>

                        <div class="recc-field">
                            <label class="recc-field__label" for="<?php echo esc_attr($buyer_calculator_id); ?>-county">
                                <?php echo esc_html($settings['county_label']); ?>
                            </label>
                            <div class="recc-field__input-wrap">       
                            <?php 
                                $counties = [
                                    'alcona' => [
                                        'name' => 'Alcona',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'alger' => [
                                        'name' => 'Alger',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'allegan' => [
                                        'name' => 'Allegan',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'alpena' => [
                                        'name' => 'Alpena',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'antrim' => [
                                        'name' => 'Antrim',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'arena' => [
                                        'name' => 'Arenac',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'baraga' => [
                                        'name' => 'Baraga',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'barry' => [
                                        'name' => 'Barry',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'bay' => [
                                        'name' => 'Bay',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'benzie' => [
                                        'name' => 'Benzie',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'berrien' => [
                                        'name' => 'Berrien',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'branch' => [
                                        'name' => 'Branch',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'calhoun' => [
                                        'name' => 'Calhoun',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'cass' => [
                                        'name' => 'Cass',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'charlevoix' => [
                                        'name' => 'Charlevoix',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'cheboygan' => [
                                        'name' => 'Cheboygan',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'chippewa' => [
                                        'name' => 'Chippewa',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'clare' => [
                                        'name' => 'Clare',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'clinton' => [
                                        'name' => 'Clinton',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'crawford' => [
                                        'name' => 'Crawford',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'delta' => [
                                        'name' => 'Delta',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'dickinson' => [
                                        'name' => 'Dickinson',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'eaton' => [
                                        'name' => 'Eaton',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'emmet' => [
                                        'name' => 'Emmet',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'genesee' => [
                                        'name' => 'Genesee',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'gladwin' => [
                                        'name' => 'Gladwin',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'gogebic' => [
                                        'name' => 'Gogebic',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'grand_traverse' => [
                                        'name' => 'Grand Traverse',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'gratiot' => [
                                        'name' => 'Gratiot',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'hillsdale' => [
                                        'name' => 'Hillsdale',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'houghton' => [
                                        'name' => 'Houghton',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'hu ron' => [
                                        'name' => 'Huron',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'ingham' => [
                                        'name' => 'Ingham',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'ionia' => [
                                        'name' => 'Ionia',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'iosco' => [
                                        'name' => 'Iosco',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'iron' => [
                                        'name' => 'Iron',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'isabella' => [
                                        'name' => 'Isabella',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'jackson' => [
                                        'name' => 'Jackson',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'kalamazoo' => [
                                        'name' => 'Kalamazoo',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'kalkaska' => [
                                        'name' => 'Kalkaska',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'kent' => [
                                        'name' => 'Kent',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'keweenaw' => [
                                        'name' => 'Keweenaw',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'lake' => [
                                        'name' => 'Lake',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'lapeer' => [
                                        'name' => 'Lapeer',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'leelanau' => [
                                        'name' => 'Leelanau',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'lenawee' => [
                                        'name' => 'Lenawee',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'livingston' => [
                                        'name' => 'Livingston',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'luce' => [
                                        'name' => 'Luce',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'mackinac' => [
                                        'name' => 'Mackinac',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'macomb' => [
                                        'name' => 'Macomb',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'manistee' => [
                                        'name' => 'Manistee',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'marquette' => [
                                        'name' => 'Marquette',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'mason' => [
                                        'name' => 'Mason',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'mecosta' => [
                                        'name' => 'Mecosta',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'menominee' => [
                                        'name' => 'Menominee',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'midland' => [
                                        'name' => 'Midland',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'missaukee' => [
                                        'name' => 'Missaukee',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'monroe' => [
                                        'name' => 'Monroe',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'montcalm' => [
                                        'name' => 'Montcalm',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'montmorency' => [
                                        'name' => 'Montmorency',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'muskegon' => [
                                        'name' => 'Muskegon',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'newaygo' => [
                                        'name' => 'Newaygo',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'oakland' => [
                                        'name' => 'Oakland',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'oceana' => [
                                        'name' => 'Oceana',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'ogemaw' => [
                                        'name' => 'Ogemaw',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'ontonagon' => [
                                        'name' => 'Ontonagon',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'osceola' => [
                                        'name' => 'Osceola',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'oscoda' => [
                                        'name' => 'Oscoda',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'otsego' => [
                                        'name' => 'Otsego',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'ottawa' => [
                                        'name' => 'Ottawa',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'presque_isle' => [
                                        'name' => 'Presque Isle',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'roscommon' => [
                                        'name' => 'Roscommon',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'saginaw' => [
                                        'name' => 'Saginaw',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'sanilac' => [
                                        'name' => 'Sanilac',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'schoolcraft' => [
                                        'name' => 'Schoolcraft',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'shiawassee' => [
                                        'name' => 'Shiawassee',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'st_clair' => [
                                        'name' => 'St. Clair',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'st_joseph' => [
                                        'name' => 'St. Joseph',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'tuscola' => [
                                        'name' => 'Tuscola',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'van_buren' => [
                                        'name' => 'Van Buren',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'washtenaw' => [
                                        'name' => 'Washtenaw',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                    'wayne' => [
                                        'name' => 'Wayne',
                                        'mortgage_fee' => 96,
                                        'warranty_fee' => 23
                                    ],
                                    'wexford' => [
                                        'name' => 'Wexford',
                                        'mortgage_fee' => 30,
                                        'warranty_fee' => 35
                                    ],
                                ];
                            ?>
                                <select id="county-select" name="county">
                                    <?php foreach ($counties as $slug => $data) {
                                        // Pass fees in data attributes so JS can use them
                                        echo '<option value="' . esc_attr($slug) . '" 
                                            data-mortgage="' . esc_attr($data['mortgage_fee']) . '" 
                                            data-warranty="' . esc_attr($data['warranty_fee']) . '">'
                                            . esc_html($data['name']) . 
                                            '</option>';
                                    }?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Credits Section -->
                    <div class="recc-section">
                        <h5 class="recc-section__title"><?php echo esc_html__('Transaction Credits', 'real-estate-closing-calculator'); ?></h5>
                        <div class="recc-field">
                            <label class="recc-field__label" for="<?php echo esc_attr($buyer_calculator_id); ?>-loan-amount">
                                <?php echo esc_html($settings['loan_amount_label']); ?>
                                <?php if (!empty($settings['loan_amount_description'])): ?>
                                    <div class="recc-field__description"><?php echo esc_html($settings['loan_amount_description']); ?></div>
                                <?php endif; ?>
                            </label>
                            <div class="recc-field__input-wrap">
                                <input type="text"
                                    class="recc-input recc-input--currency"
                                    id="<?php echo esc_attr($buyer_calculator_id); ?>-loan-amount"
                                    name="loan_amount"
                                    data-field="loan_amount"
                                    placeholder="$0">
                            </div>
                        </div>
                        <div class="recc-field">
                            <label class="recc-field__label" for="<?php echo esc_attr($buyer_calculator_id); ?>-earnest-money-deposit">
                                <?php echo esc_html($settings['earnest_money_deposit_label']); ?>
                                <?php if (!empty($settings['earnest_money_deposit_description'])): ?>
                                    <div class="recc-field__description"><?php echo esc_html($settings['earnest_money_deposit_description']); ?></div>
                                <?php endif; ?>
                            </label>
                            <div class="recc-field__input-wrap">
                                <input type="text"
                                    class="recc-input recc-input--currency"
                                    id="<?php echo esc_attr($buyer_calculator_id); ?>-earnest-money-deposit"
                                    name="earnest_money_deposit"
                                    data-field="earnest_money_deposit"
                                    placeholder="$0">
                            </div>
                        </div>
                        <div class="recc-field">
                            <label class="recc-field__label" for="<?php echo esc_attr($buyer_calculator_id); ?>-seller-credit">
                                <?php echo esc_html($settings['seller_credit_label']); ?>
                                <?php if (!empty($settings['seller_credit_description'])): ?>
                                    <div class="recc-field__description"><?php echo esc_html($settings['seller_credit_description']); ?></div>
                                <?php endif; ?>
                            </label>
                            <div class="recc-field__input-wrap">
                                <input type="text"
                                    class="recc-input recc-input--currency"
                                    id="<?php echo esc_attr($buyer_calculator_id); ?>-seller-credit"
                                    name="seller_credit"
                                    data-field="seller_credit"
                                    placeholder="$0">
                            </div>
                        </div>
                        <div class="recc-field">
                            <label class="recc-field__label" for="<?php echo esc_attr($buyer_calculator_id); ?>-agent-credit">
                                <?php echo esc_html($settings['agent_credit_label']); ?>
                                <?php if (!empty($settings['agent_credit_description'])): ?>
                                    <div class="recc-field__description"><?php echo esc_html($settings['agent_credit_description']); ?></div>
                                <?php endif; ?>
                            </label>
                            <div class="recc-field__input-wrap">
                                <input type="text"
                                    class="recc-input recc-input--currency"
                                    id="<?php echo esc_attr($buyer_calculator_id); ?>-agent-credit"
                                    name="agent_credit"
                                    data-field="agent_credit"
                                    placeholder="$0">
                            </div>
                        </div>
                    </div>

                    <!-- Title/settlement costs Section -->
                    <div class="recc-section">
                        <h5 class="recc-section__title"><?php echo esc_html__('Title/settlement costs', 'real-estate-closing-calculator'); ?></h5>
                        <div class="recc-field">
                            <label class="recc-field__label" for="<?php echo esc_attr($buyer_calculator_id); ?>-loan-insurance-policy">
                                <?php echo esc_html($settings['loan_insurance_policy_label']); ?>
                                <?php if (!empty($settings['loan_insurance_policy_description'])): ?>
                                    <div class="recc-field__description"><?php echo $settings['loan_insurance_policy_description']; ?></div>
                                <?php endif; ?>
                            </label>
                            <div class="recc-field__input-wrap">
                                <input type="text"
                                    class="recc-input recc-input--calculated"
                                    id="<?php echo esc_attr($buyer_calculator_id); ?>-loan-insurance-policy"
                                    name="loan_insurance_policy"
                                    data-field="loan_insurance_policy"
                                    placeholder="$0"
                                    readonly>
                            </div>
                        </div>
                        <div class="recc-field">
                            <label class="recc-field__label" for="<?php echo esc_attr($buyer_calculator_id); ?>-settlement-fee">
                                <?php echo esc_html($settings['settlement_fee_label']); ?>
                                <?php if (!empty($settings['settlement_fee_description'])): ?>
                                    <div class="recc-field__description"><?php echo esc_html($settings['settlement_fee_description']); ?></div>
                                <?php endif; ?>
                            </label>
                            <div class="recc-field__input-wrap">
                                <input type="text"
                                    class="recc-input recc-input--currency"
                                    id="<?php echo esc_attr($buyer_calculator_id); ?>-settlement-fee"
                                    name="settlement_fee"
                                    data-field="settlement_fee"
                                    value="$<?php echo esc_attr($settlement_fee); ?>">
                            </div>
                        </div>
                        <div class="recc-field">
                            <label class="recc-field__label" for="<?php echo esc_attr($buyer_calculator_id); ?>-security-fee">
                                <?php echo esc_html($settings['security_fee_label']); ?>
                                <?php if (!empty($settings['security_fee_description'])): ?>
                                    <div class="recc-field__description"><?php echo esc_html($settings['security_fee_description']); ?></div>
                                <?php endif; ?>
                            </label>
                            <div class="recc-field__input-wrap">
                                <input type="text"
                                    class="recc-input recc-input--currency"
                                    id="<?php echo esc_attr($buyer_calculator_id); ?>-security-fee"
                                    name="security_fee"
                                    data-field="security_fee"
                                    value="$<?php echo esc_attr($security_fee); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Recording Costs Section -->
                    <div class="recc-section">
                        <h5 class="recc-section__title"><?php echo esc_html__('Recording Costs', 'real-estate-closing-calculator'); ?></h5>
                        <div class="recc-field">
                            <label class="recc-field__label" for="<?php echo esc_attr($buyer_calculator_id); ?>-erecording-fee">
                                <?php echo esc_html($settings['erecording_fee_label']); ?>
                                <?php if (!empty($settings['erecording_fee_description'])): ?>
                                    <div class="recc-field__description"><?php echo esc_html($settings['erecording_fee_description']); ?></div>
                                <?php endif; ?>
                            </label>
                            <div class="recc-field__input-wrap">
                                <input type="text"
                                    class="recc-input recc-input--currency"
                                    id="<?php echo esc_attr($buyer_calculator_id); ?>-erecording-fee"
                                    name="erecording_fee"
                                    data-field="erecording_fee"
                                    value="$<?php echo esc_attr($erecording_fee); ?>" >
                            </div>
                        </div>
                        <div class="recc-field">
                            <!-- depends on county -->
                            <label class="recc-field__label" for="<?php echo esc_attr($buyer_calculator_id); ?>-mortgage-recording-fee">
                                <?php echo esc_html($settings['mortgage_recording_fee_label']); ?>
                                <?php if (!empty($settings['mortgage_recording_fee_description'])): ?>
                                    <div class="recc-field__description"><?php echo esc_html($settings['mortgage_recording_fee_description']); ?></div>
                                <?php endif; ?>
                            </label>
                            <div class="recc-field__input-wrap">
                                <input type="text"
                                    class="recc-input recc-input--calculated"
                                    id="<?php echo esc_attr($buyer_calculator_id); ?>-mortgage-recording-fee"
                                    name="mortgage_recording_fee"
                                    data-field="mortgage_recording_fee"
                                    placeholder="$0">
                            </div>
                        </div>
                        <div class="recc-field">
                            <!-- depends on county -->
                            <label class="recc-field__label" for="<?php echo esc_attr($buyer_calculator_id); ?>-warranty-deed-recording-fee">
                                <?php echo esc_html($settings['warranty_deed_recording_fee_label']); ?>
                                <?php if (!empty($settings['warranty_deed_recording_fee_description'])): ?>
                                    <div class="recc-field__description"><?php echo esc_html($settings['warranty_deed_recording_fee_description']); ?></div>
                                <?php endif; ?>
                            </label>
                            <div class="recc-field__input-wrap">
                                <input type="text"
                                    class="recc-input recc-input--calculated"
                                    id="<?php echo esc_attr($buyer_calculator_id); ?>-warranty-deed-recording-fee"
                                    name="warranty_deed_recording_fee"
                                    data-field="warranty_deed_recording_fee"
                                    placeholder="$0">
                            </div>
                        </div>
                    </div>
                    <!-- Other closing costs -->
                    <div class="recc-section">
                        <h5 class="recc-section__title"><?php echo esc_html__('Other closing costs', 'real-estate-closing-calculator'); ?></h5>
                        <div class="recc-field">
                            <label class="recc-field__label" for="<?php echo esc_attr($buyer_calculator_id); ?>-tax-proration-estimate">
                                <?php echo esc_html($settings['tax_proration_estimate_label']); ?>
                                <?php if (!empty($settings['tax_proration_estimate_description'])): ?>
                                    <div class="recc-field__description"><?php echo esc_html($settings['tax_proration_estimate_description']); ?></div>
                                <?php endif; ?>
                            </label>
                            <div class="recc-field__input-wrap">
                                <input type="text"
                                    class="recc-input recc-input--currency"
                                    id="<?php echo esc_attr($buyer_calculator_id); ?>-tax-proration-estimate"
                                    name="tax_proration_estimate"
                                    data-field="tax_proration_estimate"
                                    placeholder="$0">
                            </div>
                        </div>
                        <div class="recc-field">
                            <label class="recc-field__label" for="<?php echo esc_attr($buyer_calculator_id); ?>-realtor-compliance-fee">
                                <?php echo esc_html($settings['realtor_compliance_fee_label']); ?>
                                <?php if (!empty($settings['realtor_compliance_fee_description'])): ?>
                                    <div class="recc-field__description"><?php echo esc_html($settings['realtor_compliance_fee_description']); ?></div>
                                <?php endif; ?>
                            </label>
                            <div class="recc-field__input-wrap">
                                <input type="text"
                                    class="recc-input recc-input--currency"
                                    id="<?php echo esc_attr($buyer_calculator_id); ?>-realtor-compliance-fee"
                                    name="realtor_compliance_fee"
                                    data-field="realtor_compliance_fee"
                                    placeholder="$0">
                            </div>
                        </div>
                        <div class="recc-field">
                            <label class="recc-field__label" for="<?php echo esc_attr($buyer_calculator_id); ?>-hoa-transfer-fee">
                                <?php echo esc_html($settings['hoa_transfer_fee_label']); ?>
                                <?php if (!empty($settings['hoa_transfer_fee_description'])): ?>
                                    <div class="recc-field__description"><?php echo esc_html($settings['hoa_transfer_fee_description']); ?></div>
                                <?php endif; ?>
                            </label>
                            <div class="recc-field__input-wrap">
                                <input type="text"
                                    class="recc-input recc-input--currency"
                                    id="<?php echo esc_attr($buyer_calculator_id); ?>-hoa-transfer-fee"
                                    name="hoa_transfer_fee"
                                    data-field="hoa_transfer_fee"
                                    placeholder="$0">
                            </div>
                        </div>
                        <div class="recc-field">
                            <label class="recc-field__label" for="<?php echo esc_attr($buyer_calculator_id); ?>-hoa-prepaid-assessment">
                                <?php echo esc_html($settings['hoa_prepaid_assessment_label']); ?>
                                <?php if (!empty($settings['hoa_prepaid_assessment_description'])): ?>
                                    <div class="recc-field__description"><?php echo esc_html($settings['hoa_prepaid_assessment_description']); ?></div>
                                <?php endif; ?>
                            </label>
                            <div class="recc-field__input-wrap">
                                <input type="text"
                                    class="recc-input recc-input--currency"
                                    id="<?php echo esc_attr($buyer_calculator_id); ?>-hoa-prepaid-assessment"
                                    name="hoa_prepaid_assessment"
                                    data-field="hoa_prepaid_assessment"
                                    placeholder="$0">
                            </div>
                        </div>
                        <div class="recc-field">
                            <label class="recc-field__label" for="<?php echo esc_attr($buyer_calculator_id); ?>-other-fees">
                                <?php echo esc_html($settings['other_fees_label']); ?>
                                <?php if (!empty($settings['other_fees_description'])): ?>
                                    <div class="recc-field__description"><?php echo esc_html($settings['other_fees_description']); ?></div>
                                <?php endif; ?>
                            </label>
                            <div class="recc-field__input-wrap">
                                <input type="text"
                                    class="recc-input recc-input--currency"
                                    id="<?php echo esc_attr($buyer_calculator_id); ?>-other-fees"
                                    name="other_fees"
                                    data-field="other_fees"
                                    placeholder="$0">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="recc-total-wrap">                    
                    <!-- Final Totals Section -->
                    <div class="recc-section recc-section--totals">
                        <h5 class="recc-section__title"><?php echo esc_html__('Final Totals', 'real-estate-closing-calculator'); ?></h5>
                        <div class="recc-field">
                            <div class="recc-field__label">
                                <?php echo esc_html($settings['total_closing_costs_label']); ?>
                            </div>
                            <div class="recc-field__output" data-field="total_closing_costs">
                                $0.00
                            </div>
                        </div>
                        <div class="recc-field">
                            <div class="recc-field__label">
                                <?php echo esc_html($settings['estimated_net_proceeds_label']); ?>
                            </div>
                            <div class="recc-field__output" data-field="estimated_net_proceeds">
                                $0.00
                            </div>
                        </div>
                    </div>                    <!-- Action Buttons -->
                    <div class="recc-actions">
                        <div class="recc-action" id="recc-download-action">
                            <button type="button" class="recc-button recc-button--download">Download PDF</button>                    
                            <div class="recc-action__message"></div>
                        </div>
                        <div class="recc-action" id="recc-email-action">
                            <div class="recc-action__email">
                                <input type="email" name="email" placeholder="Enter your email address" required>
                                <div class="recc-action__message"></div>
                            </div>
                            <button type="button" class="recc-button recc-button--send">Send via email</button>
                        </div>
                    </div>
                    <div class="recc-disclaimer">
                        <?php echo $settings['total_disclaimer_text']; ?>
                    </div>
                </div>
            </form>
        </div>
<?php
    }
}
