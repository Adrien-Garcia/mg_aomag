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
	
	/* Obfuscation lien accueil */
	demoer = {'home':'hjkl@|'};
	$('.lienhome').bind('click', function(){
		str = demoer["home"];
		str = str.replace('h', 'http://');
		//str = str.replace('m', 'www');
		//str = str.replace('d', '.');
		str = str.replace('j', 'changermonlien');
		str = str.replace('k', '.');
		str = str.replace('l', 'preprod.addonline');
		str = str.replace('@', '.');
		str = str.replace('|', 'biz');
		document.location=str;
	});

	$('.level-top.search').on('mouseout',function(){
		if(!$(this).hasClass('over'))$(this).find('input#search').trigger('blur');
	});
	
	/* Menu : donner la hauteur à toute les colonnes de second niveau */
	// var id;
	// $("li.level0").mouseover(function() {
	// 	var o = $(this);
		
	// 	var max = 0;
	// 	$("li.level1", o).each(function(index, element) {
	// 		if($(element).height() > max) {
	// 			max = $(element).height();
	// 		}
	// 	}).height(max);
		
	// 	id = setInterval(function() {
	// 		var max = 0;
	// 		$("li.level1", o).each(function(index, element) {
	// 			if($(element).height() > max) {
	// 				max = $(element).height();
	// 			}
	// 		}).height(max);
	// 	}, 200);
	// }).mouseout(function() {
	// 	clearInterval(id);
	// });
	//Menu : cart carrousel custom
	$('li.level0.cart ul.level0 .block-cart ol.mini-products-list .item').each(function(i){	$(this).addClass(''+i);});
	if($('li.level0.cart ul.level0 .block-cart ol.mini-products-list .item').size()>6){
		$('li.level0.cart ul.level0 .block-cart ol.mini-products-list .control.previous').click(function () {
			$(this).siblings('.item').filter(':first').insertAfter($(this).siblings('.item').filter(':last'));
		});
		$('li.level0.cart ul.level0 .block-cart ol.mini-products-list .control.next').click(function () {
			$(this).siblings('.item').filter(':last').insertBefore($(this).siblings('.item').filter(':first'));
		});
	}

	function resizeCheckoutSteps(){
		var h1 = $("#checkoutSteps li.recap").height();
		var h2 = $("#checkoutSteps li.section.allow.active>.step").height();
		h1 += $("#checkoutSteps li.section").height();
		h2 += $("#checkoutSteps li.section").height();
		var height = Math.max(h1,h2);
		$("#checkoutSteps").height(height+50);
	}
    $(document).on("open_Section", function(e) {
      resizeCheckoutSteps();
    });
	resizeCheckoutSteps();
	// Accueil : carrousel
	
	var sliderHome = $('.cms-home .slideshow').bxSlider({
		minSlides: 1,
		maxSlides: 1,
		// slideWidth: 1920,
		auto: true,
		slideMargin: 0 
	});
	$(window).resize(function(){if($('body').hasClass('cms-home'))sliderHome.reloadSlider();});

	$("input.qty").click(function(){
        var input = this;
        input.focus();
        input.setSelectionRange(0,999); 
    });

	//accueil : circles
	// $(".std .circle").appendTo(".col-main .circles");
	$(".circles .circle").each(function(){
		$(this).wrap('<div class="circle-container"></div>');
		var src = $(this).find('img').attr('src');
		$(this).css('background-image','url('+src+')');
		$(this).find('img').remove();
	});
	
	$(".products-grid .mini-product, .cart .mini-product").each(function(){
		var src = $(this).find('a.product-image>img').attr('src');
		var w = $(this).find('a.product-image>img').attr('width');
		var h = $(this).find('a.product-image>img').attr('height');
		var href = $(this).find('a.product-image').attr('href');
		$(this).css({'background-image':'url('+src+')',
					 'background-repeat':'no-repeat',
					 'background-size': w+'px '+h+'px'});
		$(this).find('a.product-image>img').remove();
		$(this).click(function(){
			//window.location = href;
		})
	});
	$(".home-mea .first").each(function(){
		var src = $(this).find('img').attr('src');
		var href = $(this).find('.widget a').attr('href');
		$(this).css({'background-image':'url('+src+')'});
		$(this).find('img').remove();
		$(this).click(function(){
			window.location = href;
		});

		var text = $(this).find('.widget a span').text();
		$(this).find('.widget').remove();
		$(this).append('<h1>'+text+'</h1>');
		$(this).append('<div class="primary-button"><p>Voir la collection</p></div>');
	});
	$('.messages').append('<div class="close-button">&nbsp;</div>');
	$('.messages .close-button').click(function(){
		$(this).parent().hide('2s');
	});
	$('select').wrap('<div class="select"></div>');
	$('.main-container>.breadcrumbs-container').prependTo('.main-container>.main');
	// $('.catalog-product-view .main-container .product-shop .wrapper-sku-ratings .product-sku')
	// 	.insertAfter('.catalog-product-view .main-container .product-shop .product-collateral .box-collateral.box-description>h2');
	/* Page title */
	$(".col-main .page-title").prependTo(".main");
	
	//incrementer
	$("span.qty").each(function(){
		var input = $(this).find('input[type=number]');
		$(this).prepend("<span>-</span>");
		$(this).find('span:first-of-type').click(function(){
			//input.stepDown(1);
			input.val( parseInt(input.val()) > 0 ? parseInt(input.val()) - 1 : parseInt(input.val()));
		});
		$(this).append("<span>+</span>");
		$(this).find('span:last-of-type').click(function(){
			//input.stepUp(1);
			input.val(parseInt(input.val()) < 999 ? parseInt(input.val()) + 1 : parseInt(input.val()));
		});

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
	
	$("#wrap").on('click',".mousetrap",function() {
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

	$("#j2t_ajax_confirm").on('click','#j2t_ajax_close',function(){$("#j2t-overlay").trigger('click');});
	$("#j2t_ajax_confirm").on('click','#j2t-cart-bts-continue',function(){$("#j2t-overlay").trigger('click');});

	//reorder columns wishlist
    $(".my-wishlist table#wishlist-table tr").each( function() { 
        $(this).children(":first-child").before($(this).children(":last-child").removeClass("last"));
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
