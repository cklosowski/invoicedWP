jQuery(document).ready(function( $ ) {

	if ( ! window.console ) {
		window.console = {
			log : function(str) {
				// alert(str);
			}
		};
	}

	setInterval(function(){ calculateTotal(); }, 250);

	$( document ).on( 'click', '.add_row', function( e ) {
		e.preventDefault();
 		
 		var rowNumber = $('#invoicedDisplay tbody tr').length;
 	
		lastTR = $( '#invoicedDisplay tbody' ).find("tr:last"),
		trNew = lastTR.clone()
					  .find("input, textarea").val("").end();

		lastTR.after(trNew);

		nameID 	= 'iwp_invoice_name[' + rowNumber + ']';
		descId 	= 'iwp_invoice_description[' + rowNumber + ']';
		qtyId 	= 'iwp_invoice_qty[' + rowNumber + ']';
		priceId = 'iwp_invoice_price[' + rowNumber + ']';
		totalId = 'iwp_invoice_total[' + rowNumber + ']';

		$( '#invoicedDisplay tbody tr:last').find('.input_name').attr('id', nameID ).attr('name', nameID);
		$( '#invoicedDisplay tbody tr:last').find('.input_description').attr('id', descId ).attr('name', descId );
		$( '#invoicedDisplay tbody tr:last').find('.input_qty').attr('id', qtyId ).attr('name', qtyId );
		$( '#invoicedDisplay tbody tr:last').find('.input_price').attr('id', priceId ).attr('name', priceId );
		$( '#invoicedDisplay tbody tr:last').find('.input_total').attr('id', totalId ).attr('name', totalId );

		calculateTotal();
    });

		//price change
	$( 'body' ).on('change keyup blur', '.changesNo', function( ){
		calculateTotal();
	});

	$( 'body' ).on( 'change', '.selectTemplate', function( e ) {						 		
 		var template = $(this).val();
 		var rowNumber = $('#invoicedDisplay tbody tr').length;

	    var data = {
			'action': 'iwp_add_template_row',
			'version': rowNumber,
			'template': template
		};

		$.post(ajaxurl, data, function(response) {
			$("tbody#invoiced_rows").append( response );
		});
    });



	$( 'body' ).on( 'change', '#discountType', function( e ) {
		var discountAmount = $( '#discountAmount' ).val();					 		
 		var discountType = $( '#discountType' ).val();
 		var subTotal = $( '.calculate_invoice_subtotal' ).val();
 		
 		if( discountType == "percent" ){
 			$( ".percentSymbol").show();
 			$( ".currencySymbol").hide();
 			$( ".calculate_discount_total").val( parseFloat( subTotal * ( discountAmount / 100 ) ).toFixed(2) );
 		}

 		if( discountType == "amount" ){
 			$( ".percentSymbol").hide();
 			$( ".currencySymbol").show();
 			$( ".calculate_discount_total").val( parseFloat( discountAmount ).toFixed(2) );
 		}

 		calculateTotal();
    });

    $('body').on('click', 'td.remove', function(){
		$(this).closest('tr').remove();
		return false;
	});

	$('body').on('click', 'td.remove_discount', function(){
		$( '.add_discount' ).show();
		$( '.column-invoice-details-discounts' ).hide();
		return false;
	});

	$( '.add_discount' ).click(function(){
		$(this).closest('table').find('tfoot').prepend( $( this ).data( 'row' ) );
		$('body').trigger('row_added');
		$('.column-invoice-details-discounts').show();
		$(this).hide();
		return false;
	});

	$('body').on('click', '.toggleDescription', function(){
		$(this).closest('td').find('.iwp_invoice_description').show();
		$(this).hide();
		return false;
	});

	$('#invoiced_rows, #pricing_rows').sortable({
		items:'tr',
		cursor:'move',
		axis:'y',
		handle: '.sort',
		scrollSensitivity:40,
		forcePlaceholderSize: true,
		helper: 'clone',
		opacity: 0.65,
		placeholder: 'wc-metabox-sortable-placeholder',
		start:function(event,ui){
			ui.item.css('background-color','#f6f6f6');
		},
		stop:function(event,ui){
			ui.item.removeAttr('style');
		}
	});





	$( ".iwp_email_selection" ).change( function () {

	    $.post( ajaxurl, {
	      action: 'iwp_get_user_data',
	      user_email: $( this ).val()
	    }, function ( result ) {
	      if ( result ) {
	        user_data = result.user_data;

	        for ( var field in user_data ) {
	          $( '.iwp_newUser .iwp_' + field ).val( user_data[field] );

	        }
	        $( '.makeNewAccount' ).hide();
	      }
	    }, 'json' );

	  } );





	function calculateTotal( ) {
		// Calculate total for the invoice
		var sum = 0;

		$( 'tbody#invoiced_rows tr' ).each( function() {
			if( !isNaN( $( this ).find('.input_qty').val() ) ) {
				var qty = $( this ).find('.input_qty').val();
			} else {
				var qty = 0;
			}

			if( !isNaN( $( this ).find('.input_price').val() ) ) {
				var price = $( this ).find('.input_price').val();
			} else {
				var price = 0;
			}

			var total = $( this ).find('.input_qty').val() * $( this ).find('.input_price').val();

			$( this ).find('.input_total').val( parseFloat( total ).toFixed(2) );
			$( this ).find('.hidden_total').val( $( this ).find('.input_total').val() );

			sum += parseFloat(total);
		});
		 
		$(".calculate_invoice_subtotal").val( parseFloat( sum ).toFixed(2) );
		$(".hidden_subtotal").val( parseFloat( sum ).toFixed(2) );

		// Calculate Tax



		// Calculate discount 
		var discountAmount 	= 0;
		var discountType 	= 0;
		var subTotal 		= 0;
		var taxAmount		= 0;


		discountAmount 	= $( '#discountAmount' ).val();
		discountType 	= $('#discountType').val();
		subTotal 		= $( '.calculate_invoice_subtotal' ).val();
		taxAmount		= subTotal * ( $( '#iwp_tax_rate' ).val() / 100 );

		var paymentMade 	= $( '.calculate_invoice_payment' ).val();

		if( discountType == "percent" ){
			var discountTotal = ( subTotal + taxAmount ) * ( discountAmount / 100 );
		} else if( discountType == "amount" ){
			var discountTotal = discountAmount;
		}

			// Get Payments and subtract them
		if( isNaN( discountTotal ) ) {
			discountTotal = 0;
		}

		var grandTotal = ( parseFloat( subTotal ).toFixed(2) + parseFloat( taxAmount ).toFixed(2) ) - parseFloat( discountTotal ).toFixed(2);
		//grandTotal = parseFloat( grandTotal ).toFixed(2) - parseFloat( discountTotal ).toFixed(2);

		$( ".calculate_invoice_tax " ).val( parseFloat( taxAmount ).toFixed(2) );
		$( ".calculate_discount_total").val( parseFloat( discountTotal ).toFixed(2) );
		$( '.calculate_invoice_grandtotal').val( parseFloat( grandTotal ).toFixed(2) );
		console.log( subTotal + ' ' + taxAmount + ' ' + discountTotal + ' ' + paymentMade + ' ' + grandTotal );
	}


	$("#reoccuringPayment").change(function() {
	    if(this.checked) {
	        $("#reoccuringPaymentText").show();
	    } else {
	    	$("#reoccuringPaymentText").hide();
	    }
	});


	$("#minPayment").change(function() {
	    if(this.checked) {
	        $("#minPaymentText").show();
	    } else {
	    	$("#minPaymentText").hide();
	    }
	});

	$("#dueDate").change(function() {
	    if(this.checked) {
	        $("#dueDateText").show();
	    } else {
	    	$("#dueDateText").hide();
	    }
	});


	$( '#makeAccount' ).change(function() {
	    if(this.checked) {
	        $("#makeAccountText").show();
	        $(".makeNewAccount").hide();
	    } else {
	    	$("#makeAccountText").hide();
	    }
	});



});