<?php
class KuafuSoft_EcomPayment_Block_Standard_Form extends Mage_Payment_Block_Form
{
    /**
     * Payment method code
     * @var string
     */
    protected $_methodCode = KuafuSoft_EcomPayment_Model_Config::METHOD_STANDARD;

    /**
     * Config model instance
     *
     * @var KuafuSoft_EcomPayment_Model_Config
     */
    protected $_config;

    /**
     * Set template and redirect message
     */
    protected function _construct()
    {
        $this->_config = Mage::getModel('ecompayment/config')->setMethod($this->getMethodCode());
        $locale = Mage::app()->getLocale();
        $this->setTemplate('ecompayment/payment/redirect.phtml')
            ->setRedirectMessage(
                Mage::helper('ecompayment')->__('You will be redirected to Ecompayment website when you place an order.')
            )
            ->setMethodTitle('')
            ->setMethodLabelAfterHtml($this->_config->title);
        return parent::_construct();
    }

    /**
     * Payment method code getter
     * @return string
     */
    public function getMethodCode()
    {
        return $this->_methodCode;
    }
}
