<?php

/**
 * Plugin Name: Price Calculator for Elementor
 * Description: Custom Elementor widget for calculating pricing plan.
 * Version: 1.0
 * Author: Optimize5
 * 
 * Requires Plugins: elementor
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Register the price calculator widgets with Elementor.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager The widgets manager instance.
 */

function register_price_calculator_widgets($widgets_manager)
{
    require_once(__DIR__ . '/widgets/price-calculator-item-widget.php');
    require_once(__DIR__ . '/widgets/price-calculator-total-widget.php');

    $widgets_manager->register(new \Price_Calculator_Item_Widget());
    $widgets_manager->register(new \Price_Calculator_Total_Widget());
}
add_action('elementor/widgets/register', 'register_price_calculator_widgets');

/**
 * Register the price calculator dependencies.
 * 
 * Registers all the scripts and styles to be enqueued later.
 */
function enqueue_price_calculator_dependencies()
{
    wp_register_script('price-calculator-item', plugins_url('js/price-calculator-item.js', __FILE__));
    wp_register_script('price-calculator-total', plugins_url('js/price-calculator-total.js', __FILE__));
}

add_action('wp_enqueue_scripts', 'enqueue_price_calculator_dependencies');

function price_calculator_frontend_stylesheets() {

	wp_register_style( 'frontend-css', plugins_url( 'css/calculator.css', __FILE__ ) );

	// wp_enqueue_style( 'frontend-style' );

}
add_action( 'elementor/frontend/before_enqueue_styles', 'price_calculator_frontend_stylesheets' );
