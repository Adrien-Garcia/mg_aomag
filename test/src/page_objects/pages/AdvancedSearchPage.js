"use strict";
/******************************* INDEX ****************************************/
const pageData = {
    path: '/catalogsearch/advanced/',
    title: 'Recherche avancée',
    components: {
        // Default
	},
    locators: {
        main_form: '.sel-parameters-form_advancedsearch',
        name_input: '.sel-name-input_advancedsearch',
        description_input: '.sel-description-input_advancedsearch',
        sku_input: '.sel-sku-input_advancedsearch',
        price_from_input: '.sel-price-from-input_advancedsearch',
        price_to_input: '.sel-price-to-input_advancedsearch',
        color_select: 'select#color',
        gender_select: 'select#gender',
        manufacturer_select: 'select#computer_manufacturers',
        submit_button: '.sel-submit-button-advancedsearch'
    },
    data: {
        form_key_sets: {
            validSearchDetails: {
                name: 'produit',
                description: 'Vol en parapente',
                sku: 'bapteme-parapente-parapente-col',
                price_from: '0',
                price_to: '100'
            }
        }
    }
};



module.exports.index = Object.assign({},
    {
        AdvancedSearchPage: {
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
/**Creates an instance of AdvancedSearchPage.
 * @constructor
 * @augments WebPage
 * @param webdriver {WebDriver} The Selenium webdriver currently running.
 * @param isAuthenticated {boolean} True iff user is authenticated on this page.
 */
function AdvancedSearchPage (webdriver, isAuthenticated) {
    // WebPage abstract constructor
    WebPage.call(this, webdriver, isAuthenticated);
    // Basic information for AdvancedSearchPage
    Object.assign(this, JSON.parse(JSON.stringify(pageData)));
}
/************************** PROTOTYPE CHAIN ***********************************/
// Prototype linkage with abstract WebPage
AdvancedSearchPage.prototype = Object.create(WebPage.prototype);
// Referencing the correct constructor
AdvancedSearchPage.prototype.constructor = AdvancedSearchPage;
/************************* SPECIFIC METHODS ***********************************/
/**Clears all forms' text inputs.
 * @return {Thenable<undefined>}
 */
AdvancedSearchPage.prototype.clearCredentials = function(details) {
    let that = this;
    return this.name_input.clear()
    .then(function() {
        return that.description_input.clear();
    }).then(function() {
        return that.sku_input.clear();
    }).then(function() {
        return that.price_from_input.clear();
    }).then(function() {
        return that.price_to_input.clear();
    });
};

/**Searches for a product or list of products, from information provided.
 * @return {Thenable<undefined>}
 */
AdvancedSearchPage.prototype.searchFor = function(details) {
    let that = this;
    return this
    .clearCredentials()
    .then(function() {
        if(details.name)
            that.name_input.sendKeys(details.name);
        if(details.description)
            that.description_input.sendKeys(details.description);
        if(details.sku)
            that.sku_input.sendKeys(details.sku);
        if(details.price_from)
            that.price_from_input.sendKeys(details.price_from);
        if(details.price_to)
            that.price_to_input.sendKeys(details.price_to);
        return undefined;
    }).then(function() {
        return that.submit_button.click();
    });
};
/*************************** CLASS EXPORT *************************************/
// /** @exports AdvancedSearchPage */
module.exports.class = AdvancedSearchPage;
