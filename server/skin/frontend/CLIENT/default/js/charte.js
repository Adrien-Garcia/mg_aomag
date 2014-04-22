/*
 * Ce fichier centralise les scripts ajoutés et nécessaires
 * au fonctionnement du site.
 */

/*
 * DOM loaded event
 * Tous les scripts nécéssitants le chargement complet du DOM
 * doivent être centralisés dans cette fonction
 * Note : à l'intérieur il est possible d'utiliser la fonction $ native de jQuery
 */
jQuery(function($) {
	
	/* Menu : donner la hauteur à toute les colonnes de second niveau */
	var id;
	$("li.level0").mouseover(function() {
		var o = $(this);
		id = setInterval(function() {
			var max = 0;
			$("li.level1", o).each(function(index, element) {
				if($(element).height() > max) {
					max = $(element).height();
				}
			}).height(max);
		}, 200);
	}).mouseout(function() {
		clearInterval(id);
	});
	
	// Accueil : carrousel
	$('.cms-home .slideshow').bxSlider({
		minSlides: 1,
		maxSlides: 1,
		slideWidth: 734,
		auto: true,
		slideMargin: 0 
	});
	
	// Accueil : onglets
	$(".cms-home .tabs .titles a").click(function(e) {
		e.preventDefault();
		$(".cms-home .tabs .titles a").removeClass("current");
		var i = $(this).addClass("current").index();
		$(this).parent().parent().find("> *").slice(1).hide().eq(i).show();
	}).eq(0).click();
	
	/* Page title */
	//$(".col-main .page-title").prependTo(".main");
	
	$("input.qty").click(function(){
        var input = this;
        input.focus();
        input.setSelectionRange(0,999); 
    });
	
	var t = new Array();
	$(".more-views li a").each(function() {
		t[t.length] = $(this).attr("href");
	})
	
	if ($(".more-views li").length > 6) {
		/* On initialize bxSlider si on est sur la page produit */
		$('.bxslider').bxSlider({
			minSlides: 6,
			maxSlides: 6,
			slideWidth: 68,
			pager: false,
			slideMargin: 10 
		});
	}
		
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
	
	// Produit : carrousel sur les ventes incitatives
	if ($("#upsell-product-table > li").size() > 4) {
		$('#upsell-product-table').bxSlider({
			minSlides: 4,
			maxSlides: 4,
			slideWidth: 225,
			pager: true,
			controls: false,
			slideMargin: 20,
			responsive: false
		});
	}

	/* Modification de la hauteur des étape de commande */
//	$(".opc .step:visible").bind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function(){
//		var childHeight = $(this).find("> form").outerHeight();
//		var stepsHeight = $(".section").outerHeight();
//		console.log(childHeight);
//		$("#checkoutSteps").css("min-height", childHeight + stepsHeight + 60);
//	});
	
	// Produit : formulaire de commentaire en layer
	$(".add-my-review, .no-rating a").click(function(e) {
		e.preventDefault();
		$.fancybox($(".product-view .box-reviews .form-add"));
	});
	
	//
	$("#product-review-table .radio").each(function() {
		$(this).wrap("<span />");
	}).change(function(e) {
		$("#product-review-table span").removeClass("on");
		if(this.checked) {
			var o = this;
			var test = true;
			$("#product-review-table .radio").each(function(index, element) {
				if(test) {
					$(this).parent().addClass("on");
					if(o == element) {					
						test = false;
					}
				}
			});
		}
	});
	
	/* Produit : ouvrir automatiquement l'onglet avis dans le cas de la pagination des commentaires */
	if($("body").hasClass("catalog-product-view") && location.search.length > 0) {
		var search = location.search.substring(1);
		var urlParameters = JSON.parse('{"' + decodeURI(search).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"') + '"}');
		if(urlParameters.p !== undefined || urlParameters.limit !== undefined) {
			window.location = "#customer-reviews";
		}
	}
	
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
