<?php
namespace apis\travelport;

use InvalidArgumentException;
use SoapClient;

abstract class Client
{
	const AREA_AMERICAS                  = 'americas';
	const AREA_ASIA_PACIFIC              = 'apac';
	const AREA_EUROPE_AFRICA_MIDDLE_EAST = 'emea';
	/** @var Service */
	private $service;
	/** @var SoapClient */
	private $client;
	
	public final function __construct($username, $password, $area, $service, $isProduction = true)
	{
		if (!in_array($area, [self::AREA_AMERICAS, self::AREA_ASIA_PACIFIC, self::AREA_EUROPE_AFRICA_MIDDLE_EAST]))
		{
			throw new InvalidArgumentException('Unsupported area ' . $area);
		}
		
		$availability = $this->getServiceAvailability($area, $service);
		
		if ($availability === -1) // no such service
		{
			throw new InvalidArgumentException('Unsupported service ' . $service . '; please use the SERVICE_* constants');
		}
		
		if ($availability === 0) // not available in the area
		{
			throw new InvalidArgumentException('Service ' . $service . ' is not available in area ' . $area);
		}
		
		list($class, $wsdl) = $this->getServiceData($service);
		$this->client = new SoapClient($this->buildWsdlUrl($area, $wsdl, $isProduction), self::getOptions($username, $password));
		$this->service = new $class($this);
	}
	
	public function getService()
	{
		return $this->service;
	}
	
	public function getFunctions()
	{
		return $this->client->__getFunctions();
	}
	
	public function getTypes()
	{
		return $this->client->__getTypes();
	}
	
	public function getLastRequest()
	{
		return $this->client->__getLastRequest();
	}
	
	public function getLastRequestHeaders()
	{
		return $this->client->__getLastRequestHeaders();
	}
	
	public function getLastResponse()
	{
		return $this->client->__getLastResponse();
	}
	
	public function getLastResponseHeaders()
	{
		return $this->client->__getLastResponseHeaders();
	}
	
	public function call($function, $parameters)
	{
		return $this->client->__soapCall($function, [$parameters]);
	}
	
	/**
	 * @param string $area
	 * @param string $service
	 * @return int -1 if service does not exist, 0 if service is not available in the area, 1 - if service is available
	 */
	protected abstract function getServiceAvailability($area, $service);
	
	/**
	 * @param string $service
	 * @return array [class, wsdl]
	 */
	protected abstract function getServiceData($service);
	
	/**
	 * @param string $area
	 * @param string $serviceWsdl
	 * @param bool $isProduction
	 * @return string
	 */
	protected abstract function buildWsdlUrl($area, $serviceWsdl, $isProduction);
	
	private static function getOptions($username, $password)
	{
		return [
			'soap_version'       => SOAP_1_1,
			'ssl_method'         => SOAP_SSL_METHOD_TLS,
			'cache_wsdl'         => WSDL_CACHE_BOTH,
			'connection_timeout' => 3,
			'compression'        => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
			'authentication'     => SOAP_AUTHENTICATION_BASIC,
			'login'              => $username,
			'password'           => $password,
			'exceptions'         => true,
			'trace'              => 1,
		];
	}
}
