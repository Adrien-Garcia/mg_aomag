'use strict';

App.Common = {

    searchMiniForm : "#search_mini_form",
    $searchMiniForm : null,
    $magicLine : null,
    $mainNav : null,
    magicLineVisible : false,

    init: function() {

        this.debug("Common : init start");

        

        this.debug("Common : init end");

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
