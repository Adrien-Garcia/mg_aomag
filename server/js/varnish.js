
/**
 * Send ajax request to the Magento store in order to insert dynamic content into the
 * static page delivered from Varnish
 *
 */


jQuery(document).ready(function($) {

	var data = { getBlocks: {} };
	
	// add placeholders
	$('.varnish_placeholder').each(function() {
		data.getBlocks[$(this).attr('id')] = $(this).attr('rel');
	});

	// add current product
	if (typeof CURRENTPRODUCTID !== 'undefined' && CURRENTPRODUCTID) {
		data.currentProductId = CURRENTPRODUCTID;
	}

	if (Object.keys(data.getBlocks).length > 0) {
		$.get(
			VARNISH_DYN_URL,
			data,
			function (data) {
				for(var id in data.blocks) {
					$('#' + id).empty().html(data.blocks[id]);
					
				}
                                
				$.cookie('frontend', data.sid, { path: '/', domain : COOKIE_DOMAIN });
                                
			},
			'json'
		);
	}

});


 