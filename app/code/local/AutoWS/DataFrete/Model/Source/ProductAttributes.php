<?php
class AutoWS_DataFrete_Model_Source_ProductAttributes
{

    public function toOptionArray()
    {
    	$helper	   = Mage::helper('adminhtml');
		$results   = [];
		$results[] = ['value' => '', 'label' => $helper->__('')];

		$productAttributes = Mage::getResourceModel('catalog/product_attribute_collection');
		foreach ($productAttributes as $productAttribute) {
    		$value = $productAttribute->getAttributeCode();
    		$label = ($productAttribute->getFrontendLabel()) ? $productAttribute->getFrontendLabel() : $value;

    		$results[] = ['value' => $value, 'label' => $helper->__($label)];
		}

		return $results;
    }

}