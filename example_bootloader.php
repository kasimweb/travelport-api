<?php
spl_autoload_register(
	function ($name)
	{
		$file = __DIR__ . '/' . str_replace('\\', '/', $name) . '.php';
		
		if (file_exists($file))
		{
			require $file;
		}
	},
	true,
	true
);

if (!file_exists(__DIR__ . '/config.php'))
{
	echo 'Please create config.php';
	exit(1);
}

$config = require __DIR__ . '/config.php';

if (empty($config) || !isset($config['username'], $config['password'], $config['profile']))
{
	echo 'Missing configuration';
	exit(1);
}
