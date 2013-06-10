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
	
	var t = new Array();
	$(".more-views li a").each(function() {
		t[t.length] = $(this).attr("href");
	})
	
	$('.bxslider').bxSlider({
		minSlides: 4,
		maxSlides: 4,
		slideWidth: 50,
		pager: false,
		slideMargin: 20
	});
	
	/* Permet de mettre l'état current sur la bonnne miniature au chargement */
	$(".more-views .bxslider a").each(function(idx,el){
		var a = $(el).attr("rel");
		var b = a.substring(a.indexOf("http"), a.lastIndexOf("'")); // ATTENTION : suppose que smallimage est en fin d'attribut
		
		if($("#zoom-1 img").attr("src") == b){
			$(el).parents("li").addClass("current");
		}	
	});
	
	//Zoom sur l'image de la page produit
	$('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();
	
	$(".more-views img").bind("click", function(e) {
		$(this).parents("li").addClass("current").siblings().removeClass("current");
		var url = $(this).attr("src");
		$(this).parents("ul").find("img[src='" + url + "']").parents("li").addClass("current");
	});
	//$(".more-views li:not('.bx-clone') img").eq(0).click();
	
	$(".mousetrap").live("click", function() {
		//$(this).prev("a").click();
		//console.log($(this).prevAll("a"));
		//$.fancybox($(this).prev("a"));
		var href = $(this).prev("a").attr("href");
		//console.log("On cherche : " + href);
		var li = $(".more-views li:not(.bx-clone)");
		var idx = 0;
		li.each(function(index, element) {
			//console.log("-- " + $(element).find("a").attr("href"));
			if($(element).find("a[href='" + href + "']").attr("href") == href) {
				idx = index;
				//console.log("---- oui ! (" + index + ")");
			}
		});
		$.fancybox(t, {
		    helpers:  {
		        thumbs : {
		            width: 50,
		            height: 50
		        }
		    },
		    index: idx
		});
	});
	
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
