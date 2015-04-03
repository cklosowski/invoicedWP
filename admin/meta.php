<?php
/**
 * Helper Functions
 *
 * @package     InvoicedWP
 * @since 1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;



function iwp_details($post_id) {
		wp_enqueue_script( 'wc_invoiced_writepanel_js' );

		$values = get_post_meta( $post_id->ID, '_invoicedwp', true );
		$values = $values['lineItems'];

		$count = count( $values["iwp_invoice_name"] );

		?>
		<style type="text/css">
			#minor-publishing-actions, #visibility { display:none }

			dt {
				clear: left;
				float: left;
				text-align: left;

			}

			dd {
				clear: right;
			    float: right;
			    font-weight: bold;
			    margin-left: 0;
			    margin-right: 15px;
			    text-align: right;
			    width: 130px;

			}

			input.iwp_flatten_input {
				background: none repeat scroll 0 0 transparent !important;
				border: 0 none !important;
				color: #000 !important;
				box-shadow: none !important;
			}
		</style>

		<div class="iwp_options_panel iwp">
			<div class="panel-wrap" id="invoiced_availability">
				<div class="options_group">
					<div class="table_grid">
						<table id="invoicedDisplay" class="widefat">
							<thead>
								<tr>
									<th class="sort" width="1%">&nbsp;</th>
									<th><?php _e( 'Name', 'iwp-invoiced' ); ?></th>
									<th style="width: 70px;" ><?php _e( 'Qty', 'iwp-invoiced' ); ?></th>
									<th style="width: 70px;" ><?php _e( 'Price', 'iwp-invoiced' ); ?></th>
									<th style="width: 90px;" ><?php _e( 'Total', 'iwp-invoiced' ); ?></th>
									<th class="remove" style="width:20px !important;">&nbsp;</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<td colspan="6" style="background-color: #f9f9f9;">
										<dl style="width: 300px; float: right;">
											<dt class="hidden column-invoice-details-subtotal">Subtotal Excluding Tax:</dt>
											<dd class="hidden column-invoice-details-subtotal"><input value="" disabled="true" class="calculate_invoice_subtotal iwp_flatten_input"></dd>
											<dt class="hidden column-invoice-details-adjustments" style="display: none;">Adjustments:</dt>
											<dd class="hidden column-invoice-details-adjustments" style="display: none;"><input value="" disabled="true" class="calculate_invoice_adjustments iwp_flatten_input"></dd>
											<dt class="hidden column-invoice-details-discounts" style="display: none;">Discount:</dt>
											<dd class="hidden column-invoice-details-discounts" style="display: none;"><input value="" disabled="true" class="iwp_flatten_input calculate_discount_total"></dd>
											<dt class="hidden column-invoice-details-tax" style="display: none;">Sales Tax:</dt>
											<dd class="hidden column-invoice-details-tax" style="display: none;"><input value="" disabled="true" class="calculate_invoice_tax iwp_flatten_input"></dd>
											<dt><b>Balance:</b></dt>
											<dd><input value="0.00" disabled="true" class="calculate_invoice_total iwp_flatten_input"></dd>
										</dl>
						            </td>
								</tr>


								<tr>
									<th colspan="6">
										<a href="#" class="button button-primary add_discount" data-row="<?php
											ob_start();
											include( 'templates/meta-discount.php' );
											$html = ob_get_clean();
											echo esc_attr( $html );
										?>" style="margin-left: 10px;"><?php _e( 'Add Discount', 'iwp-invoiced' ); ?></a>
										<a href="#" class="button button-primary add_row" style="margin-left: 10px;"><?php _e( 'Add Line', 'iwp-invoiced' ); ?></a>
									<?php 
										//if( isset( $_REQUEST['post_type'] ) ) {
											if( $_REQUEST['post_type'] <> 'invoicedwp_template' ) { ?>
												<select style="float: right;">
													<option value=""></option>
													<option value="">Test Value to make sure it fits the value on the page.</option>
												</select>
											<?php } 
										//}
									?>

									</th>
								</tr>
							</tfoot>



							<tbody id="invoiced_rows">
							<?php if( $count == 1 ) { ?>
								<tr>
									<td class="sort">&nbsp;</td>
									<td style="border-right: 0 none !important;"> <?php // Name ?>
										<input class="item_name input_field" value="" name="iwp_invoice_name[0]">
										<span style="text-size 9px;"><a class="toggleDescription"  href="#" >Add Description</a></span>
										<textarea class="item_name input_field iwp_invoice_description" value="" name="iwp_invoice_description[0]" id="iwp_invoice_description[0]" style="display: none; width: 100%; margin-top: 5px; font-size= 0.88em;" placeholder="Description"></textarea>
									</td>
									<td style="border-right: 0 none !important;"> <?php // Qty ?>
										<input class="changesNo item_name input_field input_qty" value="" name="iwp_invoice_qty[0]" id="iwp_invoice_qty[0]">
									</td>
									<td style="border-right: 0 none !important;"> <?php // price ?>
										<input class="changesNo item_name input_field input_price" value="" name="iwp_invoice_price[0]" id="iwp_invoice_price[0]">
									</td>
									<td> <?php // Total ?>
										$ <input class="calculate_invoice_total input_total iwp_flatten_input" disabled="true" value="<?php echo $values["iwp_invoice_total"][0]; ?>" placeholder="0.00">
										<input class="hidden_total input_total" name="iwp_invoice_total[0]" id="iwp_invoice_total[0]" value="<?php echo $values["iwp_invoice_total"][0]; ?>" style="display: none !important;">
									</td>
									<td class="remove">&nbsp;</td>
								</tr>

							<?php }
									$i = 0;
									if( $count != 1 ) {
										for( $i = 0; $i < $count; $i++ ) {
											?>
											<tr>
												<td class="sort">&nbsp;</td>
												<td style="border-right: 0 none !important;"> <?php // Name ?>
													<input class="item_name input_field" value="<?php echo $values["iwp_invoice_name"][$i]; ?>" name="iwp_invoice_name[<?php echo $i; ?>]">
													<?php if( empty( $values["iwp_invoice_description"][$i] ) ) {  ?> <span style="text-size 10px;"><a class="toggleDescription"  href="#" >Add Description</a></span> <?php } ?>
													<textarea class="item_name input_field iwp_invoice_description" value="" name="iwp_invoice_description[<?php echo $i; ?>]" style="<?php if( empty( $values["iwp_invoice_description"][$i] ) ) { echo 'display: none;'; } ?> width: 100%; margin-top: 5px; font-size= 0.88em;" placeholder="Description"><?php echo $values["iwp_invoice_description"][$i]; ?></textarea>
												</td>
												<td style="border-right: 0 none !important;"> <?php // Qty ?>
													<input class="changesNo item_name input_field input_qty" value="<?php echo $values["iwp_invoice_qty"][$i]; ?>" name="iwp_invoice_qty[<?php echo $i; ?>]" id="iwp_invoice_qty[<?php echo $i; ?>]">
												</td>
												<td style="border-right: 0 none !important;"> <?php // price ?>
													<input class="changesNo item_name input_field input_price" value="<?php echo $values["iwp_invoice_price"][$i]; ?>" name="iwp_invoice_price[<?php echo $i; ?>]" id="iwp_invoice_price[<?php echo $i; ?>]">
												</td>
												<td>
													$ <input class="calculate_invoice_total input_total iwp_flatten_input" disabled="true" value="<?php echo $values["iwp_invoice_total"][$i]; ?>" placeholder="0.00">
													<input class="hidden_total input_total" name="iwp_invoice_total[<?php echo $i; ?>]" id="iwp_invoice_total[<?php echo $i; ?>]" value="<?php echo $values["iwp_invoice_total"][$i]; ?>" style="display: none !important;">
												</td>
												<td class="remove">&nbsp;</td>
											</tr>

											<?php
										}
									}
								?>
							</tbody>
						</table>
						<script>
						jQuery(document).ready(function( $ ) {

							var rowNumber = <?php echo $count - 1; ?>;
							
				 			$( 'body' ).on( 'click', '.add_row', function( e ) {
				 				e.preventDefault();
						 		
						 		rowNumber = rowNumber + 1;
							    var data = {
									'action': 'iwp_add_row',
									'version': rowNumber
								};

								$.post(ajaxurl, data, function(response) {
									$("tbody#invoiced_rows").append( response );
								});
						    });

				 			//price change
							$( 'body' ).on('change keyup blur', '.changesNo', function( ){
								var regExp = /\[(\d+)\]/;

								id_arr = $(this).attr('id');
								id = regExp.exec( id_arr );

								var quantity = "";
								var price = "";
								
								if( $(this).hasClass( "input_price" ) == true ) {
									quantity = $(this).closest('tr').find(".input_qty").val();
									price = $(this).val();
								} else if( $(this).hasClass( "input_qty" ) == true ) {
									price = $(this).closest('tr').find(".input_price").val();
									quantity = $(this).val();
								}
								
								if( quantity !='' && price !='' ) {
									$(this).closest('tr').find(".input_total").val( (parseFloat(price)*parseFloat(quantity)).toFixed(2) );	
									$(this).closest('tr').find(".hidden_total").val( (parseFloat(price)*parseFloat(quantity)).toFixed(2) );	
								} 

								//calculateTotal();
								
							});



			 			});


			 			</script>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<?php


	/**
	 * Save handler
	 */
function meta_box_save( $post_id ) {
		var_dump($_POST);
		if ( ! isset( $_POST['bookable_resource_details_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['bookable_resource_details_meta_box_nonce'], 'bookable_resource_details_meta_box' ) ) {
			return $post_id;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		if ( ! in_array( $_POST['post_type'], $this->post_types ) ) {
			return $post_id;
		}


		// Qty field
		update_post_meta( $post_id, 'qty', wc_clean( $_POST['_wc_booking_qty'] ) );

		// Availability
		$availability = array();
		$row_size     = isset( $_POST[ "wc_booking_availability_type" ] ) ? sizeof( $_POST[ "wc_booking_availability_type" ] ) : 0;
		for ( $i = 0; $i < $row_size; $i ++ ) {
			$availability[ $i ]['type']     = wc_clean( $_POST[ "wc_booking_availability_type" ][ $i ] );
			$availability[ $i ]['bookable'] = wc_clean( $_POST[ "wc_booking_availability_bookable" ][ $i ] );

			switch ( $availability[ $i ]['type'] ) {
				case 'custom' :
					$availability[ $i ]['from'] = wc_clean( $_POST[ "wc_booking_availability_from_date" ][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST[ "wc_booking_availability_to_date" ][ $i ] );
				break;
				case 'months' :
					$availability[ $i ]['from'] = wc_clean( $_POST[ "wc_booking_availability_from_month" ][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST[ "wc_booking_availability_to_month" ][ $i ] );
				break;
				case 'weeks' :
					$availability[ $i ]['from'] = wc_clean( $_POST[ "wc_booking_availability_from_week" ][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST[ "wc_booking_availability_to_week" ][ $i ] );
				break;
				case 'days' :
					$availability[ $i ]['from'] = wc_clean( $_POST[ "wc_booking_availability_from_day_of_week" ][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST[ "wc_booking_availability_to_day_of_week" ][ $i ] );
				break;
				case 'time' :
				case 'time:1' :
				case 'time:2' :
				case 'time:3' :
				case 'time:4' :
				case 'time:5' :
				case 'time:6' :
				case 'time:7' :
					$availability[ $i ]['from'] = wc_booking_sanitize_time( $_POST[ "wc_booking_availability_from_time" ][ $i ] );
					$availability[ $i ]['to']   = wc_booking_sanitize_time( $_POST[ "wc_booking_availability_to_time" ][ $i ] );
				break;
			}
		}
		update_post_meta( $post_id, '_wc_booking_availability', $availability );
	}


}

function iwp_payment( $invoice_id ) {


}


function iwp_client($post_id) {
  $iwp = get_post_meta($post_id->ID, '_invoicedwp', true );
  $iwp_invoice = $iwp['user_data'];

var_dump($iwp);
  wp_enqueue_script('wpi_select2_js');
  wp_enqueue_style('wpi_select2_css');

  $user_email = '';
  if ( isset( $iwp_invoice['user_email'] ) ) {
  	$userEmail = $iwp_invoice['user_email'];
  } else {
  	$userEmail = 'Select User';
  }

  ?>

  <script type="text/javascript">
    jQuery( document ).ready(function( $ ){
      $(".iwp_email_selection").select2({
        placeholder: '<?php echo $userEmail; ?>',
        multiple: false,
        width: '100%',
        minimumInputLength: 3,
        ajax: {
          url: ajaxurl,
          dataType: 'json',
          type: 'POST',
          data: function (term, page) {
            return {
              action: 'iwp_search_email',
              s: term
            };
          },
          results: function (data, page) {
            return {results: data};
          }
        },
        initSelection: function(element, callback) {
          callback(<?php echo json_encode(array('id'=>$user_email, 'title'=>$user_email)); ?>);
        },
        formatResult: function(o) {


          return o.title;
        },
        formatSelection: function(o) {
          return o.title;
        },
        escapeMarkup: function (m) { return m; }

      });
    });
	</script>

	<div class="iwp_newUser">
		<div class="iwp_email_selection_wrapper" style="margin: 10px 0;" >
			<input type="text" value="<?php echo $userEmail; ?>" name="iwp_invoice[user_data][user_email]" class="iwp_email_selection" />
		</div>


		<input title="" value="<?php echo $iwp_invoice['first_name']; ?>" placeholder="First Name" name="iwp_invoice[user_data][first_name]" class="input_field  iwp_first_name" type="text" id="" style="width: 100%;">
		<input title="" value="<?php echo $iwp_invoice['last_name']; ?>" placeholder="Last Name" name="iwp_invoice[user_data][last_name]" class="input_field  iwp_last_name" type="text" id="" style="width: 100%;">
		<input title="" value="<?php echo $iwp_invoice['company_name']; ?>" placeholder="Company Name" name="iwp_invoice[user_data][company_name]" class="input_field  iwp_company_name" type="text" id="" style="width: 100%;">
		<input title="" value="<?php echo $iwp_invoice['phonenumber']; ?>" placeholder="Phone Number" name="iwp_invoice[user_data][phonenumber]" class="input_field  iwp_phonenumber" type="text" id="" style="width: 100%;">
		<input title="" value="<?php echo $iwp_invoice['streetaddress']; ?>" placeholder="Street Address" name="iwp_invoice[user_data][streetaddress]" class="input_field  iwp_streetaddress" type="text" id="" style="width: 100%;">
		<input title="" value="<?php echo $iwp_invoice['streetaddress2']; ?>" placeholder="Street Address 2" name="iwp_invoice[user_data][streetaddress2]" class="input_field  iwp_streetaddress2" type="text" id="" style="width: 100%;">
		<input title="" value="<?php echo $iwp_invoice['city']; ?>" placeholder="City" name="iwp_invoice[user_data][city]" class="input_field  iwp_city" type="text" id="" style="width: 100%;">
		<input title="" value="<?php echo $iwp_invoice['state']; ?>" placeholder="State" name="iwp_invoice[user_data][state]" class="input_field  iwp_state" type="text" id="" style="width: 100%;">
		<input title="" value="<?php echo $iwp_invoice['zip']; ?>" placeholder="ZIP" name="iwp_invoice[user_data][zip]" class="input_field  iwp_zip" type="text" id="" style="width: 100%;">
	</div>


<?php
}














