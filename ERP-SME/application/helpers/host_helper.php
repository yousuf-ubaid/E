<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

define('SYS_NAME', 'SPUR');
$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
define('STATIC_LINK', "$protocol$_SERVER[HTTP_HOST]");



