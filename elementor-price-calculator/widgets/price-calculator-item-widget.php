<?php
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Price Calculator Item Widget.
 */
class Price_Calculator_Item_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'price-calculator-item';
    }

    public function get_title()
    {
        return esc_html__('Price Calculator Item', 'price-calculator');
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
        return ['price-calculator-item'];
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
                'label' => esc_html__('Content', 'price-calculator'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // Name
        $this->add_control(
            'name',
            [
                'label' => esc_html__('Title', 'price-calculator'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'input_type' => 'text',
                'rows' => 2,
                'default' => esc_html__('Item Name', 'price-calculator'),
                'placeholder' => esc_html__('Enter your title', 'price-calculator'),
                'frontend_available' => true,
            ]
        );

        // Description
        $this->add_control(
            'description',
            [
                'label' => esc_html__('Description', 'price-calculator'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'input_type' => 'text',
                'rows' => 4,
                'placeholder' => esc_html__('Type your description here', 'price-calculator'),
                'description' => esc_html__('Displayed below the slider', 'price-calculator'),
            ]
        );

        // Tooltip
        $this->add_control(
            'tooltip_text',
            [
                'label' => esc_html__('Tooltip text', 'price-calculator'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'input_type' => 'text',
                'rows' => 4,
                'placeholder' => esc_html__('Type your tooltip here', 'price-calculator'),
            ]
        );

        // PRICE SECTION
        $this->add_control(
            'price_settings',
            [
                'label' => esc_html__('Price settings', 'price-calculator'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        // Price
        $this->add_control(
            'price',
            [
                'label' => esc_html__('Price', 'price-calculator'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 0,
                'placeholder' => esc_html__('Enter price', 'price-calculator'),
                'frontend_available' => true,
            ]
        );

        // Price per item switcher
        $this->add_control(
            'enable_price_per_item',
            [
                'label' => esc_html__('Enable price per item', 'price-calculator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'price-calculator'),
                'label_off' => esc_html__('No', 'price-calculator'),
                'return_value' => 'yes',
                'frontend_available' => true,
            ]
        );

        // Price per item
        $this->add_control(
            'price_per_item',
            [
                'label' => esc_html__('Price per item', 'price-calculator'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 0,
                'placeholder' => esc_html__('Enter price per item', 'price-calculator'),
                'condition' => [
                    'enable_price_per_item' => 'yes',
                ],
                'frontend_available' => true,
            ]
        );

        // Included items
        $this->add_control(
            'included_quantity',
            [
                'label' => esc_html__('Included quantity (free)', 'price-calculator'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 0,
                'placeholder' => esc_html__('Enter included quantity', 'price-calculator'),
                'condition' => [
                    'enable_price_per_item' => 'yes',
                ],
                'frontend_available' => true,
            ]
        );

        // Min quantity
        $this->add_control(
            'min_quantity',
            [
                'label' => esc_html__('Minimum quantity', 'price-calculator'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 1,
                'placeholder' => esc_html__('Enter minimum quantity', 'price-calculator'),
                'condition' => [
                    'enable_price_per_item' => 'yes',
                ],
                'frontend_available' => true,
            ]
        );

        // Max quantity
        $this->add_control(
            'max_quantity',
            [
                'label' => esc_html__('Maximum quantity', 'price-calculator'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 10,
                'placeholder' => esc_html__('Enter maximum quantity', 'price-calculator'),
                'condition' => [
                    'enable_price_per_item' => 'yes',
                ],
                'frontend_available' => true,
            ]
        );

        // Item price description
        $this->add_control(
            'item_price_description',
            [
                'label' => esc_html__('Item price description', 'price-calculator'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'input_type' => 'text',
                'rows' => 4,
                'placeholder' => esc_html__('The {price} tag will be replaced with the price per item automatically', 'price-calculator'),
                'description' => esc_html__('Use {price} text to display the price in the description', 'price-calculator'),
                'condition' => [
                    'enable_price_per_item' => 'yes',
                ],
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
        $currency_symbol = '$';
        // Get the settings
        $pricePerItemEnabled = $settings['enable_price_per_item'] === 'yes';
        $price = empty($settings['price']) ? 0 : $settings['price'];
        $price_per_item = empty($settings['price_per_item']) ? 0 : $settings['price_per_item'];
        $min_quantity = empty($settings['min_quantity']) ? 0 : $settings['min_quantity'];
        $max_quantity = empty($settings['max_quantity']) ? 1 : $settings['max_quantity'];
        $quantity = $min_quantity;  // Set to min_quantity by default
        $included_quantity = empty($settings['included_quantity']) ? 0 : $settings['included_quantity'];
        $additional_quantity = $pricePerItemEnabled && $quantity > $included_quantity ? $quantity - $included_quantity : 0;

?>
        <div class="calc-item">
            <div>
                <div class="calc-item__title">
                    <label>
                        <input type="checkbox" class="calc-item__checkbox" />
                        <span class="calc-item__name"><?php echo esc_html($settings['name']); ?></span>
                    </label>
                    <?php if (!empty($settings['tooltip_text'])) { ?>
                        <div class="calc-item__tooltip">
                            <div class="calc-item__tooltip-text">
                                <?php echo $settings['tooltip_text']; ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($pricePerItemEnabled) { ?>
                        <input
                            type="number"
                            name="quantity-number"
                            class="calc-item__number-input"
                            value="<?php echo esc_html($quantity); ?>"
                            min="<?php echo esc_html($min_quantity); ?>"
                            max="<?php echo esc_html($max_quantity); ?>"
                            disabled />
                    <?php } ?>
                </div>
                <?php if ($pricePerItemEnabled) {
                    //  Quantity slider 
                ?>
                    <div class="calc-item__range">
                        <input
                            type="range"
                            name="quantity-range"
                            class="calc-item__range-input"
                            value="<?php echo esc_html($quantity); ?>"
                            min="<?php echo esc_html($min_quantity); ?>"
                            max="<?php echo esc_html($max_quantity); ?>"
                            disabled />
                        <div class="calc-item__range-values">
                            <span><?php echo esc_html($min_quantity); ?></span>
                            <span><?php echo esc_html($max_quantity); ?></span>
                        </div>
                    </div>
                <?php } ?>
                <p class="calc-item__description">
                    <?php echo $settings['description']; ?>
                </p>
            </div>
            <div class="calc-item__price">
                <p>
                    <?php echo $currency_symbol . esc_html($price + $additional_quantity * $price_per_item); ?>
                </p>
                <?php if ($pricePerItemEnabled && isset($settings['item_price_description'])) { ?>
                    <span class="calc-item__item-price">
                        <?php echo str_replace('{price}', '<span class="calc-item__price-value">' . $currency_symbol . esc_html($price_per_item) . '</span>', $settings['item_price_description']); ?>
                    </span>
                <?php } ?>
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
        <div class="calc-item">
            <div>
                <div class="calc-item__title">
                    <label>
                        <input type="checkbox" class="calc-item__checkbox" />
                        <span class="calc-item__name">
                            {{ settings.name }}
                        </span>
                    </label>
                    <# if (settings.tooltip_text) { #>
                        <div class="calc-item__tooltip">
                            <div class="calc-item__tooltip-text">
                                {{ settings.tooltip_text }}
                            </div>
                        </div>
                        <# } #>
                            <# if (settings.enable_price_per_item==='yes' ) { #>
                                <input
                                    type="number"
                                    name="quantity-number"
                                    class="calc-item__number-input"
                                    value="{{ settings.min_quantity }}"
                                    min="{{ settings.min_quantity }}"
                                    max="{{ settings.max_quantity }}"
                                    disabled />
                                <# } #>
                </div>
                <# if (settings.enable_price_per_item==='yes' ) { #>
                    <!-- Quantity slider -->
                    <div class="calc-item__range">
                        <input
                            type="range"
                            name="quantity-range"
                            class="calc-item__range-input"
                            value="{{ settings.min_quantity }}"
                            min="{{ settings.min_quantity }}"
                            max="{{ settings.max_quantity }}"
                            disabled />
                        <div class="calc-item__range-values">

                            <span>{{ settings.min_quantity }}</span>
                            <span>{{ settings.max_quantity }}</span>
                        </div>
                    </div>
                    <# } #>
                        <p class="calc-item__description">
                            {{ settings.description }}
                        </p>
            </div>
            <div class="calc-item__price">
                <p>
                    ${{ settings.price }}
                </p>
                <# if (settings.enable_price_per_item==='yes' && settings.item_price_description) { #>
                    <span class="calc-item__item-price">
                        {{{ settings.item_price_description.replace('{price}', '<span class="calc-item__price-value">$' + settings.price_per_item + '</span>') }}}
                    </span>
                    <# } #>
            </div>
        </div>
<?php
    }
}
