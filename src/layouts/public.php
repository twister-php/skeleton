<?php

/**
 *	`layouts` are something like a template pre-processor.
 *	Mainly handling common code and default options for all code that use this layout!
 */
return function($response)
{
	$response->title					=	$response->container->config['title'];

	$response->styles['font-awesome']	=	'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css';	//	http://fontawesome.io/

	$response->scripts['jquery']		=	'https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js';
	$response->scripts['bootstrap']		=	'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js';

	$response->elements['content']		=	null;
	$response->elements['header']		=	'header';
	$response->elements['footer']		=	'footer';

	$response->meta['Play']				=	'<link href="https://fonts.googleapis.com/css?family=Play:400,700" rel="stylesheet">';

	$response->renderer = function() use ($response)
	{
		echo
			'<!DOCTYPE html>' . PHP_EOL .
			'<html lang="' . $response->lang . '">' .
				'<head id="head>' .
					'<meta charset="utf-8" />' .
					'<meta http-equiv="x-ua-compatible" content="ie=edge" />' .
					'<meta http-equiv="content-type" content="text/html; charset=utf-8" />' .
					'<meta http-equiv="content-language" content="' . $response->lang . '" />' .
					'<title>' . htmlspecialchars($response->title) . '</title>' .
					'<meta name="description" content="' . htmlspecialchars($response->description) . '" />' .
					'<meta name="keywords" content="' . htmlspecialchars(implode(',', $response->keywords)) . '" />' .
					'<meta name="robots" content="' . $response->robots . '" />' .
					'<link rel="canonical" href="' . htmlspecialchars($response->canonical) . '" />' .
					'<link type="image/x-icon" href="/favicon.ico" rel="icon" />' .
					'<link type="image/x-icon" href="/favicon.ico" rel="shortcut icon" />' .
					'<meta name="viewport" content="width=device-width, initial-scale=1" />';
					foreach ($response->styles as &$url)
						echo '<link rel="stylesheet" type="text/css" href="' . $url . '" />';
					foreach ($response->scripts as &$url)
						echo '<script type="text/javascript" src="' . $url . '"></script>';
					foreach ($response->meta as &$meta)
						echo $meta;
					echo
				'</head>' .
				'<body>' .
					'<div class="container">';

						require __DIR__ . '/../elements/' . $response->elements['header']	. '/view.php';
						require __DIR__ . '/../elements/' . $response->elements['content'] . '/view.php';
						require __DIR__ . '/../elements/' . $response->elements['footer']	. '/view.php';

						echo
					'</div>' .
				'</body>' .
			'</html>';
	};
};
