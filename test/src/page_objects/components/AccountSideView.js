"use strict";

let AccountSideView = module.exports = {
	locators: {
		// Left sidebar containing account categories listing
		sidebar_tab_links: '[class*="sel-sidebar-"][class*="-link"]', // 1..n
		sidebar_active_tab_link: '.current [class*="sel-sidebar-"][class*="-link"]', // 1

		sidebar_dashboard_link: '.sel-sidebar-account-link',
		sidebar_customer_data_link: '.sel-sidebar-account_edit-link',
		sidebar_addresses_book_link: '.sel-sidebar-address_book-link',
		sidebar_orders_history_link: '.sel-sidebar-orders-link',
		sidebar_billing_agreements_link: '.sel-sidebar-billing_agreements-link',
		sidebar_recurring_profiles_link: '.sel-sidebar-recurring_profiles-link',
		sidebar_personal_reviews_link: '.sel-sidebar-reviews-link',
		sidebar_wishlist_link: '.sel-sidebar-wishlist-link',
		// sidebar_customer_token_link: '.sel-sidebar-oauth customer tokens-link',
		sidebar_newsletter_link: '.sel-sidebar-newsletter-link',
		sidebar_downloadable_link: '.sel-sidebar-downloadable_products-link'
	},
	helpers: {
		genericFunc: function() {
			console.log('Account sidebar helper')
		},
        Accountsidebar_goDashboard: function() {
            return this.sidebar_dashboard_link.click();
        },
        Accountsidebar_goCustomerData: function() {
            return this.sidebar_customer_data_link.click();
        },
        Accountsidebar_goAddressesBook: function() {
            return this.sidebar_addresses_book_link.click();
        },
        Accountsidebar_goOrdersHistory: function() {
            return this.sidebar_orders_history_link.click();
        },
        Accountsidebar_goBillingAgreement: function() {
            return this.sidebar_billing_agreements_link.click();
        },
        Accountsidebar_goRecurringProfiles: function() {
            return this.sidebar_recurring_profiles_link.click();
        },
        Accountsidebar_goPersonalReviews: function() {
            return this.sidebar_personal_reviews_link.click();
        },
        Accountsidebar_goWishlist: function() {
            return this.sidebar_wishlist_link.click();
        }
	}
};
