<!---- =============================================
-- File Name : pay_slip_selected_employee.php
-- Project Name : SME ERP
-- Module Name : Report - HRMS
-- Create date : 23 - November 2017
-- Description : This file contains pay slip print


-- =============================================-->

<?php

use Mpdf\Mpdf;

$mpdf = new Mpdf(
    [
        'mode'              => 'utf-8',
        'format'            => 'A5',
        'default_font_size' => 9,
        'default_font'      => 'arial',
        'margin_left'       => 5,
        'margin_right'      => 5,
        'margin_top'        => 0,
        'margin_bottom'     => 10,
        'margin_header'     => 0,
        'margin_footer'     => 3,
        'orientation'       => 'P'
    ]
);

$user = ucwords($this->session->userdata('username'));
$date = date('d F Y h:i:s A');

$stylesheet = file_get_contents('plugins/bootstrap/css/bootstrap.min.css');
$stylesheet2 = file_get_contents('plugins/bootstrap/css/style.css');
$stylesheet3 = file_get_contents('plugins/bootstrap/css/print_style.css');
$mpdf->WriteHTML($stylesheet, 1);
$mpdf->WriteHTML($stylesheet2, 1);
$mpdf->WriteHTML($stylesheet3, 1);

$countRecord = count($empIDs);
$i = 1;

$companyName = $this->common_data['company_data']['company_name'] . " (" . $this->common_data['company_data']['company_code'] . ")";
$html = "";


if (!empty($empIDs)) {
    foreach ($empIDs as $empID) {
        $thisPayrollID = $details['headerDet'][$empID][0]['payrollMasterID'];

        $keys = array_keys(array_column($payroll_data, 'payrollMasterID'), $thisPayrollID);
        $thisPayrollData = array_map(function ($k) use ($payroll_data) {
            return $payroll_data[$k];
        }, $keys);

        $thisPayrollData = $thisPayrollData[0];
        $date = $thisPayrollData['payrollYear'] . "-" . $thisPayrollData['payrollMonth'] . "-01";
        $pay_slip_month = date('F Y', strtotime($date));

        $margin = '0';
        $height = ($i == 1)? '0.7': '0.7';

        $html = '<div style="margin-top: '.$margin.'%" > &nbsp; </div>';
        $html .= '<div style="height: '.$height.'cm;"> &nbsp; </div>';
        $html .= '<table class="envoy-payslip-tbl" style="width: 90%; font-size: 12px; font-weight: bolder;" border="1">
                    <tbody>
                    <tr>
                        <th style="text-align: right; border:1px solid;border-bottom: none;" colspan="2" class="dot_matrix_font">
                            PAY SLIP
                        </th>
                    </tr>
                    <tr>
                        <th style="text-align: right; border-left:1px solid; border-right: 1px solid; border-top: none; height: 50px" colspan="2" valign="bottom" class="dot_matrix_font">
                            Month : '.$pay_slip_month.'
                        </th>
                    </tr>
                    <tr>
                        <th style="border-right: none">
                            <div class="dot_matrix_font">
                                Employee Name :
                                ' . (array_key_exists($empID, $details['headerDet']) ? $details['headerDet'][$empID][0]['empName'] : "") . '
                            </div>
                        </th>
                        <th style="text-align: right; border-left: none">
                            <div class="dot_matrix_font">
                                Employee No :
                                ' . (array_key_exists($empID, $details['headerDet']) ? $details['headerDet'][$empID][0]['secondaryCode'] : "") . '
                            </div>
                        </th>
                    </tr>
                    </tbody>
                </table>';


        $html .= '<table class="envoy-payslip-tbl" style="width:90%; height: 80% !important;">
                    <tr>
                        <th style="border-left:1px solid; border-right: 1px solid; height: 30px">&nbsp;</th>
                        <th style="border-left:1px solid; border-right: 1px solid">&nbsp;</th>
                    </tr>
                    <tr>
                        <th style="width:50%; vertical-align: top; padding-right:1px; border: 1px solid; border-top: none;">
                            <table class="" style="width: 100%;  height: 80%">
                                <tbody>';
        $addTot = 0;
        $dedTot = 0;
        $dedCount = 0;
        $default_dPlace = (array_key_exists($empID, $details['headerDet']) ? $details['headerDet'][$empID][0]['dPlace'] : 0);

        if (array_key_exists($empID, $details['salaryDec_A'])) {
            foreach ($details['salaryDec_A'][$empID] as $salDec) {
                $amount = number_format($salDec['transactionAmount'], $default_dPlace);
                $otHours = '';

                if($salDec['fromTB'] == 'OT' && !empty($details['OT_data'][$empID])){
                    /*$otHours = $details['OT_data'][$empID];
                    $otHours = (strlen($salDec['salaryDescription']) > 10 )? ' <br/> &nbsp; ('.$otHours.')': ' &nbsp; ('.$otHours.')';*/
                    foreach ($details['OT_data'][$empID] as $otDec) {
                        if($empID==$otDec['empID']){
                            if($salDec['salCatID']==$otDec['salCatID'] ){
                                //$otHours = $details['OT_data'];
                                $otHours = (strlen($salDec['salaryDescription']) > 10)? ' <br/> &nbsp; ('.$otDec['otHour'].')': ' &nbsp; ('.$otDec['otHour'].')';
                                $html .= '<tr>
                            <th class="paySheetDet_TD dot_matrix_font">' . $salDec['salaryDescription'] . ' '.$otHours.'</th>
                            <th class="paySheetDet_TD dot_matrix_font" align="right">' . $amount . '</th>
                          </tr>';
                            }
                        }
                    }
                }else{
                    $html .= '<tr>
                            <th class="paySheetDet_TD dot_matrix_font">' . $salDec['salaryDescription'] . ' '.$otHours.'</th>
                            <th class="paySheetDet_TD dot_matrix_font" align="right">' . $amount . '</th>
                          </tr>';
                }

                $addTot += number_format($salDec['transactionAmount'], $default_dPlace, '.', ''); //$salDec['dPlace'],
            }
        }

        //Monthly Additions
        if (array_key_exists($empID, $details['monthAdd'])) {
            if (!empty($details['monthAdd'][$empID])) {
                foreach ($details['monthAdd'][$empID] as $monthAdd) {
                    $html .= '<tr>
                    <th class="paySheetDet_TD dot_matrix_font">' . $monthAdd['description'] . '</th>
                    <th class="paySheetDet_TD dot_matrix_font" align="right"> ' . number_format($monthAdd['transactionAmount'], $default_dPlace) . ' </th>
                  </tr>';

                    $addTot += number_format($monthAdd['transactionAmount'], $default_dPlace, '.', ''); //$monthAdd['dPlace']
                }
            }
        }

        $html .= '<tr>
                    <th><div style="font-size: 12px"> &nbsp;</div></th>
                    <th align="right" class="sub_total"><div style="font-size: 12px; " class="dot_matrix_font">' . number_format($addTot, $default_dPlace) . '</div></th>
                  </tr>';

        $otherData = $details['employerContributions'][$empID];

        $html .= '<tr><th style="height: 25px" colspan="2">&nbsp;</th></tr>
                  <tr><th class="dot_matrix_font">EPF - Employer</th><th class="paySheetDet_TD dot_matrix_font" align="right">'.number_format(abs($otherData[6]), $default_dPlace, '.', '').'</th></tr>
                  <tr><th class="dot_matrix_font">ETF - Employer</th><th class="paySheetDet_TD dot_matrix_font" align="right">'.number_format(abs($otherData[18]), $default_dPlace, '.', '').'</th></tr>
                  <tr><th class="dot_matrix_font">EPF - Total</th><th class="paySheetDet_TD dot_matrix_font" align="right">'.number_format(abs($otherData[6]+$otherData[7]), $default_dPlace, '.', '').'</th></tr>';

        $html .= '</tbody></table>
                </th>';

        $html .= '<th style="width:50%; vertical-align: top; padding-right:5px; border: 1px solid; border-top: none;">
                    <table class="" style="width: 100%; height: 80%">
                        <tbody>
                        <tr> <th colspan="2"><span style="font-size: 12px" class="dot_matrix_font">Deduction as Direct</span></th> </tr>';

        $dedTot = 0;
        $dedCount = 0;
        $default_dPlace = (array_key_exists($empID, $details['headerDet']) ? $details['headerDet'][$empID][0]['dPlace'] : 0);

        //Fixed Salary Deduction
        if (array_key_exists($empID, $details['salaryDec_D'])) {
            if (!empty($details['salaryDec_D'][$empID])) {
                foreach ($details['salaryDec_D'][$empID] as $salDec) {
                    $amount = number_format($salDec['transactionAmount'], $default_dPlace); //$salDec['dPlace']
                    $html .= '<tr>
                        <th class="paySheetDet_TD dot_matrix_font">' . $salDec['salaryDescription'] . '</th>
                        <th class="paySheetDet_TD dot_matrix_font" align="right">' . $amount . '</th>
                      </tr>';

                    $dedTot += number_format($salDec['transactionAmount'], $default_dPlace, '.', '');
                    $dedCount++;
                }
            }
        }

        //Monthly Deduction
        if (array_key_exists($empID, $details['monthDec'])) {
            if (!empty($details['monthDec'][$empID])) {
                foreach ($details['monthDec'][$empID] as $monthDed) {
                    $html .= '<tr>
                    <th class="paySheetDet_TD dot_matrix_font">' . $monthDed['description'] . '</th>
                    <th class="paySheetDet_TD dot_matrix_font" align="right"> ' . number_format($monthDed['transactionAmount'], $default_dPlace) . ' </th>
                  </tr>';

                    $dedTot += number_format($monthDed['transactionAmount'], $default_dPlace, '.', ''); //$monthDed['dPlace'],
                    $dedCount++;
                }
            }
        }

        //SSO Payee
        if (array_key_exists($empID, $details['sso_payee'])) {
            if (!empty($details['sso_payee'][$empID])) {
                foreach ($details['sso_payee'][$empID] as $sso_payee) {
                    $html .= '<tr>
                    <th class="paySheetDet_TD dot_matrix_font">' . $sso_payee['description'] . '</th>
                    <th class="paySheetDet_TD dot_matrix_font" align="right"> ' . number_format($sso_payee['transactionAmount'], $default_dPlace) . ' </th>
                  </tr>';

                    $dedTot += number_format($sso_payee['transactionAmount'], $default_dPlace, '.', ''); //$sso_payee['dPlace'],
                    $dedCount++;
                }
            }
        }


        //Loan Deduction
        if (array_key_exists($empID, $details['loanDed'])) {
            if (!empty($details['loanDed'][$empID])) {
                foreach ($details['loanDed'][$empID] as $loanDed) {
                    $html .= '<tr>
                                <th class="paySheetDet_TD dot_matrix_font">
                                    ' . $loanDed['loanDescription'] . ' [ ' . $loanDed['loanCode'] . ' | Installment No : ' . $loanDed['installmentNo'] . ' ]
                                </th>
                                <th class="paySheetDet_TD dot_matrix_font" align="right"> ' . number_format($loanDed['transactionAmount'], $default_dPlace) . ' </th>
                              </tr>';

                    $dedTot += number_format($loanDed['transactionAmount'], $default_dPlace, '.', ''); //$default_dPlace
                    $dedCount++;
                }
            }
        }
        if ($dedCount == 0) {
            $html .= '<tr> <th>-</th> <th align="right">-</th> </tr>';
        }
        $html .= '<tr>
                    <th><div style="font-size: 12px" class="dot_matrix_font"> Total Deductions </div></th>
                    <th align="right" class="pull-right sub_total"><div style="font-size: 12px;" class="dot_matrix_font">' . number_format($dedTot, $default_dPlace) . '</div></th>
                  </tr>';

        $html .= '<tr>
                    <th style="height: 60px"><div style="font-size: 12px" class="dot_matrix_font"> Net Remuneration </div></th>
                    <th align="right" class="pull-right"><div style="font-size: 12px;" class="dot_matrix_font">' . number_format($addTot + $dedTot, $default_dPlace) . '</div></th>
                  </tr>';

        $html .= '</tbody></table></th></tr></table>';

        $mpdf->WriteHTML($html, 2);
        if ($countRecord != $i) {
            $mpdf->AddPage();
        }
        $html = "";

        $i++;
    }
}


$mpdf->Output();