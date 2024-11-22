<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
|--------------------------------------------------------------------------
| Paypal Config
|--------------------------------------------------------------------------
*/
$config['pay_pal_client_id'] = '';
$config['pay_pal_secret_key'] = '';

define('PBS_CONTRACT_API', '');






$allowed_urls = [ '' ]; //SME-company server urls
DEFINE('PBS_CONTRACT_REQUEST_ALLOWED_URLS', serialize($allowed_urls));
DEFINE('PBS_COMPANY_ID', 98);


/* Below two variables used for return company list for navigation process */
$config['company_id_in'] = [];
$config['company_id_not_in'] = [];


/* Client DB connection details */
$config['clientDB_host'] = 'value_db_hostname';
$config['clientDB_user'] = 'value_db_username';
$config['clientDB_password'] = 'value_db_password'; 