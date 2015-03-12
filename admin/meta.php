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
		</style>

		<div class="iwp_options_panel iwp">
			<div class="panel-wrap" id="invoiced_availability">
				<div class="options_group">
					<div class="table_grid">
						<table class="widefat">
							<thead>
								<tr>
									<th class="sort" width="1%">&nbsp;</th>
									<th><?php _e( 'Name', 'iwp-invoiced' ); ?></th>
									<th style="width: 70px;" ><?php _e( 'Qty', 'iwp-invoiced' ); ?></th>
									<th style="width: 70px;" ><?php _e( 'Price', 'iwp-invoiced' ); ?></th>
									<th style="width: 70px;" ><?php _e( 'Total', 'iwp-invoiced' ); ?></th>
									<th class="remove" width="1%">&nbsp;</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th colspan="6">
										<a href="#" class="button button-primary add_row" data-row="<?php
											ob_start();
											include( 'meta-content.php' );
											$html = ob_get_clean();
											echo esc_attr( $html );
										?>"><?php _e( 'Add Range', 'iwp-invoiced' ); ?></a>
									</th>
								</tr>
							</tfoot>
							<tbody id="availability_rows">
								<?php
									$values = get_post_meta( $post_id, '_wc_booking_availability', true );
									if ( ! empty( $values ) && is_array( $values ) ) {
										foreach ( $values as $availability ) {
											include( 'meta-content.php' );
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




function iwp_client($invoice_id) {
  wp_enqueue_script('wpi_select2_js');
  wp_enqueue_style('wpi_select2_css');

  ?>

  <script type="text/javascript">
    jQuery( document ).ready(function(){
      jQuery(".wpi_user_email_selection").select2({
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
              action: 'wpi_search_email',
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

  <div class="wpi_user_email_selection_wrapper" style="margin: 10px 0;" >
    <input type="text" value="<?php echo esc_attr($user_email); ?>" name="wpi_invoice[user_data][user_email]" class="wpi_user_email_selection" />
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



<?php
}

