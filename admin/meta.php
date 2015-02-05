<?php
/**
 * Helper Functions
 *
 * @package     InvoicedWP
 * @since 1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;



function iwp_client($invoice_id) {
?>

<table class="form-table wp_invoice_new_user">
	<tbody>

		<tr>
			<th>First Name</th>
			<td><input title="" value="" name="iwp_invoice[user_data][first_name]" class="input_field  iwp_first_name" type="text" id=""></td>
		</tr>
		<tr>
			<th>Last Name</th>
			<td><input title="" value="" name="iwp_invoice[user_data][last_name]" class="input_field  iwp_last_name" type="text" id=""></td>
		</tr>
		<tr>
			<th>Company Name</th>
			<td><input title="" value="" name="iwp_invoice[user_data][company_name]" class="input_field  iwp_company_name" type="text" id=""></td>
		</tr>
		<tr>
			<th>Phone Number</th>
			<td><input title="" value="" name="iwp_invoice[user_data][phonenumber]" class="input_field  iwp_phonenumber" type="text" id=""></td>
		</tr>
		<tr>
			<th>Street Address</th>
			<td><input title="" value="" name="iwp_invoice[user_data][streetaddress]" class="input_field  iwp_streetaddress" type="text" id=""></td>
		</tr>
		<tr>
			<th>Street Address 2</th>
			<td><input title="" value="" name="iwp_invoice[user_data][streetaddress2]" class="input_field  iwp_streetaddress2" type="text" id=""></td>
		</tr>
		<tr>
			<th>City</th>
			<td><input title="" value="" name="iwp_invoice[user_data][city]" class="input_field  iwp_city" type="text" id=""></td>
		</tr>
		<tr>
			<th>State</th>
			<td><input title="" value="" name="iwp_invoice[user_data][state]" class="input_field  iwp_state" type="text" id=""></td>
		</tr>
		<tr>
			<th>ZIP</th>
			<td><input title="" value="" name="iwp_invoice[user_data][zip]" class="input_field  iwp_zip" type="text" id=""></td>
		</tr>
	</tbody>
</table>


<?php
}











function iwp_details($invoice_id) {
	?>

	<table id="invoice" class="widefat" cellspacing="0">
		<thead>
			<tr>
				<th class="check-column"><input type="checkbox"></th>
				<th style="width:50%"><?php _e( 'Name', 'iwp' ); ?></th>
				<th><?php _e( 'Price', 'iwp' ); ?></th>
				<th><?php _e( 'Qty', 'iwp' ); ?></th>
				<th><?php _e( 'Total', 'iwp' ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">Balance: here</td>
			</tr>
			<tr>
				<th colspan="5"><a href="#" class="add button" style="margin-left: 24px"><?php _e( '+ Add Rate', 'iwp' ); ?></a> <a href="#" class="remove button"><?php _e( 'Delete selected rates', 'iwp' ); ?></a> <a href="#" class="discount button"><?php _e( 'Discount?', 'iwp' ); ?></a></th>
			</tr>
		</tfoot>
		<tbody class="invoicedItems">

		<?php


		$custom = get_post_custom();
    	if( !empty( $custom['_invoicedwp'] ) ){
			$iwp = maybe_unserialize( $custom['_invoicedwp'][0] );
/*


			$i = -1;
			if ( $this->table_rates ) {
				foreach ( $this->table_rates as $class => $rate ) {
					$i++;
					echo '<tr class="table_rate">
							<th class="check-column"><input type="checkbox" name="select" /></th>
							<td><input type="text" step="any" min="0" value="' . esc_attr( $rate['minO'] ) . '" name="' . esc_attr( $this->id .'_minO[' . $i . ']' ) . '" style="width: 90%" class="' . esc_attr( $this->id .'field[' . $i . ']' ) . '" placeholder="'.__( '0.00', 'iwp' ).'" size="10" /></td>
							<td><input type="number" step="any" min="0" value="' . esc_attr( $rate['maxO'] ) . '" name="' . esc_attr( $this->id .'_maxO[' . $i . ']' ) . '" style="width: 90%" class="' . esc_attr( $this->id .'field[' . $i . ']' ) . '" placeholder="'.__( '0.00', 'iwp' ).'" size="2" /></td>
							<td><input type="number" step="any" min="0" value="' . esc_attr( $rate['shippingO'] ) . '" name="' . esc_attr( $this->id .'_shippingO[' . $i . ']' ) . '" style="width: 90%" class="' . esc_attr( $this->id .'field[' . $i . ']' ) . '" placeholder="'.__( '0.00', 'iwp' ).'" size="2" /></td>
							<td><input type="number" step="any" min="0" value="' . esc_attr( $rate['totalO'] ) . '" name="' . esc_attr( $this->id .'_total0[' . $i . ']' ) . '" style="width: 90%" class="' . esc_attr( $this->id .'field[' . $i . ']' ) . '" placeholder="'.__( '0.00', 'iwp' ).'" size="4" /></td>
					</tr>';
				}
			}

*/

		} else {
			echo '<tr class="invoiceItem">
						<th class="check-column"><input type="checkbox" name="select" /></th>
						<td><input type="text" value="" name="itemName0" style="width: 90%" class="" placeholder="Item" size="10" /></td>
						<td><input type="number" step="any" min="0" value="" name="_itemPrice[0]" style="width: 90%" class="" placeholder="'.__( '0.00', 'iwp' ).'" size="2" /></td>
						<td><input type="number" step="any" min="0" value="" name="_itemQTY[0]" style="width: 90%" class="" placeholder="'.__( '0', 'iwp' ).'" size="2" /></td>
						<td class="lineTotal"></td>
			</tr>';
		}
			


		?>
		</tbody>
	</table>


	<script type="text/javascript">
		// Add a row
		jQuery( document ).ready(function( $ ) {
			$('#invoice').on( 'click', 'a.add', function( event ){
				var size = $( this ).closest( '#invoice' ).find('tbody.invoicedItems tr').size();						
				jQuery( '#invoice .invoicedItems tr:last').after('<tr class="invoiceItem">\
					<th class="check-column"><input type="checkbox" name="select" /></th>\
					<td><input type="text" value="" name="itemName[' + size + ']" style="width: 90%" class="" placeholder="Item" size="10" /></td>\
					<td><input type="number" step="any" min="0" name="_itemPrice[' + size + ']" style="width: 90%" class="" placeholder="0.00" size="4" /></td>\
					<td><input type="number" step="any" min="0" name="_itemQTY[' + size + ']" style="width: 90%" class="" placeholder="0.00" size="4" /></td>\
					<td class="lineTotal"></td>\
				</tr>');

				event.preventDefault();

			});
		// Remove a row
		jQuery('#invoice').on( 'click', 'a.remove', function(){
				var answer = confirm("<?php _e( 'Delete the selected rates?', 'iwp' ); ?>")
					if (answer) {
						jQuery('#invoice .invoicedItems tr th.check-column input:checked').each(function(i, el){
						jQuery(el).closest('tr').remove();
					});
				}
				return false;
			});

		//Add Discount
			$('#invoice').on( 'click', 'a.discount', function( event ){
				var size = $( this ).closest( '#invoice' ).find('tfoot tr:first').size();						
				jQuery( '#invoice tfoot tr:first').before('<tr class="invoiceItem">\
					<th class="check-column" style="border-top: solid 0px #fff;"><input type="checkbox" name="select" /></th>\
					<td><input type="text" value="" name="itemName[' + size + ']" style="width: 90%" class="" placeholder="Item" size="10" /></td>\
					<td colspan="2"><input type="number" step="any" min="0" name="_itemPrice[' + size + ']" style="width: 90%" class="" placeholder="0.00" size="4" /></td>\
					<td class="lineTotal"><input type="number" step="any" min="0" name="_discountTotatl" style="width: 90%" class="" placeholder="0.00" size="4" /></td>\
				</tr>');
				jQuery( 'a.discount' ).hide();

				event.preventDefault();

			});


		});
	</script>

<?php


}



