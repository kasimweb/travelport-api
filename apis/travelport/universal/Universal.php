<?php
namespace apis\travelport\universal;

use apis\travelport\Client;
use apis\travelport\universal\services\air\Air as Air_Air;
use apis\travelport\universal\services\air\Flight;
use apis\travelport\universal\services\GdsQueue;
use apis\travelport\universal\services\Hotel;
use apis\travelport\universal\services\Passive;
use apis\travelport\universal\services\Rail;
use apis\travelport\universal\services\System;
use apis\travelport\universal\services\Terminal;
use apis\travelport\universal\services\universal\Air as Universal_Air;
use apis\travelport\universal\services\universal\Hotel as Universal_Hotel;
use apis\travelport\universal\services\universal\ProviderReservationDisplay;
use apis\travelport\universal\services\universal\ProviderReservationDivide;
use apis\travelport\universal\services\universal\Rail as Universal_Rail;
use apis\travelport\universal\services\universal\SavedTripCreate;
use apis\travelport\universal\services\universal\SavedTripDelete;
use apis\travelport\universal\services\universal\SavedTripModify;
use apis\travelport\universal\services\universal\SavedTripRetrieve;
use apis\travelport\universal\services\universal\SavedTripSearch;
use apis\travelport\universal\services\universal\ScheduleChange;
use apis\travelport\universal\services\universal\UniversalRecord;
use apis\travelport\universal\services\universal\UniversalRecordHistorySearch;
use apis\travelport\universal\services\universal\Vehicle as Universal_Vehicle;
use apis\travelport\universal\services\UniversalProfile;
use apis\travelport\universal\services\UniversalRecordReport;
use apis\travelport\universal\services\util\AgencyFee;
use apis\travelport\universal\services\util\BrandedFare;
use apis\travelport\universal\services\util\CalculateTax;
use apis\travelport\universal\services\util\ContentProviderRetrieve;
use apis\travelport\universal\services\util\CreditCardAuth;
use apis\travelport\universal\services\util\CurrencyConversion;
use apis\travelport\universal\services\util\DocumentTransmission;
use apis\travelport\universal\services\util\FindEmployeesOnFlight;
use apis\travelport\universal\services\util\Mco;
use apis\travelport\universal\services\util\Mct;
use apis\travelport\universal\services\util\ReferenceDataLookup;
use apis\travelport\universal\services\util\ReferenceDataUpdate;
use apis\travelport\universal\services\util\Reporting;
use apis\travelport\universal\services\util\UpsellAdmin;
use apis\travelport\universal\services\util\UpsellAdminSearch;
use apis\travelport\universal\services\util\Util;
use apis\travelport\universal\services\Vehicle;

/**
 * A class for SOAP calls to the Travelport Universal API.
 *
 * @author Juris Sudmalis
 */
class Universal extends Client
{
	const SERVICE_GDS_QUEUE                                  = 'GdsQueueService';
	const SERVICE_HOTEL                                      = 'HotelService';
	const SERVICE_PASSIVE                                    = 'PassiveService';
	const SERVICE_RAIL                                       = 'RailService';
	const SERVICE_UNIVERSAL_RECORD_REPORT                    = 'UniversalRecordReportService';
	const SERVICE_SYSTEM                                     = 'SystemService';
	const SERVICE_TERMINAL                                   = 'TerminalService';
	const SERVICE_UNIVERSAL_PROFILE                          = 'UProfileService';
	const SERVICE_VEHICLE                                    = 'VehicleService';
	const SERVICE_AIR__AIR                                   = 'Air_AirService';
	const SERVICE_AIR__FLIGHT                                = 'Air_FlightService';
	const SERVICE_UNIVERSAL__PROVIDER_RESERVATION_DISPLAY    = 'Universal_ProviderReservationDisplayService';
	const SERVICE_UNIVERSAL__PROVIDER_RESERVATION_DIVIDE     = 'Universal_ProviderReservationDivideService';
	const SERVICE_UNIVERSAL__SAVED_TRIP_CREATE               = 'Universal_SavedTripCreateService';
	const SERVICE_UNIVERSAL__SAVED_TRIP_DELETE               = 'Universal_SavedTripDeleteService';
	const SERVICE_UNIVERSAL__SAVED_TRIP_MODIFY               = 'Universal_SavedTripModifyService';
	const SERVICE_UNIVERSAL__SAVED_TRIP_RETRIEVE             = 'Universal_SavedTripRetrieveService';
	const SERVICE_UNIVERSAL__SAVED_TRIP_SEARCH               = 'Universal_SavedTripSearchService';
	const SERVICE_UNIVERSAL__SCHEDULE_CHANGE                 = 'Universal_ScheduleChangeService';
	const SERVICE_UNIVERSAL__UNIVERSAL_RECORD_HISTORY_SEARCH = 'Universal_UniversalRecordHistorySearchService';
	const SERVICE_UNIVERSAL__UNIVERSAL_RECORD                = 'Universal_UniversalRecordService';
	const SERVICE_UNIVERSAL__AIR                             = 'Universal_AirService';
	const SERVICE_UNIVERSAL__HOTEL                           = 'Universal_HotelService';
	const SERVICE_UNIVERSAL__RAIL                            = 'Universal_RailService';
	const SERVICE_UNIVERSAL__VEHICLE                         = 'Universal_VehicleService';
	const SERVICE_UTIL__AGENCY_FEE                           = 'Util_AgencyFeeService';
	const SERVICE_UTIL__BRANDED_FARE                         = 'Util_BrandedFareService';
	const SERVICE_UTIL__CREDIT_CARD_AUTH                     = 'Util_UtilCreditCardAuthService';
	const SERVICE_UTIL__CURRENCY_CONVERSION                  = 'Util_CurrencyConversionService';
	const SERVICE_UTIL__REFERENCE_DATA_LOOKUP                = 'Util_ReferenceDataLookupService';
	const SERVICE_UTIL__UTIL                                 = 'Util_UtilService';
	const SERVICE_UTIL__REFERENCE_DATA_UPDATE                = 'Util_ReferenceDataUpdateService';
	const SERVICE_UTIL__FIND_EMPLOYEES_ON_FLIGHT             = 'Util_FindEmployeesOnFlightService';
	const SERVICE_UTIL__UPSELL_ADMIN_SEARCH                  = 'Util_UpsellAdminSearchService';
	const SERVICE_UTIL__UPSELL_ADMIN                         = 'Util_UpsellAdminService';
	const SERVICE_UTIL__CALCULATE_TAX                        = 'Util_CalculateTaxService';
	const SERVICE_UTIL__DOCUMENT_TRANSMISSION                = 'Util_DocumentTransmissionService';
	const SERVICE_UTIL__MCT                                  = 'Util_MctService';
	const SERVICE_UTIL__MCO                                  = 'Util_McoService';
	const SERVICE_UTIL__CONTENT_PROVIDER_RETRIEVE            = 'Util_ContentProviderRetrieveService';
	const SERVICE_UTIL__REPORTING                            = 'Util_ReportingService';
	/**
	 * @see https://support.travelport.com/webhelp/uapi/Content/Getting_Started/Easy_Overview/Complete_Services_List.htm
	 */
	private static $services = [
		self::SERVICE_GDS_QUEUE                                  => GdsQueue::class,
		self::SERVICE_HOTEL                                      => Hotel::class,
		self::SERVICE_PASSIVE                                    => Passive::class,
		self::SERVICE_RAIL                                       => Rail::class,
		self::SERVICE_UNIVERSAL_RECORD_REPORT                    => UniversalRecordReport::class,
		self::SERVICE_SYSTEM                                     => System::class,
		self::SERVICE_TERMINAL                                   => Terminal::class,
		self::SERVICE_UNIVERSAL_PROFILE                          => UniversalProfile::class,
		self::SERVICE_VEHICLE                                    => Vehicle::class,
		self::SERVICE_AIR__AIR                                   => Air_Air::class,
		self::SERVICE_AIR__FLIGHT                                => Flight::class,
		self::SERVICE_UNIVERSAL__PROVIDER_RESERVATION_DISPLAY    => ProviderReservationDisplay::class,
		self::SERVICE_UNIVERSAL__PROVIDER_RESERVATION_DIVIDE     => ProviderReservationDivide::class,
		self::SERVICE_UNIVERSAL__SAVED_TRIP_CREATE               => SavedTripCreate::class,
		self::SERVICE_UNIVERSAL__SAVED_TRIP_DELETE               => SavedTripDelete::class,
		self::SERVICE_UNIVERSAL__SAVED_TRIP_MODIFY               => SavedTripModify::class,
		self::SERVICE_UNIVERSAL__SAVED_TRIP_RETRIEVE             => SavedTripRetrieve::class,
		self::SERVICE_UNIVERSAL__SAVED_TRIP_SEARCH               => SavedTripSearch::class,
		self::SERVICE_UNIVERSAL__SCHEDULE_CHANGE                 => ScheduleChange::class,
		self::SERVICE_UNIVERSAL__UNIVERSAL_RECORD_HISTORY_SEARCH => UniversalRecordHistorySearch::class,
		self::SERVICE_UNIVERSAL__UNIVERSAL_RECORD                => UniversalRecord::class,
		self::SERVICE_UNIVERSAL__AIR                             => Universal_Air::class,
		self::SERVICE_UNIVERSAL__HOTEL                           => Universal_Hotel::class,
		self::SERVICE_UNIVERSAL__RAIL                            => Universal_Rail::class,
		self::SERVICE_UNIVERSAL__VEHICLE                         => Universal_Vehicle::class,
		self::SERVICE_UTIL__AGENCY_FEE                           => AgencyFee::class,
		self::SERVICE_UTIL__BRANDED_FARE                         => BrandedFare::class,
		self::SERVICE_UTIL__CREDIT_CARD_AUTH                     => CreditCardAuth::class,
		self::SERVICE_UTIL__CURRENCY_CONVERSION                  => CurrencyConversion::class,
		self::SERVICE_UTIL__REFERENCE_DATA_LOOKUP                => ReferenceDataLookup::class,
		self::SERVICE_UTIL__UTIL                                 => Util::class,
		self::SERVICE_UTIL__REFERENCE_DATA_UPDATE                => ReferenceDataUpdate::class,
		self::SERVICE_UTIL__FIND_EMPLOYEES_ON_FLIGHT             => FindEmployeesOnFlight::class,
		self::SERVICE_UTIL__UPSELL_ADMIN_SEARCH                  => UpsellAdminSearch::class,
		self::SERVICE_UTIL__UPSELL_ADMIN                         => UpsellAdmin::class,
		self::SERVICE_UTIL__CALCULATE_TAX                        => CalculateTax::class,
		self::SERVICE_UTIL__DOCUMENT_TRANSMISSION                => DocumentTransmission::class,
		self::SERVICE_UTIL__MCT                                  => Mct::class,
		self::SERVICE_UTIL__MCO                                  => Mco::class,
		self::SERVICE_UTIL__CONTENT_PROVIDER_RETRIEVE            => ContentProviderRetrieve::class,
		self::SERVICE_UTIL__REPORTING                            => Reporting::class,
	];
	
	protected final function getServiceAvailability($area, $service)
	{
		// all Universal API services are available in all regions
		return true;
	}
	
	protected final function getServiceData($service)
	{
		$separator = strrpos($service, '_');
		$name = substr($service, ($separator === false) ? 0 : $separator + 1);
		return [self::$services[$service], $name];
	}
	
	protected final function buildWsdlUrl($area, $serviceWsdl, $isProduction)
	{
		// https://support.travelport.com/webhelp/uapi/Content/Getting_Started/Endpoints_Services.htm
		return 'https://' . $area . '.universal-api' . ($isProduction ? '' : '.pp') //
		. '.travelport.com/B2BGateway/connect/uAPI/' . $serviceWsdl;
	}
}
