<?php
/*Written by Dylan Frankland - dylan.frankland+SnackCode@gmail.com*/
class SnackCode_OrderCancelEmailNotification_Model_Observer
{
    /*email function to alert of failed captures*/
    protected function sendEmail($subject,$body){
        $mail = Mage::getModel('core/email');
        $mail->setToName(Mage::getStoreConfig('trans_email/ident_support/name'));
        $mail->setToEmail(Mage::getStoreConfig('trans_email/ident_support/email'));
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
    
    public function orderCancelNotification($observer)
    {
        /*get all order details from the observer*/
        $event = $observer->getEvent();
        $order = $event->getOrder();
        $orderId = $event->getOrder()->getIncrementId();
        
        /*log every success or issue using email, mage log at /var/log/system.log, and also the order comments*/
        $subject = 'Order #'.$orderId.' Was Cancelled. Do not ship.';
        $body = 'Order #'.$orderId.' Was Cancelled. Do not ship.';
        $this->sendEmail($subject,$body);
        Mage::log($orderId.$subject,Zend_Log::DEBUG,'OrderCancelEmailNotification.log',true);
        $order->addStatusHistoryComment('Cancellation email sent to '.Mage::getStoreConfig('trans_email/ident_support/email'), true);
        
        return $this;
    }
}
?>