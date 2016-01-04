<?php
namespace apis\travelport;

abstract class Service
{
	protected $client;
	
	public function __construct(Client $client)
	{
		$this->client = $client;
	}
	
	public function __call($name, array $arguments)
	{
		$this->client->call($name, $arguments);
	}
}
