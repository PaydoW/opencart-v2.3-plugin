<?php
// Heading
$_['heading_title'] = 'Paydo';
$_['heading_invoices'] = 'Paydo invoices';

// Text
$_['text_payment']   = 'Payments';
$_['text_extension'] = 'Extensions';
$_['text_success']   = 'Success: You have modified Paydo settings!';
$_['text_edit']      = 'Edit Paydo';
$_['text_paydo']     = '<a href="https://paydo.com/en/solution/" target="_blank"><img src="view/image/payment/paydo.png" alt="Paydo" title="Paydo" style="max-width:150px;" /></a>';
$_['text_no_results'] = 'No invoice records found.';
$_['text_no_methods'] = 'No methods loaded yet. Enter Project ID and JWT token, then synchronize.';
$_['text_syncing'] = 'Synchronizing...';
$_['text_sync_success'] = 'Synchronized methods: %s. Select the required methods and save the settings.';
$_['text_sync_error'] = 'Could not synchronize payment methods.';
$_['text_search_method'] = 'Search payment method...';

// Entry
$_['entry_public']          = 'Public key';
$_['entry_secret']          = 'Secret key';
$_['entry_complete_status'] = 'Order success status';
$_['entry_pending_status']  = 'Order pending status';
$_['entry_failed_status']   = 'Order failed status';
$_['entry_status']          = 'Status';
$_['entry_sort_order']      = 'Sort order';
$_['entry_methods_mode']     = 'Payment method selection';
$_['entry_hosted_page']      = 'PayDo Hosted Page';
$_['entry_project_id']       = 'Project ID';
$_['entry_jwt_token']        = 'JWT token';
$_['entry_enabled_methods']  = 'Enabled PayDo methods';

// Help
$_['help_methods_mode'] = 'Let the customer choose a specific PayDo payment method during checkout.';
$_['help_hosted_page'] = 'Offer the general PayDo Hosted Page with all methods available for the payer. No paymentMethod ID is sent for this option.';
$_['help_project_id'] = 'PayDo project identifier used to synchronize available payment methods.';
$_['help_jwt_token'] = 'Bearer token used to synchronize payment methods. Invoice creation is authorized by its signature.';
$_['help_enabled_methods'] = 'Only selected methods are shown during checkout. Synchronization does not enable new methods automatically.';

// Column
$_['column_order_id'] = 'Order ID';
$_['column_invoice_id'] = 'Invoice ID';
$_['column_amount'] = 'Amount';
$_['column_currency'] = 'Currency';
$_['column_invoice_status'] = 'Invoice status';
$_['column_transaction_state'] = 'Transaction state';
$_['column_date_created'] = 'Created';
$_['column_date_modified'] = 'Modified';

// Button
$_['button_invoices'] = 'Invoices';
$_['button_sync_methods'] = 'Sync from PayDo';

// Error
$_['error_permission'] = 'Warning: You do not have permission to modify Paydo!';
$_['error_public_id']  = 'Public key is required!';
$_['error_secret_key'] = 'Secret key is required!';
$_['error_project_id'] = 'Project ID is required to synchronize payment methods!';
$_['error_jwt_token'] = 'JWT token is required to synchronize payment methods!';
$_['error_enabled_methods'] = 'Enable PayDo Hosted Page or select at least one specific payment method!';
$_['error_sync_credentials'] = 'Enter Project ID and JWT token first.';
$_['error_sync_request'] = 'Could not send the request to PayDo.';
$_['error_sync_api'] = 'PayDo API returned HTTP status %s.';
$_['error_no_methods'] = 'No enabled payment methods were found in the PayDo response.';
