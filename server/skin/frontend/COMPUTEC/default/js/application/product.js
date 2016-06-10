'use strict';

App.Product = {

   

    init: function() {

        if( !jQuery("body").hasClass("catalog-product-view") ) return;

        this.debug("Product : init start");

        //this.quantityStepper();

        this.galerie();

        this.upsellSlider();

        this.reviews();

        this.debug("Product : init end");

    },

    // Carousel sur les ventes incitatives
    upsellSlider : function() {
        if (jQuery("#upsell-product-table > li").size() > 4) {
            jQuery('#upsell-product-table').bxSlider({
                minSlides: 4,
                maxSlides: 4,
                slideWidth: 225,
                pager: true,
                controls: false,
                slideMargin: 20,
                responsive: false
            });
        }
    },

    // Mega combo cloudzoom + bxslider + fancybox
    galerie : function(){

        // feinte pour déclancher l'ouverture de la galerie sur le cloudzoom
        jQuery(".mousetrap").live("click", function() {
            //jQuery(this).prev("a").click();
            //console.log(jQuery(this).prevAll("a"));
            //jQuery.fancybox(jQuery(this).prev("a"));
            var href = jQuery(this).prev("a").attr("href");
            //console.log("On cherche : " + href);
            var li = jQuery(".more-views li:not(.bx-clone)");
            var idx = 0;
            li.each(function(index, element) {
                //console.log("-- " + jQuery(element).find("a").attr("href"));
                if(jQuery(element).find("a[href='" + href + "']").attr("href") == href) {
                    idx = index;
                    //console.log("---- oui ! (" + index + ")");
                }
            });
            jQuery.fancybox(t, {
                helpers:  {
                    thumbs : {
                        width: 50,
                        height: 50
                    }
                },
                index: idx
            });
        });

        // récupération des urls des grandes images
        t = new Array();
        jQuery(".more-views li a").each(function() {
            t[t.length] = jQuery(this).attr("href");
        });

        if (jQuery(".more-views li").length > 6) {
            /* On initialize bxSlider si on est sur la page produit */
            jQuery('.bxslider').bxSlider({
                minSlides: 6,
                maxSlides: 6,
                slideWidth: 68,
                pager: false,
                slideMargin: 10
            });
        }

        /* Permet de mettre l'état current sur la bonnne miniature au chargement */
        jQuery(".more-views .bxslider a").each(function(idx,el){
            var a = jQuery(el).attr("rel");
            var b = a.substring(a.indexOf("http"), a.lastIndexOf("'")); // ATTENTION : suppose que smallimage est en fin d'attribut

            if(jQuery("#zoom-1 img").attr("src") == b){
                jQuery(el).parents("li").addClass("current");
            }
        });

        //Zoom sur l'image de la page produit
        jQuery('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();

        jQuery(".more-views img").bind("click", function(e) {
            jQuery(this).parents("li").addClass("current").siblings().removeClass("current");
            var url = jQuery(this).attr("src");
            jQuery(this).parents("ul").find("img[src='" + url + "']").parents("li").addClass("current");
        });
        //jQuery(".more-views li:not('.bx-clone') img").eq(0).click();
    },

    // Bouton +/- au niveau du champ qty
    quantityStepper : function() {

        this.debug("Product : quantityStepper start");

        jQuery("input.qty").click(function(){
            var input = this;
            input.focus();
            input.setSelectionRange(0,999); 
        });
        
        /* Produit, panier : numeric stepper */
        jQuery(".btn-plus").click(function() {
            jQuery("input.qty", jQuery(this).parent()).val(parseInt(jQuery("input.qty", jQuery(this).parent()).val())+1);
            setTimeout(function () {
                jQuery(".btn-update").click();
            }, 200);
        });
        
        jQuery(".btn-moins").click(function() {
            if( parseInt(jQuery("input.qty", jQuery(this).parent()).val()) > 0 ) {
                jQuery("input.qty", jQuery(this).parent()).val(parseInt(jQuery("input.qty", jQuery(this).parent()).val())-1);
                setTimeout(function () {
                    jQuery(".btn-update").click();
                }, 200);
            }
        });

        this.debug("Product : quantityStepper end");

    },

    reviews : function() {
        // Produit : formulaire de commentaire en layer
        jQuery(".add-my-review, .no-rating a").click(function(e) {
            e.preventDefault();
            jQuery.fancybox(jQuery(".product-view .box-reviews .form-add"));
        });
        
        // Deco ?
        jQuery("#product-review-table .radio").each(function() {
            jQuery(this).wrap("<span />");
        }).change(function(e) {
            jQuery("#product-review-table span").removeClass("on");
            if(this.checked) {
                var o = this;
                var test = true;
                jQuery("#product-review-table .radio").each(function(index, element) {
                    if(test) {
                        jQuery(this).parent().addClass("on");
                        if(o == element) {                  
                            test = false;
                        }
                    }
                });
            }
        });
        
        /* Produit : ouvrir automatiquement l'onglet avis dans le cas de la pagination des commentaires */
        if(jQuery("body").hasClass("catalog-product-view") && location.search.length > 0) {
            var search = location.search.substring(1);
            var urlParameters = JSON.parse('{"' + decodeURI(search).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"') + '"}');
            if(urlParameters.p !== undefined || urlParameters.limit !== undefined) {
                window.location = "#customer-reviews";
            }
        }
    },
    
    debug: function(t) {
        App.debug(t);
    }
};
