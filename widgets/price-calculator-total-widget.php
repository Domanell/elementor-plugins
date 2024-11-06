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

    public function get_style_depends()
    {
        return ['frontend-css'];
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
        <?php if ($settings['description']) { ?>
            <p class="calc-description">
                <?php echo esc_html($settings['description']); ?>
            </p>
        <?php } ?>

        <div class="calc-total__details">
            <ul class="calc-total__list">
                <li class="calc-total-item">
                    <span class="calc-total-item__name">Base price</span>
                    <span class="calc-total-item__price">
                        <?php echo $currency_symbol . esc_html(empty($settings['base_price']) ? 0 : $settings['base_price']); ?>
                    </span>
                </li>
            </ul>
        </div>
        
        <div class="calc-total__info"
            <?php if (is_user_logged_in()) {
                echo 'data-logged-in="true"';
            } ?>>
            <p class="calc-total__label">Total</p>
            <p class="calc-total__price">
                <?php echo $currency_symbol . esc_html(empty($settings['base_price']) ? 0 : $settings['base_price']); ?>
            </p>
        </div>
        <?php if (is_user_logged_in()) { ?>
            <form class="calc-total__discount-form hidden">
                <input type="number" name="discount-amount" min="0" class="calc-total__discount-input" placeholder="Discount" />
                <button class="calc-total__discount-button">Apply</button>
            </form>
        <?php } ?>

    <?php
    }

    /**
     * Render the price calculator total widget in the editor.
     */
    protected function content_template()
    {
    ?>
        <# if (settings.description) { #>
            <p class="calc-description">
                {{ settings.description }}
            </p>
            <# } #>

                <div class="calc-total__details">
                    <ul class="calc-total__list">
                        <li class="calc-total-item">
                            <span class="calc-total-item__name">Base price</span>
                            <span class="calc-total-item__price">
                                {{ settings.base_price }}
                            </span>
                        </li>
                    </ul>
                </div>

                <div class="calc-total__info">
                    <p class="calc-total__label">Total</p>
                    <p class="calc-total__price">
                        {{ settings.base_price }}
                    </p>
                </div>
        <?php
    }
}
