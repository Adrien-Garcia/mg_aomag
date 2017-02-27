"use strict";
/******************************* INDEX ****************************************/
const pageData = {
    path: '/sales/guest/form/',
    title: '',
    components: {
        // Default
	},
    locators: {
        // function_and_type: 'css.selector'
        //
        // or
        //
        // Empty
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
        SalesFormPage: {
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
// Custom errors/exceptions libraries
const CustomExceptions = require('../../custom_exceptions');
const CustomError = CustomExceptions.CustomError;
const PageObjectError = CustomExceptions.PageObjectError;
const PageObjectException = CustomExceptions.PageObjectException;
const ScriptExecutionError = CustomExceptions.ScriptExecutionError;
const WebElementNotFoundException = CustomExceptions.WebElementNotFoundException;
// Other variables
const debug = 0;
/*************************** CONSTRUCTOR **************************************/
/**Creates an instance of SalesFormPage.
 * @constructor
 * @param webdriver {WebDriver} The Selenium webdriver currently running.
 * @param isAuthenticated {boolean} True iff user is authenticated on this page.
 */
function SalesFormPage (webdriver) {
    // WebPage abstract constructor
    WebPage.call(this, webdriver, false);
    // Basic information for SalesFormPage
    Object.assign(this, JSON.parse(JSON.stringify(pageData)));
}
/************************** PROTOTYPE CHAIN ***********************************/
// Prototype linkage with abstract WebPage
SalesFormPage.prototype = Object.create(WebPage.prototype);
// Referencing the correct constructor
SalesFormPage.prototype.constructor = SalesFormPage;
/************************* SPECIFIC METHODS ***********************************/
SalesFormPage.prototype.myFunc = function() {
	let that = this; // Saving current execution context
	if(debug)
        console.log('This is my prototype\'s function!');
	return this; // 'this' corresponds to the instance calling 'myFunc'
};
/*************************** CLASS EXPORT *************************************/
module.exports.class = SalesFormPage;
