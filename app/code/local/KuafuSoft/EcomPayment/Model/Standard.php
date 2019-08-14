<?php
class KuafuSoft_EcomPayment_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = KuafuSoft_EcomPayment_Model_Config::METHOD_STANDARD;
    protected $_formBlockType = 'ecompayment/standard_form';
    protected $_infoBlockType = 'ecompayment/payment_info';
    protected $_isInitializeNeeded      = true;
    protected $_canUseInternal          = false;
    protected $_canUseForMultishipping  = false;

    /**
     * Config instance
     * @var KuafuSoft_EcomPayment_Model_Config
     */
    protected $_config = null;

    /**
     * Whether method is available for specified currency
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode)
    {
        return $this->getConfig()->isCurrencyCodeSupported($currencyCode);
    }

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Create main block for standard form
     *
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('ecompayment/standard_form', $name)
            ->setMethod($this->_code)
            ->setPayment($this->getPayment())
            ->setTemplate('ecompayment/standard/form.phtml');

        return $block;
    }

    /**
     * Return Order place redirect url
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('ecompayment/standard/redirect', array('_secure' => true));
    }

    /**
     * Return form field array
     *
     * @return array
     */
    public function getStandardCheckoutFormFields()
    {
        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        $api = Mage::getModel('ecompayment/api_standard')->setConfigObject($this->getConfig());
        $api->setOrderId($orderIncrementId)
            ->setCurrencyCode($order->getBaseCurrencyCode())
            ->setRedirectorSuccess(Mage::getUrl('ecompayment/standard/success', array('order_id' => $order->getId())) . '?')
            ->setRedirectorFailed(Mage::getUrl('ecompayment/standard/fail', array('order_id' => $order->getId())) . '?');

        // export address
        $isOrderVirtual = $order->getIsVirtual();
        $address = $isOrderVirtual ? $order->getBillingAddress() : $order->getShippingAddress();
        if ($isOrderVirtual) {
            $api->setNoShipping(true);
        }
        else{
            $api->setAddress($address);
        }
        
        $api->setCustomerEmail($address->getEmail() ? $address->getEmail() : $order->getCustomerEmail());

        $grandTotal = $order->getBaseGrandTotal();
        $api->setAmount($grandTotal);
        $api->setNotes(Mage::helper('ecompayment')->__('Transaction#: %s', $orderIncrementId));

        $result = $api->getStandardCheckoutRequest();
        return $result;
    }

    /**
     * Instantiate state and set it to state object
     * @param string $paymentAction
     * @param Varien_Object
     */
    public function initialize($paymentAction, $stateObject)
    {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);
    }

    /**
     * Config instance getter
     * @return KuafuSoft_EcomPayment_Model_Config
     */
    public function getConfig()
    {
        if (null === $this->_config) {
            $params = array($this->_code);
            if ($store = $this->getStore()) {
                $params[] = is_object($store) ? $store->getId() : $store;
            }
            $this->_config = Mage::getModel('ecompayment/config', $params);
        }
        return $this->_config;
    }

    /**
     * Custom getter for payment configuration
     *
     * @param string $field
     * @param int $storeId
     * @return mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        return $this->getConfig()->$field;
    }
}
