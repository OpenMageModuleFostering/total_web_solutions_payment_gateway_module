<?php
class KuafuSoft_EcomPayment_Block_Standard_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $standard = Mage::getModel('ecompayment/standard');

        $form = new Varien_Data_Form();
        $form->setAction($standard->getConfig()->getPayPageUrl())
            ->setId('ecompayment_standard_checkout')
            ->setName('ecompayment_standard_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);
        foreach ($standard->getStandardCheckoutFormFields() as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }
        $html = '<html><body>';
        $html.= $this->__('You will be redirected to Ecompayment in a few seconds.');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("ecompayment_standard_checkout").submit();</script>';
        $html.= '</body></html>';

        return $html;
    }
}
