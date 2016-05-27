'use strict';

App.Common = {

    searchMiniForm : "#search_mini_form", // exemple de variable pour un selecteur css
    $searchMiniForm : null, // exemple de variable précédée d'un '$' pour le résultat d'une sélection jQuery => this.$searchMiniForm = jQuery(this.searchMiniForm);

    init: function() {

        this.debug("Common : init start");

        this.subMenuHeight();

        //this.scrolltop();

        /* Modification de la hauteur des étape de commande */
    //  $(".opc .step:visible").bind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function(){
    //      var childHeight = $(this).find("> form").outerHeight();
    //      var stepsHeight = $(".section").outerHeight();
    //      console.log(childHeight);
    //      $("#checkoutSteps").css("min-height", childHeight + stepsHeight + 60);
    //  });

        // Sélection du contenu intégrale du champ quantité au clic
        jQuery("input.qty").click(function(){
            var input = this;
            input.focus();
            input.setSelectionRange(0,999);
        });

        this.debug("Common : init end");

    },

    subMenuHeight : function() {
        /* Menu : donner la hauteur à toute les colonnes de second niveau */
        var id;
        jQuery("li.level0").mouseover(function() {
            var o = jQuery(this);
            
            var max = 0;
            jQuery("li.level1", o).each(function(index, element) {
                if(jQuery(element).height() > max) {
                    max = jQuery(element).height();
                }
            }).height(max);
            
            id = setInterval(function() {
                var max = 0;
                jQuery("li.level1", o).each(function(index, element) {
                    if(jQuery(element).height() > max) {
                        max = jQuery(element).height();
                    }
                }).height(max);
            }, 200);
        }).mouseout(function() {
            clearInterval(id);
        });
    },

    /* Scrool TOP */
    scrolltop : function() {

        var self = this;

        this.debug("scrolltop");

        jQuery("#totop").on('click',function(){
            jQuery('html, body').animate({scrollTop: 0}, 500, "swing");
          });

    },
    
    debug: function(t) {
        App.debug(t);
    }
};
