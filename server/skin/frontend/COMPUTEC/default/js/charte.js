/*
 * on DOM loaded
 */
jQuery(function($) {	
	
	// rien

});

/*
 *   TODO : à déplacer dans application/common.js puis à supprimer
 *
 * Plugin jQuery d'initilisation des valeurs par défaut sur les champs texte
 * Au clic, si le champ contient la valeur par défaut, celui-ci est vidé.
 * Quand on quitte le champ, si celui-ci est vide, on remet la valeur par défaut.
 */

jQuery.fn.fields = function() {
	this.each(function(index, element) {
		jQuery(element).data("defaultValue", jQuery(element).val());

		jQuery(element).focus(function() {
			if(jQuery(this).val() == jQuery(this).data("defaultValue")) {
				jQuery(this).val("");
				jQuery(this).addClass('focused');
			}
		}).blur(function() {
			if(jQuery(this).val() == "") {
				jQuery(this).val(jQuery(this).data("defaultValue"));
				jQuery(this).removeClass('focused');
			}
		});
	});
};
