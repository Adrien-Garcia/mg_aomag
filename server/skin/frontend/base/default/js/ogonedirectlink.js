// payment ogone
var PaymentOgone = Class.create();
PaymentOgone.prototype = {
    initialize: function(form, saveUrl, successUrl) {
        this.form = form;
        this.saveUrl = saveUrl;
        this.successUrl = successUrl;
        this.onSave = this.nextStep.bindAsEventListener(this);
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
    },

    init : function () {
    	var elements = Form.getElements(this.form);
        if ($(this.form)) {
            $(this.form).observe('submit', function(event){this.save();Event.stop(event);}.bind(this));
        }
    },

    save: function() {
        var validator = new Validation(this.form);
        if (validator.validate()) {
        	simpleAjaxLoader.load();
        	var layer = simpleAjaxLoader.getOverlay().find("div div p").html('Paiement en cours...');
        	var params = Form.serialize(this.form);
            var request = new Ajax.Request(
                this.saveUrl,
                {
                    method:'post',
                    onComplete: this.onComplete,
                    onSuccess: this.onSave,
                    parameters: params
                }
            );
        }
    },
    
    resetLoadWaiting: function(){
    	//simpleAjaxLoader.close();
    },

    nextStep: function(transport){
        if (transport && transport.responseText){
            try{
                response = eval('(' + transport.responseText + ')');
            }
            catch (e) {
                response = {};
            }
        }

        
        if (response.success) {
            try{
            this.isSuccess = true;
            var layer = simpleAjaxLoader.getOverlay();
            layer.find(".loading").hide();
            layer.find(".content").show().html('Votre paiement a été accepté par la banque.');
        	window.location=this.successUrl;
        	return;
            }
            catch (e) {
               alert(e);
            }
        }
        
        /*
         * if there is an error in payment, need to show error message
         */
         if (response.error) {
             if (response.fields) {
                 var fields = response.fields.split(',');
                 for (var i=0;i<fields.length;i++) {
                     var field = null;
                     if (field = $(fields[i])) {
                         Validation.ajaxError(field, response.error);
                     }
                 }
                 return;
             }
             //alert(response.error_messages);
             var layer = simpleAjaxLoader.getOverlay();
             layer.find(".loading").hide();
             layer.find(".content").show().html(response.error_messages).animate({height: "+=30"}, 200, function() {
             	jQuery(this).parents(".layer").find(".close").css({opacity: 0, display: 'block'}).animate({bottom: "10px", opacity: "1"}, function() {
             		try {
             			this.style.removeAttribute("filter");
             		} catch(err) {}
             	});
             });
             return;
         }
        
    }

}