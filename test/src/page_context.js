"use strict";
/************************** Imports and setup *********************************/
// Utility
const util = require('util'),
    fs = require('fs');

const http = require('selenium-webdriver/http'),
    httpUtil = require('selenium-webdriver/http/util'),
    request = http.Request,
    response = http.Response;

// Chai & submodules
const chai = require('chai'),
    should = chai.should(),
    expect = chai.expect,
    assert = chai.assert,
    chaiAsPromised = require('chai-as-promised');
chai.use(chaiAsPromised);

// Selenium WebDriver
const webdriver = require('selenium-webdriver'),
    By = webdriver.By,
    until = webdriver.until;

// Initiating page index
const page_index = {
    host: process.env.BASE_HOST_URL
};
const pathToPages = './page_objects/pages/';
const pageList = [
    'AdvancedSearchPage',
    'CartPage',
    'CataloguePage',
    'CheckoutPage',
    'DashboardPage',
    'ForgotPasswordPage',
    'HomePage',
    'InfoPage',
    'LoggedOutPage',
    'LoginPage',
    'ProductPage',
    'RegisterPage',
    'SalesFormPage',
    'SiteMappingPage',
    'WishlistPage'
];
for(let i = 0; i < pageList.length; i++) {
    Object.assign(
        page_index,
        JSON.parse(JSON.stringify(require(pathToPages+pageList[i]).index))
    );
}
for(let page in page_index) {
    if(page != 'host' && page_index[page].path)
        page_index[page].url = page_index.host + page_index[page].path;
}
Object.freeze(page_index);
Object.seal(page_index);
/**@exports page_index
 */
module.exports.page_index = page_index;

// Project modules
//TODO Try to programatically import modules
const AdvancedSearchPage = require(pathToPages+'AdvancedSearchPage').class;
const CartPage = require(pathToPages+'CartPage').class;
const CataloguePage = require(pathToPages+'CataloguePage').class;
const CheckoutPage = require(pathToPages+'CheckoutPage').class;
const DashboardPage = require(pathToPages+'DashboardPage').class;
const ForgotPasswordPage = require(pathToPages+'ForgotPasswordPage').class;
const HomePage = require(pathToPages+'HomePage').class;
const InfoPage = require(pathToPages+'InfoPage').class;
const LoggedOutPage = require(pathToPages+'LoggedOutPage').class;
const LoginPage = require(pathToPages+'LoginPage').class;
const ProductPage = require(pathToPages+'ProductPage').class;
const RegisterPage = require(pathToPages+'RegisterPage').class;
const SalesFormPage = require(pathToPages+'SalesFormPage').class;
const SiteMappingPage = require(pathToPages+'SiteMappingPage').class;
const WishlistPage = require(pathToPages+'WishlistPage').class;
/**********/
const CustomExceptions = require('./custom_exceptions');
const CustomError = CustomExceptions.CustomError;
const PageObjectException = CustomExceptions.PageObjectException;
const ScriptExecutionException = CustomExceptions.ScriptExecutionException;
const WebElementNotFoundException = CustomExceptions.WebElementNotFoundException;
/**********/
const debug = 0;

/******************************************************************************/
/****************************** USER CLASS ************************************/
/******************************************************************************/

/**
 * Creates an instance of User.
 * @constructor
 * @name User
 * @param webdriver {WebDriver} The Selenium webdriver currently running.
 */
function User (webdriver) {
    this.driver = webdriver;
}
/**************** Prototype linkage and init() function ***********************/
User.prototype = Object.create(Object.prototype);
User.prototype.constructor = User;
User.prototype.init = function() {
    let that = this;
    return this.driver
    .get(page_index.HomePage.url)
    .then(function() {
        return new HomePage(that.driver, false).init();
    }).then(function(webpage) {
        that.page = webpage;
        return that;
    }, function(err) {
        console.log(err);
        return Promise.resolve(err);
    }).catch(function(err) {
        return Promise.resolve(err);docker
    });
};
/************************* Assertion methods **********************************/
User.prototype.onAuthenticatedPage = function() {
    // assert(this.isAuthenticated == true, 'AuthenticationException: The user is currently not authenticated, while expected to be.');
    return Promise.resolve(this.isAuthenticated)
    .should.eventually.be.true;
}
User.prototype.isOn = function(type) {
    return this
    .samePageTypes(this.page, type)
    .catch(function(err) {
        return Promise.reject(err);
    });
};
User.prototype.samePageTypes = function(type1, type2) {
    let that = this;
    // If any param is constructor, we replace it
    // by its name (Function.name property)
    if (typeof type1 === "function")
      type1 = type1.name;
    if (typeof type2 === "function")
      type2 = type2.name;
    return Promise.resolve()
    .then(function() {
    	if(debug) {
    		console.log('Page 1 type: ' + type1);
    		console.log('Page 2 type: ' + type2);
    	}
    	// Casting page types as strings, by casting concatenation with ''
    	// {page} has toString() defined into its prototype chain
    	if(type1 == type2)
            return Promise.resolve(that);
        else {
            return Promise.reject(new PageObjectException(type1 + ' (actual) not matching ' + type2 + ' (expected)'));
        }
    });
};
/************************ Utilitaries for basic logic *************************/
/**Returns text from welcome message located into header's topest part.
 * Please note that the method returns CSS-applied text, not innerHTML attribute
 * @method
 * @returns {Thenable<string>}
 */
User.prototype.getWelcomeMessage = function() {
    return this.page.driver
    .findElement(By.css('p.welcome-msg'))
    .then(function(el) {
        return el.getText();
    });
};
/**Checks for current user's authenticated status, from contextual elements.
 * @see getWelcomeMessage
 * @function
 * @returns {Thenable<boolean>}
 * @deprecated
 */
User.prototype.isUserAuth = function() {
    if(debug)
        console.log('User::isUserAuth: start');
    return this
    .getWelcomeMessage()
    .then(function(msg) {
        return (msg != 'DEFAULT WELCOME MSG!');
    }).then(function(value) {
        return value;
    });
};

/*********** Utilitaries for page manipulation *************/
/**Signs in as specifed @user with the mentionned @password.
 * @function
 * @returns {Thenable<User>}
 */
User.prototype.connect = function() {
    let that = this;
    let successUrl = page_index.DashboardPage.url;
    let failureUrl = page_index.LoginPage.url;
    // if(debug)
    //     console.log('User::connect: ' + util.inspect(this.page, true, 0, true));
    return this.isOn('LoginPage')
    .then(function() {
        if(debug)
            console.log('connecting...');
        return that.page.connect()
    }).then(function(newUrl) {
        return newUrl == successUrl;
    }).then(function(success) {
        switch(success) {
            case true:
                if(debug)
                    console.log('Success! I return a Dashboard Page');
                that.isAuthenticated = true;
                return (new DashboardPage(that.driver).init());
            case false:
                if(debug)
                    console.log('Failure... I return a (new) LoginPage');
                return that.page.getUrl()
                .then(function(url) {
                    if(url == failureUrl)
                        return (new LoginPage(that.driver).init());
                    else {
                        if(debug)
                        console.error('**Epic fail**');
                        return undefined;
                        // To improve: throws a done() callbackerror
                        //throw {name: 'PageException', message: 'URL does not match any expected page (LoginPage::logUser)'};
                    }
                });
            case undefined:
                if(debug)
                    console.error('LoginPage::logUser: Unable to check login success.');
                return; //TODO: handle undefined return with 'epic fail' use case
        }
    }).then(function(page) {
        that.page = page;
        return that.isOn('DashboardPage');
        if(debug)
          console.log('User::connect: Returning ' + util.inspect(that, true, 0, true));
        return that;
    }).catch(function(err) {
        return Promise.reject(err);
    });
};
/**
 * Signs in as specifed user with the mentionned password.
 * @example
 * var myUser = new User(driver);
 *
 * myUser
 * .init()
 * .then(function(user) {
 *    return user.goToLogin();
 * }).then(function(user) {
 *    console.log(user.page); // Result: 'LoginPage'
 * });
 * @returns {Thenable<User>}
 * @param webdriver The Selenium webdriver currently running.
 */
User.prototype.goToLogin = function() {
    let that = this;
    // if(debug)
    //     console.log('User::toLogin: current page attribute is: ' + util.inspect(this.page, true, 0, true));
    return that.page.goHeaderLogin()
    .then(function() {
        return that.waitForLoadedPage('LoginPage', 5000);
    //   return that.isOn('LoginPage');
    }).then(function(isOnLogin) {
        if(isOnLogin == true) {
          if(debug)
            console.log('Trace: user page is now login type');
          return (new LoginPage(that.driver)).init()
          .then(function(page) {
            that.page = page;
            return that;
          });
        } else if(isOnLogin == false){
          console.error('Failed to get login page.');
          return that;
        } else {
          console.error('TypeError: Returned \'undefined.\' after checking if on login page. ')
        }
    });
};
/**
 * Signs in as specifed @user with the mentionned @password.
 * @returns {Thenable<User>}
 */
User.prototype.goToRegister = function() {
    let that = this;
    return Promise.resolve()
    .then(function() {
      return that.page.register();
    }).then(function() {
      return (new RegisterPage(that.driver)).init();
    }).then(function(page) {
      that.page = page;
      if(debug)
        console.log('User::goToRegister: ' + util.inspect(that.page, true, 0, true));
      return page.sampleRegistration();
    }).then(function (page) {
      return that;
    });
};
/**
 * !!!
 * @returns {Thenable<Array>}
 */
User.prototype.emptyCart = function() {
    let that = this;
    return this
    .isOn('CartPage')
    .then(function() {
        return that.page.emptyCart();
    }).then(function() {
        return Promise.all([
            // All conditions to be tested
            that.page.isCartEmpty().should.eventually.be.true
        ]);
    });
}
/**
 * Browses some customized product's page and operates a few basic operations.
 * @returns {Thenable<User>}
 */
User.prototype.goToProduct = function() {
    let that = this;
    return this.driver
    .get(page_index.ProductPage.url)
    .then(function() {
        return (new ProductPage(that.driver, that.page.isAuthenticated));
    }).then(function(page) {
        return page.init();
    }).then(function(page) {
        that.page = page;
        return that;
    });
};
User.prototype.addProduct = function(qty) {
    let that = this;
    return this.page
    .addProduct(qty)
    .then(function () {
        return new CartPage(that.driver, undefined);
    }).then(function(page) {
        return page.init();
    }).then(function (page) {
        that.page = page;
        return that;
    });
};
User.prototype.clickAndWait = function(element, pageType, timeout) {
    let that = this;
    return element.click()
    .then(function() {
        if(timeout != undefined) {
            return that.driver.wait(
                until.urlIs(page_index[pageType].url),
                timeout
            ).catch(function(err) {
                throw new PageObjectException('Timed out after waiting for '+pageType+' to load (exited after '+timeout/1000+'s)');
            });
        } else {
            throw new Error('No timeout provided for clickAndWait on '+pageType+'.');
        }
    })
}
/**
 * Signs in as specifed @user with the mentionned @password.
 * @returns {Thenable<User>}
 */
User.prototype.goToCart = function() {
    let that = this;
    return Promise.resolve()
    .then(function() {
        if(debug)
            console.log('click click');
            /*****/
        // return that.clickAndWait(that.page.header_cart_link, 'CartPage', 5000);
            /***/
        return that.page.header_cart_link.click();
            /*****/
        // return that.page.header_cart_link.click()
        // .then(function() {
        //     return that.driver.wait(until.urlIs(page_index.CartPage.url));
        // });
    }).then(function() {
        return new CartPage(that.page.driver, that.page.isAuthenticated);
    }).then(function(page) {
        return page.init();
    }).then(function(page) {
        that.page = page;
        if(debug)
            console.log('User::goToCart: ' + util.inspect(that.page, true, 0, true));
        // return page.goHeaderHome();// doSomething();
    }).then(function (page) {
        return that;
    });
};
/**
 * Signs out the current user.
 * @returns {Thenable<User>}
 */
User.prototype.disconnect = function() {
  let that = this;
  if(debug)
    console.log('User::disconnect:');
  return Promise.resolve()
  .then(function() {
      if(that.isAuthenticated == true) {
          return that.page.logout()
          .then(function() {
            return that.page.driver.getCurrentUrl();
          }).then(function(url) {
            if(url == page_index.LoggedOutPage.url)
              return (new LoggedOutPage(that.driver)).init();
          });
      } else {
          throw new PageObjectError('Cannot sign out, user is not authenticated');
      }
  }).then(function(page) {
    let secondsToWait = 5 + 4; // waiting after logoff to be back on homepage
    if(debug)
        console.log('Logoff timeOut initiated, '+secondsToWait+'s remaining...');
    return that.driver.wait(until.urlIs(page_index.HomePage.url), 1000*secondsToWait)
    .then(function() {
      return new HomePage(that.driver, false);
    });
  }).then(function(page) {
      that.page = page;
      return that;
  });
};
/**
 * Abc
 * @returns {Thenable<ForgotPasswordPage>}
 */
User.prototype.goToForgotPassword = function() {
    let that = this;
    return this
    .isOn('LoginPage')
    .then(function() {
        return that.page.click('forgot_password_link');
    }).then(function() {
        return new ForgotPasswordPage(that.driver);
    }).then(function(page) {
        return page.init();
    }).then(function(page) {
        that.page = page;
        return that;
    });
}
/**Abc
 * @returns {Thenable<undefined>}
 */
User.prototype.forgetPassword = function() {
    let that = this;
    return this
    .isOn('LoginPage')
    .then(function() {
        return that.goToForgotPassword();
    }).then(function(page) {
        return that.isOn('ForgotPasswordPage');
    }).then(function() {
        return that.page.requestPassword();
    });/*, function(err) {
        throw new Error('User::forgetPassword: '
            + err.name + ': ' + err.message
        );
    });*///.then
}
/**Abc
 * @returns {Thenable<ForgotPasswordPage>}
 */
User.prototype.goToAdvancedSearch = function() {
    let that = this;
    return this.page.footer_advanced_search_link.click()
    .then(function() {
        return new AdvancedSearchPage(that.driver, that.page.isAuthenticated);
    }).then(function(page) {
        return page.init();
    }).then(function(page) {
        that.page = page;
        return that.page.searchFor(that.page.data.form_key_sets.validSearchDetails);
    });
}
/**Abc
 * @returns {Thenable<ForgotPasswordPage>}
 */
User.prototype.goToHome = function() {
    let that = this;
    return this.page.header_logo_link.click()
    .then(function() {
        return new HomePage(that.driver, that.page.isAuthenticated);
    }).then(function(page) {
        return page.init();
    }).then(function(page) {
        that.page = page;
        return that;
    });
}
/******************************************************************************/
/*********************************<TESTS>**************************************/
User.prototype.titleEquals = function(expectedTitle) {
    this.page.getTitle()
    .should
    .eventually
    .equal(expectedTitle);
};
User.prototype.titleContains = function(titlePart) {
    return this.page.getTitle()
    .should
    .eventually
    .contain(titlePart);
};
User.prototype.titleStartsWith = function(titleStart) {
    return this.page.getTitle()
    .then(function(title) {
            return title.startsWith(titleStart);
    }).should
    .eventually
    .be
    .true;
};
User.prototype.titleEndsWith = function(titleEnd) {
    this.page.getTitle()
    .then(function(title) {
            return title.endsWith(titleEnd);
    }).should
    .eventually
    .be
    .true;
};
User.prototype.urlEquals = function(expectedUrl) {
    this.page.getUrl()
    .should
    .eventually
    .equal(expectedUrl);
};
User.prototype.urlContains = function(urlPart) {
    return this.page.getUrl()
    .should
    .eventually
    .contain(urlPart);
};
User.prototype.urlStartsWith = function(urlStart) {
    this.page.getUrl()
    .then(function(url) {
            return url.startsWith(urlStart);
    }).should
    .eventually
    .be
    .true;
};
User.prototype.urlEndsWith = function(urlEnd) {
    this.page.getUrl()
    .then(function(url) {
            return url.endsWith(urlEnd);
    }).should
    .eventually
    .be
    .true;
};
User.prototype.checkHttpCode = function(url, code, callback) {
    httpUtil
    .getStatus(url)
    .then(function(statusCode) {
        console.log(statusCode);
        callback();
    }, function(err) {
        callback(err);
    });
};
User.prototype.isAuthenticated = function() {
    return this.page.isAuthenticatedPage()
    .should
    .eventually
    .be
    .true;
};
User.prototype.isNotAuthenticated = function() {
    return this.page.isAuthenticatedPage()
    .should
    .eventually
    .be
    .false;
};
/**NOTE: Requires being onto CartPage
 */
User.prototype.cartIsEmpty = function() {
    let that = this;
    return this.isOn('CartPage')
    .then(function(onCartPage) {
        if(onCartPage)
            return that;
    }).then(function(user) {
        if(user)
            return that.page.isCartEmpty();
        else {
            return false; // Or other error
        }
    }).should.eventually.be.true;
};
/**NOTE: Requires being onto CartPage
 */
User.prototype.cartIsNotEmpty = function() {
    return this.driver.findElements(By.css('#shopping-cart-table > tbody > tr'))
    .should
    .eventually
    .not
    .be
    .empty;
};
/**NOTE: Requires being onto CartPage
 */
User.prototype.countItems = function() {
    return this.driver.executeScript(
        'var products = document.querySelectorAll("#shopping-cart-table tbody > tr");'
        + 'var res = new Array(products.length);'
        + 'for(let i = 0; i < products.length; i++) {'
        + ' let p = products[i];'
        + ' let str = p.querySelector("td.product-cart-info .product-cart-sku").outerText;'
        + ' res[i] = new Array(2);'
        + ' res[i][0] = str.substring(str.indexOf("SKU: ")+"SKU: ".length);'
        + ' res[i][1] = p.querySelector("td.product-cart-actions .input-text.qty").value;'
        + '}'
        + ' return res;'
    ).then(function(res) {
        if(debug)
            console.dir(res);
        return res;
    }).then(function(productsCounts) {
        let totalItems = 0;
        for(let ind in productsCounts) {
            let nb = productsCounts[ind][1];
            if(nb + 0 != nb) { // Its a string and not a number
                nb = parseInt(nb, 10);
            }
            totalItems += nb;
        }
        if(debug)
            console.log('There are currently ' + totalItems + ' products in the cart.')
        return totalItems;
    });
};
// Using User's state, pay attention to have up-to-date data
User.prototype.userIsOnPage = function(type) {
    return (''+ this.page)
    .should
    .equal(type);
};
User.prototype.userIsAuthenticated = function() {
    return this.isAuthenticated
    .should
    .be
    .true;
};
User.prototype.waitForLoadedPage = function(pageType, timeout) {
    let that = this;

    if (typeof timeout === 'undefined') {
        timeout = 5000;
    } else {
        if (typeof timeout !== 'number' || timeout <= 0) {
            throw new TypeError('timeout must be a positive integer');
        }
    }

    return this.driver.executeScript('return document.readyState;')
    .then(function(pageState) {
        return that.driver.wait(function() {
            return pageState === 'complete';
        }, timeout).then(function() {
            return true;
        });
    }).then(function() {
        return that.driver.getCurrentUrl();
    }).then(function(url) {
        return url === page_index[pageType].url;
    }).catch(function(err) {
        return Promise.reject(false);
    });
}
/**
 * @throws {CustomError} Expected behaviour (requested custom error/exception)
 * @throws {ReferenceError} The custom error/exception does not exist
 */
User.prototype.throwCustomException = function(errType, message) {
    if(!(errType instanceof CustomError))
        try {
            errType = eval(errType); // Grabbing constructor reference from a string
        } catch (e) {
            throw new ReferenceError('Could not throw custom error: '
            + 'unknown type "' + errType + '"');
        }
    // Custom exception constructor called with provided message
    throw new errType(message);
}
/********************************</TESTS>**************************************/
/******************************************************************************/
/*******************************<EXPORT />*************************************/
module.exports.User = User;
