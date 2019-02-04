<?php
class AutoWS_DataFrete_Model_Source_DisplayOrder
{

    public function toOptionArray()
    {
    	$helper = Mage::helper('adminhtml');

        return [
            ['value' => 0, 'label' => $helper->__('Price')],
            ['value' => 1, 'label' => $helper->__('Deadline')],
        ];
    }
    
}