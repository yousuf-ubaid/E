<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Paypal client ID
|--------------------------------------------------------------------------
*/
$config['pay_pal_client_id'] = 'value_pay_pal_client_id';

/*
|--------------------------------------------------------------------------
| Paypal secret key
|--------------------------------------------------------------------------
*/
$config['pay_pal_secret_key'] = 'value_pay_pal_secret_key';

/*
|--------------------------------------------------------------------------
| Image Upload Local File Server
|--------------------------------------------------------------------------
*/
/* 1.local file server 2.localpc 3.Aws */

$config['ftp_image_uplod_local'] = '2';
$config['ftp_host_name'] = 'value_ftp_host_name';
$config['ftp_username'] = 'value_ftp_username';
$config['ftp_password'] = 'value_ftp_password';
$config['ftp_host'] = 'value_ftp_host';

$config['QHSE_login_url'] = 'value_QHSE_login_url';
$config['qhse_authorization'] = 'value_qhse_authorization';

$config['vendor_portal_url'] = 'value_vendor_portal_url';
$config['google_map_key'] = 'value_google_map_key';

$config['vendor_portal_api_base_url'] = 'value_vendor_portal_api_base_url';
$config['vendor_portal_api_username'] = 'value_vendor_portal_api_username';
$config['vendor_portal_api_password'] = 'value_vendor_portal_api_password';

/*Spur Go Details Start*/
const spur_go_DB_HOST = 'value_spur_go_DB_HOST';
const spur_go_DB_USER = 'value_spur_go_DB_USER';
const spur_go_DB_PASSWORD = 'value_spur_go_DB_PASSWORD';
const spur_go_DB_NAME = 'value_spur_go_DB_NAME';
$config['modules'] = array('33', '34');
/*Spur Go Details End*/

/*
|--------------------------------------------------------------------------
| config main database
|--------------------------------------------------------------------------
*/
$main_db = [
    'host' => 'value_db_host',
    'user' => 'value_db_username',
    'password' => 'value_db_password',
    'database' => 'value_db_database'
];
DEFINE('env_DB', serialize($main_db));

/*
|--------------------------------------------------------------------------
| config email
|--------------------------------------------------------------------------
*/
$config['email_smtp_host'] = 'value_email_smtp_host';
$config['email_smtp_username'] = 'value_email_smtp_username';
$config['email_smtp_password'] = 'value_email_smtp_password';
$config['email_smtp_port'] = 'value_email_smtp_port';
$config['email_smtp_from'] = 'value_email_smtp_from';

/*
|--------------------------------------------------------------------------
| config site host url
|--------------------------------------------------------------------------
*/
$config['host_url'] = 'value_host_url';

/*
|--------------------------------------------------------------------------
| config key for email service
|--------------------------------------------------------------------------
*/
$config['email_token'] = 'value_email_token';
$config['from_email_url'] = 'value_from_email_url';

const PAY_PAL_ENABLED = 0;
