/**
 * J2T-DESIGN.
 *
 * @category   J2t
 * @package    J2t_Ajaxcheckout
 * @copyright  Copyright (c) 2003-2009 J2T DESIGN. (http://www.j2t-design.com)
 * @license    GPL
 */
/*
var loadingW = 260;
var loadingH = 50;
var confirmW = 260;
var confirmH = 134;
*/
var inCart = false;

if (window.location.toString().search('/product_compare/') != -1){
	var win = window.opener;
}
else{
	var win = window;
}

if (window.location.toString().search('/checkout/cart/') != -1){
    inCart = true;
}


function setLocation(url){
    if(!inCart && (/*(url.search('/add') != -1 ) || (url.search('/remove') != -1 ) ||*/ url.search('checkout/cart/add') != -1) ){
        sendcart(url, 'url');
    }else{
        window.location.href = url;
    }
}


function sendcart(url, type){
    showLoading();
    if (type == 'form'){
        url = ($('product_addtocart_form').action).replace('checkout', 'j2tajaxcheckout/index/cart');
        //url = ($('product_addtocart_form').action);
        var myAjax = new Ajax.Request(
        url,
        {
            method: 'post',
            postBody: $('product_addtocart_form').serialize(),
            parameters : Form.serialize("product_addtocart_form"),
            onException: function (xhr, e)
            {
                alert('Exception : ' + e);
            },
            onComplete: function (xhr)
            {   
                //alert('success');
                $('j2t-temp-div').innerHTML = xhr.responseText;
                var return_message = $('j2t-temp-div').down('.j2t_ajax_message').innerHTML;
                
                var middle_text = '<div class="j2t-cart-bts">'+$('j2t-temp-div').down('.back-ajax-add').innerHTML+'</div>';

                $('j2t_ajax_confirm').innerHTML = '<div id="j2t_ajax_confirm_wrapper">'+return_message + middle_text + '</div>';

                var link_cart_txt = $('j2t-temp-div').down('.cart_content').innerHTML;

                $$('.top-link-cart').each(function (el){
                    el.innerHTML = link_cart_txt;
                    el.title = link_cart_txt;
                });

                

                var mini_cart_txt = $('j2t-temp-div').down('.cart_side_ajax').innerHTML;
                $$('.mini-cart').each(function (el){
                    el.replace(mini_cart_txt);
                    //new Effect.Opacity(el, { from: 0, to: 1, duration: 1.5 });
                });
                $$('.block-cart').each(function (el){
                    el.replace(mini_cart_txt);
                    //new Effect.Opacity(el, { from: 0, to: 1, duration: 1.5 });
                });
                
                replaceDelUrls();
                if (ajax_cart_show_popup){
                    showConfirm();
                } else {
                    hideJ2tOverlay();
                }

            var txt = $$('.j2tajax-checkout-txt').each(function (el){el.title=el.down('span:first').innerHTML;});

            }

        });



    } else if (type == 'url'){

        url = url.replace('checkout', 'j2tajaxcheckout/index/cart');
        //alert(url);
        var myAjax = new Ajax.Request(
        url,
        {
            method: 'post',
            postBody: '',
            onException: function (xhr, e)
            {
                alert('Exception : ' + e);
            },
            onComplete: function (xhr)
            {
                $('j2t-temp-div').innerHTML = xhr.responseText;
                var return_message = $('j2t-temp-div').down('.j2t_ajax_message').innerHTML;
                var middle_text = '<div class="j2t-cart-bts">'+$('j2t-temp-div').down('.back-ajax-add').innerHTML+'</div>';


                var content_ajax = return_message + middle_text;
                
                $('j2t_ajax_confirm').innerHTML = '<div id="j2t_ajax_confirm_wrapper">'+content_ajax + '</div>';

                var link_cart_txt = $('j2t-temp-div').down('.cart_content').innerHTML;

                $$('.top-link-cart').each(function (el){
                    el.innerHTML = link_cart_txt;
                    el.title = link_cart_txt;
                });

                


                var mini_cart_txt = $('j2t-temp-div').down('.cart_side_ajax').innerHTML;

                //alert(mini_cart_txt);

                $$('.mini-cart').each(function (el){
                    el.replace(mini_cart_txt);
                    //new Effect.Opacity(el, { from: 0, to: 1, duration: 1.5 });
                });

                $$('.block-cart').each(function (el){
                    el.replace(mini_cart_txt);
                    //new Effect.Opacity(el, { from: 0, to: 1, duration: 1.5 });
                });


                replaceDelUrls();
                if (ajax_cart_show_popup){
                    showConfirm();
                } else {
                    hideJ2tOverlay();
                }
            }

        });

    }

}

function replaceDelUrls(){
    //if (!inCart){
        $$('a').each(function(el){
            if(el.up('.nav-container')!=undefined){
                if(el.href.search('checkout/cart/delete') != -1 && el.href.search('javascript:cartdelete') == -1){
                    el.href = 'javascript:cartdelete(\'' + el.href +'\',true)';
                }
            }else{
                if(el.href.search('checkout/cart/delete') != -1 && el.href.search('javascript:cartdelete') == -1){
                    el.href = 'javascript:cartdelete(\'' + el.href +'\')';
                }
            }
        });
    //}
}

function replaceAddUrls(){
    $$('a').each(function(link){
        if(link.href.search('checkout/cart/add') != -1){
            link.href = 'javascript:setLocation(\''+link.href+'\'); void(0);';
        }
    });
}

function cartdelete(url, mini_cart){
    if(typeof(mini_cart)==='undefined') mini_cart = false;
    showLoading();
    url = url.replace('checkout', 'j2tajaxcheckout/index/cartdelete');
    var myAjax = new Ajax.Request(
    url,
    {
        method: 'post',
        postBody: '',
        onException: function (xhr, e)
        {
            alert('Exception : ' + e);
        },
        onComplete: function (xhr)
        {
            $('j2t-temp-div').innerHTML = xhr.responseText;
            //$('j2t-temp-div').insert(xhr.responseText);

            var cart_content = $('j2t-temp-div').down('.cart_content').innerHTML;

            //alert(cart_content);

            $$('.top-link-cart').each(function (el){
                el.innerHTML = cart_content;
                el.title = cart_content;
            });

            
            if(mini_cart){
                location.reload();
                return;
            }
            var process_reload_cart = false;
            var full_cart_content = $('j2t-temp-div').down('.j2t_full_cart_content').innerHTML;
            var full_cart_h1 = $$('.j2t_full_cart_content ')[0].remove('.page-title').innerHTML;//$('j2t-temp-div').down('.j2t_full_cart_content .page-title').innerHTML;
            ;
            $$('.page-title').each(function (el){
                el.innerHTML = full_cart_h1;
                process_reload_cart = true;
            });
            $$('.cart').each(function (el){
                el.replace(full_cart_content);
                process_reload_cart = true;
            });

            if (!process_reload_cart){
                $$('.checkout-cart-index .col-main').each(function (el){
                    el.replace(full_cart_content);
                    //new Effect.Opacity(el, { from: 0, to: 1, duration: 1.5 });
                });
            }




            var cart_side = '';
            if ($('j2t-temp-div').down('.cart_side_ajax')){
                cart_side = $('j2t-temp-div').down('.cart_side_ajax').innerHTML;
            }

            
            $$('.mini-cart').each(function (el){
                el.replace(cart_side);
                //new Effect.Opacity(el, { from: 0, to: 1, duration: 1.5 });
            });
            $$('.block-cart').each(function (el){
                el.replace(cart_side);
                //new Effect.Opacity(el, { from: 0, to: 1, duration: 1.5 });
            });

            

            replaceDelUrls();

            //$('j2t_ajax_progress').hide();
            hideJ2tOverlay();
        }

    });


}

function showJ2tOverlay(){
    //new Effect.Appear($('j2t-overlay'), { duration: 0.5,  to: 0.8 });
	jQuery("#j2t-overlay").fadeTo("fast", 0.8);
}

function hideJ2tOverlay(){
    $('j2t-overlay').hide();
    $('j2t_ajax_progress').hide();
    $('j2t_ajax_confirm').hide();
}


function j2tCenterWindow(element) {
     if($(element) != null) {

          // retrieve required dimensions
            var el = $(element);
            var elDims = el.getDimensions();
            var browserName=navigator.appName;
            if(browserName==="Microsoft Internet Explorer") {

                if(document.documentElement.clientWidth==0) {
                    //IE8 Quirks
                    //alert('In Quirks Mode!');
                    var y=(document.viewport.getScrollOffsets().top + (document.body.clientHeight - elDims.height) / 2);
                    var x=(document.viewport.getScrollOffsets().left + (document.body.clientWidth - elDims.width) / 2);
                }
                else {
                    var y=(document.viewport.getScrollOffsets().top + (document.documentElement.clientHeight - elDims.height) / 2);
                    var x=(document.viewport.getScrollOffsets().left + (document.documentElement.clientWidth - elDims.width) / 2);
                }
            }
            else {
                // calculate the center of the page using the browser andelement dimensions
                var y = Math.round(document.viewport.getScrollOffsets().top + ((window.innerHeight - $(element).getHeight()))/2);
                var x = Math.round(document.viewport.getScrollOffsets().left + ((window.innerWidth - $(element).getWidth()))/2);
            }
            // set the style of the element so it is centered
            var styles = {
                position: 'absolute',
                // top: y + 'px',
                // left : x + 'px'
            };
            el.setStyle(styles);




     }
}


function showLoading(){
    showJ2tOverlay();
    var progress_box = $('j2t_ajax_progress');
    progress_box.show();
    progress_box.style.width = loadingW + 'px';
    progress_box.style.height = loadingH + 'px';

    
    $('j2t_ajax_progress').innerHTML = $('j2t-loading-data').innerHTML;
    progress_box.style.position = 'absolute';

    j2tCenterWindow(progress_box);
}


function showConfirm(){
    showJ2tOverlay();
    $('j2t_ajax_progress').hide();
    var confirm_box = $('j2t_ajax_confirm');
    confirm_box.show();
    confirm_box.style.width = confirmW + 'px';
    confirm_box.style.height = confirmH + 'px';
    //j2t_ajax_confirm_wrapper
    if ($('j2t_ajax_confirm_wrapper') && $('j2t-upsell-product-table')){
        //alert($('j2t_ajax_confirm_wrapper').getHeight());
        confirm_box.style.height = $('j2t_ajax_confirm_wrapper').getHeight() + 'px';
        decorateTable('j2t-upsell-product-table');
    }

    $('j2t_ajax_confirm_wrapper').replace('<div class="j2t_ajax_close" id="j2t_ajax_close">&nbsp;</div><div id="j2t_ajax_confirm_wrapper">'+$('j2t_ajax_confirm_wrapper').innerHTML);
    $$('.j2t-cart-bts')[0].insert({bottom :'<div id="j2t-cart-bts-continue" class="j2t-cart-bts-continue primary-button inv"><p>Continuer mes achats</p></div>' });
    confirm_box.style.position = 'absolute';
    j2tCenterWindow(confirm_box);
}

document.observe("dom:loaded", function() {
    replaceDelUrls();
    replaceAddUrls();
    //Event.observe($('j2t-overlay'), 'click', hideJ2tOverlay);

    var cartInt = setInterval(function(){
        if (typeof productAddToCartForm  != 'undefined'){
            if ($('j2t-overlay')){
                Event.observe($('j2t-overlay'), 'click', hideJ2tOverlay);
            }
            productAddToCartForm.submit = function(url){
                if(this.validator && this.validator.validate()){
                    sendcart('', 'form');
                    clearInterval(cartInt);
                }

                return false;
            }
        } else {
            clearInterval(cartInt);
        }
    },500);
});
