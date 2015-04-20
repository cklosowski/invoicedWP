<?php
/**
 * Post-Type Setup
 *
 * @package     InvoicedWP
 * @since       1.0.0
 */




/**
 * [ryno_setup_staff_init description]
 * @return [type] [description]
 */
function iwp_setup_init() {
	$labels = array(
		'name' 					=> _x('Invoices', 'post type general name'),
		'singular_name' 		=> _x('Invoice', 'post type singular name'),
		'add_new' 				=> _x('Add Invoice', 'Invoice'),
		'add_new_item' 			=> __('Add Invoice'),
		'edit_item' 			=> __('Edit Invoice'),
		'new_item' 				=> __('New Invoice'),
		'view_item' 			=> __('View Invoice'),
		'search_items' 			=> __('Search Invoices'),
		'exclude_from_search' 	=> true,
		'not_found' 			=>  __('No invoices found'),
		'not_found_in_trash' 	=> __('No invoices found in Trash'),
		'parent_item_colon' 	=> '',
		'all_items' 			=> 'Invoiced WP',
		'menu_name' 			=> 'Invoiced WP'
	);

	$args = array(
		'labels' 				=> $labels,
		'public' 				=> true,
		'publicly_queryable' 	=> true,
		'show_ui' 				=> true,
		'show_in_menu' 			=> true,
		'query_var' 			=> true,
		'rewrite' 				=> true,
		'capability_type' 		=> 'page',
		'has_archive' 			=> false,
		'hierarchical' 			=> false,
		'menu_position' 		=> 20,
		'rewrite' 				=> array('slug'=>'invoiced','with_front'=>false),
		'supports' 				=> array( 'title', 'editor' )
	);

	register_post_type( 'invoicedwp', $args );


	$show_in_menu = 'invoicedwp';

	$temp_labels = array(
		'name'      			=> __( 'Templates', 'invoicedwp' ),
		'singular_name'			=> __( 'Template', 'invoicedwp' ),
		'menu_name'    			=> _x( 'Templates', 'Admin menu name', 'invoicedwp' ),
		'add_new'     			=> __( 'Add Template', 'invoicedwp' ),
		'add_new_item'    		=> __( 'Add New Templates', 'invoicedwp' ),
		'edit'      			=> __( 'Edit', 'invoicedwp' ),
		'edit_item'    			=> __( 'Edit Templates', 'invoicedwp' ),
		'new_item'     			=> __( 'New Templates', 'invoicedwp' ),
		'view'      			=> __( 'View Templatess', 'invoicedwp' ),
		'view_item'    			=> __( 'View Templates', 'invoicedwp' ),
		'search_items'    		=> __( 'Search Templatess', 'invoicedwp' ),
		'not_found'    			=> __( 'No Templatess found', 'invoicedwp' ),
		'not_found_in_trash'	=> __( 'No Templatess found in trash', 'invoicedwp' ),
		'parent'     			=> __( 'Parent Templates', 'invoicedwp' )
	);


	$args = array(
		'labels' 				=> $temp_labels,
		'public' 				=> false,
		'publicly_queryable' 	=> false,
		'show_ui' 				=> true,
		'query_var' 			=> true,
		'rewrite' 				=> true,
		'capability_type' 		=> 'page',
		'has_archive' 			=> false,
		'menu_position' 		=> 20,
		'show_in_menu' 	 		=> 'edit.php?post_type=invoicedwp',
		'hierarchical' 			=> false,
		'supports'   			=> array( 'title', 'comments' )
	);

	register_post_type( 'invoicedWP_template', $args );






	register_post_status( 'quote', array(
		'label'                     => __( 'Quotes', 'invoicedwp' ),
		'public'                    => false,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Quote <span class="count">(%s)</span>', 'Quote <span class="count">(%s)</span>' )
	) );
	register_post_status( 'needPay', array(
		'label'                     => __( 'Needs Payment', 'invoicedwp' ),
		'public'                    => false,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Needs Payment <span class="count">(%s)</span>', 'Needs Payment <span class="count">(%s)</span>' )
	) );
	register_post_status( 'paid', array(
		'label'                     => __( 'Paid', 'invoicedwp' ),
		'public'                    => false,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Paid <span class="count">(%s)</span>', 'Paid <span class="count">(%s)</span>' )
	) );
}

/**
 * Adds custom columns for staff-member CPT admin display
 *
 * @param    array    $cols    New column titles
 * @return   array             Column titles
 */
function iwp_custom_columns( $cols ) {
	$cols = array(
		'cb'			=> '<input type="checkbox" />',
		'title'			=> __( 'Name' ),
		'paid'			=> __( 'Amount Paid' ),
		'recipient'		=> __( 'Recipient' ),
		//'invoiceID'		=> __( 'Invoice ID'),
		'dueDate'		=> __( 'Due Date' ),
		'date'		=> __( 'Creation Date' ),

	);
	return $cols;
}

/**
 * [ryno_staff_display_custom_columns description]
 * @param  [type] $column [description]
 * @return [type]         [description]
 */
function iwp_display_custom_columns( $column ) {
	global $post;

	$iwp = get_post_meta( $post->ID, '_invoicedwp', true );
	$curencyPos = iwp_get_option( 'currency_position', 'before' );

	//$custom = get_post_custom();
	//$iwp_columns = unserialize( $custom['_invoicedwp'][0] );

	//$_staff_title 	= $iwp_columns[""];
	
	switch ( $column ) {
		case "paid":
			$displayTotal = $iwp['invoice_totals']["total"] + $iwp['invoice_totals']["payments"];
			if ( $curencyPos == "before" ) {
				echo iwp_currency_symbol() . iwp_format_amount( $iwp['invoice_totals']["payments"] ) . ' / ' . iwp_currency_symbol() . iwp_format_amount( $displayTotal );
				$unpaid = iwp_currency_symbol() . iwp_format_amount( $iwp['invoice_totals']["total"] );
			} else {
				echo iwp_format_amount( $iwp['invoice_totals']["payments"] ) . iwp_currency_symbol() . ' / ' . iwp_format_amount( $displayTotal ) . iwp_currency_symbol();
				$unpaid = iwp_format_amount( $iwp['invoice_totals']["total"] ) . iwp_currency_symbol();
			}
			if( $iwp['invoice_totals']["total"] > 0 )
				echo '<br />Unpaid: <strong>' . $unpaid . '</strong>';

		break;

		case "recipient":
		 $name = $iwp["user_data"]["first_name"] . ' ' . $iwp["user_data"]["last_name"];
			echo $name;
		break;
		case "invoiceID":
			echo "-";
		break;
		case "dueDate":
			$dueDate = $iwp["paymentDueDate"];
			
			if( $dueDate == 0 ) {
				echo "-";
			} else {
				echo $iwp["paymentDueDateText"];
			}
		break;

	}
}




function include_invoice_template_function( $template_path ) {

    if ( get_post_type() == 'invoicedwp' ) {

        if ( is_single() ) {
            if ( $theme_file = locate_template( array ( 'single-invoicedwp.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . 'single-invoicedwp.php';
            }
        }
    }
    return $template_path;
}





