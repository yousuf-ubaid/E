<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Pdf {
    
    function Pdf(){
        $CI = & get_instance();
        log_message('Debug', 'mPDF class is loaded.');
    }
 
    function load($param=NULL){
        include_once (APPPATH.'/third_party/mpdf/mpdf.php');
        if ($params == NULL){
            $param = '"en-GB-x","A4","","",10,10,10,10,6,3';         
        }
        return new mPDF($param);
    }

    function printed($html,$format='A4',$Approved=1){
        $CI = & get_instance();
        include_once (APPPATH.'/third_party/mpdf/mpdf.php');
        $mpdf = new mPDF();
        $mpdf = new mPDF(
            'utf-8',    // mode - default ''
            $format,    // format - A4, for example, default ''
            '9',       // font size - default 0
            'arial',    // default font family
            5,          // margin_left
            5,          // margin right
            5,          // margin top
            10,          // margin bottom
            0,          // margin header
            3,          // margin footer
            'P'         // L - landscape, P - portrait
        );  

        if ($Approved!=1) {
            $waterMark = '';
            switch ($Approved){
                case 0;
                    $waterMark = 'Not Approved';
                break;

                case 2;
                    $waterMark = 'Referred Back';
                break;

                case 3;
                    $waterMark = 'Rejected';
                break;
            }
            $mpdf->SetWatermarkText($waterMark);
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.07;
        }
        $user = ucwords($CI->session->userdata('username'));
        $date = date('l jS \of F Y h:i:s A');

        $stylesheet = file_get_contents('plugins/bootstrap/css/print_style.css');
        $mpdf->SetFooter('Printed By : '.$user.'|Page : {PAGENO}|'.$date);
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($html, 2);

        $mpdf->Output();
        exit;
    }
}

