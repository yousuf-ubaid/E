<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<div style="margin-top: 5%"> &nbsp; </div>

<div class="table-responsive">
    <table style="width: 100%" border="0px">
        <tbody>
        <tr>
            
            <td style="width:60%;" valign="top">
                <table border="0px">
                    <tr>
                        <td colspan="2" style="font-size:16px">
                            
                                <strong><?php echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ').'; ?></strong>
                           
                        </td>
                    </tr>
                    <tr>
                        <td><h5 style="margin-bottom: 0px"><?php echo $this->lang->line('profile_pay_slip'); ?></td>
                    </tr>
                    <tr>
                        <?php $date = $masterData['payrollYear'] . "-" . $masterData['payrollMonth'] . "-01" ?>
                        <td colspan="2">
                            <h6 style="margin-bottom: 0px"><?php echo $this->lang->line('profile_period'); ?> - <?php echo date('F ` Y', strtotime($date)); ?></h6>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><h6 style="margin-bottom: 0px"> <?php echo $masterData['narration']; ?> </h6></td>
                    </tr>
                </table>
            </td>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td align="right">
                            <img alt="Logo" style="height: 100px; max-width 150px;"
                                 src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<hr>

<div class="table-responsive" style="margin-bottom: 2%; margin-top: 2%">
  <table class="paySheet_TB" style="width: 100%; font-size: 11px;" border="0px">
    <tbody>
      <tr>
        <td style="width:50%;text-align:left">
          <table style="width:100%">
            <tr>
              <td style="font-size: 11px;">
                <div style="font-size: 11px;" class="paySheet_TD">
                  <strong> Employee <?php echo $this->lang->line('common_name'); ?> </strong>
                </div>
              </td>
              <td style="font-size: 11px;">
                <div style="font-size: 11px;" class="paySheet_TD"> :</div>
              </td>
              <td style="font-size: 11px;" >
                <div style="font-size: 11px;" class="paySheet_TD"> <?php echo $details['headerDet']['empName']; ?> </div>
              </td>
            </tr>
            <tr>
              <td style="font-size: 11px;">
                <div style="font-size: 11px;" class="paySheet_TD">
                  <strong> Division </strong>
                </div>
              </td>
              <td style="font-size: 11px;">
                <div style="font-size: 11px;" class="paySheet_TD"> :</div>
              </td>
              <td style="font-size: 11px;" >
                <div style="font-size: 11px;" class="paySheet_TD"> <?php echo $details['headerDet2']['detail_code']; ?> </div>
              </td>
            </tr>
            <tr>
              <td style="font-size: 11px;">
                <div style="font-size: 11px;" class="paySheet_TD">
                  <strong> Department </strong>
                </div>
              </td>
              <td style="font-size: 11px;">
                <div style="font-size: 11px;" class="paySheet_TD"> :</div>
              </td>
              <td style="font-size: 11px;" >
                <div style="font-size: 11px;" class="paySheet_TD"> <?php echo $details['headerDet2']['detail_description']; ?> </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;">
                  <strong> <?php echo $this->lang->line('common_designation'); ?> </strong>
                </div>
              </td>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;"> :</div>
              </td>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;"> <?php echo $details['headerDet']['Designation']; ?> </div>
              </td>
            </tr>
            
            <tr>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;">
                  <strong> Grade </strong>
                </div>
              </td>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;"> :</div>
              </td>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;"> <?php echo $details['headerDet_grade']['gradeDescription']; ?></div>
              </td>
            </tr>
            <!--<tr>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;">
                  <strong> Bank Name </strong>
                </div>
              </td>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;"> :</div>
              </td>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;"> <?php //echo $details['headerDet']['bankName']; ?> </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;">
                  <strong> Bank Branch </strong>
                </div>
              </td>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;"> :</div>
              </td>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;"> <?php //echo $details['headerDet']['branchName']; ?> </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;">
                  <strong> Bank Account No </strong>
                </div>
              </td>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;"> :</div>
              </td>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;"> <?php //echo $details['headerDet']['accountNo']; ?> </div>
              </td>
            </tr>   -->         
          </table>
        </td>
        <td style="width:50%;text-align:left">
          <table style="width:100%">
            
            <tr>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;">
                  <strong> Start Date </strong>
                </div>
              </td>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;"> :</div>
              </td>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;"> <?php echo $details['payslip_date_info']['start_dt']; ?> </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;">
                  <strong> End Date </strong>
                </div>
              </td>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;"> :</div>
              </td>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;"> <?php echo $details['payslip_date_info']['end_dt']; ?></div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;">
                  <strong> Days Paid </strong>
                </div>
              </td>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;"> :</div>
              </td>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;"> <?php echo $details['payslip_date_info']['paid_days']; ?> </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;">
                  <strong> Joining Date</strong>
                </div>
              </td>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;"> :</div>
              </td>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;"> <?php echo $details['headerDet']['DOJ']; ?> </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;">
                  <strong> <?php echo $this->lang->line('common_currency'); ?> </strong>
                </div>
              </td>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;"> :</div>
              </td>
              <td>
                <div class="paySheet_TD" style="font-size: 11px;"> <?php echo $details['headerDet']['transactionCurrency']; ?> </div>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </tbody>
  </table>
</div>


<table border="0" style="font-size:12px; background-color: #ffffff; width:100%;">
    <tr>
        <td style="width:50px; vertical-align: top; padding-right:10px;">
            <div style="font-size:15px;padding-left: 15px;"><?php echo $this->lang->line('profile_earnings'); ?></div>
            <div class="table-responsive">
                <table class="<?php echo table_class(); ?>" style="width: 100%">
                    <thead>
                    <tr>
                        <th class="theadtr" style="width: 70%"><?php echo $this->lang->line('common_description'); ?></th>
                        <th class="theadtr" style="width: 30%"><?php echo $this->lang->line('common_amount'); ?>
                            [ <?php echo $details['headerDet']['transactionCurrency']; ?> ]
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    $addTot = 0;
                    $dedTot = 0;
                    $dedCount = 0;
                    $default_dPlace = $details['headerDet']['dPlace'];
                    /*  echo '<tr><th colspan="2" style=""><div style="font-size: 9px">Additions</div></th></tr>';*/
                    //Fixed salary Additions
                    foreach ($details['salaryDec_A'] as $salDec) {
                        $amount = number_format($salDec['transactionAmount'], $default_dPlace);
                        echo '<tr>
                    <td class="paySheetDet_TD">' . $salDec['salaryDescription'] . '</td>
                    <td class="paySheetDet_TD" align="right">' . $amount . '</td>
                  </tr>';

                        $addTot += number_format($salDec['transactionAmount'], $default_dPlace, '.', ''); //$salDec['dPlace'],
                    }

                    //Monthly Additions
                    if (!empty($details['monthAdd'])) {
                        foreach ($details['monthAdd'] as $monthAdd) {
                            echo '<tr>
                    <td class="paySheetDet_TD">' . $monthAdd['description'] . '</td>
                    <td class="paySheetDet_TD" align="right"> ' . number_format($monthAdd['transactionAmount'], $default_dPlace) . ' </td>
                  </tr>';

                            $addTot += number_format($monthAdd['transactionAmount'], $default_dPlace, '.', ''); //$monthAdd['dPlace']
                        }
                    }

                    echo '<tr>
                <th><div style="font-size: 9px">' . $this->lang->line('profile_total_earnings') . '</div></th>
                <th align="right" class="pull-right"><div style="font-size: 9px;">' . number_format($addTot, $default_dPlace) . '</div></th>
              </tr>';
                    /*echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';*/
                    /*  echo '<tr>
                              <th><div style="font-size: 9px"> Total</div></th>
                              <th align="right"><div style="font-size: 9px;">'.number_format( ($addTot - $dedTot), $default_dPlace).'</div></th>
                            </tr>';*/
                    ?>
                    </tbody>
                </table>

            </div>
        </td>
        <td style="width:50px; vertical-align: top; padding-right:10px;">
            <div style="font-size:15px;"><?php echo $this->lang->line('profile_deductions'); ?></div>

            <div class="">
                <table class="<?php echo table_class(); ?>" style="width: 100%">
                    <thead>
                    <tr>
                        <th class="theadtr" style="width: 70%"><?php echo $this->lang->line('common_description'); ?></th>
                        <th class="theadtr" style="width: 30%"><?php echo $this->lang->line('common_amount'); ?>
                            [ <?php echo $details['headerDet']['transactionCurrency']; ?> ]
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php

                    $dedTot = 0;
                    $dedCount = 0;
                    $default_dPlace = $details['headerDet']['dPlace'];

                    /*echo '<tr><th colspan="2" style=""><div style="font-size: 9px">Deductions</div></th></tr>';*/

                    //Fixed Salary Deduction
                    if (!empty($details['salaryDec_D'])) {
                        foreach ($details['salaryDec_D'] as $salDec) {
                            $amount = number_format($salDec['transactionAmount'], $default_dPlace); //$salDec['dPlace']
                            echo '<tr>
                        <td class="paySheetDet_TD">' . $salDec['salaryDescription'] . '</td>
                        <td class="paySheetDet_TD" align="right">' . $amount . '</td>
                      </tr>';

                            $dedTot += number_format($salDec['transactionAmount'], $default_dPlace, '.', '');
                            $dedCount++;
                        }
                    }

                    //Monthly Deduction
                    if (!empty($details['monthDec'])) {
                        foreach ($details['monthDec'] as $monthDed) {
                            echo '<tr>
                    <td class="paySheetDet_TD">' . $monthDed['description'] . '</td>
                    <td class="paySheetDet_TD" align="right"> ' . number_format($monthDed['transactionAmount'], $default_dPlace) . ' </td>
                  </tr>';

                            $dedTot += number_format($monthDed['transactionAmount'], $default_dPlace, '.', ''); //$monthDed['dPlace'],
                            $dedCount++;
                        }
                    }

                    //SSO Payee
                    if (!empty($details['sso_payee'])) {
                        foreach ($details['sso_payee'] as $sso_payee) {
                            echo '<tr>
                    <td class="paySheetDet_TD">' . $sso_payee['description'] . '</td>
                    <td class="paySheetDet_TD" align="right"> ' . number_format($sso_payee['transactionAmount'], $default_dPlace) . ' </td>
                  </tr>';

                            $dedTot += number_format($sso_payee['transactionAmount'], $default_dPlace, '.', ''); //$sso_payee['dPlace'],
                            $dedCount++;
                        }
                    }

                    //Loan Deduction
                    if (!empty($details['loanDed'])) {
                        foreach ($details['loanDed'] as $loanDed) {
                            echo '<tr>
                    <td class="paySheetDet_TD">' . $loanDed['loanDescription'] . ' [ ' . $loanDed['loanCode'] . ' | ' . $this->lang->line('profile_installment_no') . ' : ' . $loanDed['installmentNo'] . ' ]</td>
                    <td class="paySheetDet_TD" align="right"> ' . number_format($loanDed['transactionAmount'], $default_dPlace) . ' </td>
                  </tr>';

                            $dedTot += number_format($loanDed['transactionAmount'], $default_dPlace, '.', ''); //$default_dPlace
                            $dedCount++;
                        }
                    }


                    if ($dedCount == 0) {
                        echo '<tr> <td>-</td> <td align="right">-</td> </tr>';
                    }

                    echo '<tr>
                <th><div style="font-size: 9px">' . $this->lang->line('profile_total_deductions') . '</div></th>
                <th align="right" class="pull-right"><div style="font-size: 9px;">' . number_format($dedTot, $default_dPlace) . '</div></th>
              </tr>';
                    /*        echo '<tr>
                                    <th><div style="font-size: 9px"> Total</div></th>
                                    <th align="right"><div style="font-size: 9px;">'.number_format( ($addTot - $dedTot), $default_dPlace).'</div></th>
                                  </tr>';*/
                    ?>
                    </tbody>
                </table>

            </div>
        </td>
    </tr>
</table>
<table style="font-size:12px; width: 100%;">
    <tr>
        <td style="text-align: right; padding: 2px;padding-right: 11px;"><strong><?php echo $this->lang->line('profile_net_pay')?>
                : <?php echo number_format($addTot + $dedTot, $default_dPlace); ?></strong></td>
    </tr>
</table>

<table border="0" style="font-size:12px; background-color: #ffffff; width:100%;">
    <tr>
        <td style="width:50px; vertical-align: top; padding-right:10px;">
            <?php if (!empty($details['bankTransferDed'])) { ?>
            <div class="table-responsive">
                <div style="margin-top: 5%"><?php echo $this->lang->line('common_bank_transfer_details')?></div>
                <table class="<?php echo table_class(); ?>" style="width: 100%;">
                    <thead>
                    <tr>
                        <th class="theadtr" style="width: 40%"><?php echo $this->lang->line('common_bank')?></th>
                        <th class="theadtr" style="width: 15%"><?php echo $this->lang->line('common_swift_code')?></th>
                        <th class="theadtr" style="width: 15%"><?php echo $this->lang->line('common_account_no')?></th>
                        <th class="theadtr" style="width: 30%"><?php echo $this->lang->line('common_amount')?>
                            [ <?php echo $details['headerDet']['transactionCurrency']; ?> ]
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    $totBnkTr = 0;
                    foreach ($details['bankTransferDed'] as $bnk) {
                        echo '
            <tr>
                <td>' . $bnk['bankName'] . '</td>
                <td>' . $bnk['swiftCode'] . '</td>
                <td align="center">' . $bnk['accountNo'] . '</td>
                <td align="right">' . number_format($bnk['transactionAmount'], $default_dPlace) . '</td>
            </tr>';

                        $thisTot = number_format($bnk['transactionAmount'], $bnk['dPlace'], '.', '');
                        $totBnkTr += $thisTot;
                    }

                    if (count($details['bankTransferDed']) > 1) {
                        echo
                            '<tr>
                     <th colspan="3"><div style="font-size: 9px;">' . $this->lang->line('common_total') . '</div></th>
                     <th align="right"><div style="font-size: 9px;">' . number_format($totBnkTr, $default_dPlace) . '</div></th>
                </tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <?php
            }
            ?>
        </td>
    </tr>
</table>

<table border="0" style="font-size:12px; background-color: #ffffff; width:100%;">
    <tr>
        <td style="width:50px; vertical-align: top; padding-right:10px;">
            <?php
            if (!empty($details['salaryNonBankTransfer'])) { ?>

                <div class="table-responsive">
                    <div style="margin-top: 5%"><?php echo $this->lang->line('profile_salary_transfer_details')?></div>
                    <table class="<?php echo table_class(); ?>" style="width: 100%;">
                        <thead>
                        <tr>
                            <?php
                            if ($details['salaryNonBankTransfer']['payByBankID'] != null) {
                                echo
                                '<th class="theadtr" style="width: 40%">' . $this->lang->line('common_bank') . '</th>
                <th class="theadtr" style="width: 15%">Cheque No</th>';
                            }
                            echo
                                '<th class="theadtr" style="width: 40%">' . $this->lang->line('common_date') . '</th>
             <th class="theadtr" style="width: 30%">' . $this->lang->line('common_amount') . ' [ ' . $details['headerDet']['transactionCurrency'] . ' ] </th>';
                            ?>
                        </tr>
                        </thead>

                        <tbody>
                        <tr>
                            <?php
                            if ($details['salaryNonBankTransfer']['payByBankID'] != null) {
                                echo
                                    '<td>' . $details['salaryNonBankTransfer']['bankName'] . '</td>
                <td>' . $details['salaryNonBankTransfer']['chequeNo'] . '</td>';
                            }
                            echo
                                '<td align="center">' . $details['salaryNonBankTransfer']['processedDate'] . '</td>
                <td align="right">' . number_format($details['salaryNonBankTransfer']['transactionAmount'], $default_dPlace) . '</td>';
                            ?>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <?php
            }
            ?>
        </td>
    </tr>
</table>

<table border="0" style="font-size:12px; background-color: #ffffff; width:100%;">
    <tr>
        <td style="width:50px; vertical-align: top; padding-right:10px;">
            <?php
            if (!empty($details['loanIntPending']) && $details['loanIntPending'][0]['loanCode'] != null) { ?>

                <div class="table-responsive">
                    <div style="margin-top: 5%"><?php echo $this->lang->line('profile_loan_details') ?></div>
                    <table class="<?php echo table_class(); ?>" style="width: 100%;">
                        <thead>
                        <tr>
                            <th class="theadtr" style="width: 15%"><?php echo $this->lang->line('profile_loan_code') ?></th>
                            <th class="theadtr" style="width: 40%"><?php echo $this->lang->line('common_description') ?></th>
                            <th class="theadtr" style="width: 25%"><?php echo $this->lang->line('profile_no_pending_nstallments') ?></th>
                            <th class="theadtr" style="width: 20%"> <?php echo $this->lang->line('profile_pending_amount') ?>
                                [ <?php echo $details['headerDet']['transactionCurrency']; ?> ]
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($details['loanIntPending'] as $pending) {
                            echo '
            <tr>
                <td>' . $pending['loanCode'] . '</td>
                <td>' . $pending['loanDescription'] . '</td>
                <td align="center">' . $pending['pending_Int'] . '</td>
                <td align="right">' . number_format($pending['trAmount'], $default_dPlace) . '</td>
            </tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

                <?php
            }
            ?>
        </td>
    </tr>
</table>
<?php
if (!empty($leaveDet)) { ?>
    <div class="table-responsive">
        <div style="margin-top: 5%"><?php echo $this->lang->line('profile_leave_details') ?></div>
        <table class="<?php echo table_class(); ?>" style="width: 100%;">
            <thead>
            <tr>
                <th class="theadtr" style="auto"><?php echo $this->lang->line('common_type') ?></th>
                <th class="theadtr" style="auto"><?php echo $this->lang->line('profile_policy') ?></th>
                <th class="theadtr" style="auto"><?php echo $this->lang->line('profile_entitled') ?></th>
                <th class="theadtr" style="auto"><?php echo $this->lang->line('profile_taken') ?></th>
                <th class="theadtr" style="auto"><?php echo $this->lang->line('common_balance') ?></th>
            </tr>
            </thead>

            <tbody>
            <?php
            foreach ($leaveDet as $leave) {
                $leaveTaken = ($leave['leaveTaken'] == '') ? '-' : $leave['leaveTaken'];
                $entitled = ($leave['accrued'] == '') ? '-' : $leave['accrued'];
                $balance = (!is_int($leave['days'])) ? round($leave['days'], 1) : round($leave['days']);
                echo
                    '<tr>
                <td>' . $leave['description'] . '</td>
                <td>' . $leave['policyDescription'] . '</td>
                 <td align="right">' . $entitled . '</td>
                <td align="right">' . $leaveTaken . '</td>
                 <td align="right">' . $balance . '</td>
             
            </tr>';
            }
            ?>
            </tr>
            </tbody>
        </table>
    </div>
    <?php
}

?>
<script>
    $('.review').removeClass('hidden');
    $('#hidden_payroll_id').val(<?=$payrollMasterID?>);
</script>
<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-08-09
 * Time: 3:54 PM
 */
