jQuery(document).ready(function($) {
	// Enables the color picker in settings page
	$('.fdoe-color-picker').wpColorPicker();

	if (!$('#fdoe_popup_simple:checkbox').prop('checked') && !$('#fdoe_popup_variable:checkbox').prop('checked')) {
		$('#fdoe_product_popup_content').parents('tr').hide();
	//	if ($('#fdoe_product_popup_content').find('option:selected').attr("value") == 'theme') {
			$('#fdoe_product_popup_content_spec').parents('tr').hide();
	//	}
	}
	$('#fdoe_popup_simple:checkbox , #fdoe_popup_variable:checkbox').change(function() {
		if (this.checked) {
			$('#fdoe_product_popup_content').parents('tr').fadeIn('slow');
			if ($('#fdoe_product_popup_content').find('option:selected').attr("value") == 'custom') {
				$('#fdoe_product_popup_content_spec').parents('tr').fadeIn('slow');
			}
		} else if ((!$('#fdoe_popup_simple:checkbox').prop('checked') && !$('#fdoe_popup_variable:checkbox').prop('checked'))) {
			$('#fdoe_product_popup_content_spec').parents('tr').hide();
			$('#fdoe_product_popup_content').parents('tr').hide();
		}
	});


	if ($('#fdoe_product_popup_content').find('option:selected').attr("value") == 'theme') {
		$('#fdoe_product_popup_content_spec').parents('tr').hide();
	}
	$('#fdoe_product_popup_content').change(function() {
		if ($(this).find('option:selected').attr("value") == 'theme') {
			$('#fdoe_product_popup_content_spec').parents('tr').hide();
		} else if ($(this).find('option:selected').attr("value") == 'custom') {
			$('#fdoe_product_popup_content_spec').parents('tr').fadeIn('slow');
		}
	});
	$('.woocommerce').find('table').show();
	$('.woocommerce').find('h2').show();


});
