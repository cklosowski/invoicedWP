jQuery(document).ready(function( $ ) {

	if ( ! window.console ) {
		window.console = {
			log : function(str) {
				// alert(str);
			}
		};
	}

	var xhr = [];

	$('.wc-invoiced-booking-form')
		.on('change', 'input, select', function() {
			var index = $('.wc-invoiced-booking-form').index(this);

			if ( xhr[index] ) {
				xhr[index].abort();
			}

			$form = $(this).closest('form');

			var required_fields = $form.find('input.required_for_calculation');
			var filled          = true;
			$.each( required_fields, function( index, field ) {
				var value = $(field).val();
				if ( ! value ) {
					filled = false;
				}
			});
			if ( ! filled ) {
				$form.find('.wc-invoiced-booking-cost').hide();
				return;
			}

			$form.find('.wc-invoiced-booking-cost').block({message: null, overlayCSS: {background: '#fff', backgroundSize: '16px 16px', opacity: 0.6}}).show();

			xhr[index] = $.ajax({
				type: 		'POST',
				url: 		booking_form_params.ajax_url,
				data: 		{
					action: 'wc_invoiced_calculate_costs',
					form:   $form.serialize()
				},
				success: 	function( code ) {
					if ( code.charAt(0) !== '{' ) {
						console.log( code );
						code = '{' + code.split(/\{(.+)?/)[1];
					}

					result = $.parseJSON( code );

					if ( result.result == 'ERROR' ) {
						$form.find('.wc-invoiced-booking-cost').html( result.html );
						$form.find('.wc-invoiced-booking-cost').unblock();
						$form.find('.single_add_to_cart_button').attr('disabled', 'disabled');
					} else if ( result.result == 'SUCCESS' ) {
						$form.find('.wc-invoiced-booking-cost').html( result.html );
						$form.find('.wc-invoiced-booking-cost').unblock();
						$form.find('.single_add_to_cart_button').removeAttr('disabled');
					} else {
						$form.find('.wc-invoiced-booking-cost').hide();
						$form.find('.single_add_to_cart_button').attr('disabled', 'disabled');
						console.log( code );
					}
				},
				error: function() {
					$form.find('.wc-invoiced-booking-cost').hide();
					$form.find('.single_add_to_cart_button').attr('disabled', 'disabled');
				},
				dataType: 	"html"
			});
		})
		.each(function(){
			var button = $(this).closest('form').find('.single_add_to_cart_button');

			button.attr('disabled', 'disabled');
		});


	$('.wc-invoiced-booking-form, .wc-invoiced-booking-form-button').show();

	
	var rowNumber = $('#invoicedDisplay tbody tr').length - 1;
						
	$( document ).on( 'click', '.add_row', function( e ) {
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
	$( document ).on('change keyup blur', '.changesNo', function( ){
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


		// Calculate subtotal for the invoice
	    var sum = 0;
	    $(".calculate_invoice_total").each(function() {
			if(!isNaN(this.value) &&  this.value.length!=0) {
				sum += parseFloat(this.value);
			}

		});
		 
	    $(".calculate_invoice_subtotal").val( parseFloat( sum ).toFixed(2) );
	    $(".hidden_subtotal").val( parseFloat( sum ).toFixed(2) );
		
	    // Calculate discount 
		var discountAmount = $( '#discountAmount' ).val();
		var discountType = $('#discountType').val();
		var subTotal = $( '.calculate_invoice_subtotal' ).val();
		
		if( discountType == "percent" ){
			$( ".calculate_discount_total").val( parseFloat( subTotal * ( discountAmount / 100 ) ).toFixed(2) );
 		}

 		if( discountType == "amount" ){
 			$( ".calculate_discount_total").val( parseFloat( discountAmount ).toFixed(2) );
 		}

 		// Get Payments and subtract them
 		



 		// Get Total
 		var discountTotal = $( ".calculate_discount_total").val();
 			
 		$( '.calculate_invoice_grandtotal').val( parseFloat( subTotal - discountTotal ).toFixed(2) );



	});

	$( 'body' ).on( 'change', '.selectTemplate', function( e ) {						 		
 		var template = $(this).val();
 		rowNumber = rowNumber + 1;

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

	    //** Clear out current values just in case */
	    //$( '.iwp_newUser input' ).val( '' );

	    $.post( ajaxurl, {
	      action: 'iwp_get_user_data',
	      user_email: jQuery( this ).val()
	    }, function ( result ) {
	      if ( result ) {
	        user_data = result.user_data;

	        for ( var field in user_data ) {
	          jQuery( '.iwp_newUser .iwp_' + field ).val( user_data[field] );

	        }
	      }
	    }, 'json' );

	  } );


});