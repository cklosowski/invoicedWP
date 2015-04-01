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

    if (get_post_type($post) == 'invoicedwp') {
    	$custom = get_post_custom();
    	
        $iwp = array( 'isQuote' => 0, 'reoccuringPayment' => 0, 'minPayment' => 0, 'minPaymentText' => '' );

    	if( ! empty($custom['_invoicedwp']) ){
			$newiwp = maybe_unserialize( $custom['_invoicedwp'][0] );
            $iwp = array_merge( $iwp, $newiwp );
    	}

    	wp_nonce_field( plugin_basename(__FILE__), 'iwp_extra_nonce' );

    	$display = '<input type="checkbox" name="isQuote" id="isQuote" value="' . $iwp['isQuote'] . '" ' . checked( $iwp['isQuote'], 1, false ) .' /> <label for="isQuote" class="">Quote</label><br />';

		$display .= '<input type="checkbox" name="reoccuringPayment" id="reoccuringPayment" value="' . $iwp['reoccuringPayment'] . '" ' . checked( $iwp['reoccuringPayment'], 1, false ) .' /> <label for="reoccuringPayment" class="">Reoccuring Bill</label><br />';
    	// Need to add jQuery to update this section to slide open when the box is checked.    	
    	
        $display .= '<input type="checkbox" name="minPayment" id="minPayment" value="' . $iwp['minPayment'] . '" ' . checked( $iwp['minPayment'], 1, false ) .' /> <label for="minPayment" class="">Minimum Payment</label><br />';    
    	$display .= '<div style="display: none;"><input type="text" name="minPaymentText" id="minPaymentText" value="' . $iwp['minPaymentText'] . '" placeholder="Total On Invoice" /><br /></div>'; // Need to add jQuery to update the place holder to be the invoice total.
    	

        echo '<div class="misc-pub-section misc-pub-section-last" style="border-top: 1px solid #eee;">';
    	echo apply_filters( 'iwp_extra_options', $display );
        echo '</div>';
    }
}
add_action( 'post_submitbox_misc_actions', 'myInvoiceSettings' );



function save_myInvoiceSettings($post_id) {
	
	$custom = get_post_custom( $post_id );
	if( !empty($custom['_invoicedwp']) )
		$iwp = maybe_unserialize( $custom );
	
    if (!isset($_POST['post_type']) )
        return;

    if ( !wp_verify_nonce( $_POST['iwp_extra_nonce'], plugin_basename(__FILE__) ) )
        return;

    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
        return;

    if ( 'invoicedwp' == $_POST['post_type'] && !current_user_can( 'edit_post', $post_id ) )
        return;
    
    if (isset($_POST['isQuote'])) {
        $iwp['isQuote'] = 1;
    } else {
    	$iwp['isQuote'] = 0;
    }

    if (isset($_POST['reoccuringPayment'])) {
        $iwp['reoccuringPayment'] = 1;
    } else {
        $iwp['reoccuringPayment'] = 0;
    }

    if (isset($_POST['minPayment'])) {
        $iwp['minPayment'] = 1;
    } else {
        $iwp['minPayment'] = 0;
    }    



    $count = 0;
    $i = 0;
    $reportOptions = array();

    $totalLines = count( $_POST["iwp_invoice_price"] );

    $invoicePost["iwp_invoice_name"]            = $_POST["iwp_invoice_name"];
    $invoicePost["iwp_invoice_description"]     = $_POST["iwp_invoice_description"];
    $invoicePost["iwp_invoice_qty"]             = $_POST["iwp_invoice_qty"];
    $invoicePost["iwp_invoice_price"]           = $_POST["iwp_invoice_price"];
    $invoicePost["iwp_invoice_total"]           = $_POST["iwp_invoice_total"];

    foreach ( $invoicePost as $key => $value)
        foreach( $value as $lines )
            $reportOptions[$key][] = $lines;

    update_post_meta( $post_id, '_invoicedLineItems', $reportOptions ); // Main Line items for the invoice
    update_post_meta( $post_id, '_invoicedClientInfo', $_POST["iwp_invoice"]["user_data"] );
    update_post_meta( $post_id, '_invoicedwp', $iwp ); // Saves if the invoice is only a quote

}
add_action( 'save_post', 'save_myInvoiceSettings' );


function iwp_get_roles() {
    global $wp_roles;

	$list = array();
    $all_roles = $wp_roles->roles;
	foreach($all_roles as $role)
		$list[] = $role["name"];

    return apply_filters('iwp_get_roles', $list);
}
