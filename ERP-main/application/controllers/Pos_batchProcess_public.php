<?php
/**
 *
 * -- =============================================
 * -- File Name : Pos_batchProcess.php
 * -- Project Name : POS
 * -- Module Name : POS Batch
 * -- Create date : 23 October 2018
 * -- Description : Batch File .
 *
 * --REVISION HISTORY
 * -- =============================================
 **/
defined('BASEPATH') OR exit('No direct script access allowed');

class Pos_batchProcess_public extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('pos');
        $this->load->model('Pos_batchProcess_model');
    }

    public function dailySalesSummeryReport($id, $date = null)
    {


        /**
         * Calling URL example :
         *
         *      Custom Date     :   http://localhost/gs_sme/index.php/Pos_batchProcess_public/dailySalesSummeryReport/13/2018-10-20
         *      Current Date    :   http://localhost/gs_sme/index.php/Pos_batchProcess_public/dailySalesSummeryReport/13
         */


        $message = '';
        /** $message .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
         * <html xmlns="http://www.w3.org/1999/xhtml">
         * <head>
         * <meta name="viewport" content="width=device-width" />
         * <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';*/


        $companyID = $id;

        $companyInfo = get_companyInformation($companyID);
        if (!empty($companyInfo)) {
            $config['hostname'] = trim($this->encryption->decrypt($companyInfo["host"]));
            $config['username'] = trim($this->encryption->decrypt($companyInfo["db_username"]));
            $config['password'] = trim($this->encryption->decrypt($companyInfo["db_password"]));
            $config['database'] = trim($this->encryption->decrypt($companyInfo["db_name"]));
            $config['dbdriver'] = 'mysqli';
            $config['db_debug'] = FALSE;
            $config['char_set'] = 'utf8';
            $config['dbcollat'] = 'utf8_general_ci';
            $config['cachedir'] = '';
            $config['swap_pre'] = '';
            $config['encrypt'] = FALSE;
            $config['compress'] = FALSE;
            $config['stricton'] = FALSE;
            $config['failover'] = array();
            $config['save_queries'] = TRUE;
            $this->load->database($config, FALSE, TRUE);
        } else {
            echo 'company not found!.';
            exit;
        }

        $batchId = 1;

        /** get Mailing List and Send the Email */
        $list = $this->Pos_batchProcess_model->get_mailingList($batchId, $id);
        $fs = '';
        $outlets = $this->Pos_batchProcess_model->getAllActiveOutlet($id);
        if ($list && $outlets) {

            /*
             * Custom Style
             * */
            $fs = 'font-size: 11px; padding:2px;';

            if ($date) {
                $todayIs = strtotime($date);
            } else {
                $todayIs = time();
            }
            $day_before_time_string = strtotime("yesterday", $todayIs);
            $day_before = date('Y-m-d', $day_before_time_string);


            $message .= '<div>';

            $message .= '<h3 class="ac">' . $companyInfo['company_name'] . '</h3>';


            $message .= $formatted = '<span style="color:darkred;' . $fs . '">';
            $message .= $formatted = 'Report Date: <strong>' . $day_before . '</strong></span></span><br><br><br>';

            $netGrandTotal = 0;
            $GrandTotal_billsCount = 0;
            $GrandTotal_voidBills = 0;
            $GrandTotal_avg = 0;
            $GrandTotal_creditSales = 0;


            $message .= '<table class="table table-striped table-bordered"><thead><tr><th style="width: 150px;' . $fs . '">Outlet</th><th style="' . $fs . '">Net Sales</th><th style="' . $fs . ' width:70px;">No of Bills</th><th style="' . $fs . ' width:70px;">Void Bills</th><th style="' . $fs . '">Average<br/>Sales</th><th style="' . $fs . '">Credit Sales </th><th style="' . $fs . '">Deductions</th><th style="' . $fs . '">Shift wise Net Sales</th></tr></thead><tbody>';

            if (!empty($outlets)) {
                foreach ($outlets as $outlet) {
                    $outletID = $outlet['wareHouseAutoID'];
                    $outletDisplayName = $outlet['wareHouseCode'] . ' - ' . $outlet['wareHouseDescription'];


                    $data = $this->getSalesSummeryData($day_before . ' 00:00:00', $day_before . ' 23:59:59', null, $outletID, $id);

                    $d = 2;
                    $netTotal = 0;
                    $lessTotal = 0;
                    $paymentTypeTransaction = 0;
                    $voidedTotal = !empty($data['voidBills']['NetTotal']) ? $data['voidBills']['NetTotal'] : 0;
                    if (!empty($data['paymentMethod'])) {
                        foreach ($data['paymentMethod'] as $report2) {
                            $netTotal += $report2['NetTotal'];
                            $paymentTypeTransaction += $report2['countTransaction'];
                        }
                    }


                    if (!empty($data['lessAmounts'])) {
                        foreach ($data['lessAmounts'] as $less) {
                            $lessTotal += $less['lessAmount'];
                        }
                    }

                    $grandTotalCount = 0;
                    $billCountTotal = 0;


                    if (!empty($data['customerTypeCount'])) {
                        foreach ($data['customerTypeCount'] as $report1) {
                            /*echo $report1['countTotal'].' - '.$report1['subTotal'].'<br/>';
                            continue;*/
                            $grandTotalCount += $report1['countTotal'];
                            $billCountTotal += $report1['subTotal'];

                        }
                    }


                    $grandTotalCount = $grandTotalCount - $data['fullyDiscountBill']['fullyDiscountBills'];


                    $grossTotal = $netTotal + $lessTotal;
                    $totalBill = $grossTotal + $voidedTotal;
                    $message .= '<tr > <td style="width: 150px; ' . $fs . ' padding:5px 2px;">';
                    $message .= $outletDisplayName;
                    $message .= '</td> <td style="text-align: right; ' . $fs . '">';

                    $netGrandTotal += $netTotal;
                    $message .= number_format($netTotal, $d);
                    $message .= '</td> <td style="text-align: center;' . $fs . '">';

                    /*No of Bills */
                    $message .= number_format($grandTotalCount);
                    $GrandTotal_billsCount += $grandTotalCount;


                    $message .= '</td> <td style="text-align: center; ' . $fs . '">';
                    $message .= isset($data['voidBills']['countTransaction']) ? $data['voidBills']['countTransaction'] : 0;
                    $GrandTotal_voidBills += $data['voidBills']['countTransaction'];

                    $message .= '</td> <td style="text-align: right; ' . $fs . '">';

                    if ($paymentTypeTransaction > 0) {
                        $message .= $avg = $grandTotalCount > 0 ? number_format(($netTotal / $paymentTypeTransaction), $d) : 0;
                        $GrandTotal_avg += $avg;
                    } else {
                        $message .= 0;
                    }

                    $message .= ' </td>';


                    $totalCreditSalesCount = 0;
                    $totalCreditSalesAmount = 0;
                    $tmpData = '';
                    if (!empty($data['creditSales'])) {
                        $tmpData .= '<table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr style="font-weight: bold;">
                        <td style="border: none !important; padding:0px 2px; ' . $fs . ' "> <strong>Credit Customer</strong> </td>
                        <td style="border: none !important; ' . $fs . '"> <strong> Qty&nbsp;&nbsp;</strong> </td>
                        <td style="border: none !important; text-align: right; ' . $fs . '"> <strong> Amount</strong> </td>
                     
                    </tr>';
                        foreach ($data['creditSales'] as $creditSale) {
                            $totalCreditSalesCount += $creditSale['countCreditSales'];
                            $totalCreditSalesAmount += $creditSale['salesAmount'];
                            $tmpData .= '<tr><td style="border: none !important; ' . $fs . '">';
                            $tmpData .= $creditSale['CustomerName'];
                            $tmpData .= '</td style="border: none !important; ' . $fs . '"><td style="border: none !important; text-align: center; ' . $fs . '">';
                            $tmpData .= $creditSale['countCreditSales'];
                            $tmpData .= '</td style="border: none !important;"><td style="border: none !important; text-align: right; ' . $fs . '" class="text-right">';
                            $tmpData .= number_format($creditSale['salesAmount'], $d);
                            $tmpData .= ' </td></tr>';
                        }
                        $tmpData .= '<tr><th style="border: none !important; ' . $fs . '">Total</th><th style="border: none !important; ' . $fs . '" class="text-center">';
                        $tmpData .= $totalCreditSalesCount;
                        $tmpData .= '</th><th style="border: none !important; text-align: right; border-top: 1px solid gray !important; ' . $fs . '" class="text-right">';
                        $GrandTotal_creditSales += $totalCreditSalesAmount;
                        $tmpData .= number_format($totalCreditSalesAmount, $d);
                        $tmpData .= '</th></tr></table>';
                    }

                    $message .= '<td style="width:300px;">' . $tmpData . '</td>';


                    $message .= '<td style="width:300px;">
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">';

                    if (!empty($data['lessAmounts'])) {
                        foreach ($data['lessAmounts'] as $less) {
                            if ($less['lessAmount'] > 0) {

                                $message .= '<tr><td style=" border: none !important; ' . $fs . '">';
                                $message .= $less['customerName'];
                                $message .= '</td><td class="text-right" style=" border: none !important; ' . $fs . '">';
                                $message .= number_format($less['lessAmount'], $d);
                                $message .= '</td></tr>';
                            }
                        }
                        if ($lessTotal > 0) {
                            $message .= '<tr><th style="padding-top: 10px; border: none !important; ' . $fs . '">Total</th>
                                <th style="padding-top: 10px;  border: none !important; ' . $fs . ' " class="text-right">(';
                            $message .= number_format($lessTotal, $d);
                            $message .= ')</th></tr>';
                        }
                    }

                    $message .= '</table></td>';
                    $message .= '<td style="width:280px;">';
                    $shiftWiseSales = $this->Pos_batchProcess_model->get_report_paymentMethod_admin($day_before . ' 00:00:00',
                        $day_before . ' 23:59:59', null, $outletID, $id, true);
                    if (!empty($shiftWiseSales)) {
                        $tmpShiftWiseTotal = 0;
                        $message .= '<table border="0" cellspacing="0" cellpadding="0" width="100%">';
                        $message .= '<tbody><tr>
                                    <th style="border: none !important; ' . $fs . '">Start</th>
                                    <th style="border: none !important; ' . $fs . '">End</th>
                                    <th style="border: none !important; ' . $fs . ' text-align: right;">Sales</th>
                                    </tr>';
                        foreach ($shiftWiseSales as $shiftWiseSale) {
                            $tmpShiftWiseTotal += $shiftWiseSale['NetTotal'];
                            $message .= '<tr>';
                            $message .= '<td style="width:60px; text-align: left;  border: none !important; ' . $fs . '">' . $shiftWiseSale['startTime'] . '</td>';
                            $message .= '<td style="width:60px; text-align: center;  border: none !important; ' . $fs . '">' . $shiftWiseSale['endTime'] . '</td>';
                            $message .= '<td style="text-align: center;   border: none !important; text-align: right; ' . $fs . '">' .
                                number_format($shiftWiseSale['NetTotal'], $d) . '</td>';
                            $message .= '</tr>';
                        }
                        $message .= '<tr>';
                        $message .= '<th style="text-align: left;  border: none !important; ' . $fs . '">Total</th>';
                        $message .= '<th style="text-align: right;  border: none !important; ' . $fs . '">&nbsp;</th>';
                        $message .= '<th style="text-align: center; border: none !important; border-top: 1px solid gray !important; text-align: right; ' . $fs . '">' . number_format($tmpShiftWiseTotal, $d) . '</th>';
                        $message .= '</tr>';

                        $message .= '</tbody></table>';
                    }
                    $message .= '
</td></tr>';
                }
            }

            $message .= '</tbody><tfoot>
                            <tr>
                                <th style="' . $fs . ' padding:10px;">Total</th>
                                <th style="text-align: right; ' . $fs . '">';
            $message .= number_format($netGrandTotal, $d);
            $message .= '
                                </th>
                                <th style="text-align: center; ' . $fs . '">';
            $message .= number_format($GrandTotal_billsCount);
            $message .= '
                                </th>
                                <th style="text-align: center; ' . $fs . '">';
            $message .= number_format($GrandTotal_voidBills);
            $message .= '
                                </th>
                            <th style="text-align: right; ' . $fs . '">';
            $grossBills = $GrandTotal_billsCount;
            if ($grossBills > 0) {
                $avgGross = $netGrandTotal / $GrandTotal_billsCount;
            } else {
                $avgGross = 0;
            }
            $message .= number_format($avgGross, $d);
            $message .= '
    </th>
    <th style="text-align: right; ' . $fs . '"> <span style="font-weight: normal; ' . $fs . '">Total Credit Sales: </span>';
            $message .= number_format($GrandTotal_creditSales, $d);
            $message .= ' </th><th>&nbsp;</th><th>&nbsp;</th></tr></tfoot></table>';
        }


        /*** Top Sales Items ***/
        $message .= '<pagebreak>';
        $this->load->model('Pos_restaurant_model');
        $dPlace = $companyInfo['company_default_decimal'];
        $dPlace = (empty($dPlace)) ? 2 : $dPlace;

        $from_date = str_replace('/', '-', $date);
        $from_date = date('Y-m-d', strtotime("$from_date -1 day"));
        $to_date = "$from_date 23:59:59";
        $from_date = "$from_date 00:00:00";
        $outlets2 = array_column($outlets, 'wareHouseAutoID');

        $rpt_data = $this->Pos_restaurant_model->get_top_sales_items($from_date, $to_date, $outlets2, $companyID);

        $message .= '<h3 style="text-align: center">Top Sales Items</h3>';
        $message .= '<table class="table table-bordered table-striped table-condensed" style="font-size: 11px;">  
                     <thead>
                     <tr style=" ">
                        <th rowspan="2" style="vertical-align: middle; padding: 5px 4px; text-align: center">Outlet</th>';
        $str = '';
        foreach ($rpt_data['master_menus'] as $menu) {
            $message .= '<th colspan="3" style="width:250px; padding: 5px 4px; text-align: center">' . $menu['menuCategoryDescription'] . '</th>';
            $str .= '<th style="width:200px; padding: 5px 4px; text-align: center">Item</th><th style="width:80px; ; text-align: center">Qty</th>
                    <th style="width:100px; padding: 5px 4px; text-align: center">Value</th>';
        }

        $message .= '</tr><tr>' . $str . '</tr></thead><tbody>';
        $range = range(0, 9);

        foreach ($rpt_data['ware_house'] as $row) {
            $message .= '<tr>
                         <td rowspan="10" style="vertical-align: middle; padding: 5px 4px; text-align: center">
                            <strong>' . $row['wareHouseCode'] . ' - ' . $row['wareHouseDescription'] . '</strong>
                         </td>';

            foreach ($range as $rn) {
                $message .= ($rn > 0) ? '<tr style=" ">' : '';
                foreach ($row['items'] as $menu) {
                    if (array_key_exists($rn, $menu)) {
                        $val = $menu[$rn];
                        $message .= '<td style="padding: 5px 4px;">' . $val['menu_des'] . '</td>
                                    <td style="padding: 5px 4px; text-align: center">' . $val['qty'] . '</td>
                                    <td style="padding: 5px 4px; text-align: right">' . number_format($val['net'], $dPlace) . '</td>';
                    } else {
                        $message .= '<td style="padding: 5px 4px">&nbsp;</td><td></td><td></td>';
                    }
                }
            }
        }
        $message .= '</tbody> </table>';
        /*** End of top sales items ***/


        $message .= '<br/><br/><br/><i><span style="' . $fs . '">This is an automatically generated email, created on ' . date('d-m-Y g:i A') . '</span></i>';
        $message .= '</div>';
        //echo $message; exit;

        $this->load->library('pdf');

        $path = UPLOAD_PATH . base_url() . '/uploads/pos/sales-report-' . time('Y-m-d') . ".pdf";
        $this->pdf->save_pdf_pos_sales_report($message, 'A4', 1, $path);


        $count = 0;


        if (!empty($list)) {

            $summery = '';
            foreach ($list as $user) {

                $emailAddress = $user->email;
                if ($emailAddress) {
                    $mail_config['wordwrap'] = TRUE;
                    $mail_config['protocol'] = 'smtp';
                    $mail_config['smtp_host'] = 'smtp.sendgrid.net';
                    $mail_config['smtp_user'] = 'apikey';
                    $mail_config['smtp_pass'] = SEND_GRID_EMAIL_KEY;
                    $mail_config['smtp_crypto'] = 'tls';

                    $mail_config['smtp_port'] = '587';
                    $mail_config['crlf'] = "\r\n";
                    $mail_config['newline'] = "\r\n";
                    $this->load->library('email', $mail_config);
                    $this->email->clear(TRUE);
                    $this->email->from('noreply@redberylit.com', EMAIL_SYS_NAME);
                    $this->email->set_mailtype('html');
                    $this->email->subject('Daily Sales Report - ' . $day_before);
                    $this->email->message($message);
                    $this->email->attach($path);
                    $this->email->to($emailAddress);
                    $result = $this->email->send();
                    if ($result) {
                        $count++;
                        $this->email->clear(TRUE);
                        $summery .= $emailAddress . ' <br/>';
                    } else {
                        /** Error to email */
                        $mail_config['wordwrap'] = TRUE;
                        $mail_config['protocol'] = 'smtp';
                        $mail_config['smtp_host'] = 'smtp.sendgrid.net';
                        $mail_config['smtp_user'] = 'apikey';
                        $mail_config['smtp_pass'] = SEND_GRID_EMAIL_KEY;
                        $mail_config['smtp_crypto'] = 'tls';

                        $mail_config['smtp_port'] = '587';
                        $mail_config['crlf'] = "\r\n";
                        $mail_config['newline'] = "\r\n";
                        $this->load->library('email', $mail_config);
                        $this->email->from('noreply@redberylit.com', EMAIL_SYS_NAME);
                        $this->email->set_mailtype('html');
                        $this->email->subject('Daily Sales Summery - FAIL on' . $day_before);
                        $this->email->message('sales report mail sending fail to ' . $emailAddress);
                        $this->email->to('shafri@redberylit.com');
                        $tmpResult = $this->email->send();
                        if ($tmpResult) {
                            $this->email->clear(TRUE);
                        }
                    }
                }

            }


            if ($count) {
                /** Summary Email */
                $mail_config['wordwrap'] = TRUE;
                $mail_config['protocol'] = 'smtp';
                $mail_config['smtp_host'] = 'smtp.sendgrid.net';
                $mail_config['smtp_user'] = 'apikey';
                $mail_config['smtp_pass'] = SEND_GRID_EMAIL_KEY;
                $mail_config['smtp_crypto'] = 'tls';

                $mail_config['smtp_port'] = '587';
                $mail_config['crlf'] = "\r\n";
                $mail_config['newline'] = "\r\n";
                $this->load->library('email', $mail_config);

                $this->email->from('noreply@redberylit.com', EMAIL_SYS_NAME);
                $this->email->set_mailtype('html');
                $this->email->subject('Email Sending Summery (Daily Sales Report) - Summary on' . $day_before);
                $msg = 'Following email received Daily Sales Summery Report on ' . $day_before . '<br/><br/>' . $summery . '<br/><br/><br/><br/>This is auto generated email by ' . EMAIL_SYS_NAME;
                $this->email->message($msg);
                $this->email->to('hisham@redberylit.com');
                $tmpResult = $this->email->send();
                if ($tmpResult) {
                    $this->email->clear(TRUE);
                }
            }


        }

    }

    public function dailySalesSummeryReportV2($date = null)
    {


        $company_list_q = $this->db->query("select * from srp_erp_daybookemailcompanies")->result_array();
        $list_of_companies = array();
        foreach ($company_list_q as $item){
            array_push($list_of_companies,$item['companyID']);
        }
        /**
         * Calling URL example :
         *
         *      Custom Date     :   http://localhost/gs_sme/index.php/Pos_batchProcess_public/dailySalesSummeryReport/13/2018-10-20
         *      Current Date    :   http://localhost/gs_sme/index.php/Pos_batchProcess_public/dailySalesSummeryReport/13
         */

        foreach($list_of_companies as $id){
            $message = '';
            /** $message .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
             * <html xmlns="http://www.w3.org/1999/xhtml">
             * <head>
             * <meta name="viewport" content="width=device-width" />
             * <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';*/


            $companyID = $id;

            $companyInfo = get_companyInformation($companyID);
            if (!empty($companyInfo)) {
                $config['hostname'] = trim($this->encryption->decrypt($companyInfo["host"]));
                $config['username'] = trim($this->encryption->decrypt($companyInfo["db_username"]));
                $config['password'] = trim($this->encryption->decrypt($companyInfo["db_password"]));
                $config['database'] = trim($this->encryption->decrypt($companyInfo["db_name"]));
                $config['dbdriver'] = 'mysqli';
                $config['db_debug'] = FALSE;
                $config['char_set'] = 'utf8';
                $config['dbcollat'] = 'utf8_general_ci';
                $config['cachedir'] = '';
                $config['swap_pre'] = '';
                $config['encrypt'] = FALSE;
                $config['compress'] = FALSE;
                $config['stricton'] = FALSE;
                $config['failover'] = array();
                $config['save_queries'] = TRUE;
                $this->load->database($config, FALSE, TRUE);
            } else {
                echo 'company not found!.';
                exit;
            }

            $batchId = 1;

            /** get Mailing List and Send the Email */
            $email_list = $this->db->query("select * from srp_erp_daybookemaillist where companyID=$id")->result_array();
            $company_logo = $this->db->query("SELECT company_logo FROM `srp_erp_company` WHERE company_id = $companyID ")->row('company_logo');

            //var_dump($email_list);exit;
            $list = $email_list;//array('semira@gears-int.com');
            $fs = '';
            $outlets = $this->Pos_batchProcess_model->getAllActiveOutlet($id);
            if ($list && $outlets) {

                /*
                 * Custom Style
                 * */
                $fs = 'font-size: 11px; padding:2px;';

                if ($date) {
                    $todayIs = strtotime($date);
                } else {
                    $todayIs = time();
                }
                $day_before_time_string = strtotime("yesterday", $todayIs);
                $day_before = date('Y-m-d', $day_before_time_string);


                $message .= '<div>';

                $message .= '<h3 class="ac">' . $companyInfo['company_name'] . '</h3>';
                if($company_logo!=''){
                    $comlogopath = fetch_aws_companyimagepath($company_logo);
                    $message .= $formatted = '<img alt="Logo" style="height: 50px;" src="'.$comlogopath.'"><br><span style="color:darkred;' . $fs . '">';
                }



                $message .= $formatted = 'Report Date: <strong>' . $day_before . '</strong></span></span><br><br><br>';

                $netGrandTotal = 0;
                $GrandTotal_billsCount = 0;
                $GrandTotal_voidBills = 0;
                $GrandTotal_avg = 0;
                $GrandTotal_creditSales = 0;


                $message .= '<table class="table table-striped table-bordered"><thead><tr><th style="width: 150px;' . $fs . '">Outlet</th><th style="' . $fs . '">Net Sales</th><th style="' . $fs . ' width:70px;">No of Bills</th><th style="' . $fs . ' width:70px;">Void Bills</th><th style="' . $fs . '">Average<br/>Sales</th><th style="' . $fs . '">Credit Sales </th><th style="' . $fs . '">Deductions</th><th style="' . $fs . '">Shift wise Net Sales</th></tr></thead><tbody>';

                if (!empty($outlets)) {
                    foreach ($outlets as $outlet) {
                        $outletID = $outlet['wareHouseAutoID'];
                        $outletDisplayName = $outlet['wareHouseCode'] . ' - ' . $outlet['wareHouseDescription'];

////+++++++++++++++++++
//$day_before="2019-01-01";
                        $data = $this->getSalesSummeryData($day_before . ' 00:00:00', $day_before . ' 23:59:59', null, $outletID, $id);

                        //$data = $this->getSalesSummeryData('2020-08-24 00:00:00', '2020-08-24 23:59:59', null, $outletID, $id);

                        //var_dump($data);exit;
                        $d = 2;
                        $netTotal = 0;
                        $lessTotal = 0;
                        $paymentTypeTransaction = 0;
                        $voidedTotal = !empty($data['voidBills']['NetTotal']) ? $data['voidBills']['NetTotal'] : 0;
                        if (!empty($data['paymentMethod'])) {
                            foreach ($data['paymentMethod'] as $report2) {
                                $netTotal += $report2['NetTotal'];
                                $paymentTypeTransaction += $report2['countTransaction'];
                            }
                        }


                        if (!empty($data['lessAmounts'])) {
                            foreach ($data['lessAmounts'] as $less) {
                                $lessTotal += $less['lessAmount'];
                            }
                        }

                        $grandTotalCount = 0;
                        $billCountTotal = 0;


                        if (!empty($data['customerTypeCount'])) {
                            foreach ($data['customerTypeCount'] as $report1) {
                                /*echo $report1['countTotal'].' - '.$report1['subTotal'].'<br/>';
                                continue;*/
                                $grandTotalCount += $report1['countTotal'];
                                $billCountTotal += $report1['subTotal'];

                            }
                        }


                        $grandTotalCount = $grandTotalCount - $data['fullyDiscountBill']['fullyDiscountBills'];


                        $grossTotal = $netTotal + $lessTotal;
                        $totalBill = $grossTotal + $voidedTotal;
                        $message .= '<tr > <td style="width: 150px; ' . $fs . ' padding:5px 2px;">';
                        $message .= $outletDisplayName;
                        $message .= '</td> <td style="text-align: right; ' . $fs . '">';

                        $netGrandTotal += $netTotal;
                        $message .= number_format($netTotal, $d);
                        $message .= '</td> <td style="text-align: center;' . $fs . '">';

                        /*No of Bills */
                        $message .= number_format($grandTotalCount);
                        $GrandTotal_billsCount += $grandTotalCount;


                        $message .= '</td> <td style="text-align: center; ' . $fs . '">';
                        $message .= isset($data['voidBills']['countTransaction']) ? $data['voidBills']['countTransaction'] : 0;
                        $GrandTotal_voidBills += $data['voidBills']['countTransaction'];

                        $message .= '</td> <td style="text-align: right; ' . $fs . '">';

                        if ($paymentTypeTransaction > 0) {
                            $message .= $avg = $grandTotalCount > 0 ? number_format(($netTotal / $paymentTypeTransaction), $d) : 0;
                            $GrandTotal_avg += $avg;
                        } else {
                            $message .= 0;
                        }

                        $message .= ' </td>';


                        $totalCreditSalesCount = 0;
                        $totalCreditSalesAmount = 0;
                        $tmpData = '';
                        if (!empty($data['creditSales'])) {
                            $tmpData .= '<table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr style="font-weight: bold;">
                        <td style="border: none !important; padding:0px 2px; ' . $fs . ' "> <strong>Credit Customer</strong> </td>
                        <td style="border: none !important; ' . $fs . '"> <strong> Qty&nbsp;&nbsp;</strong> </td>
                        <td style="border: none !important; text-align: right; ' . $fs . '"> <strong> Amount</strong> </td>
                     
                    </tr>';
                            foreach ($data['creditSales'] as $creditSale) {
                                $totalCreditSalesCount += $creditSale['countCreditSales'];
                                $totalCreditSalesAmount += $creditSale['salesAmount'];
                                $tmpData .= '<tr><td style="border: none !important; ' . $fs . '">';
                                $tmpData .= $creditSale['CustomerName'];
                                $tmpData .= '</td style="border: none !important; ' . $fs . '"><td style="border: none !important; text-align: center; ' . $fs . '">';
                                $tmpData .= $creditSale['countCreditSales'];
                                $tmpData .= '</td style="border: none !important;"><td style="border: none !important; text-align: right; ' . $fs . '" class="text-right">';
                                $tmpData .= number_format($creditSale['salesAmount'], $d);
                                $tmpData .= ' </td></tr>';
                            }
                            $tmpData .= '<tr><th style="border: none !important; ' . $fs . '">Total</th><th style="border: none !important; ' . $fs . '" class="text-center">';
                            $tmpData .= $totalCreditSalesCount;
                            $tmpData .= '</th><th style="border: none !important; text-align: right; border-top: 1px solid gray !important; ' . $fs . '" class="text-right">';
                            $GrandTotal_creditSales += $totalCreditSalesAmount;
                            $tmpData .= number_format($totalCreditSalesAmount, $d);
                            $tmpData .= '</th></tr></table>';
                        }

                        $message .= '<td style="width:300px;">' . $tmpData . '</td>';


                        $message .= '<td style="width:300px;">
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">';

                        if (!empty($data['lessAmounts'])) {
                            foreach ($data['lessAmounts'] as $less) {
                                if ($less['lessAmount'] > 0) {

                                    $message .= '<tr><td style=" border: none !important; ' . $fs . '">';
                                    $message .= $less['customerName'];
                                    $message .= '</td><td class="text-right" style=" border: none !important; ' . $fs . '">';
                                    $message .= number_format($less['lessAmount'], $d);
                                    $message .= '</td></tr>';
                                }
                            }
                            if ($lessTotal > 0) {
                                $message .= '<tr><th style="padding-top: 10px; border: none !important; ' . $fs . '">Total</th>
                                <th style="padding-top: 10px;  border: none !important; ' . $fs . ' " class="text-right">(';
                                $message .= number_format($lessTotal, $d);
                                $message .= ')</th></tr>';
                            }
                        }

                        $message .= '</table></td>';
                        $message .= '<td style="width:280px;">';
                        $shiftWiseSales = $this->Pos_batchProcess_model->get_report_paymentMethod_admin($day_before . ' 00:00:00',
                            $day_before . ' 23:59:59', null, $outletID, $id, true);
                        if (!empty($shiftWiseSales)) {
                            $tmpShiftWiseTotal = 0;
                            $message .= '<table border="0" cellspacing="0" cellpadding="0" width="100%">';
                            $message .= '<tbody><tr>
                                    <th style="border: none !important; ' . $fs . '">Start</th>
                                    <th style="border: none !important; ' . $fs . '">End</th>
                                    <th style="border: none !important; ' . $fs . ' text-align: right;">Sales</th>
                                    </tr>';
                            foreach ($shiftWiseSales as $shiftWiseSale) {
                                $tmpShiftWiseTotal += $shiftWiseSale['NetTotal'];
                                $message .= '<tr>';
                                $message .= '<td style="width:60px; text-align: left;  border: none !important; ' . $fs . '">' . $shiftWiseSale['startTime'] . '</td>';
                                $message .= '<td style="width:60px; text-align: center;  border: none !important; ' . $fs . '">' . $shiftWiseSale['endTime'] . '</td>';
                                $message .= '<td style="text-align: center;   border: none !important; text-align: right; ' . $fs . '">' .
                                    number_format($shiftWiseSale['NetTotal'], $d) . '</td>';
                                $message .= '</tr>';
                            }
                            $message .= '<tr>';
                            $message .= '<th style="text-align: left;  border: none !important; ' . $fs . '">Total</th>';
                            $message .= '<th style="text-align: right;  border: none !important; ' . $fs . '">&nbsp;</th>';
                            $message .= '<th style="text-align: center; border: none !important; border-top: 1px solid gray !important; text-align: right; ' . $fs . '">' . number_format($tmpShiftWiseTotal, $d) . '</th>';
                            $message .= '</tr>';

                            $message .= '</tbody></table>';
                        }
                        $message .= '
</td></tr>';
                    }
                }

                $message .= '</tbody><tfoot>
                            <tr>
                                <th style="' . $fs . ' padding:10px;">Total</th>
                                <th style="text-align: right; ' . $fs . '">';
                $message .= number_format($netGrandTotal, $d);
                $message .= '
                                </th>
                                <th style="text-align: center; ' . $fs . '">';
                $message .= number_format($GrandTotal_billsCount);
                $message .= '
                                </th>
                                <th style="text-align: center; ' . $fs . '">';
                $message .= number_format($GrandTotal_voidBills);
                $message .= '
                                </th>
                            <th style="text-align: right; ' . $fs . '">';
                $grossBills = $GrandTotal_billsCount;
                if ($grossBills > 0) {
                    $avgGross = $netGrandTotal / $GrandTotal_billsCount;
                } else {
                    $avgGross = 0;
                }
                $message .= number_format($avgGross, $d);
                $message .= '
    </th>
    <th style="text-align: right; ' . $fs . '"> <span style="font-weight: normal; ' . $fs . '">Total Credit Sales: </span>';
                $message .= number_format($GrandTotal_creditSales, $d);
                $message .= ' </th><th>&nbsp;</th><th>&nbsp;</th></tr></tfoot></table>';
            }


//test
            /*** Top Sales Items ***/

            $this->load->model('Pos_restaurant_model');
            $dPlace = $companyInfo['company_default_decimal'];
            $dPlace = (empty($dPlace)) ? 2 : $dPlace;

            $from_date = str_replace('/', '-', $date);
            $from_date = date('Y-m-d', strtotime("$from_date -1 day"));
            $to_date = "$from_date 23:59:59";
            $from_date = "$from_date 00:00:00";
            $outlets2 = array_column($outlets, 'wareHouseAutoID');

            $rpt_data = $this->Pos_restaurant_model->get_top_sales_items_for_daybookemail($from_date, $to_date, $outlets2, $companyID);
            //var_dump($rpt_data);exit;
            foreach ($rpt_data['ware_house'] as $row) {
                if (!empty($row['items'])) {
                    $message .= '
<pagebreak>
<h3 style="text-align: center">Top Sales Items</h3>';

                    $message .= '<table class="table table-bordered table-striped table-condensed" style="font-size: 11px;">  
                     <thead>
                     <tr style=" ">
                        <th style="vertical-align: middle; padding: 5px 4px; text-align: center" rowspan="2">Outlet</th>';

                    $message .= '<th style="width:250px; padding: 5px 4px; text-align: center" colspan="3">Items</th>';
                    $message .= '</tr>
<tr style="">

<th style="width:200px; padding: 5px 4px; text-align: center">Item</th>
                    <th style="width:80px; ; text-align: center">Qty</th>
                    <th style="width:100px; padding: 5px 4px; text-align: center">Value</th>
</tr>
</thead><tbody>';


                    if (!empty($row['items'])) {
                        if (sizeof($row['items']) == 1) {
                            $rowspan = 'rowspan="2"';
                        } else {
                            $item_count = sizeof($row['items']) + 1;
                            $rowspan = 'rowspan="' . $item_count . '"';
                        }
                        $message .= '<tr>
                         <td style="vertical-align: middle; padding: 5px 4px; text-align: center" ' . $rowspan . '>
                            <strong>' . $row['wareHouseCode'] . ' - ' . $row['wareHouseDescription'] . '</strong>
                         </td>';
                        foreach ($row['items'] as $val) {
                            $message .= ' <tr><td style="padding: 5px 4px;">' . $val['menu_des'] . '</td>
                                    <td style="padding: 5px 4px; text-align: center">' . $val['qty'] . '</td>
                                    <td style="padding: 5px 4px; text-align: right">' . number_format($val['net'], $dPlace) . '</td></tr>';

                        }
                        $message .= '</tr>';
                    }


                    $message .= '</tbody> </table>';
                }
            }
            /*** End of top sales items ***/
//print_r($message);exit;

            $message .= '<br/><br/><br/><i><span style="' . $fs . '">This is an automatically generated email, created on ' . date('d-m-Y g:i A') . '</span></i>';
            $message .= '</div>';
            //echo $message; exit;

            $this->load->library('pdf');

            $path = UPLOAD_PATH . '/uploads/pos/sales-report-' . time('Y-m-d') . ".pdf";
            $this->pdf->save_pdf_pos_sales_report($message, 'A4', 1, $path);


            $count = 0;


            if (!empty($list)) {

                $summery = '';
                foreach ($list as $email) {

                    $emailAddress = $email['email'];
                    if ($emailAddress) {
                        $mail_config['wordwrap'] = TRUE;
                        $mail_config['protocol'] = 'smtp';
                        $mail_config['smtp_host'] = 'smtp.sendgrid.net';
                        $mail_config['smtp_user'] = 'apikey';
                        $mail_config['smtp_pass'] = SEND_GRID_EMAIL_KEY;
                        $mail_config['smtp_crypto'] = 'tls';

                        $mail_config['smtp_port'] = '587';
                        $mail_config['crlf'] = "\r\n";
                        $mail_config['newline'] = "\r\n";
                        $this->load->library('email', $mail_config);
                        $this->email->clear(TRUE);
                        $this->email->from('noreply@redberylit.com', EMAIL_SYS_NAME);
                        $this->email->set_mailtype('html');
                        $this->email->subject('Daily Sales Report - ' . $day_before);
                        $this->email->message($message);
                        $this->email->attach($path);
                        $this->email->to($emailAddress);
                        $result = $this->email->send();
                        if ($result) {
                            $count++;
                            $this->email->clear(TRUE);
                            $summery .= $emailAddress . ' <br/>';
                        } else {
                            /** Error to email */
                            $mail_config['wordwrap'] = TRUE;
                            $mail_config['protocol'] = 'smtp';
                            $mail_config['smtp_host'] = 'smtp.sendgrid.net';
                            $mail_config['smtp_user'] = 'apikey';
                            $mail_config['smtp_pass'] = SEND_GRID_EMAIL_KEY;
                            $mail_config['smtp_crypto'] = 'tls';

                            $mail_config['smtp_port'] = '587';
                            $mail_config['crlf'] = "\r\n";
                            $mail_config['newline'] = "\r\n";
                            $this->load->library('email', $mail_config);
                            $this->email->from('noreply@redberylit.com', EMAIL_SYS_NAME);
                            $this->email->set_mailtype('html');
                            $this->email->subject('Daily Sales Summery - FAIL on' . $day_before);
                            $this->email->message('sales report mail sending fail to ' . $emailAddress);
                            $this->email->to('shafri@gears-int.com');
                            $tmpResult = $this->email->send();
                            if ($tmpResult) {
                                $this->email->clear(TRUE);
                            }
                        }
                    }

                }


                if ($count) {
                    /** Summary Email */
                    $mail_config['wordwrap'] = TRUE;
                    $mail_config['protocol'] = 'smtp';
                    $mail_config['smtp_host'] = 'smtp.sendgrid.net';
                    $mail_config['smtp_user'] = 'apikey';
                    $mail_config['smtp_pass'] = SEND_GRID_EMAIL_KEY;
                    $mail_config['smtp_crypto'] = 'tls';

                    $mail_config['smtp_port'] = '587';
                    $mail_config['crlf'] = "\r\n";
                    $mail_config['newline'] = "\r\n";
                    $this->load->library('email', $mail_config);

                    $this->email->from('noreply@redberylit.com', EMAIL_SYS_NAME);
                    $this->email->set_mailtype('html');
                    $this->email->subject('Email Sending Summery (Daily Sales Report) - Summary on' . $day_before);
                    $msg = 'Following email received Daily Sales Summery Report on ' . $day_before . '<br/><br/>' . $summery . '<br/><br/><br/><br/>This is auto generated email by ' . EMAIL_SYS_NAME;
                    $this->email->message($msg);
                    $this->email->to('hisham@gears-int.com');
                    $tmpResult = $this->email->send();
                    if ($tmpResult) {
                        $this->email->clear(TRUE);
                    }
                }


            }
            unset($this->db);
        }

    }

    private function getSalesSummeryData($filterDate, $date2, $cashier, $outlets, $companyID, $shift = null)
    {
        $lessAmounts = $this->Pos_batchProcess_model->get_report_lessAmount_admin($filterDate, $date2, $cashier, $outlets, $companyID);
        $lessAmounts_promotion = $this->Pos_batchProcess_model->get_report_lessAmount_promotion_admin($filterDate, $date2, $cashier, $outlets, $companyID);
        $lessAmounts_discounts = $this->Pos_batchProcess_model->get_report_salesReport_discount_admin($filterDate, $date2, $cashier, $outlets, $companyID);
        $lessAmounts_discounts_item_wise = $this->Pos_batchProcess_model->get_report_salesReport_discount_item_wise_admin($filterDate, $date2, $cashier, $outlets, $companyID);
        $lessAmounts_discountsJavaApp = $this->Pos_batchProcess_model->get_report_salesReport_javaAppDiscount_admin($filterDate, $date2, $cashier, $outlets, $companyID);
        $lessAmountsAll = array_merge($lessAmounts_discounts, $lessAmounts, $lessAmounts_promotion, $lessAmounts_discountsJavaApp, $lessAmounts_discounts_item_wise);
        $data['creditSales'] = $this->Pos_batchProcess_model->get_report_creditSales($filterDate, $date2, $cashier, $outlets, $companyID);

        $data['paymentMethod'] = $this->Pos_batchProcess_model->get_report_paymentMethod_admin($filterDate, $date2, $cashier, $outlets, $companyID);
        $data['customerTypeCount'] = $this->Pos_batchProcess_model->get_report_customerTypeCount_2_admin($filterDate, $date2, $cashier, $outlets, $companyID);
        $data['lessAmounts'] = $lessAmountsAll;
        $data['totalSales'] = $this->Pos_batchProcess_model->get_report_salesReport_totalSales_admin($filterDate, $date2, $cashier, $outlets, $companyID, $shift);
        $data['voidBills'] = $this->Pos_batchProcess_model->get_report_voidBills_admin($filterDate, $date2, $cashier, $outlets, $companyID);
        $data['fullyDiscountBill'] = $this->Pos_batchProcess_model->get_report_fullyDiscountBills_admin($filterDate, $date2, $cashier, $outlets, $companyID);

        return $data;
    }

}
