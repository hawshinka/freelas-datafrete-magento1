<?php
class AutoWS_DataFrete_Helper_Slack extends Mage_Core_Helper_Abstract
{

	private $webhook = '';
	private $channel = '';
	private $sender  = 'AutoWS DATAFRETE';

	public function setConfig($webhook, $channel)
	{
		$this->webhook = $webhook;
		$this->channel = $channel;

		return $this;
	}

	public function sendNotification($title, $message, $info = null, $color = '#ff0000')
	{
		$data = [
			'username' 	  => $this->sender,
			'channel'  	  => $this->channel,
			'text'	   	  => '',
			'attachments' => [
				[
					'fallback' 		  => $message,
					'title'	   		  => $title,
					'text'	   		  => $message,
					'color'	   		  => $color,
					'attachment_type' => 'default',
				]
			]
		];

		if ($info !== null) {
			$data['attachments'][] = [
				'title' => 'Additional Info',
				'text'  => $info,
				'color'	=> '000000',
			];
		}

		$data = 'payload='. json_encode($data);

		try {
			$ch = curl_init($this->webhook);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			curl_close($ch);
		} catch (Exception $e) {
			$message = date('Y-m-d H:i:s') . ' ' . $e->getCode() . ': ' . $e->getMessage();
			Mage::log($message, null, 'autows_datafrete_exceptions.log');
		}

		return true;
	}

}