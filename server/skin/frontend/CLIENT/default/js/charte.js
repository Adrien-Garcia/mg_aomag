/*
 * Ce fichier centralise les scripts ajoutés et nécessaires
 * au fonctionnement du site.
 */

/*
 * DOM loaded event
 * Tous les scripts nécéssitants le chargement complet du DOM
 * doivent être centralisés dans cette fonction
 * Note : à l'intérieur il est possible d'utilisé la fonction $ native de jQuery
 */
jQuery(function($) {
	
	//Zoom sur l'image de la page produit
	$('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();
	
})

/*
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
