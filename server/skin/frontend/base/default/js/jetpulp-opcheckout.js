/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    design
 * @package     jetpulp_checkout
 * @copyright   Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
;
Checkout.prototype.initialize= function(accordion, urls){
    this.accordion = accordion;
    this.progressUrl = urls.progress;
    this.reviewUrl = urls.review;
    this.saveMethodUrl = urls.saveMethod;
    this.failureUrl = urls.failure;
    this.billingForm = false;
    this.shippingForm= false;
    this.syncBillingShipping = false;
    this.method = '';
    this.payment = '';
    this.loadWaiting = false;
    this.steps = ['login', 'information', 'shipping', 'shipping_method', 'payment', 'review'];
    //We use billing as beginning step since progress bar tracks from billing
    this.currentStep = 'information';

    this.accordion.sections.each(function(section) {
        Event.observe($(section).down('.step-title'), 'click', this._onSectionClick.bindAsEventListener(this));
    }.bind(this));

    this.accordion.disallowAccessToNextSections = true;
};

Checkout.prototype.gotoSection = function (section, reloadProgressBlock) {

    if (reloadProgressBlock) {
        this.reloadProgressBlock(this.currentStep);
    }
    this.currentStep = section;
    var sectionElement = $('opc-' + section);
    sectionElement.addClassName('allow');
    if (['information', 'shipping', 'shipping_method'].indexOf(section) != -1) {
        $('checkout-required').show();
    } else {
        $('checkout-required').hide();
    }
    this.accordion.openSection('opc-' + section);
    if(!reloadProgressBlock) {
        this.resetPreviousSteps();
    }
};

Checkout.prototype._onSectionClick= function(event) {
    var section = $(Event.element(event).up('li'));
    if (section.hasClassName('allow')) {
        Event.stop(event);
        this.gotoSection(section.readAttribute('id').replace('opc-', ''), false);
        return false;
    }
};

Checkout.prototype.setMethod= function(){
    // alert('setMethod information');
    if ($('login:guest') && $('login:guest').checked) {
        this.method = 'guest';
        var request = new Ajax.Request(
            this.saveMethodUrl,
            {method: 'post', onFailure: this.ajaxFailure.bind(this), parameters: {method:'guest'}}
        );
        //Element.hide('register-form-part');
        Element.hide('register-customer-password');
        this.gotoSection('information', true);
    }
    else if($('login:register') && ($('login:register').checked || $('login:register').type == 'hidden')) {
        this.method = 'register';
        var request = new Ajax.Request(
            this.saveMethodUrl,
            {method: 'post', onFailure: this.ajaxFailure.bind(this), parameters: {method:'register'}}
        );
        Element.show('register-form-part');
        Element.show('register-customer-password');
        this.gotoSection('information', true);
    }
    else{
        alert(Translator.translate('Please choose to register or to checkout as a guest').stripTags());
        return false;
    }
    document.body.fire('login:setMethod', {method : this.method});
};
/**
*   Replace setBilling to use the checkbox instead of radio
*   and replace the next step for shipping method
*
**/
Checkout.prototype.setBilling= function() {
    // if (($('billing:use_for_shipping')) && ($('billing:use_for_shipping').checked)) {
        shipping.syncWithBilling();
        $('opc-shipping_method').addClassName('allow'); 
        $('shipping:same_as_billing').checked = true;
        this.gotoSection('shipping_method', true);
    // } else if (($('billing:use_for_shipping')) && (!$('billing:use_for_shipping').checked)) {
    //     $('shipping:same_as_billing').checked = false;
    //     this.gotoSection('shipping_method', true);
    // } else {
    //     $('shipping:same_as_billing').checked = true;
    //     this.gotoSection('shipping_method', true);
    // }

    // this refreshes the checkout progress column

//        if ($('billing:use_for_shipping') && $('billing:use_for_shipping').checked){
//            shipping.syncWithBilling();
//            //this.setShipping();
//            //shipping.save();
//            $('opc-shipping').addClassName('allow');
//            this.gotoSection('shipping_method');
//        } else {
//            $('shipping:same_as_billing').checked = false;
//            this.gotoSection('shipping');
//        }
//        this.reloadProgressBlock();
//        //this.accordion.openNextSection(true);
};

Checkout.prototype.setShippingMethod= function() {
    //this.nextStep();
    this.gotoSection('payment', true);
    this.reloadReviewBlock();
    //this.accordion.openNextSection(true);
};

Checkout.prototype.reloadProgressBlock= function(toStep) {
    if(toStep == undefined ){
        var n = this.steps.indexOf(this.currentStep);
        for( var i = 0; i < n; i++) {
            this.reloadStep(this.steps[i]);
        };
        return;
    }
    this.reloadStep(toStep);
    if(toStep == 'shipping_method') {
        this.reloadStep('shipping');
    }
    if (this.syncBillingShipping) {
        this.syncBillingShipping = false;
        this.reloadStep('shipping');
    }
};

Checkout.prototype.reloadStep= function(prevStep) {
    var updater = new Ajax.Updater(prevStep + '-progress-opcheckout', this.progressUrl, {
        method:'get',
        onFailure:function(){
            //nothing: it used to redirect to checkout/cart if error but error can occur just when generating the be2bill iframe
        },
        onComplete: function(){
            this.checkout.resetPreviousSteps();
        },
        parameters:prevStep ? { prevStep:prevStep } : null
    });
};


Billing.prototype.initialize= function(form, addressUrl, saveUrl, loadUrl){
    this.form = form;
    if ($(this.form)) {
        $(this.form).observe('submit', function(event){this.save();Event.stop(event);}.bind(this));
    }
    this.addressUrl = addressUrl;
    this.saveUrl = saveUrl;
    this.loadUrl = loadUrl;
    this.onShippingTypesLoad = this.loadShippingTypes.bindAsEventListener(this);
    this.onAddressLoad = this.fillForm.bindAsEventListener(this);
    this.onSave = this.nextStep.bindAsEventListener(this);
    this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
};

Billing.prototype.save= function() {
    if (checkout.loadWaiting!=false) return;

    var validator = new Validation(this.form);
    if (validator.validate()) {
        checkout.setLoadWaiting('billing');

        var request = new Ajax.Request(
            this.saveUrl,
            {
                method: 'post',
                onComplete: this.onComplete,
                onSuccess: this.onSave,
                onFailure: checkout.ajaxFailure.bind(checkout),
                parameters: Form.serialize(this.form)
            }
        );
    }
};

Billing.prototype.nextStep= function(transport){
    if (transport && transport.responseText){
        try{
            response = eval('(' + transport.responseText + ')');
        }
        catch (e) {
            response = {};
        }
    }

    if (response.error){
        if ((typeof response.message) == 'string') {
            alert(response.message);
        } else {
            if (window.billingRegionUpdater) {
                billingRegionUpdater.update();
            }

            alert(response.message.join("\n"));
        }

        return false;
    }

    //Load ShippingTypes (billing addess, shiping address, pickup store
    var load = new Ajax.Request(
        this.loadUrl,
        {
            method: 'post',
            onSuccess: this.onShippingTypesLoad,
            onFailure: checkout.ajaxFailure.bind(checkout)
        }
    );

    checkout.setStepResponse(response);
    payment.initWhatIsCvvListeners();
    $('co-shipping-method-form').show();
    $('onepage-checkout-shipping').hide();
    //use same adress as billing, or shipping new one
    if(response.duplicateBillingInfo) {
        shipping.setSameAsBilling(true);
        shipping.save();
    } else {
        $("shipping:same_as_billing").value=0;
        $("onepage-checkout-shipping").show();
    }

};


Billing.prototype.loadShippingTypes= function(transport){
    var response = "";
    if (transport && transport.responseText){
        try{
            response = eval('(' + transport.responseText + ')');
        }
        catch (e) {
            response = {};
        }
    }
    if (response.error){
        if ((typeof response.message) == 'string') {
            alert(response.message);
        } else {
            if (window.billingRegionUpdater) {
                billingRegionUpdater.update();
            }

            alert(response.message.join("\n"));
        }

        return false;
    }
    if (response.update_section) {
        $('checkout-'+response.update_section.name+'-load').update(response.update_section.html);
    }
};


ShippingMethod.prototype.savePickup= function(form) {
    if (checkout.loadWaiting!=false) return;
    if (this.validate()) {
        checkout.setLoadWaiting('shipping-method');
        var request = new Ajax.Request(
            this.saveUrl,
            {
                method:'post',
                onComplete: this.onComplete,
                onSuccess: this.onSave,
                onFailure: checkout.ajaxFailure.bind(checkout),
                parameters: Form.serialize(form)
            }
        );
    }
    checkout.reloadReviewBlock();
};

ShippingMethod.prototype.nextStep= function(transport){
    if (transport && transport.responseText){
        try{
            response = eval('(' + transport.responseText + ')');
        }
        catch (e) {
            response = {};
        }
    }

    if (response.error) {
        alert(response.message);
        return false;
    }
    checkout.reloadReviewBlock();

    if (response.update_section) {
        $('checkout-'+response.update_section.name+'-load').update(response.update_section.html);
    }

    payment.initWhatIsCvvListeners();

    if (response.goto_section) {
        checkout.gotoSection(response.goto_section, true);
        checkout.reloadProgressBlock();
        return;
    }

    if (response.payment_methods_html) {
        $('checkout-payment-method-load').update(response.payment_methods_html);
    }


    checkout.setShippingMethod();
};


ShippingMethod.prototype.resetLoadWaiting= function(transport) {
    checkout.setLoadWaiting(false);
    $('checkout-shipping-method-load').removeClassName("method-refreshing");
    $('checkout-shipping-method-pickup-load').removeClassName("method-refreshing");

};


Shipping.prototype.saveMinimal= function(form) {
    if (checkout.loadWaiting!=false) return;
    request = false;
    $('checkout-shipping-method-pickup-load').update('');
    $('checkout-shipping-method-pickup-load').addClassName("method-refreshing");
    var validator = new Validation(this.form);
    if (validator.validate()) {
        checkout.setLoadWaiting('shipping');
        var request = new Ajax.Request(
            this.saveUrl,
            {
                method:'post',
                onSuccess: function() {
                    checkout.setLoadWaiting(false);

                    shippingMethod.savePickup(form).bind(shippingMethod);
                    $('opc-payment').addClassName('allow');
                    //checkout.gotoSection('payment', true);
                    $('checkout-shipping-method-pickup-load').removeClassName("method-refreshing");
                },
                onFailure: checkout.ajaxFailure.bind(checkout),
                parameters: Form.serialize(this.form)
            }
        );
    }

};

Shipping.prototype.setSameAsBilling = function(flag) {
    $('shipping:same_as_billing').checked = flag;
// #5599. Also it hangs up, if the flag is not false
//        $('billing:use_for_shipping_yes').checked = flag;
    if (flag) {
        this.syncWithBilling();
    } else {
        //this.unSyncWithBilling();
    }
};

Shipping.prototype.unSyncWithBilling = function () {

    $('shipping:same_as_billing').checked = false;

    arrElements = Form.getElements(this.form);
    for (var elemIndex in arrElements) {
        if (arrElements[elemIndex].id) {
            var sourceField = $(arrElements[elemIndex].id.replace(/^shipping:/, 'billing:'));
            if (sourceField && ["shipping:gender", "shipping:firstname", "shipping:lastname", "shipping:country_id"].indexOf(arrElements[elemIndex].id) != -1 ){
                arrElements[elemIndex].value = sourceField.value;
            }else if (arrElements[elemIndex].id == "shipping-address-select" || arrElements[elemIndex].id == "shipping:save_in_address_book") {
                //nothing
            } else {
                arrElements[elemIndex].value = "";
            }
        }
    }
    $('shipping:country_id').value = $('billing:country_id').value;
    //shippingForm.elementChildLoad($('shipping:country_id'), this.setRegionValue.bind(this));
};

Shipping.prototype.save= function() {
    if (checkout.loadWaiting!=false) return;
    request = false;
    $('checkout-shipping-method-load').update('');
    $('checkout-shipping-method-load').addClassName("method-refreshing");
    var validator = new Validation(this.form);
    if (validator.validate()) {
        checkout.setLoadWaiting('shipping');
        var request = new Ajax.Request(
            this.saveUrl,
            {
                method:'post',
                onComplete: this.onComplete,
                onSuccess: this.onSave,
                onFailure: checkout.ajaxFailure.bind(checkout),
                parameters: Form.serialize(this.form)
            }
        );
    }
    $('co-shipping-method-form').show();
    checkout.reloadReviewBlock();
};

Shipping.prototype.nextStep= function(transport){
    if (transport && transport.responseText){
        try{
            response = eval('(' + transport.responseText + ')');
        }
        catch (e) {
            response = {};
        }
    }
    if (response.error){
        if ((typeof response.message) == 'string') {
            alert(response.message);
        } else {
            if (window.shippingRegionUpdater) {
                shippingRegionUpdater.update();
            }
            alert(response.message.join("\n"));
        }

        return false;
    }

    checkout.setStepResponse(response);
    jQuery('html, body').animate({
        scrollTop: (jQuery("#co-shipping-method-form").offset().top - jQuery(window).height() + jQuery("#co-shipping-method-form").height() )
    }, 1000);

    /*
     var updater = new Ajax.Updater(
     'checkout-shipping-method-load',
     this.methodsUrl,
     {method:'get', onSuccess: checkout.setShipping.bind(checkout)}
     );
     */
    //checkout.setShipping();
};

Shipping.prototype.resetLoadWaiting= function(transport){
    checkout.setLoadWaiting(false);
    $('checkout-shipping-method-load').removeClassName("method-refreshing");
    $('checkout-shipping-method-pickup-load').removeClassName("method-refreshing");

};

Payment.prototype.initialize= function(form, saveUrl){
    this.form = form;
    this.saveUrl = saveUrl;
    this.onSave = this.nextStep.bindAsEventListener(this);
    this.onComplete = this.cbJetpulp.bindAsEventListener(this);
};

Payment.prototype.init= function () {
    this.beforeInit();
    var elements = Form.getElements(this.form);
    if ($(this.form)) {
        $(this.form).observe('submit', function(event){this.save();Event.stop(event);}.bind(this));
    }
    var method = null;
    for (var i=0; i<elements.length; i++) {
        if (elements[i].name=='payment[method]' || elements[i].name.substr(0, 9) == 'agreement') {
            if (elements[i].checked) {
                method = elements[i].value;
            }
        } else {
            elements[i].disabled = true;
        }
        elements[i].setAttribute('autocomplete','off');
    }
    if (method) this.switchMethod(method);
    this.afterInit();
};

Payment.prototype.cbJetpulp= function(){
    this.resetLoadWaiting(true);
    //JETPULP
    //this.openBeBill(); //for example
    review.save().bind(review);
};


Payment.prototype.save= function() {
    if (checkout.loadWaiting!=false) return;
    var validator = new Validation(this.form);
    if (this.validate() && validator.validate()) {
        checkout.setLoadWaiting('payment');
        var request = new Ajax.Request(
            this.saveUrl,
            {
                method:'post',
                onComplete: this.onComplete,
                onSuccess: this.onSave,
                onFailure: checkout.ajaxFailure.bind(checkout),
                parameters: Form.serialize(this.form)
            }
        );
    }
};

Payment.prototype.nextStep= function(transport){
    if (transport && transport.responseText){
        try{
            response = eval('(' + transport.responseText + ')');
        }
        catch (e) {
            response = {};
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
        if (typeof(response.message) == 'string') {
            alert(response.message);
        } else {
            alert(response.error);
        }
        return;
    }

    checkout.setStepResponse(response);

    //checkout.setPayment();
};

/**
* Extrait de CarréBlanc
*
*/
Payment.prototype.openBeBill = function(){
    var headers = $$('#' + checkout.accordion.container.readAttribute('id') + ' .section');
    var links = $$('a.disable-on-payment');
    var cartlink = $$('a.change-cart');
    var isrc = $('be2bill_iframe').readAttribute('data-src');
    review.save();
    Event.observe(document.body, 'review:success', function(e,el){
        $("payment-buttons-container").hide();
        $("payment-buttons-container").disabled = "disabled";

        var myVar=setInterval(function(){
            if (window.response.success) {
                window.clearInterval(myVar);
                //désactiver les liens du header
                headers.each(function(header) {
                    header.removeClassName('allow');
                });
                //désactiver les liens sortants
                links.each(function (link) {
                    link.removeClassName('disable-on-payment');
                    link.addClassName('disabled-on-payment');
                    link.removeAttribute('href');
                    link.removeAttribute('onclick');
                });
                //changer l'url du lien modifier mon panier
                cartlink.each(function (link) {
                    link.setAttribute("href","/be2bill/standard/cart");
                });
                $('be2bill_iframe').src = isrc;
                $('be2bill_iframe').show();
                checkout.loadWaiting = 'review';
                checkout.setLoadWaiting(false);
                if (checkout.accordion.currentSection == 'opc-review') {
                    $('checkout-review-submit').hide();
                }
            }
        },1000);
    });
};

Review.prototype.nextStep = function(transport) {
        if (transport && transport.responseText) {
            try{
                response = eval('(' + transport.responseText + ')');
            }
            catch (e) {
                response = {};
            }
            if (response.redirect) {
                this.isSuccess = true;
                location.href = response.redirect;
                //in case of standard be2bill, we need to fire review:success
                //to display in be2bill iframe
                if (response.redirect=='javascript:void(0)') {
                    Event.fire(document.body, 'review:success');
                }
                return;
            }
            if (response.success) {
                this.isSuccess = true;
                window.location=this.successUrl;
            }
            else{
                var msg = response.error_messages;
                if (typeof(msg)=='object') {
                    msg = msg.join("\n");
                }
                if (msg) {
                    alert(msg);
                }
            }

            if (response.update_section) {
                $('checkout-'+response.update_section.name+'-load').update(response.update_section.html);
            }

            if (response.goto_section) {
                checkout.gotoSection(response.goto_section, true);
            }
        }
};


jQuery(function($) {

    /*
     * Tunnel : hauteur dynamique
     */
    if( $("body").hasClass("jetcheckout-onepage-index") ) {
        setInterval(function() {
            jQuery("#checkoutSteps").css("min-height", jQuery(".active .step:visible").outerHeight() + 40); // 40 étant la hauteur des onglets des étapes
            $(window).resize(); // footer fixe
        }, 200);
        jQuery("#checkoutSteps").css("min-height", jQuery(".active .step:visible").outerHeight() + 40); // 40 étant la hauteur des onglets des étapes
    }

    /*
     * Ouverture/fermeture du récapitulatif
     */
    $(".collapsable-trigger").click(function() {
        if( !$(this).hasClass("up") ) {
            $(this).addClass("up").next(".collapsable").show('slow', function() {
                $(this).removeClass("collapsed").removeAttr("style");
            });
        } else {
            $(this).removeClass("up").next(".collapsable").hide('slow', function() {
                $(this).addClass("collapsed").removeAttr("style");
            });
        }
    });

    /*
     * Footer fixe
     */
    var $footer = $(".footer-container");
    var $main = $(".main-container");
    var headerHeight = $(".header-container").height();
    var footerHeight = $footer.outerHeight();
    $(window).resize(function() {
        var mainHeight = $main.height();
        //console.log($(window).height() + " < " + (headerHeight + mainHeight + footerHeight));
        if( $(window).height() < (headerHeight + mainHeight + footerHeight) ) {
            $footer.addClass("static");
        } else {
            $footer.removeClass("static");
        }
    }).resize();


})