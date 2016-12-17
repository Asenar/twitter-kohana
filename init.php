<?php

require_once realpath(__DIR__ . '/vendor/autoload.php');

Route::set('twitter-auth', 'twitter/auth')
->defaults([
	'directory'  => 'Twitterauth',
	'controller' => 'Index',
	'action'     => 'index',
]);
