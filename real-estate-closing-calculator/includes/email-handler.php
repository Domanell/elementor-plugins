<?php
/**
 * Email handler for Real Estate Closing Calculator
 * 
 * Handles sending PDF results via email
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class to handle email functionality
 */
class RECC_Email_Handler {

    /**
     * Initialize the email handler
     */
    public static function init() {
        // Register AJAX endpoint for sending emails
        add_action('wp_ajax_recc_send_email', array(__CLASS__, 'handle_email_request'));
        add_action('wp_ajax_nopriv_recc_send_email', array(__CLASS__, 'handle_email_request'));
    }

    /**
     * Handle the email request from AJAX
     */
    public static function handle_email_request() {
        // Check the nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'recc_email_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed.'));
            return;
        }

        // Get the email address
        $to = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        
        if (!is_email($to)) {
            wp_send_json_error(array('message' => 'Invalid email address.'));
            return;
        }

        // Get PDF data and template name
        $pdf_base64 = isset($_POST['pdfBase64']) ? $_POST['pdfBase64'] : '';
        $pdf_data = isset($_POST['pdfData']) ? $_POST['pdfData'] : null;
        $template_name = isset($_POST['templateName']) ? sanitize_text_field($_POST['templateName']) : 'net-sheet-template';
        
        if (empty($pdf_base64) || !$pdf_data || !isset($pdf_data['values'])) {
            wp_send_json_error(array('message' => 'Invalid PDF data provided.'));
            return;
        }

        // Decode the base64 PDF
        $pdf_content = base64_decode(str_replace('data:application/pdf;base64,', '', $pdf_base64));
        
        if (!$pdf_content) {
            wp_send_json_error(array('message' => 'Could not decode PDF data.'));
            return;
        }

        // Set up email
        $subject = apply_filters('recc_email_subject', 'Your Real Estate Closing Calculator Results');
        $message = self::get_email_body($pdf_data, $template_name);
        
        // Check if template loading failed
        if ($message === false) {
            wp_send_json_error(array('message' => 'Email template "' . $template_name . '" not found.'));
            return;
        }
        
        // Set up attachment with unique folder
        $upload_dir = wp_upload_dir();
        $unique_folder_name = 'real-estate-closing-calculator-' . time();
        $unique_folder_path = $upload_dir['path'] . '/' . $unique_folder_name;
        
        // Create unique folder if it doesn't exist
        if (!file_exists($unique_folder_path)) {
            wp_mkdir_p($unique_folder_path);
        }
        
        $pdf_filename = 'real-estate-closing-calculator.pdf';
        $pdf_path = $unique_folder_path . '/' . $pdf_filename;
        
        // Save PDF file temporarily
        file_put_contents($pdf_path, $pdf_content);
        
        // Set up email headers
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_bloginfo('admin_email') . '>',
        );
        
        // Send email with attachment
        $mail_sent = wp_mail($to, $subject, $message, $headers, array($pdf_path));
        
        // Delete the temporary file and folder
        @unlink($pdf_path);
        @rmdir($unique_folder_path);
        
        if ($mail_sent) {
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    }
    
    /**
     * Get email body HTML
     * 
     * @param array $pdf_data The calculator data
     * @param string $template_name The template name to use
     * @return string Email body HTML
     */
    private static function get_email_body($pdf_data, $template_name = 'net-sheet-template') {
        // Pass all available data to template
        $template_data = array(
            'pdf_data' => $pdf_data,
            'site_name' => get_bloginfo('name'),
            'site_url' => get_bloginfo('url'),
            'site_admin_email' => get_bloginfo('admin_email'),
            'current_year' => date('Y'),
            'template_name' => $template_name
        );
        
        // Load the template
        return self::load_template($template_name, $template_data);
    }
    
    /**
     * Load email template from file
     * 
     * @param string $template_name The template name
     * @param array $variables Variables to pass to the template
     * @return string|false Template HTML or false if template not found
     */
    private static function load_template($template_name, $variables = array()) {
        // Define template directory
        $template_dir = plugin_dir_path(__FILE__) . '../templates/email/';
        $template_file = $template_dir . $template_name . '.php';
        
        // Check if template file exists
        if (file_exists($template_file)) {
            // Extract variables for use in template
            extract($variables);
            
            // Start output buffering
            ob_start();
            
            // Include the template file
            include $template_file;
            
            // Get the content and clean buffer
            $template_content = ob_get_clean();
            
            return $template_content;
        }
        
        // Return false if template doesn't exist
        return false;
    }
}

// Initialize the email handler
RECC_Email_Handler::init();
