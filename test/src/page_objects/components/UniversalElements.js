"use strict";

let UniversalElements = module.exports = {
	locators: {
		/*** Pre-header & Header ***/
		// /!\ out of the DOM header /!\ --> pre-header
		preheader_language_select: '.sel-preheader-language-select',
		preheader_welcome_message: '.sel-welcome-message',

		// Account drop-down menu ...
		header_account_dropdown: '.sel-header-account-dropdown',
		// ...containing the following elements :
        header_cart_menu_link: '.sel-header-cart-menu-link',
		header_logo_link: '.sel-header-logo-link',

		// Navigation menu
		header_nav: 'nav#nav',
		header_nav_level0_links: 'nav#nav li.level0',
		header_nav_level1_links: 'nav#nav li.level1',
		header_nav_level2_links: 'nav#nav li.level2',

		// Search bar
		header_search_input: '.sel-header-search-input',
		header_search_button: '.sel-header-search-button',

		/*** Footer ***/
		// Quick links & Account
		footer_site_mapping_link: '.footer a[title="Plan du site"]',
		footer_advanced_search_link: '.footer a[title="Recherche avancée"]',
		footer_account_link: '.footer a[title="Mon compte"]',

		// Newsletter subscription
		footer_newsletter_input: '.footer #newsletter-validate-detail input#newsletter',
		footer_newsletter_button: '.footer #newsletter-validate-detail button[type="submit"]'
	},
	helpers: {
		genericFunc: function() {
			console.log('I am executing a generic function!')
		},
		goBackHomepage: function() {
			return this.header_logo_link.click();
		},
        setLanguage: function(lang) {
            return this.preheader_language_select;
            // TODO
        },
        /*** Account dropdown ***/
        openAccountDropdown: function() {
            // TODO
        },
        closeAccountDropdown: function() {
            // TODO
        },
        Account_goCart: function() {
            return this.header_cart_link.click();
        },
        Account_goDashboard: function() {
            return this.header_account_link.click(); // NOTE element into AccountDropdownMenuHeader.js
        },
        Account_goWishlist: function() {
            return this.header_wishlist_link.click(); // NOTE element into AccountDropdownMenuHeader.js
        },
        /*** Cart overview ***/
        openCartOverview: function() {
            return this.header_cart_overview_link.click(); // TODO
        },
        closeCartOverview: function() {
            return this.header_cart_overview_link.click(); // TODO
        },
        cartOverview_goCheckout: function() {
            return this //TODO
        },
        cartOverview_goCart: function() {
            return this // TODO
        },
        cartOverview_goProduct: function(skuOrIndex) {
            return // TODO
        },
        cartOverview_editProductQty: function(skuOrIndex, qty) {
            return // TODO
        },
        cartOverview_removeProduct: function(skuOrIndex) {
            return // TODO
        },
        cartOverview_close: function() {
            return // TODO Close through cross
        },
        /*** ? ***/
        searchFor: function(text) { // headerResearch() ?
            return this // TODO
        },
        /*** Footer ***/
        footer_goDashboard: function() {
            return this.footer_account_link.click();
        },
        footer_goAdvancedSearch: function() {
            return this.footer_advanced_search_link.click();
        },
        footer_goSiteMapping: function() {
            return this.footer_site_mapping_link.click();
        },
        footer_subscribeNewsletter: function(email) {
            return this // TODO
        }
	}
};
