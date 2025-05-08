<?php
/**
 * Plugin Name: Seller's Net Sheet Calculator
 * Description: An Elementor widget that calculates seller's estimated proceeds from a real estate transaction.
 * Version: 1.0.0
 * Author: Developer
 * Text Domain: sellers-net-sheet-calculator
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'SNSC_VERSION', '1.0.0' );
define( 'SNSC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SNSC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Check if Elementor is installed and activated
 */
function snsc_check_elementor_dependency() {
    if ( ! did_action( 'elementor/loaded' ) ) {
        add_action( 'admin_notices', 'snsc_elementor_missing_notice' );
        return false;
    }
    return true;
}

/**
 * Admin notice for missing Elementor
 */
function snsc_elementor_missing_notice() {
    if ( isset( $_GET['activate'] ) ) {
        unset( $_GET['activate'] );
    }
    
    $message = sprintf(
        esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'sellers-net-sheet-calculator' ),
        '<strong>Seller\'s Net Sheet Calculator</strong>',
        '<strong>Elementor</strong>'
    );
    
    printf( '<div class="notice notice-error"><p>%1$s</p></div>', $message );
}

/**
 * Register Elementor widget
 */
function snsc_register_elementor_widget( $widgets_manager ) {
    require_once SNSC_PLUGIN_DIR . 'widgets/sellers-net-sheet-calculator-widget.php';
    $widgets_manager->register( new \Sellers_Net_Sheet_Calculator_Widget() );
}

/**
 * Enqueue plugin scripts and styles
 */
function snsc_enqueue_scripts() {
    // Register and enqueue CSS
    wp_register_style(
        'snsc-style',
        SNSC_PLUGIN_URL . 'assets/css/sellers-net-sheet-calculator.css',
        [],
        SNSC_VERSION
    );
    wp_enqueue_style( 'snsc-style' );
    
    // Register and enqueue JavaScript
    wp_register_script(
        'snsc-script',
        SNSC_PLUGIN_URL . 'assets/js/sellers-net-sheet-calculator.js',
        [ 'jquery' ],
        SNSC_VERSION,
        true
    );
    wp_enqueue_script( 'snsc-script' );
}

/**
 * Initialize the plugin
 */
function snsc_init() {
    if ( snsc_check_elementor_dependency() ) {
        // Register widget
        add_action( 'elementor/widgets/register', 'snsc_register_elementor_widget' );
        
        // Enqueue scripts and styles
        add_action( 'wp_enqueue_scripts', 'snsc_enqueue_scripts' );
    }
}

// Hook to init to ensure all plugins are loaded
add_action( 'init', 'snsc_init' );

/**
 * Create required directories and files on plugin activation
 */
function snsc_activate() {
    // Create assets directory if it doesn't exist
    $dirs = [
        SNSC_PLUGIN_DIR . 'assets',
        SNSC_PLUGIN_DIR . 'assets/css',
        SNSC_PLUGIN_DIR . 'assets/js',
        SNSC_PLUGIN_DIR . 'widgets',
    ];
    
    foreach ( $dirs as $dir ) {
        if ( ! file_exists( $dir ) ) {
            wp_mkdir_p( $dir );
        }
    }
}
register_activation_hook( __FILE__, 'snsc_activate' );