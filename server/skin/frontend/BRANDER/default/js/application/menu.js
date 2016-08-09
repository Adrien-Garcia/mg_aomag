'use strict';

App.Menu = {

  init: function() {

      this.debug("Menu : init start");

      this.equalHeight();

      this.debug("Menu : init end");

  },

  equalHeight: function() {

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

  debug: function(t) {
      App.debug(t);
  }

}
