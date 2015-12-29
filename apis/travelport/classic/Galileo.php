<?php
namespace apis\travelport\classic;

use apis\travelport\classic\services\CruiseBooks;
use apis\travelport\classic\services\CruiseModifyCancel;
use apis\travelport\classic\services\CruiseShops;
use apis\travelport\classic\services\FlightInformation;
use apis\travelport\classic\services\ImageViewer;
use apis\travelport\classic\services\Itinerary;
use apis\travelport\classic\services\PNRQueueAndRetrieve;
use apis\travelport\classic\services\Service;
use apis\travelport\classic\services\TravelCodesTranslator;
use apis\travelport\classic\services\TripPlanner;
use apis\travelport\classic\services\XmlSelect;
use InvalidArgumentException;
use SoapClient;

/**
 * A class for SOAP calls to the Travelport Galileo Web Services APIs.
 *
 * @author Juris Sudmalis
 */
class Galileo
{
	const AREA_AMERICAS                   = 'americas';
	const AREA_ASIA_PACIFIC               = 'apac';
	const AREA_EUROPE_AFRICA_MIDDLE_EAST  = 'emea';
	const SERVICE_CRUISE_BOOKS            = 'CruiseBookService';
	const SERVICE_CRUISE_SHOPS            = 'CruiseShopService';
	const SERVICE_CRUISE_MODIFY_CANCEL    = 'CruiseModifyCancelService';
	const SERVICE_FLIGHT_INFORMATION      = 'FlightInformation';
	const SERVICE_TRAVEL_CODES_TRANSLATOR = 'TravelCodesTranslator';
	const SERVICE_TRIP_PLANNER            = 'TripPlanner';
	const SERVICE_ITINERARY               = 'Itinerary';
	const SERVICE_IMAGE_VIEWER            = 'ImageViewer';
	const SERVICE_PNR_QUEUE_AND_RETRIEVE  = 'PNRQueueAndRetrieveService';
	const SERVICE_XML_SELECT              = 'XMLSelect';
	private static $areas = [
		self::AREA_AMERICAS                  => [
			self::SERVICE_CRUISE_BOOKS,
			self::SERVICE_CRUISE_SHOPS,
			self::SERVICE_CRUISE_MODIFY_CANCEL,
			self::SERVICE_FLIGHT_INFORMATION,
			self::SERVICE_TRAVEL_CODES_TRANSLATOR,
			self::SERVICE_ITINERARY,
			self::SERVICE_IMAGE_VIEWER,
			self::SERVICE_PNR_QUEUE_AND_RETRIEVE,
			self::SERVICE_XML_SELECT,
		],
		self::AREA_ASIA_PACIFIC              => [
			self::SERVICE_CRUISE_BOOKS,
			self::SERVICE_CRUISE_SHOPS,
			self::SERVICE_CRUISE_MODIFY_CANCEL,
			self::SERVICE_FLIGHT_INFORMATION,
			// self::SERVICE_TRAVEL_CODES_TRANSLATOR, // not available in this area
			self::SERVICE_TRIP_PLANNER, // available only in this area
			self::SERVICE_ITINERARY,
			self::SERVICE_IMAGE_VIEWER,
			self::SERVICE_PNR_QUEUE_AND_RETRIEVE,
			self::SERVICE_XML_SELECT,
		],
		self::AREA_EUROPE_AFRICA_MIDDLE_EAST => [
			self::SERVICE_CRUISE_BOOKS,
			self::SERVICE_CRUISE_SHOPS,
			self::SERVICE_CRUISE_MODIFY_CANCEL,
			self::SERVICE_FLIGHT_INFORMATION,
			self::SERVICE_TRAVEL_CODES_TRANSLATOR,
			self::SERVICE_ITINERARY,
			self::SERVICE_IMAGE_VIEWER,
			self::SERVICE_PNR_QUEUE_AND_RETRIEVE,
			self::SERVICE_XML_SELECT,
		],
	];
	private static $services = [
		self::SERVICE_CRUISE_BOOKS            => CruiseBooks::class,
		self::SERVICE_CRUISE_SHOPS            => CruiseShops::class,
		self::SERVICE_CRUISE_MODIFY_CANCEL    => CruiseModifyCancel::class,
		self::SERVICE_FLIGHT_INFORMATION      => FlightInformation::class,
		self::SERVICE_TRAVEL_CODES_TRANSLATOR => TravelCodesTranslator::class,
		self::SERVICE_TRIP_PLANNER            => TripPlanner::class,
		self::SERVICE_ITINERARY               => Itinerary::class,
		self::SERVICE_IMAGE_VIEWER            => ImageViewer::class,
		self::SERVICE_PNR_QUEUE_AND_RETRIEVE  => PNRQueueAndRetrieve::class,
		self::SERVICE_XML_SELECT              => XmlSelect::class,
	];
	/** @var SoapClient */
	private $client;
	/** @var Service */
	private $service;
	
	public function __construct($username, $password, $area = self::AREA_AMERICAS, $service = self::SERVICE_XML_SELECT, $isProduction = true)
	{
		if (!isset(self::$areas[$area]))
		{
			throw new InvalidArgumentException('Unsupported area ' . $area);
		}
		
		if (!isset(self::$services[$service]))
		{
			throw new InvalidArgumentException('Unsupported service ' . $service);
		}
		
		if (!in_array($service, self::$areas[$area]))
		{
			throw new InvalidArgumentException('Service ' . $service . ' is not available in area ' . $area);
		}
		
		$options = [
			'soap_version'       => SOAP_1_2,
			'ssl_method'         => SOAP_SSL_METHOD_SSLv3,
			'cache_wsdl'         => ($isProduction ? WSDL_CACHE_BOTH : WSDL_CACHE_NONE),
			'connection_timeout' => 3,
			'compression'        => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
			'authentication'     => SOAP_AUTHENTICATION_BASIC,
			'login'              => $username,
			'password'           => $password,
			'exceptions'         => true,
			'trace'              => 1,
		];
		
		$this->client = new SoapClient(self::buildWsdlUrl($area, $service, $isProduction), $options);
		$this->service = new self::$services[$service]($this);
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
	
	private static function buildWsdlUrl($area, $service, $isProduction)
	{
		return 'https://' . $area . '.' . ($isProduction ? ''
			: 'copy-') . 'webservices.travelport.com/B2BGateway/service/' . $service . '?WSDL';
	}
}
