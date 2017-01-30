/******************************************************************************/
/********************************** GENERIC ***********************************/
/******************************************************************************/

function CustomError(message, defaultMessage) {
    this.name = this.constructor.name;
    this.defaultMessage = (defaultMessage)
    ? defaultMessage
    : 'Encountered an error';
    this.message = (message)
    ? message
    : this.defaultMessage;

    Error.captureStackTrace(this, this.constructor);
}
CustomError.prototype = Object.create(Error.prototype);
CustomError.prototype.constructor = CustomError;

module.exports.CustomError = CustomError;

/******************************************************************************/
/************************ CUSTOM ERRORS & EXCEPTIONS **************************/
/******************************************************************************/
function PageObjectException(message) {
    CustomError.call(this, message);
}
PageObjectException.prototype = Object.create(CustomError.prototype);
PageObjectException.prototype.constructor = PageObjectException;

module.exports.PageObjectException = PageObjectException;

function ScriptExecutionException() {
    let defaultMessage =  'An error occured while executing JavaScript code '
        + 'through WebDriver runtime';
    CustomError.call(this, null, defaultMessage);
}
ScriptExecutionException.prototype = Object.create(CustomError.prototype);
ScriptExecutionException.prototype.constructor = ScriptExecutionException;

module.exports.ScriptExecutionException = ScriptExecutionException;

// NOTE Code duplication from Selenium Webdriver library
function WebElementNotFoundException(identifier) {
    let defaultMessage = 'Could not resolve element identified by ' +
        JSON.stringify(identifier);
    CustomError.call(this, null, defaultMessage);
}
WebElementNotFoundException.prototype = Object.create(CustomError.prototype);
WebElementNotFoundException.prototype.constructor = WebElementNotFoundException;

module.exports.WebElementNotFoundException = WebElementNotFoundException;

function HttpStatusException(url, method, statusCode, expected) {
    let defaultMessage = method + ' request to ' + url
        + ' returned status code ' + statusCode
        + ', while expecting ' + expected;
    CustomError.call(this, null, defaultMessage);
}
HttpStatusException.prototype = Object.create(CustomError.prototype);
HttpStatusException.prototype.constructor = HttpStatusException;

module.exports.HttpStatusException = HttpStatusException;
