<?php

class Wemage_Azpay_Transaction_BoletoController extends Mage_Core_Controller_Front_Action {

    public function postbackAction() {
        $azpay = Mage::getModel('azpay/api');
        $request = $this->getRequest();

        if ($request->isPost() && $request->getPost('TID') && $request->getPost('status') == 'APPROVED') {

            $orderId = Mage::helper('azpay')->getOrderIdByTransactionId($request->getPost('TID'));
            $order = Mage::getModel('sales/order')->load($orderId);
            if (!$order->canInvoice()) {
                Mage::throwException($this->__('The order does not allow creating an invoice.'));
            }

            $invoice = Mage::getModel('sales/service_order', $order)
                    ->prepareInvoice()
                    ->register()
                    ->pay();

            $invoice->setEmailSent(true);
            $invoice->getOrder()->setIsInProcess(true);

            $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder())
                    ->save();

            $invoice->sendEmail();

            $this->getResponse()->setBody('ok');
            return;
        }

        $this->_forward('404');
    }

}
