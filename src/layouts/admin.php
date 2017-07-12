<?php

return function($response)
{
	$response->title					=	'Admin';

	$response->styles['font-awesome']	=	'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css';	//	http://fontawesome.io/

	$response->scripts['jquery']		=	'https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js';
	$response->scripts['bootstrap']		=	'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js';

	$response->elements['content']		=	null;
	$response->elements['navbar']		=	'admin/navbar';
	$response->elements['messages']		=	'admin/messages';

	$response->robots					=	'noindex,nofollow,noarchive';

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
					'<meta name="robots" content="' . $response->robots . '" />' .
					'<link type="image/x-icon" href="/favicon.ico" rel="icon" />' .
					'<link type="image/x-icon" href="/favicon.ico" rel="shortcut icon" />' .
					'<meta name="viewport" content="width=device-width, initial-scale=1" />';
					foreach ($response->styles as &$url)
						echo '<link rel="stylesheet" type="text/css" href="' . $url . '" />';
					foreach ($response->scripts as &$url)
						echo '<script type="text/javascript" src="' . $url . '"></script>';
					echo
					($response->script ? '<script type="text/javascript">' . $response->script . '</script>' : null) .
				'</head>' .
				'<body id="body">' .
					'<div class="container fluid" style="min-height: 600px">';
					//	'<div class="container-fluid">';
					//		'<div class="admin-navbar">';
								require __DIR__ . '/../elements/' . $response->elements['navbar'] . '/view.php';
								echo
					//		'</div>' .
							'<div class="admin-messages">';
								require __DIR__ . '/../elements/' . $response->elements['messages'] . '/view.php';
								echo
							'</div>' .
							'<div class="admin-content">';
								require __DIR__ . '/../elements/' . $response->elements['content'] . '/view.php';
								echo
							'</div>' .
					//	'</div>' .
					'</div>' .
				'</body>' .
			'</html>';
	};
};
