"use strict";
/******************************* INDEX ****************************************/
const pageData = {
    path: '/wishlist/index/index/',
    title: 'Ma liste d\'envies',
    components: {
        AccountSideView: true
    },
    locators: {
        title_h1: '.page-title > h1',
        // tabs_links: '.sidebar .block-account .block-content > ul > li > a',
        back_link_dashboard: 'p.back-link > a'
    },
    // components: [
    //     'UniversalElements'
    // ],
    data: {
        locators_status: {
            empty: {
                empty_message: 'p.wishlist-empty'
            },
            default: {
                product_rows: 'table.clean-table.linearize-table tbody > tr',
                product_images: 'table.clean-table.linearize-table tbody > tr a.product-image',
                product_page_links: 'table.clean-table.linearize-table tbody > tr .product-name a',
                // product_skus: 'table.clean-table.linearize-table tbody > tr',
                // product_prices: 'table.clean-table.linearize-table tbody > tr',
                qty_inputs: 'table.clean-table.linearize-table tbody > tr input.input-text.qty',
                add_cart_buttons: 'table.clean-table.linearize-table tbody > tr button.btn-cart',
                // update_buttons: 'table.clean-table.linearize-table tbody > tr button.link-edit.button-secondary',
                remove_item_links: 'table.clean-table.linearize-table tbody > tr a.btn-remove.btn-remove2', // 0..n
            }
        }
    }
};



module.exports.index = Object.assign({},
    {
        WishlistPage: {
            path: pageData.path,
            title: pageData.title
            // URL is defined elsewhere from host's base URL (page_context.js)
        }
    }
);
/************************* IMPORTS AND SETUP **********************************/
// Standard modules
// NOTE No assertion libraries should be found down below!
const By = require('selenium-webdriver').By,
    until = require('selenium-webdriver').until;
const util = require('util');
// Project modules
// NOTE Keep in mind the project's architecture to avoid fatal interdependence
const WebPage = require('./WebPage'); // abstract model for a web page
// Other variables
const debug = 0;
/*************************** CONSTRUCTOR **************************************/
/**Creates an instance of WishlistPage.
 * @constructor
 * @param webdriver {WebDriver} The Selenium webdriver currently running.
 * @param isAuthenticated {boolean} True iff user is authenticated on this page.
 */
function WishlistPage (webdriver) {
    // WebPage abstract constructor
    WebPage.call(this, webdriver, true);
    // Basic information for WishlistPage
    Object.assign(this, JSON.parse(JSON.stringify(pageData)));
}
/************************** PROTOTYPE CHAIN ***********************************/
// Prototype linkage with abstract WebPage
WishlistPage.prototype = Object.create(WebPage.prototype);
// Referencing the correct constructor
WishlistPage.prototype.constructor = WishlistPage;
/************************* SPECIFIC METHODS ***********************************/
WishlistPage.prototype.clickBackLink = function() {
	return this.back_link_dashboard;
};
/*************************** CLASS EXPORT *************************************/
module.exports.class = WishlistPage;
