"use strict";
/******************************* INDEX ****************************************/
const pageData = {
    path: '/checkout/onepage/',
    title: 'Commander',
    components: {
        // Default
	},
    locators: {
        prefix_select: 'select[id="billing:prefix"]',
        firstname_field: 'input[id="billing:firstname"]',
        lastname_field: 'input[id="billing:lastname"]',
        street_field: 'input[id="billing:street1"]',
        city_field: 'input[id="billing:city"]',
        postcode_field: 'input[id="billing:postcode"]',
        country_select: 'select[id="billing:country_id"]',
        postcode_field: 'input[id="billing:telephone"]'
    },
    data: {
        // Custom data (credentials, expected messages, etc.)
        //
        // or
        //
        // Empty
    }
};



module.exports.index = Object.assign({},
    {
        CheckoutPage: {
            path: pageData.path,
            title: pageData.title
            // Page's URL is defined elsewhere from host's base URL (page_context.js)
        }
    }
);
/************************* IMPORTS AND SETUP **********************************/
// Standard modules
// NOTE: No assertion libraries should be found down below!
const By = require('selenium-webdriver').By,
    until = require('selenium-webdriver').until;
const util = require('util');
// Project modules
// NOTE: Keep in mind the project's architecture to avoid fatal interdependence
const WebPage = require('./WebPage'); // abstract model for a web page
// Other variables
const debug = 0;
/*************************** CONSTRUCTOR **************************************/
/**Creates an instance of CheckoutPage.
 * @constructor
 * @param webdriver {WebDriver} The Selenium webdriver currently running.
 * @param isAuthenticated {boolean} True iff user is authenticated on this page.
 */
function CheckoutPage (webdriver, isAuthenticated) {
	if (!(this instanceof CheckoutPage))
    	throw new SyntaxError(
            "CheckoutPage constructor needs to be called with the 'new' keyword."
        );
    // WebPage abstract constructor
    WebPage.call(this, webdriver, isAuthenticated);
    // Basic information for CheckoutPage
    Object.assign(this, JSON.parse(JSON.stringify(pageData)));
}
/************************** PROTOTYPE CHAIN ***********************************/
// Prototype linkage with abstract WebPage
CheckoutPage.prototype = Object.create(WebPage.prototype);
// Referencing the correct constructor
CheckoutPage.prototype.constructor = CheckoutPage;
/************************* SPECIFIC METHODS ***********************************/
CheckoutPage.prototype.myFunc = function() {
	if(debug)
        console.log('This is my prototype\'s function!');
	return this; // 'this' corresponds to the instance calling 'myFunc'
};
/*************************** CLASS EXPORT *************************************/
module.exports.class = CheckoutPage;
