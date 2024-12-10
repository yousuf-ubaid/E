<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use Mpdf\Mpdf;
class Pdf
{

    function Pdf()
    {
        $CI = &get_instance();
        log_message('Debug', 'mPDF class is loaded.');
    }

    function load($param = NULL)
    {
        return new Mpdf([
            'mode'              => 'en-GB-x',
            'format'            => 'A4',  
            'default_font_size' => 9,
            'default_font'      => 'arial',
            'margin_left'       => 5,
            'margin_right'      => 5,
            'margin_top'        => 5,
            'margin_bottom'     => 10,
            'margin_header'     => 0,
            'margin_footer'     => 3,
            'orientation'       => 'P'   
        ]);
    }

    function printed($html, $format = 'A4', $Approved = 1, $printHeaderFooterYN = 1, $documentID = '')
    {
        $CI = &get_instance();
        $mpdf = new Mpdf([
            'mode'              => 'utf-8',
            'format'            => $format,
            'default_font_size' => 9,
            'default_font'      => 'arial',
            'margin_left'       => 5,
            'margin_right'      => 5,
            'margin_top'        => 5,
            'margin_bottom'     => 10,
            'margin_header'     => 0,
            'margin_footer'     => 3,
            'orientation'       => 'P'
        ]);

        $policyPIE = getPolicyValues('PIE', 'All');
        if(!$policyPIE && $policyPIE != 1) {
            $water_mark_status = policy_water_mark_status('All');
            if ($Approved != 1 and $water_mark_status == 1) {
                $waterMark = '';
                switch ($Approved) {
                    case 0;
                        $waterMark = 'Draft';
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
        }
      
        $user = ucwords($CI->session->userdata('username'));
        $date = date('j F Y');
        $stylesheet = file_get_contents('plugins/bootstrap/css/bootstrap.min.css');
        $stylesheet2 = file_get_contents('plugins/bootstrap/css/style.css');
        $stylesheet3 = file_get_contents('plugins/bootstrap/css/print_style.css');
        if ($printHeaderFooterYN == 0) {
            $mpdf->SetFooter();
        } else if ($printHeaderFooterYN == 1) {
            $mpdf->SetFooter('Pg : {PAGENO} - Printed By : ' . $user . '|This is a computer generated document and does not require signature.|' . $date);

        }else if ($printHeaderFooterYN == 2) {
            $mpdf->SetFooter();
        }else if ($printHeaderFooterYN == 3) {
            $mpdf->SetFooter('Pg : {PAGENO} - Printed By : ' . $user . '|This is a computer generated document and does not require signature.|' . $date);
        }

        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($stylesheet2, 1);
        $mpdf->WriteHTML($stylesheet3, 1);
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output();
        exit;
    }

    function printed_bank_letter($html, $format = 'A4', $Approved = 1)
    {
        $CI = &get_instance();
        $mpdf = new Mpdf([
            'mode'              => 'utf-8',
            'format'            => $format, 
            'default_font_size' => 9,
            'default_font'      => 'arial',
            'margin_left'       => 25.4,
            'margin_right'      => 25.4,
            'margin_top'        => 35,
            'margin_bottom'     => 55,
            'margin_header'     => 0,
            'margin_footer'     => 3,
            'orientation'       => 'P'  
        ]);

        $water_mark_status = policy_water_mark_status('All');
        if ($Approved != 1 and $water_mark_status == 1) {
            $waterMark = '';
            switch ($Approved) {
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
        $stylesheet = file_get_contents('plugins/bootstrap/css/bootstrap.min.css');
        $stylesheet2 = file_get_contents('plugins/bootstrap/css/style.css');
        $stylesheet3 = file_get_contents('plugins/bootstrap/css/print_style.css');

        $mpdf->SetFooter('');

        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($stylesheet2, 1);
        $mpdf->WriteHTML($stylesheet3, 1);
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output();
        exit;
    }

    function printed_pos($html)
    {
        $mpdf = new Mpdf([
            'mode'              => 'utf-8',
            'format'            => 'A8',   
            'default_font_size' => 11,
            'default_font'      => 'arial',
            'margin_left'       => 2,
            'margin_right'      => 2,
            'margin_top'        => 0,
            'margin_bottom'     => 2,
            'margin_header'     => 0,
            'margin_footer'     => 0,
            'orientation'       => 'P'
        ]);

        $mpdf->WriteHTML($html, 2);

        $url = str_replace('application\libraries', 'uploads', dirname(__FILE__) . '/kot/kot.pdf');
        $mpdf->Output($url, 'F');

    }


    function save_pdf($html, $format, $Approved, $path, $footer = null)
    {
        $CI = &get_instance();

        $mpdf = new Mpdf([
            'mode'              => 'utf-8',
            'format'            => $format, 
            'default_font_size' => 9,
            'default_font'      => 'arial',
            'margin_left'       => 5,
            'margin_right'      => 5,
            'margin_top'        => 5,
            'margin_bottom'     => 10,
            'margin_header'     => 0,
            'margin_footer'     => 3,
            'orientation'       => 'P'
        ]);

        $water_mark_status = policy_water_mark_status('All');
        if ($Approved != 1 and $water_mark_status == 1) {
            $waterMark = '';
            switch ($Approved) {
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
        $date = date('j F Y');
        $stylesheet = file_get_contents('plugins/bootstrap/css/bootstrap.min.css');
        $stylesheet2 = file_get_contents('plugins/bootstrap/css/style.css');
        $stylesheet3 = file_get_contents('plugins/bootstrap/css/print_style.css');

        if ($footer) {
            $mpdf->SetFooter($footer . ' |This is a computer generated document and does not require signature.|Pg : {PAGENO} - Printed By : ' . $user);
        } else {
            $mpdf->SetFooter('Pg : {PAGENO} - Printed By : ' . $user . '|This is a computer generated document and does not require signature.|' . $date);
        }

        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($stylesheet2, 1);
        $mpdf->WriteHTML($stylesheet3, 1);
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output($path, 'F');
    }

    function save_pdf_pos_sales_report($html, $format, $Approved, $path, $footer = null)
    {
        $CI = &get_instance();
        $mpdf = new Mpdf([
            'mode'              => 'utf-8',
            'format'            => $format, 
            'default_font_size' => 5,
            'default_font'      => 'arial',
            'margin_left'       => 5,
            'margin_right'      => 5,
            'margin_top'        => 5,
            'margin_bottom'     => 5,
            'margin_header'     => 0,
            'margin_footer'     => 0,
            'orientation'       => 'P'
        ]);

        $mpdf->AddPage('L');
        $stylesheet = file_get_contents('plugins/bootstrap/css/bootstrap.min.css');
        $batch_sales_report_css = file_get_contents('plugins/pos/batch-sales-report.css');

        $mpdf->debug = true;
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($batch_sales_report_css, 1);
        $mpdf->WriteHTML($html, 2);
        $mpdf->AddPage('L');
        $mpdf->Output($path, 'F');
    }

    function printed_mc($html, $format = 'A4', $Approved = 1, $footer = null)
    {
        $CI = &get_instance();

        $mpdf = new Mpdf([
            'mode'              => 'utf-8',
            'format'            => $format, 
            'default_font_size' => 9,
            'default_font'      => 'arial',
            'margin_left'       => 5,
            'margin_right'      => 5,
            'margin_top'        => 5,
            'margin_bottom'     => 10,
            'margin_header'     => 0,
            'margin_footer'     => 3,
            'orientation'       => 'P' 
        ]);

        $water_mark_status = policy_water_mark_status('All');
        if ($Approved != 1 and $water_mark_status == 1) {
            $waterMark = '';
            switch ($Approved) {
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

        $stylesheet = file_get_contents('plugins/bootstrap/css/bootstrap.min.css');
        $stylesheet2 = file_get_contents('plugins/bootstrap/css/style.css');
        $stylesheet3 = file_get_contents('plugins/bootstrap/css/print_style.css');

        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($stylesheet2, 1);
        $mpdf->WriteHTML($stylesheet3, 1);
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output();
        exit;
    }

    function print_without_footer($html,$format='A4',$margin_top = '10',$marginB=10,$marginR=5,$marginL=5,$title='PDF',$fn='gears_pdf_output'){
        $mpdf = new Mpdf([
            'mode'               => 'utf-8',
            'format'            => $format, 
            'default_font_size' => '',
            'default_font'      => '',
            'margin_left'       => $marginL,
            'margin_right'      => $marginR,
            'margin_top'        => $margin_top,
            'margin_bottom'     => $marginB,
            'margin_header'     => 5,
            'margin_footer'     => 5,
            'orientation'       => 'P' 
        ]);

        $stylesheet = '';
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($html, 2);
        $mpdf->SetTitle($title);
        $mpdf->Output($fn.'.pdf', 'I');
        exit;
    }
}

