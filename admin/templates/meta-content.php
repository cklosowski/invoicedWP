<tr data-row=''>
	<td class="sort">&nbsp;</td>
	<td style="border-right: 0 none !important;"> <?php // Name ?>
		<input class="item_name input_field" value="" name="iwp_invoice_name[<?php echo $value; ?>]">
		<span style="text-size 10px;"><a class="toggleDescription"  href="#" >Add Description</a></span>
		<textarea class="item_name input_field iwp_invoice_description" value="" name="iwp_invoice_description[<?php echo $value; ?>]" style="display: none; width: 100%; margin-top: 5px; font-size= 0.88em;" placeholder="Description"></textarea>
	</td>
	<td style="border-right: 0 none !important;"> <?php // Qty ?>
		<input class="item_name input_field" value="" name="iwp_invoice_qty[<?php echo $value; ?>]">
	</td>
	<td style="border-right: 0 none !important;"> <?php // price ?>
		<input class="item_name input_field" value="" name="iwp_invoice_price[<?php echo $value; ?>]">
	</td>
	<td> <?php // Total ?>
		<div class="price">$0.00</div>
	</td>
	<td class="remove">&nbsp;</td>
</tr>