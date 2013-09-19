<?php 
require '../../app/Mage.php';
// Initialize Magento
Mage::app('admin');

Mage::app()->getRequest()->setBasePath('/');

// This initalizes the session, using 'adminhtml' as the session name.
// Just ignore the returned Mage_Core_Model_Session instance
Mage::getSingleton('core/session', array('name' => 'adminhtml'));
// Get a singleton instance of the Mage_Admin_Model_Session class
// This is just the 'admin' namespace of the current session. (adminhtml in this case)
$session = Mage::getSingleton('admin/session');
//var_dump($session);
//var_dump(Mage::getSingleton('admin/session')->getUser() );
// Use the 'admin/session' object to check loggedIn status
$sesId = isset($_COOKIE['adminhtml']) ? $_COOKIE['adminhtml'] : false ;

if ( !$session->isLoggedIn() ) {
	header('HTTP/1.0 404 Not Found');
	exit;
}

