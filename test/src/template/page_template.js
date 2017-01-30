// "use strict";
// /***************************** PAGE DEFINITION ********************************/
// const pageData = {
//     path: '/path/from/host/', // host = process.env.BASE_HOST_URL
//     title: 'Page title',
//     locators: {
//         // DOM_element_function_and_tag_name: 'css.selector'
//         //
//         // or
//         //
//         // Empty
//     },
//     components: [
//         // Default
//     ],
//     data: {
//         // Custom data (credentials, expected messages, form values, etc.)
//         //
//         // or
//         //
//         // Empty
//     }
// };
// /******************************* INDEX EXPORT *********************************/
// module.exports.index = Object.assign({},
//     {
//         <<MyPage>>: {
//             path: pageData.path,
//             title: pageData.title
//             // Full URL is defined into ~/test/src/page_context.js
//         }
//     }
// );
// /************************* IMPORTS AND SETUP **********************************/
// // Standard modules
// // NOTE: No assertion libraries should be found down below!
// const By = require('selenium-webdriver').By,
//     until = require('selenium-webdriver').until;
// const util = require('util');
// // Project modules
// // NOTE: Keep in mind the project's architecture to avoid fatal interdependence
// const WebPage = require('./WebPage'); // abstract model for a web page
// // Custom errors/exceptions libraries
// const CustomExceptions = require('../../custom_exceptions');
// const CustomError = CustomExceptions.CustomError;
// const PageObjectError = CustomExceptions.PageObjectError;
// const PageObjectException = CustomExceptions.PageObjectException;
// const ScriptExecutionError = CustomExceptions.ScriptExecutionError;
// const WebElementNotFoundException = CustomExceptions.WebElementNotFoundException;
// // Other variables
// const debug = 0;
// /*************************** CONSTRUCTOR **************************************/
// /**Creates an instance of <<MyPage>>.
//  * @constructor
//  * @param webdriver {WebDriver} The Selenium webdriver currently running.
//  * @param isAuthenticated {boolean} True iff user is authenticated on this page.
//  */
// function <<MyPage>> (webdriver, isAuthenticated) {
// 	if (!(this instanceof <<MyPage>>))
//     	throw new SyntaxError(
//             "<<MyPage>> constructor needs to be called with the 'new' keyword."
//         );
//
//     // WebPage abstract constructor
//     WebPage.call(this, webdriver, isAuthenticated);
//
//     // Attaching basic data to <<MyPage>>
//     Object.assign(this, JSON.parse(JSON.stringify(pageData)));
// } // End of <<MyPage>> constructor
// /************************** PROTOTYPE CHAIN ***********************************/
// <<MyPage>>.prototype = Object.create(WebPage.prototype);
// <<MyPage>>.prototype.constructor = <<MyPage>>;
// /************************* SPECIFIC METHODS ***********************************/
// <<MyPage>>.prototype.myFunc = function() {
// 	if(debug)
//         console.log('This is my prototype\'s function!');
// 	return this;
// };
// /*************************** CLASS EXPORT *************************************/
// module.exports.class = <<MyPage>>;
