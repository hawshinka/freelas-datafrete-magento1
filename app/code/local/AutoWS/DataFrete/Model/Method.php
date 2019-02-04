<?php
class AutoWS_DataFrete_Model_Method extends Mage_Shipping_Model_Carrier_Abstract
{

	protected $_code = 'autows_datafrete';

	public function getCode()
	{
		return $this->_code;
	}

	public function isTrackingAvailable()
	{
		return true;
	}

	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		if ($this->getConfigFlag('active') === 0)
			return false;

		$productsList 		   = Mage::helper('autows_datafrete')->prepareProductsList($request->getAllItems());
		$additionalInformation = Mage::helper('autows_datafrete')->prepareAdditionalInformation();

		try {
			$wsResults = Mage::helper('autows_datafrete/api')->callWebservice($request->getDestPostcode(), $productsList, $additionalInformation);

			if ($wsResults['codigo_retorno'] != 1)
				throw new Exception($wsResults['data'], $wsResults['codigo_retorno']);

			$deadlineMessage = trim(Mage::getStoreConfig('carriers/autows_datafrete/msgShippingDeadline'));
			$methodsResults	 = Mage::getModel('autows_datafrete/rate_result');

			foreach($wsResults['data'] as $shippingMethod) {
				if ($shippingMethod['prazo'] === null)
					$shippingMethod['prazo'] = (int) Mage::getStoreConfig('carriers/autows_datafrete/defaultShippingDeadline');

				$method = Mage::getModel('shipping/rate_result_method');
				$method->setPrice((double) $shippingMethod['valor_frete']);
				$method->setCost((double) $shippingMethod['valor_frete']);
				$method->setCarrier($this->getCode());

				$method->setCarrierTitle(Mage::getStoreConfig('carriers/autows_datafrete/title'));
				$method->setMethodTitle($shippingMethod['descricao']);
				$method->setMethod(Mage::helper('autows_datafrete')->buildShippingMethodName($shippingMethod['descricao']));

				if ($deadlineMessage)
					$method->setMethodTitle($method->getMethodTitle() . ' ' . sprintf($deadlineMessage, (string) $shippingMethod['prazo']));

				$methodsResults->append($method);
			}

			return $methodsResults;
		} catch (Exception $e) {
			$slackHelper = Mage::helper('autows_datafrete/slack');

			$slackHelper->setConfig(
				Mage::getStoreConfig('carriers/autows_datafrete/slackWebhook'),
				Mage::getStoreConfig('carriers/autows_datafrete/slackChannel')
			);

			$info  = "Destination Postcode: " . $request->getDestPostcode();
			$info .= "\n\nProducts List:\n" . json_encode($productsList);
			$info .= "\n\nAdditional Information:\n" . json_encode($additionalInformation);

			$slackHelper->sendNotification('Magento Module Warning', ($e->getCode() . ': ' . $e->getMessage()), $info);

			$message = date('Y-m-d H:i:s') . ' ' . $e->getCode() . ': ' . $e->getMessage();
			Mage::log($message, null, 'autows_datafrete_exceptions.log');

			return false;
		}

	}

}