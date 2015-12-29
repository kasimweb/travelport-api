<?php
namespace apis\travelport\classic\services;

class XmlSelect extends Service
{
	private $token = null;
	
	public function beginSession($profile)
	{
		$this->token = $this->client->call('BeginSession', ['Profile' => $profile]);
		
		return $this;
	}
	
	public function submitTerminalTransaction($transaction, $intermediateResponse = '')
	{
		return $this->client->call(
			'SubmitTerminalTransaction',
			[
				'Token'                => $this->token,
				'Request'              => $transaction,
				'intermediateResponse' => $intermediateResponse,
			]
		);
	}
	
	public function endSession()
	{
		$this->client->call('EndSession', ['Token' => $this->token]);
		
		return $this;
	}
}
