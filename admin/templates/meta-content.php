<?php
if ( isset( $_POST['version'] ) ) {
  $post_data = iwp_sanitize( $_POST['version'] );
}

?>


<tr>
	<td class="sort">&nbsp;</td>
	<td style="border-right: 0 none !important;"> <?php // Name ?>
		<input class="item_name input_field iwp_invoice_name[<?php echo $post_data; ?>]" value="<?php echo $values["iwp_invoice_name"][$post_data]; ?>" name="iwp_invoice_name[<?php echo $post_data; ?>]">
		<span style="text-size 10px;"><a class="toggleDescription"  href="#" >Add Description</a></span>
		<textarea class="item_name input_field iwp_invoice_description iwp_invoice_description[<?php echo $post_data; ?>]" value="" name="iwp_invoice_description[<?php echo $post_data; ?>]" style="display: none; width: 100%; margin-top: 5px; font-size= 0.88em;" placeholder="<?php _e('Description', 'iwp-txt'); ?>"></textarea>
	</td>
	<td style="border-right: 0 none !important;"> <?php // Qty ?>
		<input class="changesNo item_name input_field iwp_invoice_qty[<?php echo $post_data; ?>]" value="" name="iwp_invoice_qty[<?php echo $post_data; ?>]">
	</td>
	<td style="border-right: 0 none !important;"> <?php // price ?>
		<input class="changesNo item_name input_field iwp_invoice_price[<?php echo $post_data; ?>]" value="" name="iwp_invoice_price[<?php echo $post_data; ?>]">
	</td>
	<td >
		$ <input class="calculate_invoice_total input_total iwp_flatten_input" disabled="true" value="" placeholder="<?php _e('0.00', 'iwp-txt' ); ?> ">
		<input class="hidden_total input_total" name="iwp_invoice_total[<?php echo $post_data; ?>]" id="iwp_invoice_total[<?php echo $post_data; ?>]" value="<?php echo $values["iwp_invoice_total"][$post_data]; ?>" style="display: none !important;">
	</td>
	<td class="remove">&nbsp;</td>
</tr>