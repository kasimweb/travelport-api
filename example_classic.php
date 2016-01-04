<?php
use apis\travelport\classic\Galileo;

require __DIR__ . '/example_bootloader.php';

try
{
	$api = new Galileo($config['username'], $config['password'], Galileo::AREA_AMERICAS, Galileo::SERVICE_XML_SELECT, false);
	/** @var \apis\travelport\classic\services\XmlSelect $service */
	$service = $api->getService();
}
catch (SoapFault $f)
{
	echo 'Cannot load wsdl', PHP_EOL;
	echo $f, PHP_EOL;
	exit(1);
}

var_dump($api->getFunctions());

try
{
	// example code for http://testws.galileo.com/GWSSample/Help/GWSHelp/mergedprojects/TRANSACTIONHELP/1API_Dev_Notes/GalileoWebServicesHostSessions.pdf
	$service->beginSession($config['profile']);
	$response = $service->submitTerminalTransaction('A25MAYLAXLGA');
	$lines = str_split($response, 64); // 64 characters is the fixed length of one line in this terminal response
	
	foreach ($lines as $line)
	{
		echo $line, PHP_EOL;
	}
	
	$service->endSession();
}
catch (SoapFault $f)
{
	echo strval($f), PHP_EOL, PHP_EOL;
	echo 'Failed request:', PHP_EOL;
	echo $api->getLastRequest(), PHP_EOL;
}
