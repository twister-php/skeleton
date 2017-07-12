<?php

/**
 *	This config file overwrites values in my_server_name.php
 *	my_server_name.php is not loaded in code except here
 *	We load config files by lowercase(COMPUTERNAME).php
 *	The COMPUTERNAME value has been set to 'demo' in .htaccess
 *	Change the `my_server_name` name to your server's name
 */
return	array_merge(require __DIR__ . '/my_server_name.php',
		[
			'debug'			=>	true,
			'db'			=>	[	'host'		=>	'127.0.0.1',
									'username'	=>	'root',
									'password'	=>	'(-Y${K_c,Hg*3H|E2nu^XT?R3>68!@m:U]5eM&2#',
									'schema'	=>	'fcm',
									'port'		=>	'33306',
									'charset'	=>	'utf8mb4',
									'collation'	=>	'utf8mb4_unicode_ci',
									'debug'		=>	true
								]
		]);
