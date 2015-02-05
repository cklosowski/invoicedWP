jQuery(document).ready(function ($) {
	jQuery('.iwp_settings_upload_button').click(function() {
	    formfield = jQuery('#iwp_settings[email_logo]').attr('name');
	    tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
	    return false;
	});

	window.send_to_editor = function(html) {
	 imgurl = jQuery('img',html).attr('src');
	 jQuery('#iwp_settings[email_logo]').val(imgurl);
	 tb_remove();
	}



	if( $('select.iwp-no-states').length ) {
		$('select.iwp-no-states').closest('tr').hide();
	}

	// Update base state field based on selected base country
	$('select[name="iwp_settings[business_country]"]').change(function() {
		var $this = $(this), $tr = $this.closest('tr');
		data = {
			action: 'iwp_get_shop_states',
			country: $(this).val(),
			field_name: 'iwp_settings[business_state]'
		};
		
		$.post(ajaxurl, data, function ( response ) {
			alert( response );
			if( 'false' == response ) {
				$tr.next().hide();
			} else {
				$tr.next().show();
				$tr.next().find('select').replaceWith( response );
			}
		});

		return false;
	});


});