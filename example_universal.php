<?php
use apis\travelport\universal\Universal;

require __DIR__ . '/example_bootloader.php';

try
{
	// TODO Houston, we have a problem! Travelport Universal API requires username:password to be prefixed with "UniversalAPI/",
	// but SoapClient does not support such functionality.
	$api = new Universal($config['username'], $config['password'], Universal::AREA_AMERICAS, Universal::SERVICE_TERMINAL, false);
	/** @var \apis\travelport\universal\services\Terminal $service */
	$service = $api->getService();
}
catch (SoapFault $f)
{
	echo $f;
	die('Cannot load wsdl');
}

var_dump($api->getFunctions());

try
{
	/*$service->beginSession($config['profile']);
	$response = $service->submitTerminalTransaction('A25MAYLAXLGA');
	$lines = str_split($response, 64); // 64 characters is the fixed length of one line in this terminal response
	
	foreach ($lines as $line)
	{
		echo $line, PHP_EOL;
	}
	
	$service->endSession();*/
}
catch (SoapFault $f)
{
	echo strval($f), PHP_EOL, PHP_EOL;
	echo 'Failed request:', PHP_EOL;
	echo $api->getLastRequest(), PHP_EOL;
}
