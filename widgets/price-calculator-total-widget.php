<?php
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Price Calculator Total Widget.
 */
class Price_Calculator_Total_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'price-calculator-total';
    }

    public function get_title()
    {
        return esc_html__('Price Calculator Total', 'price-calculator-total');
    }

    public function get_icon()
    {
        return 'eicon-price-list';
    }

    public function get_categories()
    {
        return ['general'];
    }

    public function get_script_depends()
    {
        return ['price-calculator-total'];
    }

    public function get_style_depends() {
		return [ 'frontend-css' ];
	}

    protected function _register_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content', 'price-calculator-total'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'base_price',
            [
                'label' => esc_html__('Base Price', 'price-calculator-total'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'input_type' => 'text',
                'rows' => 3,
                'placeholder' => esc_html__('Enter your base price', 'price-calculator-total'),
                'default' => 0,
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'description',
            [
                'label' => esc_html__('Description', 'price-calculator-total'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'placeholder' => esc_html__('Enter your description', 'price-calculator-total'),
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render the price calculator total widget on the frontend.
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $currency_symbol = '$';
?>

        <p class="calc-total__price">
            <?php echo $currency_symbol . esc_html(isset($settings['base_price']) ? $settings['base_price'] : 0); ?>
        </p>

        <?php if ($settings['description']) { ?>
            <p class="calc-description">
                <?php echo esc_html($settings['description']); ?>
            </p>
        <?php } ?>

        <div class="calc-total__details">
        </div>
    <?php
    }

    /**
     * Render the price calculator total widget in the editor.
     */
    protected function content_template()
    {
    ?>
        <p class="calc-total__price">
            {{ settings.base_price }}
        </p>

        <# if (settings.description) { #>
            <p class="calc-description">
                {{ settings.description }}
            </p>
            <# } #>

                <div class="calc-total__details">
                </div>
        <?php
    }
}
