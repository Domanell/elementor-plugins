<?php
/**
 * Buyer Estimated Cash to Close Calculator Template 
 * 
 * Available variables:
 * $pdf_data - Complete PDF data array
 * $site_name - Site name
 * $site_url - Site URL
 * $site_admin_email - Site admin email
 * $current_year - Current year
 * $template_name - Template name being used
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Extract buyer-specific data with fallbacks
$purchase_price = isset($pdf_data['values']['purchase_price']) ? '$' . number_format($pdf_data['values']['purchase_price'], 2) : 'N/A';
$estimated_net = isset($pdf_data['values']['estimated_net_proceeds']) ? '$' . number_format($pdf_data['values']['estimated_net_proceeds'], 2) : 'N/A';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto;">
    <div style="background-color: #f7f7f7; padding: 20px; border-bottom: 3px solid #ddd;">
        <h1 style="color: #333; margin: 0;">Buyer Estimated Cash to Close Calculator Results</h1>
        <p style="margin: 5px 0 0 0;">From <?php echo esc_html($site_name); ?></p>
    </div>
    
    <div style="padding: 20px;">
        <p>Thank you for using our Buyer Estimated Cash to Close Calculator. Please find your results attached as a PDF document.</p>
        
        <div style="background-color: #f9f9f9; border: 1px solid #ddd; padding: 15px; margin: 20px 0;">
            <h3 style="margin-top: 0;">Summary:</h3>
            <p><strong>Purchase Price:</strong> <?php echo esc_html($purchase_price); ?></p>
            <p><strong>Estimated Net Proceeds:</strong> <?php echo esc_html($estimated_net); ?></p>
        </div>
        
        <p>For a detailed breakdown of all costs and calculations, please review the attached PDF.</p>
        
        <p style="margin-top: 30px;">If you have any questions, please don't hesitate to contact us.</p>
    </div>
    
    <div style="background-color: #f7f7f7; padding: 15px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #ddd;">
        <p>&copy; <?php echo date('Y'); ?> <?php echo esc_html($site_name); ?> | <a href="<?php echo esc_url($site_url); ?>" style="color: #666;"><?php echo esc_url($site_url); ?></a></p>
    </div>
</body>
</html>
