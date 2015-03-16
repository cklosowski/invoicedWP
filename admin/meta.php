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
		wp_nonce_field( 'bookable_resource_details_meta_box', 'bookable_resource_details_meta_box_nonce' );
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
									<th style="width: 70px;" ><?php _e( 'Total', 'iwp-invoiced' ); ?></th>
									<th class="remove" style="width:20px !important;">&nbsp;</th>
								</tr>
							</thead>
							<tfoot>
							<?php if( $_REQUEST['post_type'] <> 'invoicedwp_template' ) { ?>
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
							<?php } ?>
								<tr>
									<th colspan="6">
									<?php if( $_REQUEST['post_type'] <> 'invoicedwp_template' ) { ?>
										<a href="#" class="button button-primary add_discount" data-row="<?php
											ob_start();
											include( 'templates/meta-discount.php' );
											$html = ob_get_clean();
											echo esc_attr( $html );
										?>" style="margin-left: 10px;"><?php _e( 'Add Discount', 'iwp-invoiced' ); ?></a>
									<?php } ?>
										<a href="#" class="button button-primary add_row" data-row="<?php
											ob_start();
											include( 'templates/meta-content.php' );
											$html = ob_get_clean();
											echo esc_attr( $html );
										?>" style="margin-left: 10px;"><?php _e( 'Add Line', 'iwp-invoiced' ); ?></a>
									<?php if( $_REQUEST['post_type'] <> 'invoicedwp_template' ) { ?>
										<select style="float: right;">
											<option value=""></option>
											<option value="">Test Value to make sure it fits the value on the page.</option>
										</select>
									<?php } ?>

									</th>
								</tr>
							</tfoot>
							<tbody id="availability_rows">
								<tr data-row="1">
									<td class="sort">&nbsp;</td>
									<td style="border-right: 0 none !important;"> <?php // Name ?>
										<input class="item_name input_field" value="" name="iwp_invoice_name[0]">
										<span style="text-size 9px;"><a class="toggleDescription"  href="#" >Add Description</a></span>
										<textarea class="item_name input_field iwp_invoice_description" value="" name="iwp_invoice_description[0]" style="display: none; width: 100%; margin-top: 5px; font-size= 0.88em;" placeholder="Description"></textarea>
									</td>
									<td style="border-right: 0 none !important;"> <?php // Qty ?>
										<input class="item_name input_field" value="" name="iwp_invoice_qty[0]">
									</td>
									<td style="border-right: 0 none !important;"> <?php // price ?>
										<input class="item_name input_field" value="" name="iwp_invoice_price[0]">
									</td>
									<td> <?php // Total ?>
										<div class="price">$0.00</div>
									</td>
									<td class="remove">&nbsp;</td>
								</tr>
								<?php
									$values = get_post_meta( $post_id, '_wc_booking_availability', true );
									if ( ! empty( $values ) && is_array( $values ) ) {
										foreach ( $values as $availability ) {
											include( 'templates/meta-content.php' );
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


	/**
	 * Save handler
	 */
function meta_box_save( $post_id ) {
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


function iwp_client($invoice_id) {
  wp_enqueue_script('wpi_select2_js');
  wp_enqueue_style('wpi_select2_css');

  ?>

  <script type="text/javascript">
    jQuery( document ).ready(function(){
      jQuery(".iwp_email_selection").select2({
        placeholder: 'Select User',
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
			<input type="text" value="<?php echo esc_attr($user_email); ?>" name="iwp_invoice[user_data][user_email]" class="iwp_email_selection" />
		</div>


		<input title="" value="" placeholder="First Name" name="iwp_invoice[user_data][first_name]" class="input_field  iwp_first_name" type="text" id="" style="width: 100%;">
		<input title="" value="" placeholder="Last Name" name="iwp_invoice[user_data][last_name]" class="input_field  iwp_last_name" type="text" id="" style="width: 100%;">
		<input title="" value="" placeholder="Company Name" name="iwp_invoice[user_data][company_name]" class="input_field  iwp_company_name" type="text" id="" style="width: 100%;">
		<input title="" value="" placeholder="Phone Number" name="iwp_invoice[user_data][phonenumber]" class="input_field  iwp_phonenumber" type="text" id="" style="width: 100%;">
		<input title="" value="" placeholder="Street Address" name="iwp_invoice[user_data][streetaddress]" class="input_field  iwp_streetaddress" type="text" id="" style="width: 100%;">
		<input title="" value="" placeholder="Street Address 2" name="iwp_invoice[user_data][streetaddress2]" class="input_field  iwp_streetaddress2" type="text" id="" style="width: 100%;">
		<input title="" value="" placeholder="City" name="iwp_invoice[user_data][city]" class="input_field  iwp_city" type="text" id="" style="width: 100%;">
		<input title="" value="" placeholder="State" name="iwp_invoice[user_data][state]" class="input_field  iwp_state" type="text" id="" style="width: 100%;">
		<input title="" value="" placeholder="ZIP" name="iwp_invoice[user_data][zip]" class="input_field  iwp_zip" type="text" id="" style="width: 100%;">
	</div>


<?php
}

