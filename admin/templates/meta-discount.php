<tr class="discount_row" style="background-color: #fdfdfd;">
	<td class="discount_row" style="background-color: #fdfdfd; border-right: 0 none !important;">&nbsp;</td>
	<td class="discount_row" style="background-color: #fdfdfd; border-right: 0 none !important;"> <?php // Name ?>
		<input class="item_name input_field" value="<?php echo $iwp["iwp_invoice_discount"]["name"]; ?>" name="iwp_invoice_discount_name">
	</td>
	<td colspan="2" class="discount_row" style="background-color: #fdfdfd; border-right: 0 none !important;">
		<select id="discountType" name="iwp_invoice_discountType" >
			<option value="amount" <?php selected( $iwp["iwp_invoice_discount"]["type"], "amount" ); ?>><?php _e('Amount Discount', 'iwp-txt'); ?></option>
			<option value="percent" <?php selected( $iwp["iwp_invoice_discount"]["type"], "percent" ); ?>><?php _e('Percent Discount', 'iwp-txt'); ?></option>
		</select> 
	</td>
	<td class="discount_row" style="background-color: #fdfdfd;"> <?php // price ?>
		<?php 
			$theDiscount = 0;
			if( ! empty( $iwp["iwp_invoice_discount"]["discount"] ) ) {
				$theDiscount = $iwp["iwp_invoice_discount"]["discount"]; 
			}
		?>
		<div class="currencySymbol">$</div><div class="hidden percentSymbol" style="float:right; margin-left: 10px;">%</div><input type="number" id="discountAmount" class="item_name input_field changesNo" value="<?php echo $theDiscount; ?>" name="iwp_invoice_discount" style="width: 74%; float: right;"  step="0.01">
	</td>
	<td class="remove_discount remove">&nbsp;</td>
</tr>