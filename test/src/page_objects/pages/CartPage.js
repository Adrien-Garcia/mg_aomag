"use strict";
/******************************* INDEX ****************************************/
const pageData = {
	path: '/checkout/cart/',
	title: 'Shopping Cart',
	components: {
		// Default
	},
	locators: {
		page_title: '.page-title h1'// TODO Delete when added to components
	},
	data: {
		locators_status: {
			empty: {
				// Empty
			},
			default: {
				item_rows: 'table#shopping-cart-table tbody > tr',
				remove_item_links: 'table#shopping-cart-table tbody > tr > td.product-cart-remove a.btn-remove.btn-remove2', // 0..n
				qty_inputs: 'table#shopping-cart-table tbody > tr input.input-text.qty', // 0..n
				update_qty_buttons: 'table#shopping-cart-table tbody > tr button.button.btn-update', // 0..n
				empty_cart_button: '#empty_cart_button'
			}
		}
	}
};


// NOTE: exports nativly integrate the functionality
module.exports.index = Object.assign({},
    {
        CartPage: {
            path: pageData.path,
            title: pageData.title
        }
    }
);
/******************************************************************************/
/***** Imports and setup *****/
// Standard modules
const By = require('selenium-webdriver').By,
        until = require('selenium-webdriver').until;
const util = require('util');
// Project modules
const WebPage = require('./WebPage');
/*****/
const CustomExceptions = require('../../custom_exceptions');
const CustomError = CustomExceptions.CustomError;
const PageObjectError = CustomExceptions.PageObjectError;
const PageObjectException = CustomExceptions.PageObjectException;
const ScriptExecutionError = CustomExceptions.ScriptExecutionError;
const WebElementNotFoundException = CustomExceptions.WebElementNotFoundException;

const debug = 0;

/**
 * Creates an instance of CartPage.
 * @constructor
 * @param webdriver {WebDriver} The Selenium webdriver currently running.
 * @param isAuthenticated {boolean} True iff user is authenticated on this page.
 * @see WebPage
 */
function CartPage (webdriver, isAuthenticated) {
	WebPage.call(this, webdriver, isAuthenticated);
	this.checkLocatorsStatus();
    Object.assign(this, JSON.parse(JSON.stringify(pageData)));
	if(debug)
    	console.log(util.inspect(this, true, 0, true));
} // <== End of CartPage constructor

// Prototype linkage
CartPage.prototype = Object.create(WebPage.prototype);
CartPage.prototype.constructor = CartPage;
/******************************************************************************/

/* Overridding WebPage methods */
CartPage.prototype.checkLocatorsStatus = function () {
    let that = this;
	return this.isCartEmpty()
	.then(function(isEmpty) {
		return that.selectLocatorsStatus(isEmpty ? 'empty' : 'default');
	});
};

/**
 * First checks if user's cart is already empty:
 * - if not, clicks the "Empty cart" button;
 * - after that, or if already empty, returns a promise resolved with undefined value
 * If an error occurs when trying to click the button, the returned promised
 * will be rejected with such corresponding error.
 *
 * The page should refresh and show the cart has successfully been emptied.
 * @returns {Thenable<undefined>}
 */
CartPage.prototype.emptyCart = function() {
    let that = this;
	return that.isCartEmpty()
	.then(function (cartIsEmpty) {
		if(cartIsEmpty == false) {
			if(debug) {
				console.log('CartPage::emptyCart: Cart not empty, removing all products...');
				console.log(util.inspect(that, true, 0, true));
			}
			return that.empty_cart_button.click();
		} else {
			if(debug)
				console.log('CartPage::emptyCart: Cart empty, already done!');
			return Promise.resolve();
		}
    })/*.then(function () {
		return that;
    })*/;
};
/**
 * Indicates if the shopping cart is currently empty.
 * @returns {Thenable<boolean>}
 * @deprecated
 */
 CartPage.prototype.isCartEmptyDeprecated = function() {
    let that = this;
 	return this.driver.findElement(By.css('.page-title h1'))
 	.getText()
 	.then(function(text) {
 		if(debug)
 		  	console.log('CartPage::isCartEmpty: Cart title is: "' + text + '"');
 		let res;
 		switch(text) {
 			case 'PANIER':
 				res = false; break;
 			case 'LE PANIER EST VIDE':
 				res = true; break;
 			default:
 				throw new Error('CartPage::isCartEmpty: Cart title "' + text + '" does not match anything expected');
 				break;
 		}
 		return res;
     });
 };
 /**
  * Indicates if the shopping cart is currently empty by checking for the
  * presence of products table. If not present
  * @returns {Thenable<boolean>}
  */
 CartPage.prototype.isCartEmpty = function() {
 	return this.driver.findElement(By.css('table#shopping-cart-table'))
 	.then(function() {
		return false;
	}, function() {
		return true;
	});
 };
// /** @module ~/src/page_objects/pages/CartPage */
module.exports.class = CartPage;

// To remove soon
// const LoginPage = require('./LoginPage');
