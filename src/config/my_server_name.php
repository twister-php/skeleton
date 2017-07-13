<?php

/**
 *	This `my_server_name.php` file is not loaded directly in code, except in demo.php
 *	'demo' represents the COMPUTERNAME value of a dev machine
 *	So config files are loaded by lowercase(COMPUTERNAME).php,
 *		which then loads and overwrites these default values
 *	For example, here we set 'debug' to false, but in our 'dev' machines we overwrite it
 *	Also, our dev machine(s) can overwrite the database connection values etc.
 */
return	[
			'debug'			=>	false,
			'maintenance'	=>	false,
			'email_from'	=>	'DEMO <noreply@example.com>',
			'charset'		=>	'UTF-8',
			'title'			=>	'Demo Application',
			'lang'			=>	'en',
			'languages'		=>	['en'],
			'paths'			=>	[	'layouts'	=>	__DIR__ . '/../layouts/',
									'elements'	=>	__DIR__ . '/../elements/'
								],
			'db'			=>	[	'host'		=>	'127.0.0.1',
									'username'	=>	'root',
									'password'	=>	'XR7mswQKfgsgdfhgregGEzAedxYUDqk6iHBCtI3pcN',
									'schema'	=>	'demo',
									'port'		=>	'3306',
									'charset'	=>	'utf8mb4',
									'collation'	=>	'utf8mb4_unicode_ci'
								],
			'google'		=>	[	'places'			=>	'34fgdrrdg343t4dfgdfg34tdfsdfghg3q4tghert',	//	Google Places API server key ... SECRET!!!
									'recaptcha-site'	=>	'hj345wehjqwereghjj56io54unety354khngdrg3',	//	`Use this in the HTML code your site serves to users.`
									'recaptcha-secret'	=>	'hj345wehjqwereghjj56io54unety354khngdrg3'	//	`Use this for communication between your site and Google. Be sure to keep it a secret.`
								]
		];
