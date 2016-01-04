<?php
namespace apis\travelport\classic;

use apis\travelport\classic\services\CruiseBooks;
use apis\travelport\classic\services\CruiseModifyCancel;
use apis\travelport\classic\services\CruiseShops;
use apis\travelport\classic\services\FlightInformation;
use apis\travelport\classic\services\ImageViewer;
use apis\travelport\classic\services\Itinerary;
use apis\travelport\classic\services\PNRQueueAndRetrieve;
use apis\travelport\classic\services\TravelCodesTranslator;
use apis\travelport\classic\services\TripPlanner;
use apis\travelport\classic\services\XmlSelect;
use apis\travelport\Client;

/**
 * A class for SOAP calls to the Travelport Galileo Web Services APIs.
 *
 * @author Juris Sudmalis
 */
class Galileo extends Client
{
	const SERVICE_CRUISE_BOOKS         = 'CruiseBookService';
	const SERVICE_CRUISE_SHOPS         = 'CruiseShopService';
	const SERVICE_CRUISE_MODIFY_CANCEL = 'CruiseModifyCancelService';
	const SERVICE_FLIGHT_INFORMATION   = 'FlightInformation';
	/** Note: this service is not available in the Asia and Pacific region! */
	const SERVICE_TRAVEL_CODES_TRANSLATOR = 'TravelCodesTranslator';
	/** Note: this service is only available in the Asia and Pacific region! */
	const SERVICE_TRIP_PLANNER           = 'TripPlanner';
	const SERVICE_ITINERARY              = 'Itinerary';
	const SERVICE_IMAGE_VIEWER           = 'ImageViewer';
	const SERVICE_PNR_QUEUE_AND_RETRIEVE = 'PNRQueueAndRetrieveService';
	const SERVICE_XML_SELECT             = 'XMLSelect';
	private $services = [
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
	
	protected final function getServiceAvailability($area, $service)
	{
		if (!isset($this->services[$service]))
		{
			return -1;
		}
		
		if ($area === self::AREA_ASIA_PACIFIC)
		{
			// Travel codes translator is not available in Asia/Pacific region;
			// instead, Trip Planner service is only available in this region.
			return (($service === self::SERVICE_TRAVEL_CODES_TRANSLATOR) ? 0 : 1);
		}
		
		// Trip Planner is only available in Asia/Pacific region.
		return (($service === self::SERVICE_TRIP_PLANNER) ? 0 : 1);
	}
	
	protected final function getServiceData($service)
	{
		// for Galileo services, the service name is the WSDL
		return [$this->services[$service], $service];
	}
	
	protected final function buildWsdlUrl($area, $serviceWsdl, $isProduction)
	{
		return 'https://' . $area . '.' . ($isProduction ? '' : 'copy-') //
		. 'webservices.travelport.com/B2BGateway/service/' . $serviceWsdl . '?WSDL';
	}
}
