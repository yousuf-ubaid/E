<!---- =============================================
-- File Name : pay_slip_selected_employee.php
-- Project Name : SME ERP
-- Module Name : Report - HRMS
-- Create date : 23 - March 2017
-- Description : This file contains pay slip print

-- REVISION HISTORY
    - Modification date : 2017-09-22
    - Change the pay slip according to multiple payroll per month logic

-- =============================================-->

<?php

use Mpdf\Mpdf;

$mpdf = new Mpdf(
    [
        'mode'              => 'utf-8',
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
    ]
);

$user = ucwords($this->session->userdata('username'));
$date = date('l jS \of F Y h:i:s A');

$stylesheet = file_get_contents('plugins/bootstrap/css/bootstrap.min.css');
$stylesheet2 = file_get_contents('plugins/bootstrap/css/style.css');
$stylesheet3 = file_get_contents('plugins/bootstrap/css/print_style.css');
$mpdf->SetFooter('Printed By : ' . $user . '|Page : {PAGENO}|' . $date);
$mpdf->WriteHTML($stylesheet, 1);
$mpdf->WriteHTML($stylesheet2, 1);
$mpdf->WriteHTML($stylesheet3, 1);

$countRecord = count($empIDs);
$i = 1;

$companyName = $this->common_data['company_data']['company_name'] . " (" . $this->common_data['company_data']['company_code'] . ")";
$html = "";
$html = '<div style="margin-top: 5%" > &nbsp; </div>';

if (!empty($empIDs)) {
    foreach ($empIDs as $empID) {
        $thisPayrollID = $details['headerDet'][$empID][0]['payrollMasterID'];

        $keys = array_keys(array_column($payroll_data, 'payrollMasterID'), $thisPayrollID);
        $thisPayrollData = array_map(function ($k) use ($payroll_data) {
            return $payroll_data[$k];
        }, $keys);

        $thisPayrollData = $thisPayrollData[0];
        $date = $thisPayrollData['payrollYear'] . "-" . $thisPayrollData['payrollMonth'] . "-01";

        $html .= '<div class="table-responsive">
                    <table style="width: 100%" border="0px">
                        <tbody>
                            <tr>
                                <td style="width:40%;">
                                    <table>
                                        <tr>
                                            <td>
                                                <img alt="Logo" style="height: 130px" src="' . mPDFImage . $this->common_data['company_data']['company_logo'] . '">
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="width:60%;" valign="top">
                                    <table border="0px">
                                        <tr>
                                            <td colspan="2">
                                                <h4><strong>' . $companyName . '</strong></h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><h5 style="margin-bottom: 0px">Pay Slip</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"> <h6 style="margin-bottom: 0px">Period - ' . date('F ` Y', strtotime($date)) . '</h6> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"> <h6 style="margin-bottom: 0px">' . $thisPayrollData['narration'] . '</h6> </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                         </tbody>
                    </table>
                  </div>';
        $html .= "<hr>";


        $html .= '<div class="table-responsive" style="margin-bottom: 2%; margin-top: 2%; margin-left: -15px">
                    <table class="paySheet_TB" style="width: 50%; font-size: 12px; font-weight: bolder" border="0px">
                        <tbody>
                        <tr>
                            <td> <div class="paySheet_TD"> <strong>Name </strong> </div></td>
                            <td> <div class="paySheet_TD"> : </div></td>
                            <td>
                                <div class="paySheet_TD">
                                    ' . (array_key_exists($empID, $details['headerDet']) ? $details['headerDet'][$empID][0]['empName'] : "") . '
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td> <div class="paySheet_TD"> <strong>Designation</strong>  </div></td>
                            <td> <div class="paySheet_TD"> : </div></td>
                            <td>
                                <div class="paySheet_TD">
                                    ' .(array_key_exists($empID, $details['headerDet']) ? $details['headerDet'][$empID][0]['Designation'] : "") . '
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td> <div class="paySheet_TD"> <strong>Currency</strong>  </div></td>
                            <td> <div class="paySheet_TD"> : </div></td>
                            <td>
                                <div class="paySheet_TD">
                                    ' . (array_key_exists($empID, $details['headerDet']) ? $details['headerDet'][$empID][0]['transactionCurrency'] : "") . '
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                  </div>';

        $html .= '<table border="0" style="font-size:12px; background-color: #ffffff; width:100%;"><tr>';
        $addTot = 0;
        $dedTot = 0;
        $dedCount = 0;
        $default_dPlace = (array_key_exists($empID, $details['headerDet']) ? $details['headerDet'][$empID][0]['dPlace'] : 0);
        $html .= '<td style="width:50px; vertical-align: top; padding-right:10px;">
            <div style="font-size:15px; height: 10px;  padding-left: 15px;">Earnings</div>
            <div class="table-responsive">
                <table class="'.table_class().'" style="width: 100%">
                    <thead>
                    <tr>
                        <th class="theadtr" style="width: 70%">Description</th>
                        <th class="theadtr" style="width: 30%">
                            Amount [' . (array_key_exists($empID, $details['headerDet']) ? $details['headerDet'][$empID][0]['transactionCurrency'] : "") . ']
                        </th>
                    </tr>
                    </thead>
                    <tbody>';


        if (array_key_exists($empID, $details['salaryDec_A'])) {
            foreach ($details['salaryDec_A'][$empID] as $salDec) {
                $amount = number_format($salDec['transactionAmount'], $default_dPlace);
                $html .= '<tr>
                    <td class="paySheetDet_TD">' . $salDec['salaryDescription'] . '</td>
                    <td class="paySheetDet_TD" align="right">' . $amount . '</td>
                  </tr>';

                $addTot += number_format($salDec['transactionAmount'], $default_dPlace, '.', ''); //$salDec['dPlace'],
            }
        }


        //Monthly Additions
        if (array_key_exists($empID, $details['monthAdd'])) {
            if (!empty($details['monthAdd'][$empID])) {
                foreach ($details['monthAdd'][$empID] as $monthAdd) {
                    $html .= '<tr>
                    <td class="paySheetDet_TD">' . $monthAdd['description'] . '</td>
                    <td class="paySheetDet_TD" align="right"> ' . number_format($monthAdd['transactionAmount'], $default_dPlace) . ' </td>
                  </tr>';

                    $addTot += number_format($monthAdd['transactionAmount'], $default_dPlace, '.', ''); //$monthAdd['dPlace']
                }
            }
        }

        $html .= '<tr>
                <th><div style="font-size: 9px"> Total Earnings</div></th>
                <th align="right"><div style="font-size: 9px;">' . number_format($addTot, $default_dPlace) . '</div></th>
              </tr>';

        $html .= '</tbody></table></div></td>';

        $html .= '<td style="width:50px; vertical-align: top; padding-right:10px;">
                    <div style="font-size:15px; height: 10px; padding-left: 15px;">Deductions</div>
                    <div class="table-responsive">
                        <table class="'.table_class().'" style="width: 100%">
                            <thead>
                            <tr>
                                <th class="theadtr" style="width: 70%">Description</th>
                                <th class="theadtr" style="width: 30%">Amount
                                    [' . (array_key_exists($empID, $details['headerDet']) ? $details['headerDet'][$empID][0]['transactionCurrency'] : "") . ']
                                </th>
                            </tr>
                            </thead>
                            <tbody>';

        $dedTot = 0;
        $dedCount = 0;
        $default_dPlace = (array_key_exists($empID, $details['headerDet']) ? $details['headerDet'][$empID][0]['dPlace'] : 0);

        //Fixed Salary Deduction
        if (array_key_exists($empID, $details['salaryDec_D'])) {
            if (!empty($details['salaryDec_D'][$empID])) {
                foreach ($details['salaryDec_D'][$empID] as $salDec) {
                    $amount = number_format($salDec['transactionAmount'], $default_dPlace); //$salDec['dPlace']
                    $html .= '<tr>
                        <td class="paySheetDet_TD">' . $salDec['salaryDescription'] . '</td>
                        <td class="paySheetDet_TD" align="right">' . $amount . '</td>
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
                    <td class="paySheetDet_TD">' . $monthDed['description'] . '</td>
                    <td class="paySheetDet_TD" align="right"> ' . number_format($monthDed['transactionAmount'], $default_dPlace) . ' </td>
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
                    <td class="paySheetDet_TD">' . $sso_payee['description'] . '</td>
                    <td class="paySheetDet_TD" align="right"> ' . number_format($sso_payee['transactionAmount'], $default_dPlace) . ' </td>
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
                                <td class="paySheetDet_TD">
                                    ' . $loanDed['loanDescription'] . ' [ ' . $loanDed['loanCode'] . ' | Installment No : ' . $loanDed['installmentNo'] . ' ]
                                </td>
                                <td class="paySheetDet_TD" align="right"> ' . number_format($loanDed['transactionAmount'], $default_dPlace) . ' </td>
                              </tr>';

                    $dedTot += number_format($loanDed['transactionAmount'], $default_dPlace, '.', ''); //$default_dPlace
                    $dedCount++;
                }
            }
        }
        if ($dedCount == 0) {
            $html .= '<tr> <td>-</td> <td align="right">-</td> </tr>';
        }
        $html .= '<tr>
                    <th><div style="font-size: 9px"> Total Deductions </div></th>
                    <th align="right"><div style="font-size: 9px;">' . number_format($dedTot, $default_dPlace) . '</div></th>
                  </tr>';

        $html .= '</tbody></table></div></td></tr></table>';

        $html .= '<table style="font-size:12px; width: 100%;">
                    <tr>
                        <td style="text-align: right; padding-right: 10px !important;"><strong>Net Pay :' . number_format($addTot + $dedTot, $default_dPlace) . '</strong></td>
                    </tr>
                  </table>';

        if (array_key_exists($empID, $details['bankTransferDed'])) {
            if (!empty($details['bankTransferDed'][$empID])) {
                $html .= '<div class="table-responsive">
                            <div style="margin-top: 5%">Bank Transfer Details</div>
                            <table class="'.table_class().'" style="width: 100%;">
                                <thead>
                                <tr>
                                    <th class="theadtr" style="width: 40%">Bank Name</th>
                                    <th class="theadtr" style="width: 15%">Swift Code</th>
                                    <th class="theadtr" style="width: 15%">Account No</th>
                                    <th class="theadtr" style="width: 30%">Amount
                                        [' . (array_key_exists($empID, $details['headerDet']) ? $details['headerDet'][$empID][0]['transactionCurrency'] : "") . ']
                                    </th>
                                </tr>
                                </thead>
                                <tbody>';


                $totBnkTr = 0;
                foreach ($details['bankTransferDed'][$empID] as $bnk) {
                    $html .= '<tr>
                                  <td>' . $bnk['bankName'] . '</td>
                                  <td>' . $bnk['swiftCode'] . '</td>
                                  <td align="center">' . $bnk['accountNo'] . '</td>
                                  <td align="right">' . number_format($bnk['transactionAmount'], $default_dPlace) . '</td>
                              </tr>';
                    $thisTot = number_format($bnk['transactionAmount'], $bnk['dPlace'], '.', '');
                    $totBnkTr += $thisTot;
                }
                if (count($details['bankTransferDed'][$empID]) > 1) {
                    $html .=
                        '<tr>
                     <th colspan="3"><div style="font-size: 9px;">Total</div></th>
                     <th align="right"><div style="font-size: 9px;">' . number_format($totBnkTr, $default_dPlace) . '</div></th>
                </tr>';
                }
            }
        }
        $html .= '</tbody></table></div>';


        if (array_key_exists($empID, $details['salaryNonBankTransfer'])) {
            if (!empty($details['salaryNonBankTransfer'][$empID])) {
                $html .= '<div class="table-responsive">
                            <div style="margin-top: 5%">Salary Transfer Details</div>
                            <table class="' . table_class() . '" style="width: 100%;">
                                <thead>
                                <tr>';
                                if ($details['salaryNonBankTransfer'][$empID][0]['payByBankID'] != null) {
                                    $html .= '<th class="theadtr" style="width: 40%">Bank Name</th>
                                              <th class="theadtr" style="width: 15%">Cheque No</th>';
                                }

                                $html .=
                                '<th class="theadtr" style="width: 40%">Date</th>
                                <th class="theadtr" style="width: 30%">
                                    Amount [ ' . (array_key_exists($empID, $details['headerDet']) ? $details['headerDet'][$empID][0]['transactionCurrency'] : "") . ' ]
                                </th>';

                $html .= '</tr></thead><tbody><tr>';

                if ($details['salaryNonBankTransfer'][$empID][0]['payByBankID'] != null) {
                    $html .=
                        '<td>' . $details['salaryNonBankTransfer'][$empID][0]['bankName'] . '</td>
                         <td>' . $details['salaryNonBankTransfer'][$empID][0]['chequeNo'] . '</td>';
                }

                $html .= '<td align="center">' . $details['salaryNonBankTransfer'][$empID][0]['processedDate'] . '</td>
                          <td align="right">' . number_format($details['salaryNonBankTransfer'][$empID][0]['transactionAmount'], $default_dPlace) . '</td>';
                $html .= '</tr></tbody></table></div>';
            }
        }


        if (array_key_exists($empID, $details['loanIntPending'])) {
            $html .= '<div class="table-responsive">
                <div style="margin-top: 5%">Loan Details</div>
                <table class="'.table_class().'" style="width: 100%;">
                    <thead>
                    <tr>
                        <th class="theadtr" style="width: 15%">Loan Code</th>
                        <th class="theadtr" style="width: 40%">Description</th>
                        <th class="theadtr" style="width: 5%">No.Pending Installments</th>
                        <th class="theadtr" style="width: 15%"> Pending Amount
                            [' . (array_key_exists($empID, $details['headerDet']) ? $details['headerDet'][$empID][0]['transactionCurrency'] : "") . ']
                        </th>
                    </tr>
                    </thead>
                    <tbody>';
            if (!empty($details['loanIntPending'][$empID])) {

                foreach ($details['loanIntPending'][$empID] as $pending) {
                    $html .= '
                        <tr>
                            <td>' . $pending['loanCode'] . '</td>
                            <td>' . $pending['loanDescription'] . '</td>
                            <td align="center">' . $pending['pending_Int'] . '</td>
                            <td align="right">' . number_format($pending['trAmount'], $default_dPlace) . '</td>
                        </tr>';
                }
            }
            $html .= '</tbody></table></div>';
        }


        if (!empty($leaveDet)) {
            $html .= '<div class="table-responsive">
            <div style="margin-top: 5%">Leave Details</div>
            <table class="'.table_class().'" style="width: 100%;">
                <thead>
                <tr>
                    <th class="theadtr" style="auto">Type</th>
                    <th class="theadtr" style="auto">Policy</th>
                    <th class="theadtr" style="auto">Entitled</th>
                    <th class="theadtr" style="auto">Taken</th>
                    <th class="theadtr" style="auto">Balance</th>
                </tr>
                </thead>

                <tbody>';

                foreach ($leaveDet as $leave) {
                    $leaveTaken = ($leave['leaveTaken'] == '') ? '-' : $leave['leaveTaken'];
                    $entitled = ($leave['accrued'] == '') ? '-' : $leave['accrued'];
                    $balance = (!is_int($leave['days'])) ? round($leave['days'], 1) : round($leave['days']);
                    $html .=
                        '<tr>
                    <td>' . $leave['description'] . '</td>
                    <td>' . $leave['policyDescription'] . '</td>
                     <td align="right">' . $entitled . '</td>
                    <td align="right">' . $leaveTaken . '</td>
                     <td align="right">' . $balance . '</td>

                </tr>';
                }
            $html .= '</tr></tbody></table></div>';
        }

        $mpdf->WriteHTML($html, 2);
        if ($countRecord != $i) {
            $mpdf->AddPage();
        }
        $html = "";

        $i++;
    }
}


$mpdf->Output();

