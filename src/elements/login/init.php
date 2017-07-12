<?php

	return function($response)
	{

//$user = $response->container->user;

//echo $response->container->request->uri->getLeftPart(Uri::PARTIAL_AUTHORITY) . '/';
//echo $response->container->request->uri->getLeftPart(Uri::PARTIAL_PATH) . '/mdfg';

//echo $response->container->request->uri->withScheme(null);


		if ( ! $response->container->request->is_https)	//	|| ! isset($_COOKIE[session_name()])
			$response->container->request->redirect('https');

		$response->scripts['recaptcha']	= 'https://www.google.com/recaptcha/api.js';

		//	PROBLEM: if we only create session when the session cookie is set, it's only set (retrieved) on the SECOND page view!
		//				What I mean is, if you go immediately to the login page (no other page views), it will NOT have a session cookie stored yet!
		//				Actually, the cookie is stored on the client side, but it's not retrieveable yet, until the second page view!?!?
		//				Therefore, this session challenge will not be stored in the database if we first check to see if the cookie exists!?!?
		//				Maybe we should `fake` it on this page only, by manually setting the session cookie, so when the page ends, and the session must write,
		//					it will write the challenge. ie. We need to manually set `$_COOKIE[session_name()] = session_create_id();`
		$_SESSION['challenge'] = md5(uniqid(microtime().mt_rand(), true));	//	http://php.net/manual/en/function.mcrypt-create-iv.php

		//	HACK
		//	We might need to do this!?!?
		//	What we are doing here is CREATING a cookie that didn't exist before.
		//	ALTERNATIVE: We REDIRECT back to this page if the cookie didn't exist before!
		//	This `might` be necessary on this page only, to FORCE the session_write_close() function to write the session data (which includes the `challenge`) to the database!
		$_COOKIE[session_name()] = session_id();
	};
