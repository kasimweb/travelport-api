<?php
namespace apis\travelport\classic\services;

use apis\travelport\Service;

class XmlSelect extends Service
{
	private $token = null;
	
	public function beginSession($hostAccessProfile)
	{
		$response = $this->client->call('BeginSession', ['Profile' => $hostAccessProfile]);
		$this->token = $response->BeginSessionResult;
		
		return $this;
	}
	
	public function submitTerminalTransaction($transaction, $intermediateResponse = '')
	{
		if (empty($this->token))
		{
			throw new \ErrorException('Terminal transactions require a session; do not forget to end the session afterwards!');
		}
		
		$response = $this->client->call(
			'SubmitTerminalTransaction',
			[
				'Token'                => $this->token,
				'Request'              => $transaction,
				'intermediateResponse' => $intermediateResponse,
			]
		);
		
		return $response->SubmitTerminalTransactionResult;
	}
	
	public function endSession()
	{
		$this->client->call('EndSession', ['Token' => $this->token]);
		
		return $this;
	}
}
