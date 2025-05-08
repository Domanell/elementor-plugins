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
                'label' => esc_html__('Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Title', 'textdomain'),
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
                'default' => 100,
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
                'default' => 10000,
                'placeholder' => esc_html__('Enter maximum quantity', 'impact-calculator'),
                'frontend_available' => true,
            ]
        );
        // Default quantity
        $this->add_control(
            'default_quantity',
            [
                'label' => esc_html__('Default quantity', 'impact-calculator'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 1000,
                'placeholder' => esc_html__('Enter default quantity', 'impact-calculator'),
                'frontend_available' => true,
            ]
        );

        // Step quantity
        $this->add_control(
            'step_quantity',
            [
                'label' => esc_html__('Step quantity', 'impact-calculator'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 100,
                'placeholder' => esc_html__('Enter step quantity', 'impact-calculator'),
                'frontend_available' => true,
            ]
        );

        $this->end_controls_section();

        /* BOX 1 */
        $this->start_controls_section(
            'box_1',
            [
                'label' => esc_html__('Infoboxes', 'impact-calculator'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'box_1_content',
            [
                'label' => esc_html__('First Box Content', 'impact-calculator'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'box_1_title',
            [
                'label' => esc_html__('Title', 'impact-calculator'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'rows' => 4,
                'default' => esc_html__('Default title', 'impact-calculator'),
                'placeholder' => esc_html__('Type your description here', 'impact-calculator'),
            ]
        );
        $this->add_control(
            'box_1_image',
            [
                'type' => \Elementor\Controls_Manager::MEDIA,
                'label' => esc_html__('Choose Image', 'impact-calculator'),
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        /* BOX 2 */
        $this->add_control(
            'box_2_content',
            [
                'label' => esc_html__('Second Box Content', 'impact-calculator'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'box_2_title',
            [
                'label' => esc_html__('Title', 'impact-calculator'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'rows' => 4,
                'default' => esc_html__('Default title', 'impact-calculator'),
                'placeholder' => esc_html__('Type your description here', 'impact-calculator'),
            ]
        );
        $this->add_control(
            'box_2_image',
            [
                'type' => \Elementor\Controls_Manager::MEDIA,
                'label' => esc_html__('Choose Image', 'impact-calculator'),
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
                'label' => esc_html__('Result Content', 'impact-calculator'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'result_title',
            [
                'label' => esc_html__('Title', 'impact-calculator'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'rows' => 4,
                'default' => esc_html__('Default title', 'impact-calculator'),
                'placeholder' => esc_html__('Type your description here', 'impact-calculator'),
            ]
        );
        $this->add_control(
            'result_image',
            [
                'type' => \Elementor\Controls_Manager::MEDIA,
                'label' => esc_html__('Choose Image', 'impact-calculator'),
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
                'label' => esc_html__('Infobox 1 Style', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'box_1_typography',
                'label' => esc_html__('Title Typography', 'impact-calculator'),
                'selector' => '{{WRAPPER}} .infobox__title_1',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'box_1_number',
                'label' => esc_html__('Number Typography', 'impact-calculator'),
                'selector' => '{{WRAPPER}} .impact-value_standard',
            ]
        );
        $this->add_control(
            'box_1_number_color',
            [
                'type' => \Elementor\Controls_Manager::COLOR,
                'label' => esc_html__('Number Color', 'impact-calculator'),
                'selectors' => [
                    '{{WRAPPER}} .impact-value_standard' => 'color: {{VALUE}}',
                ],
                // 'global' => [
                //     'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_SECONDARY,
                // ],
            ]
        );
        $this->add_control(
            'box_1_bg',
            [
                'type' => \Elementor\Controls_Manager::COLOR,
                'label' => esc_html__('Box Background', 'impact-calculator'),
                'selectors' => [
                    '{{WRAPPER}} .infobox_1' => 'background-color: {{VALUE}}',
                ],
                'separator' => 'before'
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'box_1_border',
                'selector' => '{{WRAPPER}} .infobox_1',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'box_2_styles',
            [
                'label' => esc_html__('Infobox 2 Style', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'box_2_typography',
                'label' => esc_html__('Title Typography', 'impact-calculator'),
                'selector' => '{{WRAPPER}} .infobox__title_2',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'box_2_number',
                'label' => esc_html__('Number Typography', 'impact-calculator'),
                'selector' => '{{WRAPPER}} .impact-value_sustainable',
            ]
        );
        $this->add_control(
            'box_2_number_color',
            [
                'type' => \Elementor\Controls_Manager::COLOR,
                'label' => esc_html__('Number Color', 'impact-calculator'),
                'selectors' => [
                    '{{WRAPPER}} .impact-value_sustainable' => 'color: {{VALUE}}',
                ],
                // 'global' => [
                //     'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_SECONDARY,
                // ],
            ]
        );
        $this->add_control(
            'box_2_bg',
            [
                'type' => \Elementor\Controls_Manager::COLOR,
                'label' => esc_html__('Box Background', 'impact-calculator'),
                'selectors' => [
                    '{{WRAPPER}} .infobox_2' => 'background-color: {{VALUE}}',
                ],
                'separator' => 'before'
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'box_2_border',
                'selector' => '{{WRAPPER}} .infobox_2',
            ]
        );

        $this->end_controls_section();

        /* RESULT BOX STYLES */

        $this->start_controls_section(
            'box_result_styles',
            [
                'label' => esc_html__('Result Style', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'box_result_typography',
                'label' => esc_html__('Title Typography', 'impact-calculator'),
                'selector' => '{{WRAPPER}} .infobox__title_3',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'box_result_number',
                'label' => esc_html__('Number Typography', 'impact-calculator'),
                'selector' => '{{WRAPPER}} .impact-value_result',
            ]
        );
        $this->add_control(
            'box_result_number_color',
            [
                'type' => \Elementor\Controls_Manager::COLOR,
                'label' => esc_html__('Number Color', 'impact-calculator'),
                'selectors' => [
                    '{{WRAPPER}} .impact-value_result' => 'color: {{VALUE}}',
                ],
                // 'global' => [
                //     'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_SECONDARY,
                // ],
            ]
        );
        $this->add_control(
            'box_result_bg',
            [
                'type' => \Elementor\Controls_Manager::COLOR,
                'label' => esc_html__('Box Background', 'impact-calculator'),
                'selectors' => [
                    '{{WRAPPER}} .class' => 'background-color: {{VALUE}}',
                ],
                'separator' => 'before'
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
     * Render the calculator item widget on the frontend.
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->add_inline_editing_attributes('title', 'basic');
        $this->add_inline_editing_attributes('description', 'basic');


        // Get the settings
        $min_quantity = empty($settings['min_quantity']) ? 0 : $settings['min_quantity'];
        $max_quantity = empty($settings['max_quantity']) ? 10000 : $settings['max_quantity'];
        $quantity = empty($settings['default_quantity']) ? 1000 : $settings['default_quantity'];;  // Set to min_quantity by default
        $step_quantity = empty($settings['step_quantity']) ? 1 : $settings['step_quantity'];
        $units = 'kg CO'.'<sub>2</sub>' . 'e';
?>
        <div class="impact-calculator">
            <div class="impact-calculator__wrap">
                <div class="impact-calculator__title">
                    <span <?php $this->print_render_attribute_string('title'); ?>><?php echo $settings['title']; ?></span>
                </div>

                <div class="impact-calculator__range">
                    <input
                        type="range"
                        name="quantity-range"
                        class="calc__range-input"
                        value="<?php echo esc_html($quantity); ?>"
                        min="<?php echo esc_html($min_quantity); ?>"
                        max="<?php echo esc_html($max_quantity); ?>"
                        step="<?php echo esc_html($step_quantity); ?>" />
                </div>
                <div class="impact-calculator__num">
                    <input
                        type="number"
                        name="quantity-number"
                        class="calc__number-input"
                        value="<?php echo esc_html($quantity); ?>"
                        min="<?php echo esc_html($min_quantity); ?>"
                        max="<?php echo esc_html($max_quantity); ?>" />
                </div>
            </div>

            <!-- <div <?php //$this->print_render_attribute_string( 'description' ); 
                        ?>><?php //echo $settings['description']; 
                            ?></div> -->

            <div class="infoboxes-row">
                <div class="infobox infobox_1">
                    <?php
                    if (!empty($settings['box_1_image']['url'])) {
                        echo '<img src="' . $settings['box_1_image']['url'] . '" alt="Box 1 image">';
                    }
                    ?>
                    <p class="infobox__title infobox__title_1">
                        <?php echo $settings['box_1_title']; ?>
                    </p>
                    <p class="impact-value impact-value_standard"><?php echo round($quantity * 15.33, 0); ?></p>
                    <p class="impact-units"><?php echo $units ;?> </p>
                </div>

                <div class="infobox infobox_2">
                    <?php
                    if (!empty($settings['box_2_image']['url'])) {
                        echo '<img src="' . $settings['box_2_image']['url'] . '" alt="Box 2 image">';
                    }
                    ?>
                    <p class="infobox__title infobox__title_2">
                        <?php echo $settings['box_2_title']; ?>
                    </p>
                    <p class="impact-value impact-value_sustainable"><?php echo round($quantity * 3.51, 0); ?></p>
                    <p class="impact-units"><?php echo $units ;?> </p>
                </div>
                <div class="infobox infobox_result">
                    <?php
                    if (!empty($settings['result_image']['url'])) {
                        echo '<img src="' . $settings['result_image']['url'] . '" alt="Result image">';
                    }
                    ?>
                    <p class="infobox__title infobox__title_3">
                        <?php echo $settings['result_title']; ?>
                    </p>
                    <p class="impact-value impact-value_result">77,1%</p>
                </div>
            </div>
        </div>
    <?php
    }


    /**
     * Render the calculator item widget in the editor.
     */
    protected function content_template()
    {
    ?>
        <#
            view.addInlineEditingAttributes( 'title' , 'none' );
            view.addInlineEditingAttributes( 'description' , 'basic' );
            #>
            <div class="impact-calculator">
                <div class="impact-calculator__wrap">
                    <div class="impact-calculator__title">
                        <span {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ settings.title }}}</span>
                        
                    </div>
                    <div class="impact-calculator__range">
                        <input
                            type="range"
                            name="quantity-range"
                            class="calc__range-input"
                            value="{{{ settings.min_quantity }}}"
                            min="{{{ settings.min_quantity }}}"
                            max="{{{ settings.max_quantity }}}"
                            step="{{{ settings.step_quantity }}}" />
                    </div>
                    <div class="impact-calculator__num">
                        <input
                            type="number"
                            name="quantity-number"
                            class="calc__number-input"
                            value="{{{ settings.min_quantity }}}"
                            min="{{{ settings.min_quantity }}}"
                            max="{{{ settings.max_quantity }}}" />
                    </div>
                </div>
                <div class="infoboxes-row">
                    <div class="infobox infobox_1">
                        <!-- image -->
                        <# if (settings.box_1_image.url) { #>
                            <img src="{{{ settings.box_1_image.url }}}" alt="Box 1 image">
                            <# } #>
                                <p class="infobox__title infobox__title_1">{{{ settings.box_1_title }}}</p>
                                <p class="impact-value impact-value_standard">{{{ settings.min_quantity * 15.33 }}}</p>
                    </div>
                    <div class="infobox infobox_2">
                        <!-- image 2-->
                        <# if (settings.box_2_image.url) { #>
                            <img src="{{{ settings.box_2_image.url }}}" alt="Box 2 image">
                            <# } #>
                                <p class="infobox__title infobox__title_2">{{{ settings.box_2_title }}}</p>
                                <p class="impact-value impact-value_sustainable">{{{ settings.min_quantity * 3.51 }}}</p>
                    </div>
                    <div class="infobox infobox_result">
                        <!-- result image -->
                        <# if (settings.result_image.url) { #>
                            <img src="{{{ settings.result_image.url }}}" alt="Result image">
                            <# } #>
                                <p class="infobox__title infobox__title_3">{{{ settings.box_3_title }}}</p>
                                <p class="impact-value">77.1%</p>
                    </div>
                </div>
            </div>
            
    <?php
    }
}
