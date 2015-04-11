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

	register_post_type( 'invoicedWP_template',
		array(
			'labels' => array(
				'name'      			=> __( 'Line Items', 'invoicedwp' ),
				'singular_name'			=> __( 'Line Item', 'invoicedwp' ),
				'menu_name'    			=> _x( 'Line Items', 'Admin menu name', 'invoicedwp' ),
				'add_new'     			=> __( 'Add Line Items', 'invoicedwp' ),
				'add_new_item'    		=> __( 'Add New Line Items', 'invoicedwp' ),
				'edit'      			=> __( 'Edit', 'invoicedwp' ),
				'edit_item'    			=> __( 'Edit Line Items', 'invoicedwp' ),
				'new_item'     			=> __( 'New Line Items', 'invoicedwp' ),
				'view'      			=> __( 'View Line Itemss', 'invoicedwp' ),
				'view_item'    			=> __( 'View Line Items', 'invoicedwp' ),
				'search_items'    		=> __( 'Search Line Itemss', 'invoicedwp' ),
				'not_found'    			=> __( 'No Line Itemss found', 'invoicedwp' ),
				'not_found_in_trash'	=> __( 'No Line Itemss found in trash', 'invoicedwp' ),
				'parent'     			=> __( 'Parent Line Items', 'invoicedwp' )
				),

			'public'  				=> true,
			'has_archive' 			=> true,
			'publicly_queryable'	=> false,
			'exclude_from_search'	=> true,
			'show_in_menu' 	 		=> 'edit.php?post_type=invoicedwp',
			'hierarchical' 			=> false,
			'supports'   			=> array( 'title', 'comments' )
		)
	);


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
		'invoiceID'		=> __( 'Invoice ID'),
		'dueDate'		=> __( 'Due Date' ),

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

	$custom = get_post_custom();
	//$iwp_columns = unserialize( $custom['_invoicedwp'][0] );

	//$_staff_title 	= $iwp_columns[""];
	
	/*switch ( $column ) {
		case "photo":
			if( has_post_thumbnail() ){
				echo get_the_post_thumbnail( $post->ID, array( 75, 75 ) );
			}
		break;

	}*/
}