<?php

//	if (empty($_SESSION['id'])) redirect(request::build_url(array('scheme' => 'https', 'path' => '/login')), array('message' => 'invalid permissions', 'next' => '/admin/'));
//	if (empty($_SESSION['id'])) redirect(request::build_url('https'), 'invalid-permissions');
	if (empty($_SESSION['id'])) request::redirect('https', '/login', ['message' => 'invalid permissions', 'next' => '/admin/']);
