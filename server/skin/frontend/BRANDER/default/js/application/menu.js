'use strict';

App.Menu = {

  init: function() {

      this.debug("Menu : init start");

      this.equalHeight();

      this.debug("Menu : init end");

  },

  equalHeight: function() {

    var id;
  	$("li.level0").mouseover(function() {
  		var o = $(this);

  		var max = 0;
  		$("li.level1", o).each(function(index, element) {
  			if($(element).height() > max) {
  				max = $(element).height();
  			}
  		}).height(max);

  		id = setInterval(function() {
  			var max = 0;
  			$("li.level1", o).each(function(index, element) {
  				if($(element).height() > max) {
  					max = $(element).height();
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
