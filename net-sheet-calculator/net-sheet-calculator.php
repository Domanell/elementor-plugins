<?php
/**
 * Plugin Name: Net Sheet Calculator
 * Description: An Elementor widget that calculates estimated proceeds from a real estate transaction.
 * Version: 1.0.0
 * Author: MKDev
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
    // Register utilities script
    wp_register_script(
        'nsc-utils',
        plugins_url('assets/js/nsc-utils.js', __FILE__),
        array('jquery'),
        '1.0.0',
        true
    );

    // Register PDF generator script
    wp_register_script(
        'net-sheet-calculator-pdf-generator',
        plugins_url('assets/js/pdf-generator.js', __FILE__),
        array('jquery', 'pdf-lib', 'nsc-utils'),
        '1.0.0',
        true
    );    // Register email handler script
    wp_register_script(
        'net-sheet-calculator-email-handler',
        plugins_url('assets/js/email-handler.js', __FILE__),
        array('jquery', 'nsc-utils'),
        '1.0.0',
        true
    );

    // Register message handler script
    wp_register_script(
        'net-sheet-calculator-message-handler',
        plugins_url('assets/js/message-handler.js', __FILE__),
        array('jquery'),
        '1.0.0',
        true
    );
    
    // Register main calculator script
    wp_register_script(
        'net-sheet-calculator-script', 
        plugins_url('assets/js/net-sheet-calculator.js', __FILE__), 
        array('jquery', 'nsc-utils', 'net-sheet-calculator-pdf-generator', 'net-sheet-calculator-email-handler', 'net-sheet-calculator-message-handler'), 
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'net_sheet_calculator_register_scripts');

function net_sheet_calculator_register_styles() {
    wp_register_style('recc-style', plugins_url('assets/css/net-sheet-calculator.css', __FILE__));
}
add_action('elementor/frontend/before_enqueue_styles', 'net_sheet_calculator_register_styles');

/**
 * Include the email handler functionality
 */
require_once(__DIR__ . '/includes/email-handler.php');

/**
 * Add data needed for PDF generation and email operations
 */
function net_sheet_calculator_add_localized_data() {
    // Get site logo information
    $logo_data = array(
        'url' => '',
        'width' => 98, // Width limit for PDF logo
        'height' => 48, // Height limit for PDF logo
    );
      // Get site logo ID
    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        // Use medium size logo - optimal for PDFs to reduce file size
        $logo_size = 'medium';
        
        // Get the logo image URL
        $logo_url = wp_get_attachment_image_url($custom_logo_id, $logo_size);
        
        if ($logo_url) {
            $logo_data['url'] = $logo_url;
            
            // Get the actual image size if available
            $image_metadata = wp_get_attachment_metadata($custom_logo_id);
            if (isset($image_metadata['width']) && isset($image_metadata['height'])) {                
                // Calculate aspect ratio to maintain proportions
                $aspect_ratio = $image_metadata['width'] / $image_metadata['height'];
                $logo_data['height'] = min($logo_data['height'], round($logo_data['width'] / $aspect_ratio));
            }
        }
    }
    
    // Localize script with all necessary data
    wp_localize_script('net-sheet-calculator-script', 'nscEmailData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('nsc_email_nonce'),
        'siteLogo' => $logo_data
    ));
}
add_action('wp_enqueue_scripts', 'net_sheet_calculator_add_localized_data');
