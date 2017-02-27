"use strict";
/******************************* INDEX ****************************************/
const pageData = {
    path: '/customer/account/login/',
    title: 'Identifiant client',
    components: {
        // Default
	},
    locators: {
        email_input: '.sel-email-input_login',
        password_input: '.sel-password-input_login',
        remember_me_checkbox: '.sel-remember-me-checkbox_login',
        remember_me_tip_link: '.sel-remember-me-tip-link_login',
        forgot_password_link: '.sel-forgot-password-link_login',
        submit_button: '.sel-submit-button_login',
        register_link: '.sel-register-link_login'
    },
    data: {
        credentials: {
            default_username: 'jett.poulpe@jetpulp.fr',
            default_password: 'jettlepoulpe',
            valid_username: 'jose@puertadelsol.es',
            valid_password: 'soldelapuerta',
            invalid_username: 'm.poulpe@jetpoulpe.fr',
            invalid_password: 'forgotmypassword',
            bad_format_username: 'how+does=it.even@work?',
            short_password: 'short'
        }
    }
};


// NOTE: exports nativly integrate the functionality
module.exports.index = Object.assign({},
    {
        LoginPage: {
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
// For some reason, the line below does not work, and gets undefined
const debug = 0;

/**
 * Creates an instance of LoginPage.
 * @constructor
 * @param webdriver {WebDriver} The Selenium webdriver currently running.
 */
function LoginPage (webdriver) {
    // Webpage abstract constructor
    WebPage.call(this, webdriver, false);
	Object.assign(this, JSON.parse(JSON.stringify(pageData)));
}

/***** Prototype linkage *****/
LoginPage.prototype = Object.create(WebPage.prototype);
LoginPage.prototype.constructor = LoginPage;
/******************************************************************************/
/******************************************************************************/
/***** LoginPage specific methods *****/
/**
 * Empties email and password inputs.
 */
LoginPage.prototype.clearCredentials = function () {
	let that = this;
    return Promise.resolve()
    .then(function() {
        return that.email_input.clear();
	}).then(function() {
        that.password_input.clear();
    });
};
/**
 * Performs a click onto button to registering page.
 * @type {WebPage}
 * @returns undefinedThenable
 */
LoginPage.prototype.register = function() {
	return this.register_link.click();
	// return (new RegisterPage(this.driver, false)).init();
};
// const DashboardPage = require('./DashboardPage').class;
/**
 * Creates an instance of LoginPage.
 * @param user {string} The username to type in.
 * @param pass {string} The password to type in.
 * @returns Thenable<DashboardPage> | Thenable<LoginPage>
 */
LoginPage.prototype.logUser = function(user, pass, remember) {
	let that = this;
	return this.clearCredentials()
    .then(function() {
        return that.email_input.sendKeys(user);
    }).then(function() {
        return that.password_input.sendKeys(pass);
    }).then(function() {
        return that.remember_me_checkbox.isSelected()
    }).then(function(selected) {
        if( (selected === true && remember === false)
        || (selected === false && remember === true) )
            return that.remember_me_checkbox.click();
        else { // undefined by user
            return;
        }
    }).then(function() {
        return that.submit_button.click();
    }).then(function(isSelect) {
        return that.driver.wait(function() {
            return that
            .getUrl()
            .then(function(newUrl) {
                return newUrl;
            });
        }, 10000, 'Failed to log in after 10s');
    });
};
/**
 * Signs user in, with first predefined valid credentials
 * @returns Thenable< LoginPage >
 */
LoginPage.prototype.connect = function() {
	let user = this.data.credentials.valid_username,
	pass = this.data.credentials.valid_password;
	if(debug)
		console.log('Signing in with credentials ("'+user+'", "'+pass+'").');
	return this.logUser(user, pass)
	.then(function(res) {
		if(debug)
			console.log('LoginPage::connect: logUser returned: ' + res);
		return res;
	}).catch(function(err) {
        return Promise.reject(err);
    });
};

// /** @module ~/src/page_objects/pages/LoginPage */
module.exports.class = LoginPage;
