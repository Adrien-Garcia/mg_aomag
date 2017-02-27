"use strict";
/******************************* INDEX ****************************************/
const pageData = {
    path: '/customer/account/logoutSuccess/',
    title: 'Magento Commerce',
    components: {
        // Default
	},
    locators: {
      // Empty
    }
};


// NOTE: exports nativly integrate the functionality
module.exports.index = Object.assign({},
    {
        LoggedOutPage: {
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
const debug = 0;
/**
 * Creates an instance of LoggedOutPage.
 * @constructor
 * @requires {WebPage}
 * @param webdriver {WebDriver} The Selenium webdriver currently running.
 */
function LoggedOutPage (webdriver) {
    // Webpage abstract constructor
    WebPage.call(this, webdriver, false);
	// Basic information for HomePage
	Object.assign(this, JSON.parse(JSON.stringify(pageData)));
	if(debug)
    	console.log(util.inspect(this, true, 0, true));
} // <== End of HomePage constructor

// Prototype linkage
LoggedOutPage.prototype = Object.create(WebPage.prototype);
LoggedOutPage.prototype.constructor = LoggedOutPage;

// /** @module ~/src/page_objects/pages/LoggedOutPage */
module.exports.class = LoggedOutPage;
