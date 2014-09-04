
/**
 * Send ajax request to the Magento store in order to insert dynamic content into the
 * static page delivered from Varnish
 *
 */


jQuery(document).ready(function($) {

	var data = { getBlocks: {} , getPriceBlocks: {} };
	
	// add placeholders
	$('.varnish_placeholder').each(function() {
		data.getBlocks[$(this).attr('id')] = $(this).attr('rel');
	});

	$('.varnish_catalog_product').each(function() {
		data.getPriceBlocks[$(this).attr('id')] = $(this).attr('rel');
	});

	// add current product
	if (typeof CURRENTPRODUCTID !== 'undefined' && CURRENTPRODUCTID) {
		data.currentProductId = CURRENTPRODUCTID;
	}

	if (Object.keys(data.getBlocks).length > 0 || Object.keys(data.getPriceBlocks).length > 0) {
		$.get(
			VARNISH_DYN_URL,
			data,
			function (data) {

				//On remplace les blocks html dynamiques dans la page	
				for(var id in data.blocks) {
					$('#' + id).empty().html(data.blocks[id]);
				}
                                
				//on remplace les formkey dans les formulaires
				$("input[name='form_key']").val(data.formkey);
				
				//on initialise le cookie de session
				$.cookie('frontend', data.sid, { path: '/', domain : COOKIE_DOMAIN });
                                
			},
			'json'
		);
	}

});


 