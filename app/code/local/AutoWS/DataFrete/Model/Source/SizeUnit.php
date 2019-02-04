<?php
class AutoWS_DataFrete_Model_Source_SizeUnit
{

    public function toOptionArray()
    {
    	$helper = Mage::helper('adminhtml');

        return [
            ['value' => 'cm', 'label' => $helper->__('cm')],
            ['value' => 'm',  'label' => $helper->__('m')],
        ];
    }
    
}