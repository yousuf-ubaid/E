<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
const hstGeras = '0';
const PRODUCT_ID = '1';

/*QUANTUM*/
/*const SYS_NAME = 'QUANTUM';
const EMAIL_SYS_NAME = 'Quantum';

const LOGO = 'quantum-header.PNG';
const LOGO_SMALL = 'quantum-header-small.png';*/

/*COREX*/
const SYS_NAME = 'COREX';
const EMAIL_SYS_NAME = 'Corex';

const LOGO = 'corex-header.PNG';
const LOGO_SMALL = 'corex-header-small.png';

if (!function_exists('getLoginPage')) {
    function getLoginPage(): string
    {
        $login_page = 'login_page';
        if (SYS_NAME === 'COREX') {
            $login_page = 'corex_login_page';
        }
        return $login_page;
    }
}

if (!function_exists('loadTopNavigationCss')) {
    function loadTopNavigationCss(): void
    {
        if (SYS_NAME === 'QUANTUM') {
            echo '<link rel="stylesheet" href="' . base_url('plugins/dist/css/top-purple-navigation.css') . '">';
        }
        if (SYS_NAME === 'COREX') {
            echo '<link rel="stylesheet" href="' . base_url('plugins/dist/css/top-white-navigation.css') . '">';
        }
    }
}