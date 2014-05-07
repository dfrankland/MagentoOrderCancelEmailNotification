<?php
/*Written by Dylan Frankland - dylan.frankland+SnackCode@gmail.com*/
class SnackCode_OrderCancelEmailNotification_Model_Observer
{
    public function orderCancelNotification($observer)
    {
        /*get all order details from the observer*/
        $event = $observer->getEvent();
        $order = $event->getOrder();
        $orderId = $event->getOrder()->getIncrementId();
        
        /*email function to alert of failed captures*/
        function sendEmail($subject,$body){
            $mail = Mage::getModel('core/email');
            $mail->setToName(Mage::getStoreConfig('trans_email/ident_support/name'));
            $mail->setToEmail('dylan@prosoundcommunications.com');//(Mage::getStoreConfig('trans_email/ident_support/email'));
            $mail->setFromEmail(Mage::getStoreConfig('trans_email/ident_support/email'));
            $mail->setFromName(Mage::app()->getStore()->getName());
            $mail->setBody($body);
            $mail->setSubject($subject);
            $mail->setType('html');// You can use 'html' or 'text'
            try
            {
                $mail->send();
            }
            catch (Exception $e)
            {
                Mage::logException('Magento Order: Cancel Notification // Exception message: '.$e->getMessage());
            }
        }
        
        /*log every success or issue using email, mage log at /var/log/system.log, and also the order comments*/
        $subject = 'Order #'.$orderId.' Was Cancelled. Do not ship.';
        $body = '<h1>Please click <a href="http://192.168.1.72:8069/?db=PCI#page=0&limit=80&view_type=list&model=sale.order&menu_id=297&action=374">here</a> to find the order.</h1>';
        sendEmail($subject,$body);
        Mage::log($oid.$subject,Zend_Log::DEBUG,'OrderCancelEmailNotification.log',true);
        $order->addStatusHistoryComment($subject.$body, true);
        
        return $this;
    }
}
?>