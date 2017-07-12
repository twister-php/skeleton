<?php

/**
 *	This login controller does NOT extend the base/abstract Controller class
 *	For an example of one that does, look at the AdminController
 *	The router builds these action handlers (class methods) parameter lists dynamically
 *	So in the first action, I added DB $db (current database connection) (which is not needed inside the function, just an example)
 *	And the registerAction() asks for a non-existant parameter
 *	These functions can also receive parameters from the $_GET and $_POST arrays.
 *	eg. /search?q=term%20term  =>  searchAction($q)
 *		value of $q above will be filled with $_GET['q'] value
 */

class LoginController
{
	static function loginAction(DB $db, Container $c)
	{
		if ($c->request->isGet())
			(new Response($c, 'public', 'login'))->render();
		else
			throw new \Exception('Process login action here ...');
	}

	static function registerAction(Container $c, $non_existing_param1 = null)
	{
		(new Response($c, 'public', 'register'))->render();
	}
}
