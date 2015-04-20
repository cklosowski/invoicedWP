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

	$iwp 			= get_post_meta( $post_id->ID, '_invoicedwp', true );
	$iwp_options 	= get_option( 'iwp_settings' );

	$values 		= isset( $iwp['lineItems'] ) ? $iwp['lineItems'] : NULL;
	
	$iwp_currency 	= iwp_currency_symbol();
	$count 			= count( $values["iwp_invoice_total"] );

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
	<?php
	//echo '<pre>';
	//print_r( $iwp );
	//echo '</pre>';
	?>

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

						<?php 
						if( isset( $post_id->post_type )  ) {
							//if( $post_id->post_type <> 'invoicedwp_template' ) { ?>
							<tfoot>
								<tr>
									<td colspan="6" style="background-color: #f9f9f9;">
										<table style="float: right; ">
											<tr>
												<td style="background: none; border: 0px;" ><?php _e( 'Subtotal', 'iwp-txt'); ?>:</td>
												<td style="background: none; border: 0px;" ><div style="float: left;"><?php echo $iwp_currency; ?></div><input name="iwp_totals[subtotal]" value=""  class="calculate_invoice_subtotal iwp_flatten_input" style="float: right; width: 90%;" ></td>
											</tr>
											<?php if( $iwp_options["enable_taxes"] == 1 ) { ?>
												<tr>
													<td style="background: none; border: 0px;" class="column-invoice-details-tax"><?php _e( 'Sales Tax', 'iwp-txt'); ?>:</td>
													<td style="background: none; border: 0px;"  class="column-invoice-details-tax"><div style="float: left;"><?php echo $iwp_currency; ?></div><input name="iwp_totals[tax]" value="0.00"  class="calculate_invoice_tax iwp_flatten_input" style="float: right; width: 90%;" ><input type="hidden" id="iwp_tax_rate" value='<?php echo $iwp_options["tax_rate"]; ?>' ></td>
												</tr>
											<?php } ?>
											<tr>
												<td style="background: none; border: 0px;"  class="hidden column-invoice-details-adjustments"><?php _e( 'Adjustments', 'iwp-txt'); ?>:</td>
												<td style="background: none; border: 0px;"  class="hidden column-invoice-details-adjustments"><div style="float: left;"><?php echo $iwp_currency; ?></div><input name="iwp_totals[adjustments]" value="0.00"  class="calculate_invoice_adjustments iwp_flatten_input" style="float: right; width: 90%;" ></td>
											</tr>
											<tr>
												<td style="background: none; border: 0px;" class="hidden column-invoice-details-discounts"><?php _e( 'Discount', 'iwp-txt'); ?>:</td>
												<td style="background: none; border: 0px;" class="hidden column-invoice-details-discounts"><div style="float: left;"><?php echo $iwp_currency; ?></div><input name="iwp_totals[discount]" value="0.00"  class="iwp_flatten_input calculate_discount_total" style="float: right; width: 90%;" ></td>
											</tr>
											<tr>
												<td style="background: none; border: 0px;" ><b><?php _e( 'Total', 'iwp-txt'); ?>:</b></td>
												<td style="background: none; border: 0px;" ><div style="float: left;"><?php echo $iwp_currency; ?></div><input name="iwp_totals[total]" id="iwp_totals[total]" value="0.00"  class="calculate_invoice_grandtotal iwp_flatten_input" style="float: right; width: 90%;" ></td>
											</tr>
										</table>
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
										<select style="float: right; width: 400px;" class="selectTemplate" >
											<?php echo iwp_get_templates(); ?>
										</select>
									</th>
								</tr>
								
							</tfoot>
						<?php 	//} 
						} ?>


						<tbody id="invoiced_rows">
						<?php if( $count == 0 ) { ?>
							<tr>
								<td class="sort">&nbsp;</td>
								<td style="border-right: 0 none !important;"> <?php // Name ?>
									<input class="item_name input_field" value="" name="iwp_invoice_name[0]">
									<span><a class="toggleDescription"  href="#" style="text-size 9px !important;" >Add Description</a></span>
									<textarea class="item_name input_field input_description iwp_invoice_description0" value="" name="iwp_invoice_description[0]" id="iwp_invoice_description[0]" style="display: none; width: 100%; margin-top: 5px; font-size= 0.88em;" placeholder="Description"></textarea>
								</td>
								<td style="border-right: 0 none !important;"> <?php // Qty ?>
									<input type="number" class="changesNo item_name input_field input_qty" value="" name="iwp_invoice_qty[0]" id="iwp_invoice_qty[0]">
								</td>
								<td style="border-right: 0 none !important;"> <?php // price ?>
									<input type="number" class="changesNo item_name input_field input_price" value="" name="iwp_invoice_price[0]" id="iwp_invoice_price[0]"  step="0.01">
								</td>
								<td> <?php // Total ?>
									<?php echo $iwp_currency; ?> <input class="calculate_invoice_total input_total iwp_flatten_input"  value="<?php echo $values["iwp_invoice_total"][0]; ?>" placeholder="0.00">
									<input class="hidden_total input_total" name="iwp_invoice_total[0]" id="iwp_invoice_total[0]" value="<?php echo $values["iwp_invoice_total"][0]; ?>" style="display: none !important;">
								</td>
								<td class="remove">&nbsp;</td>
							</tr>

							<?php }
							$i = 0;
							if( $count != 0 ) {
								for( $i = 0; $i < $count; $i++ ) {
									?>
									<tr>
										<td class="sort">&nbsp;</td>
										<td style="border-right: 0 none !important;"> <?php // Name ?>
											<input class="item_name input_field input_name" value="<?php echo $values["iwp_invoice_name"][$i]; ?>" name="iwp_invoice_name[<?php echo $i; ?>]">
											<?php if( empty( $values["iwp_invoice_description"][$i] ) ) {  ?> <span><a class="toggleDescription"  href="#" style="text-size 9px !important;" >Add Description</a></span> <?php } ?>
											<textarea class="item_name input_field input_description iwp_invoice_description" value="" name="iwp_invoice_description[<?php echo $i; ?>]" style="<?php if( empty( $values["iwp_invoice_description"][$i] ) ) { echo 'display: none;'; } ?> width: 100%; margin-top: 5px; font-size= 0.88em;" placeholder="Description"><?php echo $values["iwp_invoice_description"][$i]; ?></textarea>
										</td>
										<td style="border-right: 0 none !important;"> <?php // Qty ?>
											<input type="number" class="changesNo item_name input_field input_qty" value="<?php echo $values["iwp_invoice_qty"][$i]; ?>" name="iwp_invoice_qty[<?php echo $i; ?>]" id="iwp_invoice_qty[<?php echo $i; ?>]">
										</td>
										<td style="border-right: 0 none !important;"> <?php // price ?>
											<input type="number" class="changesNo item_name input_field input_price" value="<?php echo $values["iwp_invoice_price"][$i]; ?>" name="iwp_invoice_price[<?php echo $i; ?>]" id="iwp_invoice_price[<?php echo $i; ?>]" step="0.01">
										</td>
										<td>
											<?php echo $iwp_currency; ?> <input class="calculate_invoice_total input_total iwp_flatten_input"  value="<?php echo $values["iwp_invoice_total"][$i]; ?>" placeholder="0.00">
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
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</div>

	<?php
}

function iwp_payment( $invoice_id ) {
	$iwp = get_post_meta($invoice_id->ID, '_invoicedwp', true );

	do_action( 'iwp_before_payment');
	?>


	<table width="100%" class="widefat striped" id="paymentDisplay">
	<?php
	
		$amount = $iwp["iwp_invoice_payment"]["amount"];

		foreach( $amount as $key => $payment) {
			$number = $key + 1;
			echo '<tr>';
			echo '<td>' . $number . '.</td>';
			echo '<td style="text-align: right;">' . iwp_currency_symbol() . ' ' . iwp_format_amount(  $iwp["iwp_invoice_payment"]["amount"][$key] ) . '</td>';
			echo '<td>' . $iwp["iwp_invoice_payment"]["method"][$key] . '</td>';
			echo '</tr>';

			//$myPayments += $iwp["iwp_invoice_payment"]["amount"][$key];
		}
		$remainingBalance = (float) $iwp["invoice_totals"]["total"] - (float) $iwp["invoice_totals"]["payments"];
	?>
	</table>
	<div style="margin-top: 10px;">
		<input type="number" class="item_name input_field input_qty" value="<?php echo $iwp["invoice_payment"]; ?>" name="iwp_payment_amount" id="iwp_payment_amount">
		<select name="iwp_payment_method" id="iwp_payment_method">
			<option>Cash</option>
			<option>Check</option>
			<option>CC</option>
		</select>
		<div class='iwp_invoiceBalance'><?php echo __( 'Remaining Balance', 'iwp-txt') . ':<br />' . iwp_currency_symbol() . ' ' . iwp_format_amount( $remainingBalance ); ?></div>
	</div>

	<?php
	do_action( 'iwp_after_payment');
}


function iwp_notice($post_id) {
	$iwp = get_post_meta($post_id->ID, '_invoicedwp', true );
  	?>

	<div class="iwp_notice">
		<textarea placeholder="Notice" name="iwp_notice" class="input_field iwp_notice" type="text" id="" style="width: 100%;"><?php echo isset ( $iwp['invoice_notice'] ) ? $iwp['invoice_notice'] : ''; ?></textarea>
	</div>

	<?php
}

function iwp_client($post_id) {
	$iwp = get_post_meta($post_id->ID, '_invoicedwp', true );
	
	$userEmail = 'Select User';

	if( isset( $iwp['user_data'] )) {
		$iwp_invoice = $iwp['user_data'];

		if ( isset( $iwp_invoice['user_email'] ) )
			$userEmail = $iwp_invoice['user_email'];

	}

	wp_enqueue_script('wpi_select2_js');
	wp_enqueue_style('wpi_select2_css');

	$format = 'MM dd, yy';

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

			$( '.iwp-date-picker' ).datepicker({
				dateFormat: '<?php echo $format; ?>',
				numberOfMonths: 1, 
				buttonImageOnly: true
			});

			$('.select2-choice span').text('<?php echo $userEmail; ?>');
		});
	</script>

	<div class="iwp_newUser">
		<div class="iwp_email_selection_wrapper" style="margin: 10px 0;" >
			<input type="text" value="<?php echo $userEmail; ?>" name="iwp_invoice[user_data][user_email]" class="iwp_email_selection" />
		</div>

		<input title="" value="<?php echo isset( $iwp_invoice['first_name'] ) ? $iwp_invoice['first_name'] : ''; ?>" placeholder="First Name" name="iwp_invoice[user_data][first_name]" class="input_field  iwp_first_name" type="text" id="" style="width: 100%;">
		<input title="" value="<?php echo isset( $iwp_invoice['last_name'] ) ? $iwp_invoice['last_name'] :  ''; ?>" placeholder="Last Name" name="iwp_invoice[user_data][last_name]" class="input_field  iwp_last_name" type="text" id="" style="width: 100%;">
		<input title="" value="<?php echo isset( $iwp_invoice['company_name'] ) ? $iwp_invoice['company_name'] :  ''; ?>" placeholder="Company Name" name="iwp_invoice[user_data][company_name]" class="input_field  iwp_company_name" type="text" id="" style="width: 100%;">
		<input title="" value="<?php echo isset( $iwp_invoice['phonenumber'] ) ? $iwp_invoice['phonenumber'] :  ''; ?>" placeholder="Phone Number" name="iwp_invoice[user_data][phonenumber]" class="input_field  iwp_phonenumber" type="text" id="" style="width: 100%;">
		<input title="" value="<?php echo isset( $iwp_invoice['streetaddress'] ) ? $iwp_invoice['streetaddress'] :  ''; ?>" placeholder="Street Address" name="iwp_invoice[user_data][streetaddress]" class="input_field  iwp_streetaddress" type="text" id="" style="width: 100%;">
		<input title="" value="<?php echo isset( $iwp_invoice['streetaddress2'] ) ? $iwp_invoice['streetaddress2'] :  ''; ?>" placeholder="Street Address 2" name="iwp_invoice[user_data][streetaddress2]" class="input_field  iwp_streetaddress2" type="text" id="" style="width: 100%;">
		<input title="" value="<?php echo isset( $iwp_invoice['city'] ) ? $iwp_invoice['city'] :  ''; ?>" placeholder="City" name="iwp_invoice[user_data][city]" class="input_field  iwp_city" type="text" id="" style="width: 100%;">
		<input title="" value="<?php echo isset( $iwp_invoice['state'] ) ? $iwp_invoice['state'] :  ''; ?>" placeholder="State" name="iwp_invoice[user_data][state]" class="input_field  iwp_state" type="text" id="" style="width: 100%;">
		<input title="" value="<?php echo isset( $iwp_invoice['zip'] ) ? $iwp_invoice['zip'] :  ''; ?>" placeholder="ZIP" name="iwp_invoice[user_data][zip]" class="input_field  iwp_zip" type="text" id="" style="width: 100%;">
		<input type="text" name="makeAccountText" id="makeAccountText" value="<?php echo isset( $iwp['makeAccountText'] ) ? $iwp_invoice['makeAccountText'] :  ''; ?>" placeholder="Email address for account"  style="width: 100%; display: none;" />
		<div class="makeNewAccount" style="margin-top: 10px">
			<input type="checkbox" name="makeAccount" id="makeAccount" value="' . $iwp['makeAccount'] . '" ' . checked( $iwp['makeAccount'], 1, false ) .' /> <label for="makeAccount">Make Customer Account</label>
	        
        </div>
	</div>


<?php
}

//  Function to get the information for the template that is showing in the dropdown box
function iwp_get_templates() {

	$query = new WP_Query( array( 'post_type' => array( 'invoicedWP_template' ) ) );
	$lines = $query->posts;

	$return = '<option value="">' . __( 'Select Invoice Template', 'iwp-text') . '</option>';
	foreach ($lines as $key => $line ){
		$return .= '<option value="' . $line->ID . '">' . $line->post_title . '</option>';
	}

	return $return;
}











