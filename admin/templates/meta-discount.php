<tr class="discount_row" style="background-color: #fdfdfd;">
	<td class="discount_row" style="background-color: #fdfdfd; border-right: 0 none !important;">&nbsp;</td>
	<td class="discount_row" style="background-color: #fdfdfd; border-right: 0 none !important;"> <?php // Name ?>
		<input class="item_name input_field" value="" name="iwp_invoice_name">
	</td>
	<td colspan="2" class="discount_row" style="background-color: #fdfdfd; border-right: 0 none !important;">
		<select id="discountType" name="discountType" >
			<option value="amount">Amount Discount</option>
			<option value="percent">Percent Discount</option>
		</select> 
	</td>
	<td class="discount_row" style="background-color: #fdfdfd;"> <?php // price ?>
		<div class="currencySymbol">$</div><div class="hidden percentSymbol" style="float:right; margin-left: 10px;">%</div><input id="discountAmount" class="item_name input_field changesNo" value="" name="iwp_invoice_discount" style="width: 74%; float: right;">
	</td>
	<td class="remove_discount remove">&nbsp;</td>
</tr>