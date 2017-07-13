<?php	//	expressive, fast, flexible

namespace Twister;

return	[
			'patterns'	=>	[	//	official named patterns
								'alnum'		=>	'[A-Za-z0-9]',
								'word'		=>	'[A-Za-z0-9_]',
								'alpha'		=>	'[A-Za-z]',
								'digit'		=>	'[0-9]',
								'lower'		=>	'[a-z]',
								'xdigit'	=>	'[A-Fa-f0-9]',

								//	common
								'any'		=>	'[^/]+',
								'string'	=>	'[A-Za-z0-9_-]+',
								'id'		=>	'[0-9]+',
								'int'		=>	'[0-9]+',
								'num'		=>	'[0-9]+',
								'i'			=>	'[0-9]+',
								'd'			=>	'[0-9]',
								'x'			=>	'[A-Fa-f0-9]',
								'l'			=>	'[a-z]',
								'u'			=>	'[A-Z]',
								'U'			=>	'[A-Z]',
								'hex'		=>	'[A-Fa-f0-9]',
								'date'		=>	'\d{4}-\d{2}-\d{2}',
								'day'		=>	'0[1-9]|[12][0-9]|3[01]',
								'month'		=>	'0[1-9]|1[012]',
								'year'		=>	'[12][0-9]{3}',
								'uuid'		=>	'[A-Fa-f0-9]{8}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{12}',
							//	'action'	=>	'index|show|add|create|edit|update|remove|del|delete|view|item',
							],

			'routes'	=>	[	''						=>	[	null, '/',	function(Container $c) { (new Response($c, 'public', 'index'))->render(); }, 'home' ],
								'admin'					=>	[	[	'GET',	'', 'AdminController::indexAction', 'admin' ],
																[	'GET',	'asset-groups', 'AdminController::assetGroupsAction', 'admin_asset_groups' ],
																[	'POST',	'asset-groups', 'AdminController::assetGroupsPostAction', 'admin_asset_groups_post' ],
																[	'GET',	'asset-servers', 'AdminController::assetServersAction', 'admin_asset_servers' ],
																[	'GET',	'assets', 'AdminController::assetsAction', 'admin_assets' ],
																[	'GET',	'asset/{id}', 'AdminController::assetAction', 'admin_asset' ],
																[	'GET',	'article/{id}', 'AdminController::articleAction', 'admin_article' ],
																[	'GET',	'articles', 'AdminController::articlesAction', 'admin_articles' ],
																[	'GET',	'languages', 'AdminController::languagesAction', 'admin_languages' ],
																[	'GET',	'club/{id}', 'AdminController::clubAction', 'admin_club' ],
															],

								'login'					=>	[	null,	null,	'LoginController::loginAction',					'login' ],
								'register'				=>	[	null,	null,	'LoginController::registerAction',				'register' ],
								'forgot-password'		=>	[	null,	null,	'LoginController::forgotPasswordAction',		'forgot_password' ],
								'resend-verification'	=>	[	null,	null,	'LoginController::resendVerificationAction',	'resend_verification' ],
								'logout'				=>	[	null,	null,	'LoginController::logoutAction',				'logout' ],

								'robots.txt'			=>	'robots.txt',
								'sitemap.xml'			=>	'sitemap.xml'
							],

			404		=>	function(Container $c) { (new Response($c, 'public', '404'))->render(); }
		];
