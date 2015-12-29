<?php
namespace apis\travelport\classic\services;

use apis\travelport\classic\Galileo;

abstract class Service
{
	protected $client;
	
	public function __construct(Galileo $client)
	{
		$this->client = $client;
	}
}
