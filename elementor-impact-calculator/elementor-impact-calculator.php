<?php

/**
 * Plugin Name: Impact Calculator for Elementor
 * Description: Custom Elementor widget for calculating emission impact.
 * Version: 1.0
 * Author: MKDesign
 * 
 * Requires Plugins: elementor
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Register the calculator widgets with Elementor.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager The widgets manager instance.
 */

function register_impact_calculator_widgets($widgets_manager)
{
    require_once(__DIR__ . '/widgets/calculator-widget.php');

    $widgets_manager->register(new \Impact_Calculator_Widget());
}
add_action('elementor/widgets/register', 'register_impact_calculator_widgets');

/**
 * Register the calculator dependencies.
 * 
 * Registers all the scripts and styles to be enqueued later.
 */
function enqueue_impact_calculator_dependencies()
{
    wp_register_script('impact-calculator', plugins_url('js/impact-calculator.js', __FILE__));
}

add_action('wp_enqueue_scripts', 'enqueue_impact_calculator_dependencies');

function impact_calculator_frontend_stylesheets()
{

    wp_register_style('frontend-css', plugins_url('css/calculator.css', __FILE__));
}
add_action('elementor/frontend/before_enqueue_styles', 'impact_calculator_frontend_stylesheets');
