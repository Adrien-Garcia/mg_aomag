"use strict";

let PublicHeader = {
	locators: {
		/*** Header ***/
		// expand account drop-down menu to display the following elements (public user specific):
		header_register_link: 'a[title="Register"]',
		header_login_link: '.sel-header-login-link',

		/*** Footer ***/
		sales_form_link: 'a[title="Commandes et retours"]'
	},
	helpers: {
		genericFunc: function() {
			console.log('Public header helper')
		},
		publicFunc: function() {
			return console.log('PUBLIC');
		}
	}
};

PublicHeader.locators = Object.assign(
	{},
	JSON.parse(JSON.stringify(require('./AccountDropdownMenuHeader').locators)),
	PublicHeader.locators
);

PublicHeader.helpers = Object.assign(
	{},
	JSON.parse(JSON.stringify(require('./AccountDropdownMenuHeader').helpers)),
	PublicHeader.helpers
);

module.exports = PublicHeader;
