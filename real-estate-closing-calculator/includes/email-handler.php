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

        // Get PDF data
        $pdf_base64 = isset($_POST['pdfBase64']) ? $_POST['pdfBase64'] : '';
        $pdf_data = isset($_POST['pdfData']) ? $_POST['pdfData'] : null;
        
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
        $message = self::get_email_body($pdf_data);
        
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
     * @return string Email body HTML
     */
    private static function get_email_body($pdf_data) {
        $site_name = get_bloginfo('name');
        $site_url = get_bloginfo('url');
        
        $estimated_net = isset($pdf_data['values']['estimated_net_proceeds']) ? '$' . number_format($pdf_data['values']['estimated_net_proceeds'], 2) : 'N/A';
        $purchase_price = isset($pdf_data['values']['purchase_price']) ? '$' . number_format($pdf_data['values']['purchase_price'], 2) : 'N/A';
        
        $email_body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #f7f7f7; padding: 20px; border-bottom: 3px solid #ddd;">
                <h1 style="color: #333; margin: 0;">Real Estate Closing Calculator Results</h1>
                <p style="margin: 5px 0 0 0;">From ' . esc_html($site_name) . '</p>
            </div>
            
            <div style="padding: 20px;">
                <p>Thank you for using our Real Estate Closing Calculator. Please find your results attached as a PDF document.</p>
                
                <div style="background-color: #f9f9f9; border: 1px solid #ddd; padding: 15px; margin: 20px 0;">
                    <h3 style="margin-top: 0;">Summary:</h3>
                    <p><strong>Purchase Price:</strong> ' . esc_html($purchase_price) . '</p>
                    <p><strong>Estimated Net Proceeds:</strong> ' . esc_html($estimated_net) . '</p>
                </div>
                
                <p>For a detailed breakdown of all costs and calculations, please review the attached PDF.</p>
                
                <p style="margin-top: 30px;">If you have any questions, please don\'t hesitate to contact us.</p>
            </div>
            
            <div style="background-color: #f7f7f7; padding: 15px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #ddd;">
                <p>&copy; ' . date('Y') . ' ' . esc_html($site_name) . ' | <a href="' . esc_url($site_url) . '" style="color: #666;">' . esc_url($site_url) . '</a></p>
            </div>
        </body>
        </html>';
        
        return $email_body;
    }
}

// Initialize the email handler
RECC_Email_Handler::init();
