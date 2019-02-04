<?php
class AutoWS_DataFrete_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function prepareProductsList($cartProducts)
    {
        $configPath      = 'carriers/autows_datafrete/';         
        $heightAttribute = Mage::getStoreConfig($configPath. 'heightAttribute');
        $widthAttribute  = Mage::getStoreConfig($configPath. 'widthAttribute');
        $lengthAttribute = Mage::getStoreConfig($configPath. 'lengthAttribute');
        $weightAttribute = Mage::getStoreConfig($configPath. 'weightAttribute');
        $sizeUnit        = Mage::getStoreConfig($configPath. 'sizeUnit');
        $weightUnit      = Mage::getStoreConfig($configPath. 'weightUnit');

        $productsList = [];
        foreach ($cartProducts as $cartProduct) {
            $changedQty = false;
            if (!empty($cartProduct->getParentItemId())) {
                foreach ($cartProducts as $foundParent) {
                    if ($foundParent->getId() == $cartProduct->getParentItemId()) {
                        $changedQty = true;
                        $oldQty     = $cartProduct->getQty();
                        $cartProduct->setQty($foundParent->getQty());
                    }
                }
            }

            $product = Mage::getModel('catalog/product')->load($cartProduct->getProduct()->getId());
            $height  = empty($heightAttribute) ? 0 : $product->getData($heightAttribute);
            $width   = empty($widthAttribute)  ? 0 : $product->getData($widthAttribute);
            $length  = empty($lengthAttribute) ? 0 : $product->getData($lengthAttribute);
            $weight  = empty($weightAttribute) ? 0 : $product->getData($weightAttribute);

            if ($sizeUnit == 'cm') {
                $height = $height / 100;
                $width  = $width  / 100;
                $length = $length / 100;
            }

            if ($weightUnit == 'g') {
                $weight = $weight / 1000;
            }

            $productsList[] = [
                'sku'         => $product->getSku(),
                'descricao'   => $product->getName(),
                'altura'      => (double) $height,
                'largura'     => (double) $width,
                'comprimento' => (double) $length,
                'peso'        => (double) $weight,
                'preco'       => (double) $cartProduct->getPrice(),
                'qtd'         => (double) $cartProduct->getQty(),
                'volume'      => 1,
            ];

            if ($changedQty === true) {
                $cartProduct->setQty($oldQty);
            }
        }

        return $productsList;
    }

    public function prepareAdditionalInformation()
    {
        $additionalInformation = [
            'exibir_resultados' => (int) 0,
            'tipo_ordenacao'    => (int) Mage::getStoreConfig('carriers/autows_datafrete/displayOrder'),
            'cod_empresa'       => '001',
            'doc_empresa'       => $this->getOnlyNumbers(Mage::getStoreConfig('carriers/autows_datafrete/accessTaxvat')),
        ];

        return $additionalInformation;
    }

    public function getOnlyNumbers($str)
    {
        return preg_replace('/[^0-9]/s', '', $str);
    }

	public function buildShippingMethodName($title)
	{
		$title 		  = trim(strip_tags((function_exists('mb_strtolower')) ? mb_strtolower($title, 'UTF-8') : strtolower($title)));
		$arraySearch  = array('á','à','ã','â','ä','é','è','ẽ','ê','ë','í','ì','ĩ','î','ï','ó','ò','õ','ô','ö','ú','ù','ũ','û','ü');
	    $arrayReplace = array('a','a','a','a','a','e','e','e','e','e','i','i','i','i','i','o','o','o','o','o','u','u','u','u','u');
	    $arrayStrip   = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]", "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;", "â€”", "â€“", ",", "<", ".", ">", "/", "?");

	    $title = str_replace(' ', '-', $title);
	    $title = str_replace($arraySearch, $arrayReplace, $title);
	    $title = str_replace($arrayStrip, '', $title);
	    $title = preg_replace('/\s+/', '-', $title);
	    $title = preg_replace('/[^a-zA-Z0-9\-]/', '', $title);

	    return $title;
	}

}