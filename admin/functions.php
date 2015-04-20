<?php
/**
 * Helper Functions
 *
 * @package     InvoicedWP
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;


function myInvoiceSettings() {
    global $post;

    wp_nonce_field('iwp_extra_nonce', 'iwp_extra_nonce' );

    if ( ( get_post_type($post) == 'invoicedwp' ) || ( get_post_type($post) == 'invoicedwp_template' ) ) {
    	$custom = get_post_custom();
    	
        $iwp = array( 'isQuote' => 0, 'reoccuringPayment' => 0, 'minPayment' => 0, 'minPaymentText' => '' );

    	if( ! empty($custom['_invoicedwp']) ){
			$newiwp = maybe_unserialize( $custom['_invoicedwp'][0] );
            $iwp = array_merge( $iwp, $newiwp );
    	}

        if ( get_post_type($post) == 'invoicedwp') {

        	$display = '<input type="checkbox" name="isQuote" id="isQuote" value="' . $iwp['isQuote'] . '" ' . checked( $iwp['isQuote'], 1, false ) .' /> <label for="isQuote" class="">Quote</label><br />';

    		$display .= '<input style="display: none;" type="checkbox" name="reoccuringPayment" id="reoccuringPayment" value="' . $iwp['reoccuringPayment'] . '" ' . checked( $iwp['reoccuringPayment'], 1, false ) .' /> <label for="reoccuringPayment" class="" style="display: none;">Reoccuring Bill</label>';
            $display .= '<input type="text" name="reoccuringPaymentText" id="reoccuringPaymentText" value="' . $iwp['reoccuringPaymentText'] . '" placeholder="Number of Days to Next Payment"  style="width: 100%; display: none;" /><br />'; // Need to add jQuery to update the place holder to be the invoice total.
            // Need to add jQuery to update this section to slide open when the box is checked.    	
        	
            if( $iwp['minPayment'] == 0 ) 
                $displayMinPayment = 'display: none;';

            $display .= '<input type="checkbox" name="minPayment" id="minPayment" value="' . $iwp['minPayment'] . '" ' . checked( $iwp['minPayment'], 1, false ) .' /> <label for="minPayment" class="">Minimum Payment</label>';    
        	$display .= '<input type="text" name="minPaymentText" id="minPaymentText" value="' . $iwp['minPaymentText'] . '" placeholder="Minimum Payment"  style="width: 100%; ' . $displayMinPayment . '" /><br />'; // Need to add jQuery to update the place holder to be the invoice total.
        	
            if( $iwp['paymentDueDate'] == 0 ) 
                $displayDueDate = 'display: none;';

            $display .= '<input type="checkbox" name="paymentDueDate" id="dueDate" value="' . $iwp['paymentDueDate'] . '" ' . checked( $iwp['paymentDueDate'], 1, false ) .' /> <label for="paymentDueDate" class="">Set Due Date</label>';    
            $display .= '<input type="text" name="paymentDueDateText" id="dueDateText" value="' . $iwp['paymentDueDateText'] . '" placeholder="Due Date"  style="width: 100%; ' . $displayDueDate . ' " class="iwp-date-picker" /><br />'; // Need to add jQuery to update the place holder to be the invoice total.

            echo '<div class="misc-pub-section misc-pub-section-last" style="border-top: 1px solid #eee;">';
        	echo apply_filters( 'iwp_extra_options', $display );
            echo '</div>';


        }
    }
}
add_action( 'post_submitbox_misc_actions', 'myInvoiceSettings' );



function save_myInvoiceSettings($post_id) {
	
    if (!isset($_POST['post_type']) )
        return;

    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
        return;

    if ( ('invoicedwp' == $_POST['post_type'] || 'invoicedwp_template' == $_POST['post_type'] ) && !current_user_can( 'edit_post', $post_id ) )
        return;

    if ( ! wp_verify_nonce( $_POST['iwp_extra_nonce'], 'iwp_extra_nonce' ) )
        return;

    $iwp = get_post_meta( $post_id, '_invoicedwp', true );
    
    if ( 'invoicedwp' == $_POST['post_type'] ) {
        if (isset($_POST['isQuote'])) {
            $iwp['isQuote'] = 1;
        } else {
        	$iwp['isQuote'] = 0;
        }

        if (isset($_POST['reoccuringPayment'])) {
            $iwp['reoccuringPayment'] = 1;
            $iwp['reoccuringPaymentText'] = iwp_sanitize( $_POST['reoccuringPaymentText'] );
        } else {
            $iwp['reoccuringPayment'] = 0;
            $iwp['reoccuringPaymentText'] = '';
        }
        
        if (isset($_POST['minPayment'])) {
            $iwp['minPayment'] = 1;
            $iwp['minPaymentText'] = iwp_sanitize( $_POST['minPaymentText'] );
        } else {
            $iwp['minPayment'] = 0;
            $iwp['minPaymentText'] = '';
        }   
        
        if (isset($_POST['paymentDueDate'])) {
            $iwp['paymentDueDate'] = 1;
            $iwp['paymentDueDateText'] = iwp_sanitize( $_POST['paymentDueDateText'] );
        } else {
            $iwp['paymentDueDate'] = 0;
            $iwp['paymentDueDateText'] = '';
        }
        
    }

    $count = 0;
    $i = 0;
    $reportOptions = array();

    $totalLines = count( $_POST["iwp_invoice_price"] );

    // Handles the Invoice fields
    $invoicePost["iwp_invoice_name"]            = iwp_sanitize( $_POST["iwp_invoice_name"] );
    $invoicePost["iwp_invoice_description"]     = iwp_sanitize( $_POST["iwp_invoice_description"] );
    $invoicePost["iwp_invoice_qty"]             = iwp_sanitize( $_POST["iwp_invoice_qty"] );
    $invoicePost["iwp_invoice_price"]           = iwp_sanitize( $_POST["iwp_invoice_price"] );
    $invoicePost["iwp_invoice_total"]           = iwp_sanitize( $_POST["iwp_invoice_total"] );

    foreach ( $invoicePost as $key => $value)
        foreach( $value as $lines )
            $reportOptions[$key][] = $lines;

    $iwp['lineItems']       = $reportOptions;
    $iwp['invoice_notice']  = iwp_sanitize( $_POST["iwp_notice"] );
    $iwp['invoice_totals']  = iwp_sanitize( $_POST["iwp_totals"] );
    $iwp['user_data']       = iwp_sanitize( $_POST["iwp_invoice"]["user_data"] );

    // Handles Payments
    if( ! empty( $_POST["iwp_payment_amount"] ) ) {
        $iwp["iwp_invoice_payment"]["amount"][] = iwp_sanitize( $_POST["iwp_payment_amount"] );
        $iwp["iwp_invoice_payment"]["method"][] = $_POST["iwp_payment_method"];
    }

    $payment = 0;
    foreach ($iwp["iwp_invoice_payment"]["amount"] as $key => $value)
        $payment += $value;

    $iwp['invoice_totals']["payments"]  = $payment;

    update_post_meta( $post_id, '_invoicedwp', $iwp ); // Saves if the invoice is only a quote

}
add_action( 'save_post', 'save_myInvoiceSettings' );


function secureLink( $post_ID, $post, $update ) {
    global $wpdb;

    $where = array( 'ID' => $post_ID );
    $postNameMD5 = md5( $post->post_name );

    $wpdb->update( $wpdb->posts, array( 'post_name' => $postNameMD5 ), $where );

}
add_action( 'save_post_invoicedwp', 'secureLink', 10, 3 );



function iwp_sanitize( $items ) {
    if( ! is_array( $items ) )
        return sanitize_text_field( $items );

    $newItem = array(); 
    foreach( $items as $key => $item )
        $newItem[$key] = sanitize_text_field( $item );

    return $newItem;
}

function iwp_get_roles() {
    global $wp_roles;

	$list = array();
    $all_roles = $wp_roles->roles;
	foreach($all_roles as $role)
		$list[] = $role["name"];

    return apply_filters('iwp_get_roles', $list);
}




