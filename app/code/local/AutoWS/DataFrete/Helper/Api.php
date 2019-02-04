<?php
class AutoWS_DataFrete_Helper_Api extends Mage_Core_Helper_Abstract
{

    public function callWebservice($destinationPostcode, $productsList, $additionalInformation)
    {
        $wsdlUrl             = Mage::getStoreConfig('carriers/autows_datafrete/wsdlUrl');
        $accessLogin         = Mage::getStoreConfig('carriers/autows_datafrete/accessLogin');
        $accessPassword      = Mage::getStoreConfig('carriers/autows_datafrete/accessPassword');
        $accessKey           = Mage::getStoreConfig('carriers/autows_datafrete/accessKey');

        $helper              = Mage::helper('autows_datafrete');
        $collectPostcode     = $helper->getOnlyNumbers(Mage::getStoreConfig('carriers/autows_datafrete/collectPostcode'));
        $destinationPostcode = $helper->getOnlyNumbers($destinationPostcode);

        $soapClient = new SoapClient($wsdlUrl, [
            'trace' => true,
        ]);

        $results = $soapClient->getValorFrete($accessLogin, $accessPassword, $accessKey,
            $collectPostcode,
            $destinationPostcode,
            json_encode($productsList),
            json_encode($additionalInformation)
        );

        return json_decode($results, true);
    }

}