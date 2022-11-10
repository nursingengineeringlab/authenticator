<?php

$config = array();

// Providers
$config['providers'] = array();
$config['providers']['google'] = array(
	'client-id' => 'xxx.apps.googleusercontent.com',
	'client-secret' => 'xxx',
	'configuration' => 'https://accounts.google.com/.well-known/openid-configuration',
	'response-type' => 'code',
	'response-mode' => '',
	'login-recovery' => false,
);
$config['providers']['msft'] = array(
	'client-id' => 'xxx',
	'client-secret' => '',
	'configuration' => 'https://login.microsoftonline.com/common/v2.0/.well-known/openid-configuration',
	'response-type' => 'id_token',
	'response-mode' => 'form_post',
	'login-recovery' => 'prompt=login',
);
$config['providers']['yahoo'] = array(
	'client-id' => 'xxx',
	'client-secret' => 'xxx',
	'configuration' => 'https://api.login.yahoo.com/.well-known/openid-configuration',
	'response-type' => 'code',
	'response-mode' => '',
	'login-recovery' => false,
);

// Email suffixes
$config['emails'] = array(
	'gmail.com' => 'google',

	'hotmail.com' => 'msft',
	'live.com' => 'msft',
	'outlook.com' => 'msft',

	'yahoo.com' => 'yahoo',
	'aol.com' => 'yahoo',
);

// Email servers (DNS MX)
$config['mx'] = array(
	'google.com' => 'google',
	'hotmail.com' => 'msft',
	'outlook.com' => 'msft',
	'live.com' => 'msft',
	'yahoo.com' => 'yahoo',
);

// Clients
$config['for'] = array(
	'example' => 'http://example.com',
);
?>
