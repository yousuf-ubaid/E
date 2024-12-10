<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class PrintCloud {

    function PrintCloud(){
        $CI = & get_instance();
    }

    function printReceipt($pId,$pkp,$cm){
        $CI = & get_instance();
        include_once (APPPATH.'/third_party/cloudprint/vendor/glavweb/php-google-cloud-print/example/CloudPrint.php');
        $cloudPrint = new CloudPrint($pId,$pkp,$cm);
        $cloudPrint->printCloud();
        exit;
    }
}

