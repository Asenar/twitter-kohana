<?php

require "vendor/autoload.php";

Route::set('twitter-auth', 'twitter/auth')
->defaults(array(
	'directory'  => 'Twitterauth',
	'controller' => 'Index',
	'action' => 'index'
));
