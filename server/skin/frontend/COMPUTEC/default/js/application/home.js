'use strict';

App.Home = {

   

    init: function() {

        if( !jQuery("body").hasClass("cms-index-index") ) return;

        this.debug("Common : init start");

        this.slider();

        this.tabs();

        this.debug("Common : init end");

    },

    // Accueil : carrousel
    slider: function() {
        jQuery('.cms-home .slideshow').bxSlider({
            minSlides: 1,
            maxSlides: 1,
            slideWidth: 734,
            auto: true,
            slideMargin: 0 
        });
    },
    
    // Accueil : onglets
    tabs : function() {
        jQuery(".product-grid-mea .category_name").appendTo(".tabs .titles");
        jQuery(".cms-home .tabs .titles a").click(function(e) {
            e.preventDefault();
            jQuery(".cms-home .tabs .titles a").removeClass("current");
            var i = jQuery(this).addClass("current").index();
            jQuery(this).parent().parent().find("> *").slice(1).hide().eq(i).show();
        }).eq(0).click();
    },
    
    debug: function(t) {
        App.debug(t);
    }
};
