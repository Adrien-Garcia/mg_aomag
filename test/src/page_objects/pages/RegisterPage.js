"use strict";
/******************************* INDEX ****************************************/
const pageData = {
    path: '/customer/account/create/',
    title: 'Créer un nouveau compte client',
    components: {
        // Default
	},
    locators: {
        main_form: 'form#form-validate',
        prefix_select: 'select#prefix',
        firstname_field: 'input#firstname',
        lastname_field: 'input#lastname',
        email_field: 'input#email_address',
        password_field: 'input#password',
        password_confirmation_field: 'input#confirmation',
        newsletter_checkbox: 'input#is_subscribed',
        remember_me_checkbox: 'input[id^="remember_me"]',
        submit: 'form#form-validate button[type="submit"]',

        back_link_login: 'a.back-link'
    },
    data: {
        credentials: {
            valid: {
                prefix_select: 'M.',
                firstname_field: 'José',
                // middlename_field: 'de la',
                lastname_field: 'Puerta del Sol',
                email_field: 'jose@puertadelsol.es',
                password_field: 'soldelapuerta',
                password_confirmation_field: 'soldelapuerta',
                newsletter_checkbox: '1',
                remember_me_checkbox: '1',
            }
        }
    }
};


// NOTE: exports nativly integrate the functionality
module.exports.index = Object.assign({},
    {
        RegisterPage: {
            path: pageData.path,
            title: pageData.title
        }
    }
);
/******************************************************************************/
// Standard modules
const By = require('selenium-webdriver').By,
        until = require('selenium-webdriver').until;
const util = require('util');
// Project modules
const WebPage = require('./WebPage');
const debug = 0;
/**
 * Creates an instance of RegisterPage.
 * @constructor
 * @param webdriver {WebDriver} The Selenium webdriver currently running.
 */
function RegisterPage (webdriver) {
    // WebPage abstract constructor
    WebPage.call(this, webdriver, false);
	// Assignating some predefined information about the page
    Object.assign(this, JSON.parse(JSON.stringify(pageData)));
}

// Prototype linkage
RegisterPage.prototype = Object.create(require('./WebPage').prototype);
RegisterPage.prototype.constructor = RegisterPage;
/**
 * Clears all the text fields of the registering page.
 * @returns {Thenable<undefined>}
 */
RegisterPage.prototype.clearCredentials = function() {
  let p = [];
  p.push(this.firstname_field.clear());
  // p.push(this.middlename_field.clear());
  p.push(this.lastname_field.clear());
  p.push(this.email_field.clear());
  p.push(this.password_field.clear());
  p.push(this.password_confirmation_field.clear());
  return Promise.all(p)
  .then(function() {
      return Promise.resolve();
  }, function() {
      return Promise.reject('RegisterPage::clearCredentials; Something gone wrong.');
  });
};
/**
 * Fills the registering form with predefined data, and then submits the form.
 * @returns {Thenable<undefined>}
 */
RegisterPage.prototype.register = function() {
  let that = this;
  let validCred = this.data.credentials.valid;
  return this.clearCredentials()
  .then(function() {
    // properties' name into validCred must equals the name of each field to fill
    // e.g.: this.someField.sendKeys(validCred.someField);
    for(let field in validCred) {
      that[field].sendKeys(validCred[field]);
    }
    return that.submit.click();
  });
};
/**
 * Strange method.
 * @returns {Thenable<undefined>}
 */
RegisterPage.prototype.sampleRegistration = function() {
    if(debug)
        console.log('RegisterPage::sampleRegistration: starting');
    let that = this;
    this.getRandomOption(this.prefix_select)
    .then(function(validOption) {
        return that.setOptionByValue(that.prefix_select, validOption);
    });
    this.firstname_field.sendKeys('Trolololol');
    // this.middlename_field.sendKeys('dazedefzef');
    this.lastname_field.sendKeys('aaaaaaaaaaaaaa');
    this.email_field.sendKeys('fvrgth');
    this.password_field.sendKeys('bbbbbbbbbbbbbb');
    this.password_confirmation_field.sendKeys('bbbbbbbbbbbbbb');
    return this.register();
    if(debug)
        console.log('RegisterPage::sampleRegistration: end');
};
// /** @module ~/src/page_objects/pages/RegisterPage */
module.exports.class = RegisterPage;
