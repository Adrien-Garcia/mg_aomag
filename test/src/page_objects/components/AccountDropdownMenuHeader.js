"use strict";

let AccountDropdownMenuHeader = module.exports = {
	locators: {
		// Main header containing nav and general links wrappers
		// (expand account drop-down menu to display the following elements)
		header_account_link: '#header-account a[title="Mon compte"]',
		header_wishlist_link: '#header-account a[href="' + require('../../page_context').page_index.host + '/wishlist/"]',
		header_cart_link: '.sel-header-cart-link',
		header_checkout_link: '.sel-header-checkout-link'
	},
	helpers: {
		// None
	}
};
