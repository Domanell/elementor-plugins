<?php
/**
 * Plugin Name: Net Sheet Calculator
 * Description: An Elementor widget that calculates estimated proceeds from a real estate transaction.
 * Version: 1.0.0
 * Author: MK-Dev
 * Text Domain: net-sheet-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the calculator widgets with Elementor.
 */
function net_sheet_calculator_register_widgets($widgets_manager) {
    require_once(__DIR__ . '/widgets/net-sheet-calculator-widget.php');

    $widgets_manager->register(new \Net_Sheet_Calculator_Widget());
}
add_action('elementor/widgets/register', 'net_sheet_calculator_register_widgets');

/**
 * Register dependencies. Registers all the scripts and styles to be enqueued later.
 */
function net_sheet_calculator_register_scripts() {
    // Register PDF-Lib library
    wp_register_script(
        'pdf-lib',
        plugins_url('assets/lib/pdf-lib.min.js', __FILE__),
        array(),
        '1.17.1',
        true
    );

    // Register PDF generator script
    wp_register_script(
        'net-sheet-calculator-pdf-generator',
        plugins_url('assets/js/pdf-generator.js', __FILE__),
        array('pdf-lib'),
        '1.0.0',
        true
    );
    
    // Register main calculator script
    wp_register_script(
        'net-sheet-calculator-script', 
        plugins_url('assets/js/net-sheet-calculator.js', __FILE__), 
        array('jquery', 'net-sheet-calculator-pdf-generator'), 
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'net_sheet_calculator_register_scripts');

function net_sheet_calculator_register_styles() {
    wp_register_style('net-sheet-calculator-style', plugins_url('assets/css/net-sheet-calculator.css', __FILE__));
}
add_action('elementor/frontend/before_enqueue_styles', 'net_sheet_calculator_register_styles');
