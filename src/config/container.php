<?php

namespace Twister;

/**
 *	Pre-configured default properties for the master Container
 *	This is the default property array of the main Container class. It features some common properties, object instances and factory methods.
 *	The Container object itself essentially takes the place of any `global` variables. eg. configuration files, database connection etc.
 *	The great thing about this technique is that ALL these objects/properties are lazy loaded! ie. they are not instantiated until we need/call them!
 *	This technique is somewhat siliar to this pseudo-code: 'new Request(new DB(new Config()))' where 'config' will be resolved first!
 *		Maybe it can also be explained like this: '$c->request($c->db($c->config))' ... but this is just pseudo-code!
 *	These are anonymous functions that get called when one of the Container properties are accessed
 *		eg. A statement like `$c->db->query(...)`; will call the 'db' function handler code below (when it tries to resolve `$c->db` and automatically builds the `db` instance.
 *			The code will create the object instance internally,
 *				then assign the new instance value to the corresponding (same) location in the Container class at the end of the function,
 *				effectively overwriting/replacing the array member (anonymous function) with the new object instance,
 *				essentially building something like a singleton object instance,
 *				since calling `$c->db` the next time will return the new object and not the original function.
 *			If you require multiple instances, you can just skip assigning the value at the end and just return a new object each time eg. 'return $obj;' instead of 'return $c->obj = $obj;'
 *	If you require multiple arguments, like `$c->db('primary_conn')`, or `$c->myFunc($param1)`
 *			then you can just create a function like `function($c, $param1)`,
 *			the container object itself ($this) is always inserted as the first parameter in the parameter list before the function is called (with array_unshift()).
 *	Each one of these `properties` are called by the __get, __set and __call magic methods in the Container class!
 *	The `__get` method of the Container is actually called when we ask for '$c->db' (eg. $c->__get('db')) ... but the internal value of 'db' is actually the function below.
 *		Our `__get` method inside the Container class will recognize that the property is actually a callable function,
 *			and proceed to actually CALL the function, passing it the `$this` (container) value as the first parameter of the function call
 */
return	[
			'execute'	=>	function($c)
							{
								/**
								 *	Register an Exception Handler
								 */
								/**
								 *	The Twister Exception Handler is just a slightly improved version of the default internal PHP handler
								 *	new \Twister\ExceptionHandler();
								 *	or use Whoops below!
								 */
								$whoops = new \Whoops\Run;
								$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
							//	$whoops->pushHandler(new \Whoops\Handler\JsonResponseHandler);
								$whoops->register();
								$c->request->execute_route(require __DIR__ . '/routes.php');
							},

			'config'	=>	function($c)
							{
								$config = require __DIR__ . '/' . strtolower(getenv('COMPUTERNAME')) . '.php';
								$c->is_debug = $c->isDebug = $config['debug'];	//	shorthand
								return $c->config = &$config;
							},

			'db'		=>	function($c)
							{
								$dbc = $c->config['db'];
								mysqli_report( MYSQLI_REPORT_ALL );
								try {
									$db = new DB($dbc['host'], $dbc['username'], $dbc['password'], $dbc['schema'], $dbc['port']);
								} catch (mysqli_sql_exception $e) {
									header('HTTP/1.1 503 Service Temporarily Unavailable');
									header('Status: 503 Service Temporarily Unavailable');
									header('Retry-After: 7200'); // seconds
									die('<b>Website under scheduled maintenance!</b><br />' .
										'We are aware of the situation and apologize for the inconvenience!<br />' .
										'Normal operation will resume shortly, please be patient and try again later!<br />' . ($c->is_debug ? mysqli_connect_error() : null));
								}
								$db->set_charset($dbc['charset']);
								$db->real_query('SET NAMES ' . $dbc['charset'] . ' COLLATE ' . $dbc['collation']);
								return $c->db = &$db;
							},

							/**
							 *	This Request object will instantiate the 'db' object internally, because the container is Injected into the Request object constructor,
							 *		it includes the 'db' handler above, so when Request calls '$this->container->db->query(...)';
							 *			it will (unknowingly) instantiate the 'db' object when the container tries to resolve the 'db' handler above,
							 *			the 'db' handler is the function above, which returns the new 'db' object.
							 *		Another way to do this is use a static variable inside the function, but then we have the unecessary function call overhead.
							 *	Upon executing any 'db' queries, the 'db' object is automatically instantiated by the 'db' code above.
							 *	Code like: `$db = new DB(...);` is never actually called anywhere else in code.
							 */
			'request'	=>	function($c)
							{
								return $c->request = new Request($c);
							},

			'session'	=>	function($c)
							{
								return $c->session = new Session($c->db);
								/*	//	alternative: check if HTTPS is enabled
								if ($c->request->is_https)
									return $c->session = new Session($c->db);
								else
									return $c->session = new Session($c->db);
								*/
							},

			'user'		=>	function($c)
							{
								return $c->user = new User($c);
							},

			'aliases'	=>	[	//'Twister\Container' => $this
							]
		];
