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
	
	var id_parent = "";
	if(name == "SoColissimoLiberte" || name == "SoColissimoFlexibilite") {
		if(name == "SoColissimoLiberte") {
			id_parent = "row_socolissimo_licence_generator_liberte";
		} else {
			id_parent = "row_socolissimo_licence_generator_flexi";
		}
		var hostname = jQuery("#"+id_parent+" .hostname").val();
	} else {
		var hostname = jQuery("input.hostname").val();
	}
	
	jQuery.ajax({
		url: "/aomagento/generation/getlicence",
		method:"get",
		data : {hostname : hostname, module: name, id_parent: id_parent},
		success: function(data) {
			if(id_parent != "") {
				jQuery("#"+id_parent+" div.key_generated").html(data);
			} else {
				jQuery("div.key_generated").html(data);
			}
			
		}
	});
}
