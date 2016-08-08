'use strict';

/*
 * Plugin jQuery d'initilisation des valeurs par défaut sur les champs texte
 * Au clic, si le champ contient la valeur par défaut, celui-ci est vidé.
 * Quand on quitte le champ, si celui-ci est vide, on remet la valeur par défaut.
 */

App.Common = {

  init: function() {

      this.debug("Common : init start");
      this.qtyUpdate();
      this.debug("Common : init end");

  },

  qtyUpdate: function() {

    $("input.qty").click(function(){
      var input = this;
      input.focus();
      input.setSelectionRange(0,999);
    });

  },

  debug: function(t) {
      App.debug(t);
  }

}
