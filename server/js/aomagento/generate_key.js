jQuery(document).ready(function($) {
	
	var module_name = jQuery(".content-header h3").html();
	var is_aomagento = jQuery(".is_aomagento").val();
	if(is_aomagento == 0) {
		jQuery("tr[id^='row_"+module_name+"_licence_generator']").hide();
	} else {
		jQuery("tr[id^='row_"+module_name+"_licence_generator']").show();
	}
	
});

function generate_key(event, name, module_id = "") {
	
	var hostname_val = jQuery("#"+module_id+" .hostname").val();
	
	jQuery.ajax({
		url: "/aomagento/generation/getlicence",
		method:"get",
		data : {hostname : hostname_val, module: name},
		success: function(data) {
			var datas = data.split("::");
			jQuery("#"+module_id+" .hostname").val(datas[0]);
			jQuery("#"+module_id+" div.key_generated").html(datas[1]);
			
		}
	});
}
