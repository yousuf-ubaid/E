<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
define('UPLOAD_PATH',$_SERVER["DOCUMENT_ROOT"]."/images/module");


if (!function_exists('addon_path')) {
    function addon_path(){
       echo  base_url(). '../../images/module/';

    //  echo   'http://'.$_SERVER["DOCUMENT_ROOT"]."/gs_sme/images/module";
    }
}
