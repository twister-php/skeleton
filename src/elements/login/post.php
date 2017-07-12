<?php

	$email		=	(string)	get('email');
	$password	=	(string)	get('password');
	$challenge	=	(string)	get('challenge');
	$next		=	(string)	get('next');	//	get('next', '', false);
	$persistent	=	(bool)		get('persistent');
	$response	=	(string)	get('g-recaptcha-response');

	/*
		// DEBUGGING
		print_pre('');
		var_dump($_POST);
		print_pre('');
		var_dump($_SESSION);
		print_pre('');
		var_dump($_COOKIE);
		print_pre('');
		var_dump(session_id());
		print_pre('');
		var_dump($challenge);
	*/

	$from			=	'/login';
	//	Used in login & register scripts
	function delay($msg)
	{
		global $next, $from;
	//	unset($_SESSION['challenge']);	//	should be destroyed with session_destroy() below!
		session_regenerate_id(true);
	//	session_write_close(); // was originally using session_regenerate_id(true) + session_write_close() ... changed to session_regenerate_id(true) + session_destroy()
		session_destroy();
		db::conn()->close();
		sleep(3);
		redirect($from, array('message' => $msg, 'next' => $next));
	}
//print_r($_POST);
//print_r($_SESSION);

	if (empty($_SESSION['challenge']) || $challenge != $_SESSION['challenge'])	delay('login-failed1');
	if (empty($email))		delay('login-failed2');
	if (empty($password))	delay('login-failed3');
	if (empty($response))	delay('login-failed4');


	//
	//	Verify reCAPTCHA
	//
	//	https://www.google.com/recaptcha/admin#site/319890180?setup
	//	https://developers.google.com/recaptcha/docs/verify

	require CODE_PATH . 'lib/curl.php';
	$curl = new curl();
	$curl->post('https://www.google.com/recaptcha/api/siteverify', 'secret=' . env::get('google')['recaptcha-secret'] . '&response=' . $response, array(CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0));

	//curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0); // I needed these to pass my self signed certificate. cURL complains about unable to verify local certificate or something!
	//curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);

	if ($curl->response === false && env::isDebug())
	{
		var_dump($curl->response);
		var_dump($curl->error());
		var_dump($curl->errno());
		var_dump($curl->info);
	}

	if ($curl->response === false)
		delay('login-captcha-failed');
	$response = json_decode($curl->response, true);
	if (!isset($response['success']) || $response['success'] === false) // https://developers.google.com/recaptcha/docs/verify
		delay('login-captcha-failed');

	$user = db::lookup('SELECT id, email_verified, password, salt FROM users WHERE email_hash = 0x' . md5($email) . ' LIMIT 1');
	if (empty($user) || hash_hmac('sha256', $password, $user['salt'], true) !== $user['password'])
		delay('login-failed6');

	unset($_SESSION['challenge']);

//var_dump($_COOKIE);
//var_dump($_SESSION);

	/*
		// Original code in session class
		static function login($user_id, $persistent)
		{
			session_regenerate_id(true);
			setcookie(session_name(), session_id(), $persistent ? 0x7fffffff : 0, '/');
			$_SESSION['id'] = $user_id;
			self::$_db->real_query('INSERT INTO sessions (id, timestamp, persistent, user_id, data) VALUES (0x' . session_id() . ', UNIX_TIMESTAMP(), ' . ($persistent ? 'UNIX_TIMESTAMP(), ' : '0, ') . $user_id . ', "")');
		}

		// call the function
		session::login($user['id'], $persistent);
	*/
	//	We do almost everything here, because I don't want to 'polute' the session class with functionality that's only called/used here!
	session_regenerate_id(true);

//var_dump($_COOKIE);
//var_dump($_SESSION);

	//	This cookie is used to `force` (redirect) the browser to the HTTPS url of ALL pages due to a possible session timeout!
	setrawcookie('HTTPS_ONLY', time(), 0x7fffffff, '/');

	setrawcookie(session_name(), session_id(), $persistent ? 0x7fffffff : 0, '/', null, true, true);	//	$_SERVER["HTTP_HOST"] || null ??? ... This ALSO ensures that the session cookie will ONLY be sent over HTTPS!
	$_SESSION['id'] = $user['id'];
//	session::login($user['id'], $persistent);	//	This is the only thing we do in the session class, because it requires the session's DB connection ... HOW RETARDED!

//var_dump($_COOKIE);
//var_dump($_SESSION);

	//	We MUST lock the `sessions` table, just in-case the session garbage collector runs between the scripts and deletes some extra sessions!
	db::real_query('LOCK TABLES sessions WRITE, user_sessions WRITE, user_session_history WRITE');

		// ARCHIVE USER SESSIONS: `user_sessions` => `user_session_history`
		db::real_query('INSERT INTO user_session_history (id, user_id, ip, cc, agent_id, forwarded_for_id, via_id, created, modified, last_request, requests, page_views, time_on_site, persistent) SELECT id, user_id, ip, cc, agent_id, forwarded_for_id, via_id, created, modified, last_request, requests, page_views, time_on_site, persistent FROM user_sessions WHERE id NOT IN (SELECT id FROM sessions)');
		db::real_query('DELETE FROM user_sessions WHERE id NOT IN (SELECT id FROM sessions)');

		// Create new session
		if ($persistent) session::create_persistent_session();	//	NEW!
		session_write_close(); // might as well do this while we have a table lock !?!?

	db::real_query('UNLOCK TABLES');	//	I think it's important to unlock these tables early so other scripts can continue to execute !?!? However, this runs so infrequently it shouldn't be an issue!?!?

	//	Create a new entry in `user_sessions` ... most of the values can be empty, because we will UPDATE them on each page view!
	db::real_query('INSERT INTO user_sessions (id, user_id, ip, cc, agent_id, forwarded_for_id, via_id, created, modified, last_request, requests, page_views, time_on_site, persistent) VALUES (0x' . session_id() . ',' . $user['id'] . ', 0x' . request::$ip2hex . ', "' . request::$cc . '", ' . request::$agent_id . ', ' . request::$forwarded_for_id . ', ' . request::$via_id . ', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 1, 1, 0, ' . (int) $persistent . ')');

	//	ORIGINAL
	/*
	$sql = 'UPDATE users SET ' .
			'previous_online = last_online,' .
			'last_login = NOW(),' .
			'last_online = NOW(),' .
			//'last_domain_id = ' . $GLOBALS['FW']['domain']['id'] . ',' .
			'locale = "' . $GLOBALS['FW']['locale'] . '",' .
			'logins = logins + 1,' .
			'ip = @ip,' .
			'cc = @cc,' .
			'for_id = ' . $logs['forwarder_id'] . ',' .
			'via_id = ' . $logs['proxy_id'] . ',' .
			'agent_id = ' . $logs['agent_id'] . ',' .
			'referer_id = ' . $logs['referer_id'] . ',' .
			'language_id = ' . $logs['language_id'] .
		' WHERE id = ' . $_SESSION['id'];
	*/
	db::real_query('UPDATE users SET last_login = UNIX_TIMESTAMP(), logins = logins + 1 WHERE id = ' . $user['id']);

//	session_write_close(); // moved to the session table lock above, can be moved back without side-effects!
	db::close();

//	ORIGINAL
//	redirect('http://' . $fqdn . $next, array('error' => &$errors, 'warning' => &$warnings, 'message' => &$messages));

	redirect(empty($next) ? '/dashboard/' : $next, array('message' => 'login-welcome'));


/*
//	Generate the first password ...
$salt = microtime();
echo md5($salt) . '<br>';
echo hash_hmac('sha256', 'abc123', md5($salt, true)) . '<br>';
UPDATE users SET email_hash = UNHEX(MD5(email)), password = 0x9f37ce72aaad946e3a98b9dc6126e7c39855600a9d063058dfe79084cbf3a3e4, salt = 0x4c06ff520bc5198942657313a153ec09;
exit;
*/


/*
	// ORIGINAL
	$this->update_user_logs();
	session_id(md5(uniqid(microtime().mt_rand(),true)));
	//setrawcookie(session_name(), session_id(), 0x7fffffff, '/', '.iedb.net', false, true); // problem with this is that it doesn't override the previous session cookie. This is only applicable if we've deleted the cookies and refreshed the post page.
	header('Set-Cookie: PHPSESSID=' . session_id() . ($persistent ? '; expires=Tue, 19-Jan-2038 03:14:07 GMT' : '') . '; path=/; httponly', true); // Later we can modify this to an SSL only cookie!
	$DB->real_query('INSERT INTO sessions (id, timestamp, persistent, user_id, data) VALUES (0x' . session_id() . ', UNIX_TIMESTAMP(), ' . ($persistent ? 'UNIX_TIMESTAMP(), ' : '0, ') . $_SESSION['id'] . ', "")');
	return true;

		function update_user_logs() // try to run this in the framework after we have the $FW['domain']['id'] -- problem is: we don't know if the user has just "logged in"
		{
			$logs = $this->logs();

			$GLOBALS['DB']->init_ipcc();

			$sql = 'UPDATE users SET ' .
					'previous_online = last_online,' .
					'last_login = NOW(),' .
					'last_online = NOW(),' .
					//'last_domain_id = ' . $GLOBALS['FW']['domain']['id'] . ',' .
					'locale = "' . $GLOBALS['FW']['locale'] . '",' .
					'logins = logins + 1,' .
					'ip = @ip,' .
					'cc = @cc,' .
					'for_id = ' . $logs['forwarder_id'] . ',' .
					'via_id = ' . $logs['proxy_id'] . ',' .
					'agent_id = ' . $logs['agent_id'] . ',' .
					'referer_id = ' . $logs['referer_id'] . ',' .
					'language_id = ' . $logs['language_id'] .
				' WHERE id = ' . $_SESSION['id'];

			$GLOBALS['DB']->real_query($sql);
		}
*/
