'use strict';

App.Cart = {

    init: function() {

        if( !jQuery("body").hasClass("checkout-cart-index") ) return;

        this.debug("Cart : init start");

        this.refreshCart();

        this.debug("Cart : init end");

    },

    // Cart update
    refreshCart : function() {
      jQuery(".btn-update").click(function() {
          jQuery(this).addClass("refreshing");
          jQuery("#form-cart").submit();
      });
    },

    debug: function(t) {
        App.debug(t);
    }
};
