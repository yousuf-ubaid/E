<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| bucket Name project to project we will create it.
|--------------------------------------------------------------------------
|
*/
$config['bucket'] = 'value_bucket_name';

/*
|--------------------------------------------------------------------------
| Use SSL
|--------------------------------------------------------------------------
|
| Run this over HTTP or HTTPS. HTTPS (SSL) is more secure but can cause problems
| on incorrectly configured servers.
|
*/

$config['use_ssl'] = FALSE;

/*
|--------------------------------------------------------------------------
| Verify Peer
|--------------------------------------------------------------------------
|
| Enable verification of the HTTPS (SSL) certificate against the local CA
| certificate store.
|
*/

$config['verify_peer'] = TRUE;




$config['region'] = 'value_bucket_region';
//$config['region'] = 'EU (Frankfurt)';



/*
|--------------------------------------------------------------------------
| Access Key
|--------------------------------------------------------------------------
|
| Your Amazon S3 access key.
|
*/

$config['access_key'] = 'value_bucket_access_key';

/*
|--------------------------------------------------------------------------
| Secret Key
|--------------------------------------------------------------------------
|
| Your Amazon S3 Secret Key.
|
*/

$config['secret_key'] = 'value_bucket_secret_key';

/*
|--------------------------------------------------------------------------
| Use Enviroment?
|--------------------------------------------------------------------------
|
| Get Settings from enviroment instead of this file?
| Used as best-practice on Heroku
|
*/

$config['get_from_enviroment'] = FALSE;

/*
|--------------------------------------------------------------------------
| Access Key Name
|--------------------------------------------------------------------------
|
| Name for access key in enviroment
|
*/
$config['access_key_envname'] = 'value_bucket_access_key';

/*
|--------------------------------------------------------------------------
| Access Key Name
|--------------------------------------------------------------------------
|
| Name for access key in enviroment
|
*/
$config['secret_key_envname'] = 'value_bucket_secret_key';

/*
|--------------------------------------------------------------------------
| If get from enviroment, do so and overwrite fixed vars above
|--------------------------------------------------------------------------
|
*/



if ($config['get_from_enviroment']){
	$config['access_key'] = getenv($config['access_key_envname']);
	$config['secret_key'] = getenv($config['secret_key_envname']);
}

