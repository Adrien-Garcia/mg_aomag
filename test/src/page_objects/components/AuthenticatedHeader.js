"use strict";

let AuthenticatedHeader = {
	locators: {
		/*** Header ***/
		// expand account drop-down menu to display the following elements (authenticated user specific):
		header_logout_link: '.sel-header-logout-link',
	},
	helpers: {
		genericFunc: function() {
			console.log('Authenticated header helper')
		},
		privateFunc: function() {
			return console.log('PRIVATE');
		}
	}
};

AuthenticatedHeader.locators = Object.assign(
	{},
	JSON.parse(JSON.stringify(require('./AccountDropdownMenuHeader').locators)),
	AuthenticatedHeader.locators
);

AuthenticatedHeader.helpers = Object.assign(
	{},
	JSON.parse(JSON.stringify(require('./AccountDropdownMenuHeader').helpers)),
	AuthenticatedHeader.helpers
);

module.exports = AuthenticatedHeader;
