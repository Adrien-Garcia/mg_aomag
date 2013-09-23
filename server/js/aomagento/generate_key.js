jQuery(document).ready(function($) {
	
	var module_name = jQuery(".content-header h3").html();
	var is_aomagento = jQuery(".is_aomagento").val();
	if(is_aomagento == 0) {
		jQuery("tr[id^='row_"+module_name+"_licence_generator']").hide();
	} else {
		jQuery("tr[id^='row_"+module_name+"_licence_generator']").show();
	}
	
});

function generate_key(event, name) {
	
	var parent = jQuery(event.target).parent().parent();
	var hostname_object = jQuery("#"+parent[0]['id']+" .hostname");
	var hostname = hostname_object[0]['value'];
	
	jQuery.ajax({
		url: "/aomagento/generation/getlicence",
		method:"get",
		data : {hostname : hostname, module: name, id_parent: parent[0]['id']},
		success: function(data) {
			jQuery("#"+parent[0]['id']+" div.key_generated").html(data);
		}
	});
}
