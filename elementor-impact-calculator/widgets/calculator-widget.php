<?php
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Calculator Widget
 */
class Impact_Calculator_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'impact-calculator';
    }

    public function get_title()
    {
        return esc_html__('Impact Calculator', 'impact-calculator');
    }

    public function get_icon()
    {
        return 'eicon-check-circle';
    }

    public function get_categories()
    {
        return ['general'];
    }

    public function get_script_depends()
    {
        return ['impact-calculator'];
    }

    public function get_style_depends()
    {
        return ['frontend-css'];
    }

    protected function _register_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content', 'impact-calculator'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // Title
        $this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Title', 'textdomain' ),
			]
		);

        // Description
        // $this->add_control(
		// 	'description',
		// 	[
		// 		'label' => esc_html__( 'Description', 'textdomain' ),
		// 		'type' => \Elementor\Controls_Manager::TEXTAREA,
		// 		'default' => esc_html__( 'Description', 'textdomain' ),
		// 	]
		// );

        // Min quantity
        $this->add_control(
            'min_quantity',
            [
                'label' => esc_html__('Minimum quantity', 'impact-calculator'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 1,
                'placeholder' => esc_html__('Enter minimum quantity', 'impact-calculator'),
                'frontend_available' => true,
            ]
        );

        // Max quantity
        $this->add_control(
            'max_quantity',
            [
                'label' => esc_html__('Maximum quantity', 'impact-calculator'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 10,
                'placeholder' => esc_html__('Enter maximum quantity', 'impact-calculator'),
                'frontend_available' => true,
            ]
        );

        $this->end_controls_section();

        /* BOX 1 */
        $this->start_controls_section(
			'box_1',
			[
				'label' => esc_html__( 'Infoboxes', 'impact-calculator' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control(
			'box_1_content',
			[
				'label' => esc_html__( 'First Box Content', 'impact-calculator' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_control(
			'box_1_title',
			[
				'label' => esc_html__( 'Title', 'impact-calculator' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'rows' => 4,
				'default' => esc_html__( 'Default title', 'impact-calculator' ),
				'placeholder' => esc_html__( 'Type your description here', 'impact-calculator' ),
			]
		);
        $this->add_control(
			'box_1_image',
			[
				'type' => \Elementor\Controls_Manager::MEDIA,
				'label' => esc_html__( 'Choose Image', 'impact-calculator' ),
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
			]
		);

        /* BOX 2 */
        $this->add_control(
			'box_2_content',
			[
				'label' => esc_html__( 'Second Box Content', 'impact-calculator' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
        $this->add_control(
			'box_2_title',
			[
				'label' => esc_html__( 'Title', 'impact-calculator' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'rows' => 4,
				'default' => esc_html__( 'Default title', 'impact-calculator' ),
				'placeholder' => esc_html__( 'Type your description here', 'impact-calculator' ),
			]
		);
        $this->add_control(
			'box_2_image',
			[
				'type' => \Elementor\Controls_Manager::MEDIA,
				'label' => esc_html__( 'Choose Image', 'impact-calculator' ),
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
			]
		);

        $this->end_controls_section();

        /* BOX 3 */
        $this->start_controls_section(
			'result',
			[
				'label' => esc_html__( 'Result Content', 'impact-calculator' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control(
			'result_title',
			[
				'label' => esc_html__( 'Title', 'impact-calculator' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'rows' => 4,
				'default' => esc_html__( 'Default title', 'impact-calculator' ),
				'placeholder' => esc_html__( 'Type your description here', 'impact-calculator' ),
			]
		);
        $this->add_control(
			'result_image',
			[
				'type' => \Elementor\Controls_Manager::MEDIA,
				'label' => esc_html__( 'Choose Image', 'impact-calculator' ),
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
                'separator' => 'after',
			]
		);


        $this->end_controls_section();
        
        
        
        /* TAB STYLES */
        $this->start_controls_section(
			'box_1_styles',
			[
				'label' => esc_html__( 'Infobox 1 Style', 'textdomain' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
        $this->add_control(
			'box_1_bg',
			[
				'type' => \Elementor\Controls_Manager::COLOR,
				'label' => esc_html__( 'Box Background', 'impact-calculator' ),
				'selectors' => [
					'{{WRAPPER}} .infobox_1' => 'background-color: {{VALUE}}',
				],
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'box_1_typography',
                'label' => esc_html__( 'Title Typography', 'impact-calculator' ),
				'selector' => '{{WRAPPER}} .class',
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'box_1_border',
				'selector' => '{{WRAPPER}} .class',
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(
			'box_2_styles',
			[
				'label' => esc_html__( 'Infobox 2 Style', 'textdomain' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
        $this->add_control(
			'box_2_bg',
			[
				'type' => \Elementor\Controls_Manager::COLOR,
				'label' => esc_html__( 'Box Background', 'impact-calculator' ),
                'selectors' => [
					'{{WRAPPER}} .infobox_2' => 'background-color: {{VALUE}}',
				],
                // 'global' => [
                //     'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_SECONDARY,
                // ],
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'box_2_typography',
                'label' => esc_html__( 'Title Typography', 'impact-calculator' ),
				'selector' => '{{WRAPPER}} .class',
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'box_2_border',
				'selector' => '{{WRAPPER}} .class',
			]
		);

        $this->end_controls_section();

        /* RESULT BOX STYLES */
        
        $this->start_controls_section(
			'box_result_styles',
			[
				'label' => esc_html__( 'Result Style', 'textdomain' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
        $this->add_control(
			'box_result_bg',
			[
				'type' => \Elementor\Controls_Manager::COLOR,
				'label' => esc_html__( 'Box Background', 'impact-calculator' ),
                'selectors' => [
					'{{WRAPPER}} .class' => 'background-color: {{VALUE}}',
				],
                // 'global' => [
                //     'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_SECONDARY,
                // ],
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'box_result_typography',
                'label' => esc_html__( 'Title Typography', 'impact-calculator' ),
				'selector' => '{{WRAPPER}} .class',
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'box_result_border',
				'selector' => '{{WRAPPER}} .infobox_result',
			]
		);

        $this->end_controls_section();





    }


    /**
     * Render the price calculator item widget on the frontend.
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->add_inline_editing_attributes( 'title', 'basic' );
        $this->add_inline_editing_attributes( 'description', 'basic' );

        // Get the settings
        $min_quantity = empty($settings['min_quantity']) ? 0 : $settings['min_quantity'];
        $max_quantity = empty($settings['max_quantity']) ? 1 : $settings['max_quantity'];
        $quantity = $min_quantity;  // Set to min_quantity by default
?>
        <div class="impact-calculator">
            <div class="calc__title">

                <span <?php $this->print_render_attribute_string( 'title' ); ?>><?php echo $settings['title']; ?></span>
                
                <input
                type="number"
                name="quantity-number"
                class="calc__number-input"
                value="<?php echo esc_html($quantity); ?>"
                min="<?php echo esc_html($min_quantity); ?>"
                max="<?php echo esc_html($max_quantity); ?>"
                />
            </div>
            
            <div class="calc__range">
                <input
                type="range"
                name="quantity-range"
                class="calc__range-input"
                value="<?php echo esc_html($quantity); ?>"
                min="<?php echo esc_html($min_quantity); ?>"
                max="<?php echo esc_html($max_quantity); ?>"
                />
                
            </div>
            
            <!-- <div <?php //$this->print_render_attribute_string( 'description' ); ?>><?php //echo $settings['description']; ?></div> -->

            <div class="calc__price">
                <p>
                    <?php esc_html($quantity); ?>
                </p>
            </div>

            <div class="infoboxes-row">
                <div class="infobox infobox_1">
                    <!-- image -->
                    image
                    <p class="infobox_1__title">
                        <?php echo $settings['box_1_title']; ?>
                    </p>
                </div>

                <div class="infobox infobox_2">
                    <!-- image 2-->
                    image
                    <p class="infobox__title">
                        <?php echo $settings['box_2_title']; ?>
                    </p>
                </div>
                <div class="infobox infobox_result">
                    <!-- result image -->
                    image
                    <p class="infobox__title">
                        <?php echo $settings['result_title']; ?>
                    </p>
                </div>
            </div>


        </div>
    <?php
    }


    /**
     * Render the price calculator item widget in the editor.
     */
    protected function content_template()
    {
    ?>
        <#
            view.addInlineEditingAttributes( 'title', 'none' );
            view.addInlineEditingAttributes( 'description', 'basic' );
        #>
        <div class="calc">
            <div>
                    <div class="calc__title" >
                    <p {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ settings.title }}}</p>
                    </div>
                    
                    <!-- Quantity slider -->
                    <div class="calc__range">
                        <input
                        type="range"
                        name="quantity-range"
                        class="calc__range-input"
                        value="{{ settings.min_quantity }}"
                        min="{{ settings.min_quantity }}"
                        max="{{ settings.max_quantity }}"
                        disabled />
                    </div>
                    <!-- <p {{{ view.getRenderAttributeString( 'description' ) }}}>{{{ settings.description }}}</p> -->
                </div>
                <div class="calc__price">
                    <p>
                        <!-- ${{ settings.price }} -->
                    </p>
                </div>

                {{{ settings.box_1_title }}}

            </div>
    <?php
    }
}
