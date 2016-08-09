var App = {};
var $ = jQuery;

App = {

  debug: function (t) {
      console.log(t);
  }

}

jQuery(document).ready(function($) {
    App.Common.init();
    App.Menu.init();
    App.Home.init();
    App.Product.init();
    App.Cart.init();
});
