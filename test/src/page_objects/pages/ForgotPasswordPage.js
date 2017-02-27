"use strict";
/******************************* INDEX ****************************************/
const pageData = {
    path: '/customer/account/forgotpassword/',
    title: 'Mot de passe oublié',
    components: {
        // Default
	},
    locators: {
        main_form: 'form#form-validate',
        email_input: 'input#email_address',
        submit: 'form#form-validate button[type="submit"]',
        back_login: '.back-link a'
    },
    data: {
        valid_email: 'adrien.garcia@jetpulp.fr',
        invalid_email: 'a@b.cd'
    }
};



module.exports.index = Object.assign({},
    {
        ForgotPasswordPage: {
            path: pageData.path,
            title: pageData.title
            // Page's URL is defined elsewhere from host's base URL (page_context.js)
        }
    }
);
/************************* IMPORTS AND SETUP **********************************/
// Standard modules
// NOTE: NO ASSERTION LIBRARY HERE!
const By = require('selenium-webdriver').By,
        until = require('selenium-webdriver').until;
const util = require('util');
// Project modules
// NOTE: Keep in mind the project's architecture to avoid fatal interdependence
const WebPage = require('./WebPage');
// Other variables
const debug = 0;
/*************************** CONSTRUCTOR **************************************/
/**Creates an instance of ForgotPasswordPage.
 * @constructor
 * @param webdriver {WebDriver} The Selenium webdriver currently running.
 * @param isAuthenticated {boolean} True iff user is authenticated on this page.
 */
function ForgotPasswordPage (webdriver) {
    // Webpage abstract constructor
    WebPage.call(this, webdriver, false);
    // Basic information for ForgotPasswordPage
    Object.assign(this, JSON.parse(JSON.stringify(pageData)));
}
/************************** PROTOTYPE CHAIN ***********************************/
// Prototype linkage with abstract WebPage
ForgotPasswordPage.prototype = Object.create(WebPage.prototype);
// Referencing the correct constructor
ForgotPasswordPage.prototype.constructor = ForgotPasswordPage;
/************************* SPECIFIC METHODS ***********************************/
/**Abc
 * @returns {Thenable<undefined>}
 */
ForgotPasswordPage.prototype.clearCredentials = function() {
	if(debug)
        console.log('ForgotPasswordPage::clearCredentials: start');
	return this.email_input.clear();
};
/**Abc
 * @returns {Thenable<undefined>}
 */
ForgotPasswordPage.prototype.requestPassword = function() {
	if(debug)
        console.log('ForgotPasswordPage::requestPassword: start');
    let that = this;
	return this
    .clearCredentials()
    .then(function() {
        return that.email_input.sendKeys(that.data.valid_email);
    }).then(function() {
        return that.submit.click();
    });
};
/*************************** CLASS EXPORT *************************************/
// /** @exports ForgotPasswordPage */
module.exports.class = ForgotPasswordPage;
