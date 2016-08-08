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
      $(".btn-update").click(function() {
          $(this).addClass("refreshing");
          $("#form-cart").submit();
      });
    },

    debug: function(t) {
        App.debug(t);
    }
};
