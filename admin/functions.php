<?php
/**
 * Helper Functions
 *
 * @package     InvoicedWP
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;



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
				'publicly_queryable' 	=> false,
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
				'invoiceID'		=> __( 'Invoice ID')

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
			$iwp_columns = unserialize( $custom['_invoicedwp'][0] );

			//$_staff_title 	= $iwp_columns[""];
			
			/*switch ( $column ) {
				case "photo":
					if( has_post_thumbnail() ){
						echo get_the_post_thumbnail( $post->ID, array( 75, 75 ) );
					}
				break;

			}*/
		}

add_action( 'post_submitbox_misc_actions', 'myInvoiceSettings' );
add_action( 'save_post', 'save_myInvoiceSettings' );
function myInvoiceSettings() {
    global $post;

    if (get_post_type($post) == 'invoicedwp') {
    	$custom = get_post_custom();
    	
    	if( !empty($custom['_invoicedwp']) ){
			$iwp = maybe_unserialize( $custom['_invoicedwp'][0] );
    	} else {
    		$iwp = array( 'isQuote' => 0 );

    	}

        echo '<div class="misc-pub-section misc-pub-section-last" style="border-top: 1px solid #eee;">';
    
        wp_nonce_field( plugin_basename(__FILE__), 'isQuote_nonce' );
    
        echo '<input type="checkbox" name="isQuote" id="isQuote" value="' . $iwp['isQuote'] . '" ' . checked( $iwp['isQuote'], 1, false ) .' /> <label for="isQuote" class="">Quote</label><br />';
        echo '</div>';
    }
}

function save_myInvoiceSettings($post_id) {
	
	$custom = get_post_custom( $post_id );
	if( !empty($custom['_invoicedwp']) )
		$iwp = maybe_unserialize( $custom['_invoicedwp'][0] );

	//var_dump($_POST);
	
    if (!isset($_POST['post_type']) )
        return;

    if ( !wp_verify_nonce( $_POST['isQuote_nonce'], plugin_basename(__FILE__) ) )
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

    update_post_meta( $post_id, '_invoicedwp', $iwp );


}


function iwp_get_roles() {
    global $wp_roles;

	$list = array();
    $all_roles = $wp_roles->roles;
	foreach($all_roles as $role)
		$list[] = $role["name"];

    return apply_filters('iwp_get_roles', $list);
}
