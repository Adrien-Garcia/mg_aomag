<?php
if ((string)Mage::getConfig()->getModuleConfig('Quadra_Extensions')->active != 'true')
{
	class Quadra_Extensions_Model_Sales_Order extends Mage_Sales_Model_Order{}
}
class Addonline_EmailCgv_Model_Emailcgv extends Quadra_Extensions_Model_Sales_Order
{
	const XML_PATH_EMAIL_CGV_TEMPLATE               = 'emailcgv/order/template';
	const XML_PATH_EMAIL_CGV_IDENTITY               = 'emailcgv/order/identity';
	const XML_PATH_EMAIL_CGV_COPY_TO                = 'emailcgv/order/copy_to';
	const XML_PATH_EMAIL_CGV_COPY_METHOD            = 'emailcgv/order/copy_method';
	const XML_PATH_EMAIL_CGV_ENABLED                = 'emailcgv/order/enabled';
	const XML_PATH_EMAIL_CGV_TYPE               	= 'emailcgv/order/typeofcontent';
	const XML_PATH_EMAIL_CGV_CMS_ID               	= 'emailcgv/order/idcmsagreement';

	/*public function _construct()
    {
        parent::_construct();
        $this->_init('emailcgv/emailcgv');
    }
	function getIdFieldName(){

	}*/
	function sendNewOrderEmail(){

		$storeId = $this->getStore()->getId();
		$enable = Mage::getStoreConfig(self::XML_PATH_EMAIL_CGV_ENABLED, $storeId);
		if($this->_9cd4777ae76310fd6977a5c559c51820()){
			parent::sendNewOrderEmail();
			return;
		}


		if ($enable){

			$copyTo = $this->_getEmails(self::XML_PATH_EMAIL_CGV_COPY_TO);
			$copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_CGV_COPY_METHOD, $storeId);
			$templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_CGV_TEMPLATE, $storeId);
			$type = Mage::getStoreConfig(self::XML_PATH_EMAIL_CGV_TYPE, $storeId);
			//FB::log($copyTo);
			$idContent = Mage::getStoreConfig(self::XML_PATH_EMAIL_CGV_CMS_ID, $storeId);
			$customerName = $this->getBillingAddress()->getName();


			$mailer = Mage::getModel('core/email_template_mailer');
			$emailInfo = Mage::getModel('core/email_info');

			$emailInfo->addTo($this->getCustomerEmail(), $customerName);



			//Récuperation du contenu des CGV
			if ($type=='cms') {
				$model = Mage::getModel('cms/page');
				$data = $model->load($idContent);
				if ($data) {
					$content = $data->getData();
					$texte = $content['content'];
					$title = $content['title'];
				} else{
					$texte = 'NO DATA CONTENT :'.$idContent.'<br />'.var_export();
					$title = '';
				}
			} else {
				$model = Mage::getModel('checkout/agreement');
				$data = $model->load($idContent);
				if ($data) {
					$content = $data->getData();
					if ($content['is_html']) {
						$texte = $content['content'];
					} else {
						$texte = '<p>'.nl2br($content['content']).'</p>';
					}
					$title = $content['name'];
				} else{
					$texte = ' NO DATA CONTENT :'.$idContent;
					$title = '';
				}

			}
			if ($copyTo && $copyMethod == 'bcc') {
				// Add bcc to customer email
				foreach ($copyTo as $email) {
					$emailInfo->addBcc($email);
				}
			}
			$mailer->addEmailInfo($emailInfo);
			// Email copies are sent as separated emails if their copy method is 'copy'
			if ($copyTo && $copyMethod == 'copy') {
				foreach ($copyTo as $email) {
					$emailInfo = Mage::getModel('core/email_info');
					$emailInfo->addTo($email);
					$mailer->addEmailInfo($emailInfo);
				}
			}




			try {
				//$mailer->addEmailInfo($emailInfo);
			// Set all required params and send emails
				$mailer->setSender(Mage::getStoreConfig(self::XML_PATH_EMAIL_CGV_IDENTITY, $storeId));
				$mailer->setStoreId($storeId);
				$mailer->setTemplateId($templateId);
				$mailer->setTemplateParams(array(
					'content'  => $this,
					'cgv_html' => $texte
				)
				);


				$mailer->send();
				//exit;
			} catch(Exception $e) {

			}
		}
		parent::sendNewOrderEmail();
	}

    public function _9cd4777ae76310fd6977a5c559c51820(){
		return true;
		if (Mage::getStoreConfig('addonline/licence/aomagento')) { return true; }
    	$key = 'e983cfc54f88c7114e99da95f5757df6'; if(md5(Mage::getStoreConfig('web/unsecure/base_url').$key.'Emailcgv')!=Mage::getStoreConfig('emailcgv/licence/serial')){
    		$severity=Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR;$title= "Vous devez renseigner une clé licence valide pour le module Emailcgv. Le module a été désactivé";$description= "Le module EmailCGV n'a pas une clé licence valide";	$date = date('Y-m-d H:i:s'); Mage::getModel('adminnotification/inbox')->parse(array(array('severity' => $severity,'date_added'=> $date,'title'=> $title,'description'   => $description,'url'=> '','internal'      => true)));
    		return false;
    	}else{$_unreadNotices = Mage::getModel('adminnotification/inbox')->getCollection()->getItemsByColumnValue('is_read', 0); foreach($_unreadNotices as $notice): if(strpos($notice->getData('description'),'Emailcgv')): $notice->setIsRead(1)->save();	endif;endforeach;return true;
    	}
    }

}