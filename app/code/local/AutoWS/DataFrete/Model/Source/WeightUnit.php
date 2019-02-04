<?php
class AutoWS_DataFrete_Model_Source_WeightUnit
{

    public function toOptionArray()
    {
    	$helper = Mage::helper('adminhtml');

        return [
            ['value' => 'kg', 'label' => $helper->__('Kg')],
            ['value' => 'g',  'label' => $helper->__('g')],
        ];
    }
    
}