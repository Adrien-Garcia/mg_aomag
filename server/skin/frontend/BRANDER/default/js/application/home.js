'use strict';

App.Home = {

  init: function() {

    if( !jQuery("body").hasClass("cms-index-index") ) return;

    this.debug("Home : init start");
    this.homeCaroussel();
    this.homeTabs();
    this.debug("Home : init start");

  },

  homeCaroussel: function() {

  	$('.cms-home .slideshow').bxSlider({
  		minSlides: 1,
  		maxSlides: 1,
  		slideWidth: 1263,
  		auto: true,
  		slideMargin: 0
  	});

  },

  homeTabs: function() {

    $(".product-grid-mea .category_name").appendTo(".tabs .titles");
  	$(".cms-home .tabs .titles a").click(function(e) {
  		e.preventDefault();
  		$(".cms-home .tabs .titles a").removeClass("current");
  		var i = $(this).addClass("current").index();
  		$(this).parent().parent().find("> *").slice(1).hide().eq(i).show();
  	}).eq(0).click();

  },

  debug: function(t) {
      App.debug(t);
  }

}
