"use strict";
/******************************* INDEX ****************************************/
const pageData = {
    path: '/contenu',
    title: 'Contenu',
    components: {
        // Default
	},
    locators: {
        // Empty
    },
    data: {
        // Empty
    }
};



module.exports.index = Object.assign({},
    {
        InfoPage: {
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
/**Creates an instance of InfoPage.
 * @constructor
 * @param webdriver {WebDriver} The Selenium webdriver currently running.
 * @param isAuthenticated {boolean} True iff user is authenticated on this page.
 */
function InfoPage (webdriver, isAuthenticated) {
	if (!(this instanceof InfoPage))
    	throw new SyntaxError(
            "InfoPage constructor needs to be called with the 'new' keyword."
        );
    // WebPage abstract constructor
    WebPage.call(this, webdriver, isAuthenticated);
    // Basic information for InfoPage
    Object.assign(this, JSON.parse(JSON.stringify(pageData)));
}
/************************** PROTOTYPE CHAIN ***********************************/
// Prototype linkage with abstract WebPage
InfoPage.prototype = Object.create(WebPage.prototype);
// Referencing the correct constructor
InfoPage.prototype.constructor = InfoPage;
/************************* SPECIFIC METHODS ***********************************/
InfoPage.prototype.myFunc = function() {
	if(debug)
        console.log('This is my prototype\'s function!');
	return this; // 'this' corresponds to the instance calling 'myFunc'
};
/*************************** CLASS EXPORT *************************************/
module.exports.class = InfoPage;
