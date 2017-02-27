"use strict";
/***** Imports and setup *****/
// Standard modules
const By = require('selenium-webdriver').By,
        until = require('selenium-webdriver').until;
const util = require('util');
// Project modules
const CustomExceptions = require('../../custom_exceptions');
const CustomError = CustomExceptions.CustomError;
const PageObjectError = CustomExceptions.PageObjectError;
const PageObjectException = CustomExceptions.PageObjectException;
const ScriptExecutionError = CustomExceptions.ScriptExecutionError;
const WebElementNotFoundException = CustomExceptions.WebElementNotFoundException;
const HttpStatusException = CustomExceptions.HttpStatusException;

const debug = 0;

/**
 * Abstract model for a webpage.
 * @version 0.1
 * @abstract
 * @constructor
 * @classdesc Abstract page object for an actual in-browser webpage, wrapping
 * all generic methods for DOM elements manipulation throught the Selenium
 * WebDriver API.
 * @abstract
 * @param webdriver {WebDriver} The Selenium webdriver currently running the browser.
 * @param isAuthenticated {boolean} True iff user is authenticated on this page.
 */
function WebPage (webdriver, isAuthenticated) {
    if (!(this instanceof WebPage))
        throw new SyntaxError(this.constructor.name + " constructor needs to be called with the 'new' keyword.");

    /**@memberof WebPage
     * @instance
     * @type {WebDriver}
     * @alias driver
     */
	this.driver = webdriver;

    /**@memberof WebPage
     * @instance
     * @type boolean
     * @alias isAuthenticated
     * @public
     */
    this.isAuthenticated = isAuthenticated;

}

// Prototype linkage
WebPage.prototype = Object.create(Object.prototype);
WebPage.prototype.constructor = WebPage;
// Initialization
/**Browses the URL associated to the current WebPage
 * @private
 * @deprecated
 * @returns {Thenable<WebPage>}
 */
WebPage.browse = function() {
	let that = this;
	return that
	.getUrl()
	.then(function(url) {
		if(url && (url != that.url)) {
			if(debug) {
				console.log('WebPage::browse: I have page: ' + url);
				console.log('WebPage::browse: I want page: ' + that.url);
				console.log('WebPage::browse: URL not matching, browsing ' + that);
			}
			return that.driver.get(that.url).then(function() {
				return that;
			});
		}
	});
};
/**
 * I should really write some description for this method
 * @private
 * @returns {Thenable<WebPage>}
 */
// NOTE Under development
WebPage.prototype.init = function() {
    let that = this;

    return this.initComponents()
    .then(function() {
        return that.driver
        .findElement(By.css('header#header .account-cart-wrapper a.skip-account'))
        .click();
    }).then(function() {
        // DOM selectors storage: ['name'] => 'cssSelector'
		if(debug)
			console.log('Const: ' + util.inspect(that.locators, true, 1, true));
		let elemNames = [], elemPromises = [];
		for(let prop in that.locators) {
			if(debug)
				console.log('Element ' + prop + ' is being added to page object...');
            elemNames.push(prop);
            elemPromises.push(that.driver.findElements(By.css(that.locators[prop])));
		}
        // Resolve all WebElementPromises
		return Promise.all(elemPromises)
        .then(function(webElements) {
            return ([elemNames, webElements]);
        }, function(err) {
            // Should never be reached since WebDriver.findElements(...) always
            // returns a fulfilled Promise (unlike Webdriver.findElement(...))
            throw new WebElementNotFoundException();
        });
    }).then(function([names, elements]) {
        for(let index in elements) {
			switch(elements[index].length) {
                case 1: that[names[index]] = elements[index][0];
                    break;
                case 0:
                    throw new WebElementNotFoundException({CSS2: that.locators[names[index]]});
                default: that[names[index]] = elements[index];
            }
		}
		if(debug) {
			console.log('Ended DOM elements initiation successfully, final object is :');
			that.print();
		}
		return Promise.resolve(that);
	});
};
/**Initializes the current WebPage with its attributes
 * @private
 * @returns {Thenable<Object>}
 */
 WebPage.prototype.initComponents = function() {
    let that = this;
    // Includes elements common to all pages if not already defined
    if(this.components.hasOwnProperty('UniversalElements') === false)
         this.components['UniversalElements'] = true;
    //  if(this.components.includes('Header') === false)
    //       this.components.push('Header');

    //
    let authStatusPromise = (that.isAuthenticated === undefined)
    ? that.checkAuthenticationStatus()
    : Promise.resolve(that.isAuthenticated);

    return authStatusPromise
    .then(function(authStatus) {
        if(authStatus === true || authStatus === false) {
            return authStatus;
        } else {
            throw new PageObjectException('Trying to initiate current page with [authentication_status: ' + authStatus + ']');
        }
    }).then(function(authenticated) {
        // TODO Update method whenever using new locators storage strategy
        let headerType = (authenticated === true)
            ? 'AuthenticatedHeader'
            : 'PublicHeader';
        return that.components[headerType] = true;
    }).then(function() {
        // Transfering components locators to WebPage
        for(let key in that.components) {
            let component = require('../components/' + key);

            if(that.components[key] === true) {
                Object.assign(that.locators, JSON.parse(JSON.stringify(component.locators)));
                for (let fn in component.helpers) {
                    Object.getPrototypeOf(that)[fn] = component.helpers[fn];
                }
            }
        }
    });
 };
/**
 */
WebPage.prototype.selectLocatorsStatus = function(status) {
    let that = this;
    if(status === undefined)
        throw new PageObjectException();

    let locStatusPromise = new Promise(function(resolve, reject) {
        return (that.data.locators_status[status])
        ? resolve(that.data.locators_status[status])
        : reject(status);
    });

    return locStatusPromise
    .then(function(loc) {
        return Object.assign(that.locators, JSON.parse(JSON.stringify(loc)));
    }, function(status) {
        throw new PageObjectError('No locators specified for [page_status: ' + status + ']');
    });
};
/**
 */
WebPage.prototype.checkLocatorsStatus = function (getStatus) {
    let that = this;
	return this.getStatus() // NOTE Evolve to array if multiple status can stack up onto the same page
	.then(function(status) {
		return that.selectLocatorsStatus(status);
	});
};

/**
 * NOTE CAUTION: The following function may be strongly based onto the website's
 * mechanics (URL redirection, conditional displays, etc.). Please make sure
 * they are in adequation with the expected result, at any time.
 */
WebPage.prototype.checkAuthenticationStatus = function() {
    let that = this, menuWasActive;
    return this.driver.findElement(By.css('header#header .account-cart-wrapper a.skip-account.skip-active'))
    .then(function(headerMenu) {
        return menuWasActive = true;
	}, function(headerMenu) {
		menuWasActive = false;
        return that.driver.findElement(By.css('header#header .account-cart-wrapper a.skip-account')).click();
	}).then(function() {
        return that.driver.findElement(By.css('#header-account a[title="Connexion"]'))
    }).then(function() {
		return false;
	}, function() {
		return true;
	}).then(function(result) {
        if(debug) {
            console.log('Call to WebPage.checkAuthenticationStatus()')
            console.log('CLASS: ' + that.constructor.name);
            console.log('value of page.isAuthenticated: ' + that.isAuthenticated);
            console.log('Menu was active: ' + menuWasActive);
            console.log('- - - Result - - -');
            console.log('User is authenticated: ' + result);
        }
        if(!menuWasActive){
            return that.driver
            .findElement(By.css('header#header .account-cart-wrapper a.skip-account.skip-active'))
            .click()
            .then(function() {
                return result;
            }, function() {
                if(debug)
                    console.log(util.inspect(that, true, 0, true));
                throw new WebElementNotFoundException( {CSS3: 'header#header .account-cart-wrapper a.skip-account.skip-active'} );
            });
        }else
            return result;
    });
}
// Initialization ^^^ |||
// 				  ||| vvv Utilitary
/**
 * @private
 * @returns {string}
 */
WebPage.prototype.toString = function() {
	return this.constructor.name;
};
if(debug) {
    /**
     * @instance WebPage
     * @returns {string}
     */
     WebPage.prototype.printName = function() {
         let str = '' + this;
         console.log(str);
         return str;
	};
    /**
     * @instance WebPage
     * @returns {string}
     */
     WebPage.prototype.print = function() {
		let str = util.inspect(this, true, 0, true);
        console.log(str);
		return str;
	};
}
/**
 * Indicates the number of elements on the current page corresponding to
 * the provided CSS selector.
 * @instance WebPage.prototype
 * @param selector {string} CSS selector used for research onto current page.
 * @returns {Thenable<number>}
 */
WebPage.prototype.countByCss = function(selector) {
	return this.driver.findElements(By.css(selector))
		.then(function(res) {
			return res.length;
		})
};
/**
 */
WebPage.prototype.navigateBack = function() {
	return this.driver.navigate().back();
};
/** Useless
 */
WebPage.prototype.click = function(element) {
	return this[element].click();
};/**
 * Purges a select-dropdown from empty values, and then select the option
 * located at provided index.
 * NOTE: The index refers to the newly created set of values, and where the
 * remaining valid options still have the same relative order to each other.
 * @instance WebPage
 * @param select {WebElement} The HTML Select element to seek into.
 * @param index {number | "first" | "last" | "random"} Index to chose
 * @returns {Thenable<string>}
 */
 WebPage.prototype.getValidOption = function(select, index) {
 	let that = this;
 	return select.findElements(By.css('option'))
 	.then(function(opt) {
        let options = [];
 		for(let op in opt) {
 			options[op] = opt[op];
 		}
 		return options;
 	}).then(function(options) {
        let values = [];
 		for(let op in options) {
 			values[op] = options[op].getAttribute('value');
 		}
 		return Promise.all(values);
 	}).then(function(val) {
 		let validValues = [];
 		for(let v in val) {
 			// if(debug)
 			// 	console.log('-v- is -'+v+'- and ..val[v].. is ..'+val[v]+'..');
 			if(val[v].length >= 1) {
 				if(debug)
 					console.log('yup');
         validValues.push(val[v]);
 			} else {
 				if(debug)
 					console.log('nope');
 			}
 			if(debug)
 				console.dir(validValues);
 		}
 		if(debug)
            console.dir(validValues);
 		return validValues;
    }).then(function(val) {
        if(debug)
            console.dir('val afterwards: [' + val + ']');
 		// Interpreting index for authorized string values
        switch(index) {
 			case 'first':
 				index = 0; break;
 			case 'last':
 				index = val.length-1; break;
 			case 'random':
 				index = Math.floor(val.length*Math.random()); break;
 		}
 		if(debug)
            console.log('index equals: '+index);
 		if(Number.isInteger(index) && index >= 0) {
            if(debug)
                console.log('Today\'s happy customer is ' + val[index]);
 			return Promise.resolve(val[index]);
 		} else {
 			throw new Error('' + that + '::selectValidOption');
 			return Promise.reject('Invalid options.');
 		}
 	})
 };
/**Set the current option of a select element to the one owning
 * the provided @value.
 * @instance WebPage
 * @param select {WebElement} The HTML Select element to seek into.
 * @param value {string} The HTML option's value.
 * @returns {Thenable<undefined>}
 */
WebPage.prototype.setOptionByValue = function(select, value) {
    return select
    .findElement(By.css('option[value="' + value + '"]'))
    .getAttribute('innerHTML')
    .then(function(html) {
        return select.sendKeys(html);
    });
};
    /**
   * Reads all the available options from a select-dropdown, returning a random
   * one among those with a defined and non-empty value.
    * @instance WebPage
    * @see getValidOption
    * @param select {WebElement} The HTML Select element to seek into.
    * @returns {Thenable<string>}
    */
WebPage.prototype.getRandomOption = function(select) {
	return this.getValidOption(select, 'random');
};
/**
 * @instance WebPage
 * @returns {Thenable<boolean>}
 */
 // TODO: rewrite to return true/false/undefined, with a better testing condition
WebPage.prototype.isAuthenticatedPage = function() {
	if(debug)
		console.log('checking auth');
	return this.getWelcomeMessage()
    .then(function(msg) {
		return (msg != 'undefined' && msg != '' && msg != 'DEFAULT WELCOME MSG!');
	}).then(function(isAuth) {
		return isAuth;
	});
};
/**
 * @instance WebPage
 * @returns {Thenable<undefined>}
 */
WebPage.prototype.getTitle = function() {
	return this.driver
    .getTitle();
};
/**
 * @instance WebPage
 * @returns {Thenable<undefined>}
 */
WebPage.prototype.getUrl = function() {
	return this.driver
    .getCurrentUrl();
};
/**
 * @instance WebPage
 * @returns {Thenable<string>}
 */
WebPage.prototype.getWelcomeMessage = function() {
	return this.driver
    .findElement(By.css('p.welcome-msg'))
	.then(function(el) {
		return el.getText(); // <!> Returns processed text, not innerHTML attribute
	});
};
/**
 * @instance WebPage
 * @returns {Thenable<undefined>}
 * @deprecated
 */
WebPage.prototype.clickMenu = function() {
    return this.header_account_dropdown
    .click();
};
// TODO: Remove from here into NavBar files (as functions of the mixin)
/** Performs a click onto the homepage's link (within header's account menu).
 * @instance WebPage
 * @returns {Thenable<undefined>}
 */
WebPage.prototype.goHeaderHome = function() {
    let that = this;
    return this.driver
    .findElement(By.css('header#header .account-cart-wrapper a.skip-account.skip-active'))
    .click()
    .then(function(success) {
        return success;
    }, function(logoNotFound) {
        return Promise.reject(logoNotFound);
    });
};
/** Performs a click onto the login page's link (within header's account menu).
 * @instance WebPage
 * @returns {Thenable<undefined>}
 */
WebPage.prototype.goHeaderLogin = function() {
    let that = this;
    return this.driver
    .findElement(By.css('header#header .account-cart-wrapper a.skip-account.skip-active'))
    .then(function(menuDisplayed) {
        if(debug)
        {
            console.log('menu already opened');
            console.log(that.locators.header_login_link);
        }
        return that.header_login_link.click();
    }, function(menuHidden) {
        console.log('opening menu');
        return that.header_account_dropdown
        .click()
        .then(function() {
            return that.header_login_link.click();
        }, function(err) {
            return Promise.reject(err);
        })
    }).then(function() {
        return true;
    }, function(err) {
        return false;
    });
};
/**
 * @instance WebPage
 * @returns {Thenable<undefined>}
 */
WebPage.prototype.logout = function() {
	let that = this;
    return this.driver
    .findElement(By.css('header#header .account-cart-wrapper a.skip-account.skip-active'))
    .then(function(menuDisplayed) {
        return that.header_logout_link.click();
    }, function(menuHidden) {
        return that.header_account_dropdown
        .click()
        .then(function() {
            return that.header_logout_link.click();
        }, function(err) {
            return Promise.reject(err);
        })
    });
};
// Utilitary ^^^
// 			 |||

// /** @module ~/src/page_objects/pages/WebPage */
module.exports = WebPage;
