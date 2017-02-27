"use strict";
/******************************* INDEX ****************************************/
const pageData = {
    path: '/catalog/seo_sitemap/category/',
    title: 'Plan du site',
    components: {
        // Default
    },
    locators: {
        page_title: '.page-title h1'
    },
    data: {
        // Empty
    }
};



module.exports.index = Object.assign({},
    {
        SiteMappingPage: {
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
/**Creates an instance of SiteMappingPage.
 * @constructor
 * @param webdriver {WebDriver} The Selenium webdriver currently running.
 * @param isAuthenticated {boolean} True iff user is authenticated on this page.
 */
function SiteMappingPage (webdriver, isAuthenticated) {
    // WebPage abstract constructor
    WebPage.call(this, webdriver, isAuthenticated);
    // Basic information for SiteMappingPage
    Object.assign(this, JSON.parse(JSON.stringify(pageData)));
}
/************************** PROTOTYPE CHAIN ***********************************/
// Prototype linkage with abstract WebPage
SiteMappingPage.prototype = Object.create(WebPage.prototype);
// Referencing the correct constructor
SiteMappingPage.prototype.constructor = SiteMappingPage;
/************************* SPECIFIC METHODS ***********************************/

/*************************** CLASS EXPORT *************************************/
module.exports.class = SiteMappingPage;
