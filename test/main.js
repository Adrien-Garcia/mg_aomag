"use strict";

// Utility
const util = require('util'),
    fs = require('fs');

// Selenium WebDriver
const webdriver = require('selenium-webdriver'),
    test = require('selenium-webdriver/testing'),
    // Promise = webdriver.Promise,
    By = webdriver.By,
    until = webdriver.until;
const firefox = require('selenium-webdriver/firefox'),
    chrome = require('selenium-webdriver/chrome');
    // opera = require('selenium-webdriver/opera'),
    // edge = require('selenium-webdriver/edge'),
    // safari = require('selenium-webdriver/safari'),
    // ie = require('selenium-webdriver/ie'),
const http = require('selenium-webdriver/http'),
    httpUtil = require('selenium-webdriver/http/util'),
    HttpClient = http.HttpClient,
    Request = http.Request,
    Response = http.Response;

// Page Objects pattern
const User = require('./src/page_context').User;
const page_index = require('./src/page_context').page_index;

// Global scope variables
var builder, driver, myUser;
const debug = 0;
const WINDOW_MIN_WIDTH=800,
    WINDOW_MIN_HEIGHT=500;
const DISPLAY_TRANSITION_WIDTH=763; // limit between desktop and tablet display
console.log('Base host URL: '+util.inspect(process.env.BASE_HOST_URL, true, 1, true));

// Browser config (generated through 'jetdocker test')
const browserName = process.env.SEL_BROWSER_NAME,
    browserVersion = process.env.SEL_BROWSER_VERSION,
    browserPlatform = process.env.SEL_BROWSER_PLATFORM,
    browser = process.env.SELENIUM_BROWSER,
    remoteUrl = process.env.SELENIUM_REMOTE_URL;
if(debug){
    console.log('browser: ' + browserName);
    console.log('version: ' + browserVersion);
    console.log('platform: ' + browserPlatform);
    console.log('Selenium hub: ' + (remoteUrl ? remoteUrl : 'none'));
}

// NOTE Move to User API ?
// Classic use : setupBrowser(myUser.driver,  myUser, done)
const setupBrowser = function(driver, user, callback) {
    driver
    .manage()
    .deleteAllCookies() // Cleans up temporary files
    .then(function() {
        return user.init(); // Refreshes landing page in browser and prepare data
    }).then(function() {
        callback();
    }).catch(function(err) {
        return callback(err);
    });
};

const shutDownBrowser = function(driver, callback) {
    driver
    .quit()
    .then(function() {
        callback();
    }).catch(function(err) {
        return callback(err);
    });
};

/******************************************************************************/
/********************************* TESTS **************************************/
/******************************************************************************/
test.describe('MOCHA - Tests Suite', function() {
    // SETUP

    // NOTE Firefox specific config :
    //     AdBlock : activated (V2.8.2)
    //     profile : seleniumProfile (at './lib/klfyjbpv.seleniumProfile')

    // Mocha hooks
    before(function(done) {
        // Temporarly accepting untrusted certificate issuers
        process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";

        if(browserName === 'firefox') {
            let profile = new firefox.Profile('./lib/klfyjbpv.seleniumProfile');
            profile.setAcceptUntrustedCerts(true);
            profile.acceptUntrustedCerts(true);
            // /!\ Uncomment to activate AdBlock Plus /!\
            // profile.addExtension('./lib/adblock_plus-2.8.2-an+fx+sm+tb.xpi');

            let options = new firefox.Options()
            .setProfile(profile);

            var wd = new webdriver.Builder()
            .forBrowser(browserName, browserVersion, browserPlatform)
            .setFirefoxOptions(options)
            .build();

            // Loading a page times out after driver.get(...) has been called
            wd.manage()
            .timeouts()
            .pageLoadTimeout(10000); // ~ 2000-15000

            // When a click() requests for a new page to get loaded
            wd.manage()
            .timeouts()
            .implicitlyWait(6000); // ~ 4000-8000

        } else { // browser != Firefox
            var wd = new webdriver.Builder()
            .withCapabilities(
                new webdriver.Capabilities()
                    .set(webdriver.Capability.BROWSER_NAME, webdriver.Browser[browserName.toUpperCase()])
                    .set(webdriver.Capability.PLATFORM, browserPlatform.toUpperCase())
                    .set(webdriver.Capability.SUPPORTS_JAVASCRIPT, true)
                    .set(webdriver.Capability.ACCEPT_SSL_CERTS, true)
            )
            .build();
        }

        Promise.resolve(wd)
        .then(function(wd) {
            driver = wd;
            return new User(driver);
        }).then(function(user) {
            return user.init();
        }).then(function(user) {
            myUser = user;
            return driver.manage().window().getSize();
        }).then(function(size) {
            if(size.width < WINDOW_MIN_WIDTH || size.height < WINDOW_MIN_HEIGHT) {
                return driver.manage().window().setSize(WINDOW_MIN_WIDTH, WINDOW_MIN_HEIGHT);
            } else {
                return;
            }
        }).then(function() {
            return done();
        }).catch(function(err) {
            return done(err);
        });
    });

    after(function(done) {
        // Restoring untrusted certificate policy
        process.env.NODE_TLS_REJECT_UNAUTHORIZED = undefined;

        shutDownBrowser(driver, done);
    });

    // TESTS
    test.it('Should throw an Error', function(done) {
        done(new Error('Implement me!'));
    });

    // SUBSUITES
    test.describe('Pages are reachable (status 200)', function(done) {
        var clientServer, clientUrl;

        /**
         * Requests a client server for a given ressource
         * @param httpClient {HttpClient} The client processing the request
         * @param method {string} HTTP method used for the request
         * @param path {string} Path of the ressource onto the server
         * @returns {Promise<number|Error>} TODO
         */
        const checkHttpStatus = function(httpClient, method, path) {
            var httpRequest = new Request(method, path);

            return httpClient.send(httpRequest)
            .then(function(response) {
                if(response.status == 200) {
                    return Promise.resolve(response.status);
                } else {
                    const HttpStatusException = require('./src/custom_exceptions').HttpStatusException;
                    return Promise.reject(new HttpStatusException(clientUrl+path, method, response.status, '200'));
                }
            }, function(err) {
                return Promise.reject(err);
            });
        };

        // SETUP
        before(function() {
            clientUrl = page_index.host;
            clientServer = new HttpClient(clientUrl);
        });

        // TESTS
        test.it('Homepage', function() {
            return checkHttpStatus(clientServer, 'GET', page_index.HomePage.path);
        });
        test.it('Login', function() {
            return checkHttpStatus(clientServer, 'GET', page_index.LoginPage.path);
        });
        test.it('Registering', function() {
            return checkHttpStatus(clientServer, 'GET', page_index.RegisterPage.path);
        });
        test.it('Dashboard', function() {
            return checkHttpStatus(clientServer, 'GET', page_index.DashboardPage.path);
        });
    }); // End of suite 'Pages are reachable (status 200)'

    test.describe('Classic setup', function() {
        before(function(done) {
            done();
        });

        beforeEach(function(done) {
            setupBrowser(driver, myUser, done);
        });

        test.describe('Regression tests', function() {
            after(function(done) {
                driver.manage().window().setSize(1600, 800)
                .then(function() {
                    done();
                }).catch(function(err) {
                    done(err);
                });
            });

            test.it('Feature/helper functions within components', function(done) {
                Promise.resolve()
                .then(function() {
                    return myUser.page.publicFunc();
                }).then(function() {
                    return myUser.page.privateFunc();
                }).then(function() {
                    done(new Error());
                }, function() {
                    return;
                }).then(function() {
                    return myUser.goToLogin();
                }).then(function() {
                    return myUser.connect();
                }).then(function() {
                    return myUser.page.privateFunc();
                }).then(function() {
                    return myUser.publicFunc();
                }).then(function() {
                    done(new Error());
                }, function() {
                    return;
                }).then(function() {
                    return myUser.goToHome();
                }).then(function() {
                    return myUser.page.privateFunc();
                }).then(function() {
                    return myUser.publicFunc();
                }).then(function() {
                    done(new Error());
                }, function() {
                    done();
                }).catch(function(err) {
                    done(err);
                });
            })

            test.it('Fix/pageData.components (values shared between page instances)', function(done) {
                Promise.resolve()
                .then(function() {
                    return myUser.goToCart();
                }).then(function() {
                    return myUser.page.goHeaderHome();
                }).then(function() {
                    let HomePage = require('./src/page_objects/pages/HomePage').class;
                    return new HomePage(driver, myUser.page.isAuthenticated);
                }).then(function(page) {
                    return page.init();
                }).then(function(page) {
                    myUser.page = page;
                    return myUser.goToLogin();
                }).then(function() {
                    return myUser.connect();
                }).then(function() {
                    return myUser.goToCart();
                }).then(function() {
                    return done();
                }).catch(function(err) {
                    return done(err);
                })
            });

            test.it('Other/cookie policy div covering clickable links', function(done) {
                let screenSize;

                driver.manage().window().getSize()
                .then(function(size) {
                    return screenSize = size;
                }).then(function() {
                    // Needs to resize window so that desired link gets covered
                    return driver.manage().window().setSize(400, 500);
                }).then(function() {
                    return myUser.goToAdvancedSearch();
                }).then(function() {
                    return driver
                    .manage()
                    .window()
                    .setSize(screenSize.width, screenSize.height)
                    .then(function() {
                        done(new Error());
                    });
                }, function() {
                    return driver
                    .manage()
                    .window()
                    .setSize(screenSize.width, screenSize.height)
                    .then(function() {
                        done();
                    });
                }).catch(function(err) {
                    return driver
                    .manage()
                    .window()
                    .setSize(screenSize.width, screenSize.height)
                    .then(function() {
                        done(err);
                    });
                });
            });

        }); // End of suite 'Regression tests'

        test.describe('Guest user', function() {

            test.describe('Homepage', function() {
                beforeEach(function(done) {
                    myUser.driver.get(page_index.HomePage.url);
                    done();
                });

                test.it('Correct display of principal elements', function(done) {
                    myUser.driver
                    .getCurrentUrl()
                    .then(function(url) {
                        if(url === page_index.HomePage.url)
                            done();
                        else {
                            done(new Error());
                        }
                    }, function(err) {
                        done(err);
                    })
                });

            });


            test.describe('Waiting for page loading to continue execution flow', function() {
                test.it('Woohoo!', function(done) {
                    // console.log(util.inspect(myUser.page, true, 0, true));
                    myUser
                    .goToLogin()
                    .then(function() {
                        return myUser.waitForLoadedPage('LoginPage');
                    }).then(function(res) {
                        if(debug)
                            console.log(res);
                        return;
                    }).then(function() {
                        return done();
                    }).catch(function(err) {
                        done(err);
                    });
                });

                test.it('Boohoo...', function(done) {
                    myUser
                    .goToLogin()
                    .then(function() {
                        return myUser.connect();
                    }).then(function() {
                        return myUser.disconnect();
                    }).then(function() {
                        return myUser.waitForLoadedPage('HomePage');
                    }).then(function(res) {
                        if(debug)
                            console.log(res);
                        return;
                    }).then(function() {
                        return done();
                    }).catch(function(err) {
                        done(err);
                    });
                });
            });

        }); // End of suite 'Guest user'



        test.describe('Misc.', function() {
            test.it('Accessing mg_aomagento', function(done) {
                // driver.get('https://www.google.fr');
                driver.get('https://mg-aomagento.agarcia.jetpulp.dev/rwd/customer/account/login/')
                .then(function() {
                    return driver.findElement(By.css('input#email')).sendKeys('Hay low werld');
                }).then(function() {
                   done();
                });
            });

            test.it('Woohoo!', function(done) {
                myUser
                .goToLogin()
                .then(function() {
                    return myUser.waitForLoadedPage('LoginPage');
                }).then(function(res) {
                    if(debug)
                        console.log(res);
                    return;
                }).then(function() {
                    return done();
                }).catch(function(err) {
                    done(err);
                });
            });

            test.it('Boohoo...', function(done) {
                myUser
                .goToLogin()
                .then(function() {
                    return myUser.connect();
                }).then(function() {
                    return myUser.disconnect();
                }).then(function() {
                    return myUser.waitForLoadedPage('HomePage');
                }).then(function(res) {
                    if(debug)
                        console.log(res);
                    return;
                }).then(function() {
                    return done();
                }).catch(function(err) {
                    done(err);
                });
            });

            test.it('Under development functionalities', function(done) {
                myUser
                .goToLogin()
                .then(function() {
                    return myUser.connect();
                }).then(function() {
                    return myUser.goToProduct();
                }).then(function() {
                    return myUser.addProduct(2);
                }).then(function() {
                    return myUser.driver.get(page_index.WishlistPage.url);
                }).then(function() {
                    return new (require('./src/page_objects/pages/WishlistPage').class)(myUser.driver, myUser.page.isAuthenticated).init();
                }).then(function(page) {
                    myUser.page = page;
                    return myUser.page.navigateBack();
                }).then(function(count) {
                    return myUser.page.getTitle();
                }).then(function(title) {
                    title.should.equal('Shopping Cart');
                    return done();
                }).catch(function(err) {
                    done(err);
                });
            });

            test.it.skip('Saving execution context', function(done) {
                driver.get('https://time.is/')
                .then(function() {
                    return driver.takeScreenshot()
                    .then(function(data) {
                        var base64Data = data.replace(/^data:image\/png;base64,/,"");
                        fs.writeFile("./screenshot_time.png", base64Data, 'base64', function(err) {
                            if(err) console.log(err);
                        });
                    });
                }).then(function() {
                    return driver.get('https://www.whatismybrowser.com/');
                }).then(function() {
                    return driver.takeScreenshot()
                    .then(function(data) {
                        var base64Data = data.replace(/^data:image\/png;base64,/,"");
                        fs.writeFile("./screenshot_capabilities.png", base64Data, 'base64', function(err) {
                            if(err) console.log(err);
                        });
                    });
                }).then(function() {
                    done();
                }).catch(function(err) {
                    done(err);
                });
            });

            test.describe('Shopping cart emptying', function() {
                test.it('Containing items', function(done) {
                    myUser
                    .goToLogin()
                    .then(function() {
                        return myUser.connect();
                    }).then(function() {
                        return myUser.goToProduct();
                    }).then(function() {
                        return myUser.addProduct(5);
                    }).then(function() {
                        return myUser.countItems();// return myUser.goToCart();
                    }).then(function(count) {
                        if(debug)
                            console.log('There are currently ' + count + ' products in the cart.');
                        return;
                    }).then(function() { // NOTE Returned a WebElement
                        return myUser.emptyCart();
                    }).then(function() {
                        return myUser.cartIsEmpty();
                    }).then(function() {
                        done();
                    }).catch(function(err) {
                        done(err);
                    });
                });

                test.it('Already empty', function(done) {
                    myUser
                    .goToLogin()
                    .then(function() {
                        return myUser.connect();
                    }).then(function() {
                        return myUser.goToCart();
                    }).then(function() {
                        return myUser.emptyCart();
                    }).then(function() {
                        return myUser.cartIsEmpty();
                    }).then(function() {
                        done();
                    }).catch(function(err) {
                        done(err);
                    });
                });
            });

            test.describe('Login functionalities', function() {
                test.it('Signing in, and then out', function(done) {
                    myUser
                    .isOn('HomePage')
                    .then(function(user) {
                        return user.goToLogin();
                    }).then(function(user) {
                        return user.connect();
                    }).then(function(user) {
                        return user.disconnect();
                    }).then(function() {
                        done();
                    }).catch(function(err) {
                        done(err);
                    });
                });

                test.it('Signing in, landing on dashboard', function(done) {
                    myUser
                    .isOn('HomePage')
                    .then(function(user) {
                        return user.goToLogin();
                    }).then(function(user) {
                        return user.connect();
                    }).then(function(user) {
                        return user.isOn('DashboardPage');
                    }).then(function() {
                        done();
                    }).catch(function(err) {
                        done(err);
                    });
                });
            });

            test.describe('Registering functionalities', function() {
                test.it('Registering denied if existing user', function(done) {
                    myUser.goToLogin()
                    .then(function(user) {
                        return Promise.all([
                            myUser.titleStartsWith('Ident'),
                            myUser.titleContains('dentifiant cl'),
                            myUser.titleEndsWith('ent'),
                            myUser.titleEquals('Identifiant client')
                        ]);
                    }).then(function() {
                        return myUser.goToRegister();
                    }).then(function() {
                        done();
                    }).catch(function(err) {
                        done(err);
                    });
                });

                // test.it('Registering with correct information', function(done) {
                //     Promise.resolve()
                //     .then(function() {
                //       return myUser.goToLogin();
                //     }).then(function(user) {
                //       return user.goToRegister();
                //     }).then(function() {
                //         done();
                //     }).catch(function(err) {
                //       done(err);
                //     });
                // });
            });

            test.it('Sample ProductPage operations', function(done) {
                Promise.resolve()
                .then(function() {
                    return myUser;
                }).then(function(user) {
                    return user.goToLogin();
                }).then(function(user) {
                    return user.connect();
                }).then(function(user) {
                    return user.goToProduct();
                }).then(function() {
                    done();
                }).catch(function(err) {
                    done(err);
                });
            });

            test.it('Sample CartPage operations', function(done) {
                Promise.resolve()
                .then(function() {
                    return myUser;
                }).then(function(user) {
                    return user.goToLogin();
                }).then(function(user) {
                    return myUser.connect();
                }).then(function(user) {
                    return myUser.goToCart();
                }).then(function() {
                    done();
                }).catch(function(err) {
                    done(err);
                });
            });

            test.it('Sample RegisterPage operations', function(done) {
                Promise.resolve()
                .then(function() {
                    return myUser;
                }).then(function(user) {
                    return user.goToLogin();
                }).then(function(user) {
                    return user.goToRegister();
                }).then(function() {
                    done();
                }).catch(function(err) {
                    done(err);
                });
            });

            test.it('Sample ForgotPassword operations', function(done) {
                Promise.resolve()
                .then(function() {
                    return myUser;
                }).then(function(user) {
                    return user.goToLogin();
                }).then(function(user) {
                    return user.forgetPassword();
                }).then(function() {
                    done();
                }).catch(function(err) {
                    done(err);
                });
            });

            test.it('Sample AdvancedSearch operations', function(done) {
                Promise.resolve()
                .then(function() {
                    return myUser;
                }).then(function(user) {
                    return user.goToAdvancedSearch();
                }).then(function() {
                    done();
                }).catch(function(err) {
                    done(err);
                });
            });

            test.it('Super basic assertions', function(done) {
                Promise.resolve()
                .then(function() {
                    return myUser.titleEquals(page_index.HomePage.title);
                }).then(function(user) {
                    return myUser.titleContains('cc');
                }).then(function() {
                    return myUser.urlEquals(page_index.HomePage.url);
                }).then(function() {
                    return myUser.urlContains('ev/rw');
                }).then(function() {
                    return myUser.isNotAuthenticated();
                }).then(function() {
                    return myUser.goToCart();
                }).then(function() {
                    return myUser.cartIsEmpty();
                }).then(function() {
                    return myUser.userIsOnPage('CartPage');
                }).then(function() {
                    done();
                }).catch(function(err) {
                    done(err);
                });
            })
        }); // end of suite 'Misc.'

    });

});
