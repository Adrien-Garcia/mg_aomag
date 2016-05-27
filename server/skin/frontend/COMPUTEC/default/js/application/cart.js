'use strict';

App.Cart = {

   

    init: function() {

        if( !jQuery("body").hasClass("checkout-cart-index") ) return;

        this.debug("Cart : init start");

        //this.quantityStepper();

        this.refreshCart();

        this.debug("Cart : init end");

    },

    /*
     * MAJ du panier
     */
     refreshCart : function() {
        $(".btn-update").click(function() {
            $(this).addClass("refreshing");
            $("#form-cart").submit();
        });
    },

    quantityStepper : function() {

        this.debug("Cart : quantityStepper start");

        jQuery("input.qty").click(function(){
            var input = this;
            input.focus();
            input.setSelectionRange(0,999); 
        });
        
        /* Produit, panier : numeric stepper */
        jQuery(".btn-plus").click(function() {
            jQuery("input.qty", jQuery(this).parent()).val(parseInt(jQuery("input.qty", jQuery(this).parent()).val())+1);
            console.log("ici");
            setTimeout(function () {
                jQuery("#update_cart_form").submit();
            }, 200);
        });
        
        jQuery(".btn-moins").click(function() {
            if( parseInt(jQuery("input.qty", jQuery(this).parent()).val()) > 0 ) {
                jQuery("input.qty", jQuery(this).parent()).val(parseInt(jQuery("input.qty", jQuery(this).parent()).val())-1);
                setTimeout(function () {
                    jQuery("#update_cart_form").submit();
                }, 200);
            }
        });

        this.debug("Cart : quantityStepper end");

    },
    
    debug: function(t) {
        App.debug(t);
    }
};
