<?php
use apis\travelport\classic\Galileo;

spl_autoload_register(function ($name)
{
	$file = __DIR__ . '/' . str_replace('\\', '/', $name) . '.php';
	
	if (file_exists($file))
	{
		require $file;
	}
}, true, true);

try
{
	$username = 'my-username';
	$password = 'my-password';
	
	$api = new Galileo($username, $password, Galileo::AREA_AMERICAS, Galileo::SERVICE_XML_SELECT, false);
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
	$service->beginSession('my-profile');
	$service->endSession();
}
catch (SoapFault $f)
{
	echo strval($f), PHP_EOL, PHP_EOL;
	echo 'Failed request:', PHP_EOL;
	echo $api->getLastRequest(), PHP_EOL;
}
