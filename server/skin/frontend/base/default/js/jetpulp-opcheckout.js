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

    var load = new Ajax.Request(
        this.loadUrl,
        {
            method: 'post',
            onSuccess: this.onShippingTypesLoad,
            onFailure: checkout.ajaxFailure.bind(checkout)
        }
    );
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

Shipping.prototype.saveMinimal= function(form) {
    if (checkout.loadWaiting!=false) return;
    request = false;
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
                },
                onFailure: checkout.ajaxFailure.bind(checkout),
                parameters: Form.serialize(this.form)
            }
        );
    }
    checkout.reloadReviewBlock();
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

Shipping.prototype.resetLoadWaiting= function(transport){
    checkout.setLoadWaiting(false);
    $('checkout-shipping-method-load').removeClassName("method-refreshing");

};