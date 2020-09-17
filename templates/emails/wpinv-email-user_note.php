<?php
/**
 * Template that generates the user note invoice email.
 *
 * This template can be overridden by copying it to yourtheme/invoicing/email/wpinv-email-user_note.php.
 *
 * @version 1.0.19
 */

defined( 'ABSPATH' ) || exit;

do_action( 'wpinv_email_header', $email_heading, $invoice, $email_type, $sent_to_admin );

do_action( 'wpinv_email_before_note_details', $invoice, $email_type, $sent_to_admin, $customer_note );

// Generate the custom message body.
echo $message_body;

do_action( 'wpinv_email_invoice_details', $invoice, $email_type, $sent_to_admin );

do_action( 'wpinv_email_invoice_items', $invoice, $email_type, $sent_to_admin );

do_action( 'wpinv_email_billing_details', $invoice, $email_type, $sent_to_admin );

do_action( 'wpinv_email_after_note_details', $invoice, $email_type, $sent_to_admin, $customer_note );

do_action( 'wpinv_email_footer', $invoice, $email_type, $sent_to_admin );